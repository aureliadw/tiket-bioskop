<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Film;
use App\Models\Jadwal;

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
    // Cari film berdasarkan ID
    $film = Film::findOrFail($id);

    // Ambil tanggal dari request, default hari ini
    $selectedDate = $request->get('date', now()->toDateString());
    
    // Validasi: hanya boleh hari ini atau besok
    $today = now()->toDateString();
    $tomorrow = now()->addDay()->toDateString();
    
    if (!in_array($selectedDate, [$today, $tomorrow])) {
        $selectedDate = $today;
    }

    // 🧠 Tambahkan logika auto-update status jadwal
    Jadwal::where('status_aktif', true)
        ->whereRaw("CONCAT(tanggal_tayang, ' ', jam_tayang) < NOW()")
        ->update(['status_aktif' => false]);

    // Ambil jadwal per studio (group by studio)
    $jadwalsByStudio = Jadwal::with('studio')
        ->where('film_id', $id)
        ->where('tanggal_tayang', $selectedDate)
        ->where('status_aktif', true)
        ->orderBy('jam_tayang', 'asc')
        ->get()
        ->groupBy('studio_id'); // Group berdasarkan studio

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
