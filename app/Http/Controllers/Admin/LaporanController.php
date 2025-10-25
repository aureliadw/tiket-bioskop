<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemesanan;
use App\Models\Film;
use App\Models\Studio;
use App\Models\Jadwal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * ============================================
     * INDEX - Halaman Utama Laporan
     * ============================================
     * Menampilkan pilihan jenis laporan
     */
    public function index()
    {
        return view('admin.laporan.index');
    }

    /**
     * ============================================
     * LAPORAN TRANSAKSI/PEMESANAN
     * ============================================
     * Filter: tanggal, status pembayaran, tipe pemesanan
     */
    public function transaksi(Request $request)
    {
        // Default filter: bulan ini
        $tanggalDari = $request->input('tanggal_dari', now()->startOfMonth()->format('Y-m-d'));
        $tanggalSampai = $request->input('tanggal_sampai', now()->endOfMonth()->format('Y-m-d'));
        $statusPembayaran = $request->input('status_pembayaran', 'all');
        $tipePemesanan = $request->input('tipe_pemesanan', 'all');

        // Query data pemesanan
        $query = Pemesanan::with([
                'user',
                'jadwal.film',
                'jadwal.studio',
                'pembayaran',
                'kursi'
            ])
            ->whereBetween('tanggal_pesan', [$tanggalDari . ' 00:00:00', $tanggalSampai . ' 23:59:59']);

        // Filter status pembayaran
        if ($statusPembayaran !== 'all') {
            $query->where('status_pembayaran', $statusPembayaran);
        }

        // Filter tipe pemesanan
        if ($tipePemesanan !== 'all') {
            $query->where('tipe_pemesanan', $tipePemesanan);
        }

        $pemesanans = $query->orderBy('tanggal_pesan', 'desc')->get();

        // Statistik ringkasan
        $totalTransaksi = $pemesanans->count();
        $totalPendapatan = $pemesanans->where('status_pembayaran', 'sudah_bayar')->sum('total_bayar');
        $totalBelumBayar = $pemesanans->where('status_pembayaran', 'belum_bayar')->sum('total_bayar');
        $totalRefund = $pemesanans->where('status_pembayaran', 'refund')->sum('total_bayar');

        // Group by metode pembayaran
        $byMetode = $pemesanans->where('status_pembayaran', 'sudah_bayar')
            ->groupBy(function($item) {
                return $item->pembayaran->metode_pembayaran ?? 'unknown';
            })
            ->map(function($group) {
                return [
                    'jumlah' => $group->count(),
                    'total' => $group->sum('total_bayar')
                ];
            });

        return view('admin.laporan.transaksi', compact(
            'pemesanans',
            'tanggalDari',
            'tanggalSampai',
            'statusPembayaran',
            'tipePemesanan',
            'totalTransaksi',
            'totalPendapatan',
            'totalBelumBayar',
            'totalRefund',
            'byMetode'
        ));
    }

    /**
     * ============================================
     * LAPORAN PENDAPATAN
     * ============================================
     * Dengan grafik trend pendapatan harian
     */
    public function pendapatan(Request $request)
    {
        // Default filter: bulan ini
        $tanggalDari = $request->input('tanggal_dari', now()->startOfMonth()->format('Y-m-d'));
        $tanggalSampai = $request->input('tanggal_sampai', now()->endOfMonth()->format('Y-m-d'));

        // Total pendapatan (hanya yang sudah bayar)
        $totalPendapatan = Pemesanan::whereBetween('tanggal_pesan', [$tanggalDari . ' 00:00:00', $tanggalSampai . ' 23:59:59'])
            ->where('status_pembayaran', 'sudah_bayar')
            ->sum('total_bayar');

        // Total transaksi
        $totalTransaksi = Pemesanan::whereBetween('tanggal_pesan', [$tanggalDari . ' 00:00:00', $tanggalSampai . ' 23:59:59'])
            ->where('status_pembayaran', 'sudah_bayar')
            ->count();

        // Rata-rata transaksi
        $rataRataTransaksi = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;

        // Pendapatan per hari (untuk grafik)
        $pendapatanHarian = Pemesanan::selectRaw('DATE(tanggal_pesan) as tanggal, SUM(total_bayar) as total')
            ->whereBetween('tanggal_pesan', [$tanggalDari . ' 00:00:00', $tanggalSampai . ' 23:59:59'])
            ->where('status_pembayaran', 'sudah_bayar')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Pendapatan per metode pembayaran
        $pendapatanPerMetode = Pemesanan::join('pembayarans', 'pemesanans.id', '=', 'pembayarans.pemesanan_id')
            ->selectRaw('pembayarans.metode_pembayaran, COUNT(*) as jumlah, SUM(pemesanans.total_bayar) as total')
            ->whereBetween('pemesanans.tanggal_pesan', [$tanggalDari . ' 00:00:00', $tanggalSampai . ' 23:59:59'])
            ->where('pemesanans.status_pembayaran', 'sudah_bayar')
            ->groupBy('pembayarans.metode_pembayaran')
            ->get();

        // Top 5 Film berdasarkan pendapatan
        $topFilms = Pemesanan::join('jadwals', 'pemesanans.jadwal_id', '=', 'jadwals.id')
            ->join('films', 'jadwals.film_id', '=', 'films.id')
            ->selectRaw('films.id, films.judul, COUNT(*) as jumlah_transaksi, SUM(pemesanans.total_bayar) as total_pendapatan')
            ->whereBetween('pemesanans.tanggal_pesan', [$tanggalDari . ' 00:00:00', $tanggalSampai . ' 23:59:59'])
            ->where('pemesanans.status_pembayaran', 'sudah_bayar')
            ->groupBy('films.id', 'films.judul')
            ->orderBy('total_pendapatan', 'desc')
            ->limit(5)
            ->get();

        return view('admin.laporan.pendapatan', compact(
            'tanggalDari',
            'tanggalSampai',
            'totalPendapatan',
            'totalTransaksi',
            'rataRataTransaksi',
            'pendapatanHarian',
            'pendapatanPerMetode',
            'topFilms'
        ));
    }

    /**
     * ============================================
     * LAPORAN FILM
     * ============================================
     * Performa setiap film (penonton, pendapatan)
     */
    public function film(Request $request)
    {
        // Default filter: bulan ini
        $tanggalDari = $request->input('tanggal_dari', now()->startOfMonth()->format('Y-m-d'));
        $tanggalSampai = $request->input('tanggal_sampai', now()->endOfMonth()->format('Y-m-d'));
        $statusFilm = $request->input('status_film', 'all');

        // Query film dengan statistik
        $query = Film::selectRaw('
                films.*,
                COUNT(DISTINCT pemesanans.id) as total_transaksi,
                SUM(pemesanans.jumlah_kursi) as total_penonton,
                SUM(CASE WHEN pemesanans.status_pembayaran = "sudah_bayar" THEN pemesanans.total_bayar ELSE 0 END) as total_pendapatan
            ')
            ->leftJoin('jadwals', 'films.id', '=', 'jadwals.film_id')
            ->leftJoin('pemesanans', function($join) use ($tanggalDari, $tanggalSampai) {
                $join->on('jadwals.id', '=', 'pemesanans.jadwal_id')
                    ->whereBetween('pemesanans.tanggal_pesan', [$tanggalDari . ' 00:00:00', $tanggalSampai . ' 23:59:59']);
            })
            ->groupBy('films.id');

        // Filter status film
        if ($statusFilm !== 'all') {
            $query->where('films.status', $statusFilm);
        }

        $films = $query->orderBy('total_pendapatan', 'desc')->get();

        // Statistik ringkasan
        $totalFilm = $films->count();
        $totalPenonton = $films->sum('total_penonton');
        $totalPendapatan = $films->sum('total_pendapatan');
        $filmTerlaris = $films->first();

        return view('admin.laporan.film', compact(
            'films',
            'tanggalDari',
            'tanggalSampai',
            'statusFilm',
            'totalFilm',
            'totalPenonton',
            'totalPendapatan',
            'filmTerlaris'
        ));
    }

    /**
     * ============================================
     * EXPORT PDF - Laporan Transaksi
     * ============================================
     */
    public function transaksiPdf(Request $request)
    {
        // Ambil data yang sama dengan method transaksi()
        $tanggalDari = $request->input('tanggal_dari', now()->startOfMonth()->format('Y-m-d'));
        $tanggalSampai = $request->input('tanggal_sampai', now()->endOfMonth()->format('Y-m-d'));
        $statusPembayaran = $request->input('status_pembayaran', 'all');
        $tipePemesanan = $request->input('tipe_pemesanan', 'all');

        $query = Pemesanan::with([
                'user',
                'jadwal.film',
                'jadwal.studio',
                'pembayaran',
                'kursi'
            ])
            ->whereBetween('tanggal_pesan', [$tanggalDari . ' 00:00:00', $tanggalSampai . ' 23:59:59']);

        if ($statusPembayaran !== 'all') {
            $query->where('status_pembayaran', $statusPembayaran);
        }

        if ($tipePemesanan !== 'all') {
            $query->where('tipe_pemesanan', $tipePemesanan);
        }

        $pemesanans = $query->orderBy('tanggal_pesan', 'desc')->get();

        $totalTransaksi = $pemesanans->count();
        $totalPendapatan = $pemesanans->where('status_pembayaran', 'sudah_bayar')->sum('total_bayar');

        // Generate PDF
        $pdf = Pdf::loadView('admin.laporan.pdf.transaksi', compact(
            'pemesanans',
            'tanggalDari',
            'tanggalSampai',
            'statusPembayaran',
            'tipePemesanan',
            'totalTransaksi',
            'totalPendapatan'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('Laporan-Transaksi-' . date('Y-m-d') . '.pdf');
    }

    /**
     * ============================================
     * EXPORT PDF - Laporan Pendapatan
     * ============================================
     */
    public function pendapatanPdf(Request $request)
    {
        $tanggalDari = $request->input('tanggal_dari', now()->startOfMonth()->format('Y-m-d'));
        $tanggalSampai = $request->input('tanggal_sampai', now()->endOfMonth()->format('Y-m-d'));

        $totalPendapatan = Pemesanan::whereBetween('tanggal_pesan', [$tanggalDari . ' 00:00:00', $tanggalSampai . ' 23:59:59'])
            ->where('status_pembayaran', 'sudah_bayar')
            ->sum('total_bayar');

        $totalTransaksi = Pemesanan::whereBetween('tanggal_pesan', [$tanggalDari . ' 00:00:00', $tanggalSampai . ' 23:59:59'])
            ->where('status_pembayaran', 'sudah_bayar')
            ->count();

        $rataRataTransaksi = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;

        $pendapatanPerMetode = Pemesanan::join('pembayarans', 'pemesanans.id', '=', 'pembayarans.pemesanan_id')
            ->selectRaw('pembayarans.metode_pembayaran, COUNT(*) as jumlah, SUM(pemesanans.total_bayar) as total')
            ->whereBetween('pemesanans.tanggal_pesan', [$tanggalDari . ' 00:00:00', $tanggalSampai . ' 23:59:59'])
            ->where('pemesanans.status_pembayaran', 'sudah_bayar')
            ->groupBy('pembayarans.metode_pembayaran')
            ->get();

        $topFilms = Pemesanan::join('jadwals', 'pemesanans.jadwal_id', '=', 'jadwals.id')
            ->join('films', 'jadwals.film_id', '=', 'films.id')
            ->selectRaw('films.judul, COUNT(*) as jumlah_transaksi, SUM(pemesanans.total_bayar) as total_pendapatan')
            ->whereBetween('pemesanans.tanggal_pesan', [$tanggalDari . ' 00:00:00', $tanggalSampai . ' 23:59:59'])
            ->where('pemesanans.status_pembayaran', 'sudah_bayar')
            ->groupBy('films.id', 'films.judul')
            ->orderBy('total_pendapatan', 'desc')
            ->limit(5)
            ->get();

        $pdf = Pdf::loadView('admin.laporan.pdf.pendapatan', compact(
            'tanggalDari',
            'tanggalSampai',
            'totalPendapatan',
            'totalTransaksi',
            'rataRataTransaksi',
            'pendapatanPerMetode',
            'topFilms'
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Pendapatan-' . date('Y-m-d') . '.pdf');
    }

    /**
     * ============================================
     * EXPORT PDF - Laporan Film
     * ============================================
     */
    public function filmPdf(Request $request)
    {
        $tanggalDari = $request->input('tanggal_dari', now()->startOfMonth()->format('Y-m-d'));
        $tanggalSampai = $request->input('tanggal_sampai', now()->endOfMonth()->format('Y-m-d'));
        $statusFilm = $request->input('status_film', 'all');

        $query = Film::selectRaw('
                films.*,
                COUNT(DISTINCT pemesanans.id) as total_transaksi,
                SUM(pemesanans.jumlah_kursi) as total_penonton,
                SUM(CASE WHEN pemesanans.status_pembayaran = "sudah_bayar" THEN pemesanans.total_bayar ELSE 0 END) as total_pendapatan
            ')
            ->leftJoin('jadwals', 'films.id', '=', 'jadwals.film_id')
            ->leftJoin('pemesanans', function($join) use ($tanggalDari, $tanggalSampai) {
                $join->on('jadwals.id', '=', 'pemesanans.jadwal_id')
                    ->whereBetween('pemesanans.tanggal_pesan', [$tanggalDari . ' 00:00:00', $tanggalSampai . ' 23:59:59']);
            })
            ->groupBy('films.id');

        if ($statusFilm !== 'all') {
            $query->where('films.status', $statusFilm);
        }

        $films = $query->orderBy('total_pendapatan', 'desc')->get();

        $totalFilm = $films->count();
        $totalPenonton = $films->sum('total_penonton');
        $totalPendapatan = $films->sum('total_pendapatan');

        $pdf = Pdf::loadView('admin.laporan.pdf.film', compact(
            'films',
            'tanggalDari',
            'tanggalSampai',
            'statusFilm',
            'totalFilm',
            'totalPenonton',
            'totalPendapatan'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('Laporan-Film-' . date('Y-m-d') . '.pdf');
    }
}
