<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Film;
use App\Models\Jadwal;
use App\Models\Kursi;
use App\Models\Pemesanan;
use App\Models\PemesananKursi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function pilihKursi($jadwalId)
{
    // ⏰ AUTO-RELEASE: Hapus pemesanan yang expired (> 10 menit) dan belum bayar
    Pemesanan::where('status_pembayaran', 'belum_bayar')
        ->where('created_at', '<', now()->subMinutes(10))
        ->delete();

    $jadwal = Jadwal::with(['film', 'studio'])->findOrFail($jadwalId);
    
    // Ambil semua kursi di studio
    $kursis = Kursi::where('studio_id', $jadwal->studio_id)
        ->orderBy('baris')
        ->orderBy('kolom')
        ->get();

    // Kursi TERJUAL (status pembayaran = sudah_bayar)
    $kursiTerjual = DB::table('pemesanan_kursis')
        ->join('pemesanans', 'pemesanan_kursis.pemesanan_id', '=', 'pemesanans.id')
        ->where('pemesanans.jadwal_id', $jadwalId)
        ->where('pemesanans.status_pembayaran', 'sudah_bayar')
        ->pluck('pemesanan_kursis.kursi_id')
        ->toArray();

    // Kursi PENDING (status = belum_bayar, created_at < 10 menit yang lalu)
    $kursiPending = DB::table('pemesanan_kursis')
        ->join('pemesanans', 'pemesanan_kursis.pemesanan_id', '=', 'pemesanans.id')
        ->where('pemesanans.jadwal_id', $jadwalId)
        ->where('pemesanans.status_pembayaran', 'belum_bayar')
        ->where('pemesanans.created_at', '>=', now()->subMinutes(10)) // Belum 10 menit
        ->pluck('pemesanan_kursis.kursi_id')
        ->toArray();

    return view('pelanggan.pilih-kursi', compact('jadwal', 'kursis', 'kursiTerjual', 'kursiPending'));
}

    public function prosesKursi(Request $request, $jadwal_id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $request->validate([
            'kursi' => 'required|array|min:1',
            'kursi.*' => 'integer|exists:kursis,id',
        ]);

        $jadwal = Jadwal::findOrFail($jadwal_id);
        $hargaDasar = floatval($jadwal->harga_dasar ?? 0);

        DB::beginTransaction();
        try {
            $kodePemesanan = 'TIX-' . strtoupper(uniqid());

            $pemesanan = Pemesanan::create([
                'user_id'           => Auth::id(),
                'jadwal_id'         => $jadwal->id,
                'kode_pemesanan'    => $kodePemesanan,
                'jumlah_kursi'      => count($request->kursi),
                'total_bayar'       => count($request->kursi) * $hargaDasar,
                'status_pemesanan'  => 'pending',
                'status_pembayaran' => 'belum_bayar',
                'tipe_pemesanan'    => 'online',
                'tanggal_pesan'     => now(),
            ]);

            foreach ($request->kursi as $kursiId) {
                DB::table('pemesanan_kursis')->insert([
                    'pemesanan_id' => $pemesanan->id,
                    'kursi_id'     => $kursiId,
                    'harga_kursi'  => $hargaDasar,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('pembayaran.show', $pemesanan->id)
                ->with('success', 'Kursi berhasil dipilih, silakan lanjut ke pembayaran.');
                
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Proses kursi gagal: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
