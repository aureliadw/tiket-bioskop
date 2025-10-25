<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Film;
use App\Models\Jadwal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FilmController extends Controller
{
    /**
     * ============================================
     * NOW PLAYING - Film yang Sedang Tayang
     * ============================================
     * Auto-update status film dari "akan_tayang" ke "sedang_tayang"
     * jika tanggal rilis sudah lewat
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
     * Fitur:
     * - Detail lengkap film
     * - Filter jadwal (hari ini/besok)
     * - Hitung ketersediaan kursi
     * - Disable jadwal yang lewat/penuh
     */
    public function detail(Request $request, $id)
    {
        // 1. AMBIL DATA FILM
        $film = Film::findOrFail($id);

        // 2. VALIDASI TANGGAL (hanya hari ini & besok)
        $selectedDate = $request->get('date', now()->toDateString());
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();
        
        if (!in_array($selectedDate, [$today, $tomorrow])) {
            $selectedDate = $today;
        }

        // 3. AMBIL JADWAL & HITUNG KETERSEDIAAN KURSI
        $jadwalsByStudio = Jadwal::with('studio')
            ->where('film_id', $id)
            ->where('tanggal_tayang', $selectedDate)
            ->orderBy('jam_tayang', 'asc')
            ->get()
            ->map(function ($jadwal) {
                // Hitung total kursi di studio
                $totalKursi = DB::table('kursis')
                    ->where('studio_id', $jadwal->studio_id)
                    ->count();

                // Hitung kursi yang sudah dibooking (status: sudah_bayar)
                $kursiTerpesan = DB::table('pemesanan_kursis')
                    ->join('pemesanans', 'pemesanan_kursis.pemesanan_id', '=', 'pemesanans.id')
                    ->where('pemesanans.jadwal_id', $jadwal->id)
                    ->where('pemesanans.status_pembayaran', 'sudah_bayar')
                    ->count();

                // Hitung sisa kursi
                $jadwal->kursi_tersedia = $totalKursi - $kursiTerpesan;
                $jadwal->kursi_penuh = ($jadwal->kursi_tersedia <= 0);

                // Cek apakah jadwal sudah lewat
                try {
                    $tanggal = \Carbon\Carbon::parse($jadwal->tanggal_tayang)->format('Y-m-d');
                    $jam = \Carbon\Carbon::parse($jadwal->jam_tayang)->format('H:i:s');
                    
                    $jadwalDateTime = \Carbon\Carbon::createFromFormat(
                        'Y-m-d H:i:s', 
                        $tanggal . ' ' . $jam
                    );
                    
                    $jadwal->sudah_lewat = $jadwalDateTime->isPast();
                    
                } catch (\Exception $e) {
                    $jadwal->sudah_lewat = false;
                }

                return $jadwal;
            })
            ->groupBy('studio_id');

        return view('pelanggan.detail', compact('film', 'jadwalsByStudio', 'selectedDate'));
    }

    /**
     * ============================================
     * SEARCH - Pencarian Film
     * ============================================
     * Mencari berdasarkan judul, genre, dan deskripsi
     */
    public function search(Request $request)
    {
        $query = $request->input('q');

        $films = Film::where('judul', 'like', "%{$query}%")
            ->orWhere('genre', 'like', "%{$query}%")
            ->orWhere('deskripsi', 'like', "%{$query}%")
            ->get();

        return view('pelanggan.search', compact('films', 'query'));
    }
}
