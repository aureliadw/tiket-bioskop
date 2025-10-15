<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Film;
use App\Models\Jadwal;
use Illuminate\Support\Facades\DB;

class FilmController extends Controller
{
    public function nowPlaying()
    {
        Film::where('status', 'akan_tayang')
            ->whereDate('tanggal_rilis', '<=', now()->toDateString())
            ->update(['status' => 'sedang_tayang']);

        $nowPlaying = Film::where('status', 'sedang_tayang')->get();

        return view('pelanggan.now-playing', compact('nowPlaying'));
    }


    public function comingSoon()
    {
        $comingSoon = Film::where('status', 'akan_tayang')->get();
        return view('pelanggan.coming-soon', compact('comingSoon'));
    }

    public function detail(Request $request, $id)
{
    $film = Film::findOrFail($id);
    $selectedDate = $request->get('date', now()->toDateString());

    $today = now()->toDateString();
    $tomorrow = now()->addDay()->toDateString();

    if (!in_array($selectedDate, [$today, $tomorrow])) {
        $selectedDate = $today;
    }

    // Update status jadwal lewat
    Jadwal::where('status_aktif', true)
        ->whereRaw("CONCAT(tanggal_tayang, ' ', jam_tayang) < NOW()")
        ->update(['status_aktif' => false]);

    $jadwalsByStudio = Jadwal::with('studio')
        ->where('film_id', $id)
        ->where('tanggal_tayang', $selectedDate)
        ->where('status_aktif', true)
        ->orderBy('jam_tayang', 'asc')
        ->get()
        ->map(function ($jadwal) {
            // Total kursi studio
            $totalKursi = DB::table('kursis')
                ->where('studio_id', $jadwal->studio_id)
                ->count();

            // Kursi terpesan (status sudah_bayar)
            $kursiTerpesan = DB::table('pemesanan_kursis')
                ->join('pemesanans', 'pemesanan_kursis.pemesanan_id', '=', 'pemesanans.id')
                ->where('pemesanans.jadwal_id', $jadwal->id)
                ->where('pemesanans.status_pembayaran', 'sudah_bayar')
                ->count();

            $jadwal->kursi_tersedia = $totalKursi - $kursiTerpesan;
            $jadwal->kursi_penuh = $jadwal->kursi_tersedia <= 0;

            // Parse datetime dengan aman
            try {
                // Coba parse jam_tayang langsung
                $jadwalDateTime = \Carbon\Carbon::parse($jadwal->jam_tayang);
                $jadwal->sudah_lewat = $jadwalDateTime->isPast();
            } catch (\Exception $e) {
                // Fallback: gabungkan manual
                $tanggal = date('Y-m-d', strtotime($jadwal->tanggal_tayang));
                $jam = date('H:i:s', strtotime($jadwal->jam_tayang));
                $jadwalDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $tanggal . ' ' . $jam);
                $jadwal->sudah_lewat = $jadwalDateTime->isPast();
            }

            return $jadwal;
        })
        ->groupBy('studio_id');

    return view('pelanggan.detail', compact('film', 'jadwalsByStudio', 'selectedDate'));
}

    public function search(Request $request)
{
    $query = $request->input('q'); // ambil input dari search bar

    $films = Film::where('judul', 'like', "%{$query}%")
                ->orWhere('genre', 'like', "%{$query}%")
                ->orWhere('deskripsi', 'like', "%{$query}%")
                ->get();

    return view('pelanggan.search', compact('films', 'query'));
}
}
