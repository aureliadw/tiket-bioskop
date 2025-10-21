<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Film;
use App\Models\Jadwal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FilmController extends Controller
{
    /**
     * ============================================
     * NOW PLAYING - Film yang Sedang Tayang
     * ============================================
     * 
     * Fitur Auto-Update Status:
     * - Mengecek film dengan status "akan_tayang"
     * - Jika tanggal rilis sudah lewat/hari ini → ubah jadi "sedang_tayang"
     * - Menampilkan semua film yang sedang tayang
     * 
     * @return \Illuminate\View\View
     */
    public function nowPlaying()
    {
        // Auto-update status film yang sudah waktunya tayang
        Film::where('status', 'akan_tayang')
            ->whereDate('tanggal_rilis', '<=', now()->toDateString())
            ->update(['status' => 'sedang_tayang']);

        // Ambil semua film yang sedang tayang
        $nowPlaying = Film::where('status', 'sedang_tayang')
            ->orderBy('tanggal_rilis', 'desc')
            ->get();

        return view('pelanggan.now-playing', compact('nowPlaying'));
    }

    /**
     * ============================================
     * COMING SOON - Film yang Akan Tayang
     * ============================================
     * 
     * Menampilkan daftar film dengan status "akan_tayang"
     * (tanggal rilisnya masih di masa depan)
     * 
     * @return \Illuminate\View\View
     */
    public function comingSoon()
    {
        $comingSoon = Film::where('status', 'akan_tayang')
            ->orderBy('tanggal_rilis', 'asc')
            ->get();

        return view('pelanggan.coming-soon', compact('comingSoon'));
    }

    /**
     * ============================================
     * DETAIL FILM - Halaman Detail + Jadwal Tayang
     * ============================================
     * 
     * Fitur Utama:
     * 1. Menampilkan detail lengkap film
     * 2. Filter jadwal berdasarkan tanggal (hari ini / besok)
     * 3. Hitung ketersediaan kursi per jadwal
     * 4. Disable jadwal yang sudah lewat atau kursi penuh
     * 
     * @param Request $request
     * @param int $id - ID Film
     * @return \Illuminate\View\View
     */
    public function detail(Request $request, $id)
    {
        // 1. AMBIL DATA FILM
        $film = Film::findOrFail($id);

        // 2. VALIDASI TANGGAL YANG DIPILIH
        $selectedDate = $request->get('date', now()->toDateString());
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();
        
        if (!in_array($selectedDate, [$today, $tomorrow])) {
            $selectedDate = $today;
        }

        // 3. AUTO-UPDATE STATUS JADWAL YANG SUDAH LEWAT
        Jadwal::where('status_aktif', true)
            ->whereRaw("CONCAT(tanggal_tayang, ' ', jam_tayang) < NOW()")
            ->update(['status_aktif' => false]);

        // 4. AMBIL JADWAL & HITUNG KETERSEDIAAN KURSI
        $jadwalsByStudio = Jadwal::with('studio')
            ->where('film_id', $id)
            ->where('tanggal_tayang', $selectedDate)
            ->where('status_aktif', true)
            ->orderBy('jam_tayang', 'asc')
            ->get()
            ->map(function ($jadwal) {
                // Hitung total kursi di studio ini
                $totalKursi = DB::table('kursis')
                    ->where('studio_id', $jadwal->studio_id)
                    ->count();

                // Hitung kursi yang sudah dipesan (status: sudah_bayar)
                $kursiTerpesan = DB::table('pemesanan_kursis')
                    ->join('pemesanans', 'pemesanan_kursis.pemesanan_id', '=', 'pemesanans.id')
                    ->where('pemesanans.jadwal_id', $jadwal->id)
                    ->where('pemesanans.status_pembayaran', 'sudah_bayar')
                    ->count();

                // Hitung sisa kursi tersedia
                $jadwal->kursi_tersedia = $totalKursi - $kursiTerpesan;
                $jadwal->kursi_penuh = $jadwal->kursi_tersedia <= 0;

                // CEK APAKAH JADWAL SUDAH LEWAT
                try {
                    // Method 1: Parse langsung dari kolom jam_tayang
                    $jadwalDateTime = Carbon::parse($jadwal->jam_tayang);
                    $jadwal->sudah_lewat = $jadwalDateTime->isPast();
                } catch (\Exception $e) {
                    // Method 2: Fallback - Gabungkan tanggal + jam manual
                    $tanggal = date('Y-m-d', strtotime($jadwal->tanggal_tayang));
                    $jam = date('H:i:s', strtotime($jadwal->jam_tayang));
                    $jadwalDateTime = Carbon::createFromFormat(
                        'Y-m-d H:i:s', 
                        $tanggal . ' ' . $jam
                    );
                    $jadwal->sudah_lewat = $jadwalDateTime->isPast();
                }

                return $jadwal;
            })
            ->groupBy('studio_id'); // Group berdasarkan studio

        return view('pelanggan.detail', compact('film', 'jadwalsByStudio', 'selectedDate'));
    }

    /**
     * ============================================
     * SEARCH - Pencarian Film
     * ============================================
     * 
     * Mencari film berdasarkan:
     * - Judul
     * - Genre
     * - Deskripsi
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        // Ambil keyword pencarian dari input 'q'
        $query = $request->input('q');

        // Cari film yang cocok (LIKE query)
        $films = Film::where('judul', 'like', "%{$query}%")
            ->orWhere('genre', 'like', "%{$query}%")
            ->orWhere('deskripsi', 'like', "%{$query}%")
            ->get();

        return view('pelanggan.search', compact('films', 'query'));
    }
}
