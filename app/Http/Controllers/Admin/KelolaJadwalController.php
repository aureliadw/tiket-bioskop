<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Film;
use App\Models\Studio;

class KelolaJadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jadwals = Jadwal::with(['film', 'studio'])->latest()->paginate(10);
        return view('admin.jadwal.index', compact('jadwals'));
    }

    /*public function create()
    {
        $films = Film::where('status', 'sedang_tayang')->get(); // ✅ Filter hanya yang sedang tayang
        $studios = Studio::all();
        
        // ✅ Ambil semua jadwal untuk ditampilkan (grouped by studio & tanggal)
        $existingJadwals = Jadwal::with(['film', 'studio'])
            ->whereDate('tanggal_tayang', '>=', today()) // Hanya jadwal hari ini ke depan
            ->orderBy('tanggal_tayang')
            ->orderBy('jam_tayang')
            ->get()
            ->groupBy(function($jadwal) {
                return $jadwal->studio_id . '-' . $jadwal->tanggal_tayang;
            });
        
        return view('admin.jadwal.create', compact('films', 'studios', 'existingJadwals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'film_id' => 'required|exists:films,id',
            'studio_id' => 'required|exists:studios,id',
            'tanggal_tayang' => 'required|date|after_or_equal:today',
            'jam_tayang' => 'required|date_format:H:i',
            'harga_dasar' => 'required|numeric|min:0',
            'kursi_tersedia' => 'required|integer|min:0',
            'status_aktif' => 'boolean',
        ], [
            'tanggal_tayang.after_or_equal' => 'Tanggal tayang tidak boleh di masa lalu!',
        ]);

        // ✅ CEK BENTROK JADWAL
        $bentrok = Jadwal::where('studio_id', $request->studio_id)
                         ->where('tanggal_tayang', $request->tanggal_tayang)
                         ->where('jam_tayang', $request->jam_tayang)
                         ->exists();

        if ($bentrok) {
            return back()->withErrors([
                'jam_tayang' => '⚠️ Jam tayang ini sudah dipakai oleh film lain di studio yang sama pada tanggal tersebut!'
            ])->withInput();
        }

        // ✅ Set status_aktif default true kalau tidak dicentang
        $validated['status_aktif'] = $request->has('status_aktif') ? true : false;

        Jadwal::create($validated);

        return redirect()->route('admin.jadwal.index')
                         ->with('success', '✅ Jadwal berhasil ditambahkan!');
    }*/

    public function edit($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $films = Film::all();
        $studios = Studio::all();
        return view('admin.jadwal.edit', compact('jadwal', 'films', 'studios'));
    }

    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);

        $validated = $request->validate([
            'film_id' => 'required|exists:films,id',
            'studio_id' => 'required|exists:studios,id',
            'tanggal_tayang' => 'required|date',
            'jam_tayang' => 'required|date_format:H:i',
            'harga_dasar' => 'required|numeric|min:0',
            'kursi_tersedia' => 'required|integer|min:0',
            'status_aktif' => 'boolean',
        ]);

        // ✅ CEK BENTROK (Kecuali jadwal yang sedang diedit)
        $bentrok = Jadwal::where('studio_id', $request->studio_id)
                         ->where('tanggal_tayang', $request->tanggal_tayang)
                         ->where('jam_tayang', $request->jam_tayang)
                         ->where('id', '!=', $id) // ← Jangan cek jadwal sendiri
                         ->exists();

        if ($bentrok) {
            return back()->withErrors([
                'jam_tayang' => '⚠️ Jam tayang ini sudah dipakai oleh film lain!'
            ])->withInput();
        }

        $validated['status_aktif'] = $request->has('status_aktif') ? true : false;

        $jadwal->update($validated);

        return redirect()->route('admin.jadwal.index')
                         ->with('success', '✅ Jadwal berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Jadwal::findOrFail($id)->delete();
        return redirect()->route('admin.jadwal.index')
                         ->with('success', '✅ Jadwal berhasil dihapus!');
    }

    // Method untuk tampilkan form bulk input
public function bulkCreate()
{
    $films = Film::where('status', 'sedang_tayang')->get();
    $studios = Studio::where('status_aktif', true)->get();
    
    // Jam tayang default
    $jamTersedia = ['11:30:00', '14:00:00', '16:30:00', '19:00:00', '21:30:00', '23:00:00'];
    
    return view('admin.jadwal.bulk', compact('films', 'studios', 'jamTersedia'));
}

// Method untuk proses bulk input
public function bulkStore(Request $request)
{
    $request->validate([
        'film_id' => 'required|exists:films,id',
        'tanggal_dari' => 'required|date',
        'tanggal_sampai' => 'required|date|after_or_equal:tanggal_dari',
        'studio_ids' => 'required|array|min:1',
        'studio_ids.*' => 'exists:studios,id',
        'jam_tayang' => 'required|array|min:1',
        'harga_dasar' => 'required|numeric|min:0',
    ]);

    $created = 0;
    $skipped = 0;
    
    $currentDate = \Carbon\Carbon::parse($request->tanggal_dari);
    $endDate = \Carbon\Carbon::parse($request->tanggal_sampai);

    while ($currentDate->lte($endDate)) {
        foreach ($request->studio_ids as $studioId) {
            $studio = Studio::find($studioId);
            
            foreach ($request->jam_tayang as $jam) {
                // Cek apakah jadwal sudah ada (bentrok)
                $exists = Jadwal::where('studio_id', $studioId)
                               ->where('tanggal_tayang', $currentDate->toDateString())
                               ->where('jam_tayang', $jam)
                               ->exists();
                
                if (!$exists) {
                    Jadwal::create([
                        'film_id' => $request->film_id,
                        'studio_id' => $studioId,
                        'tanggal_tayang' => $currentDate->toDateString(),
                        'jam_tayang' => $jam,
                        'harga_dasar' => $request->harga_dasar,
                        'kursi_tersedia' => $studio->total_kursi,
                        'status_aktif' => true,
                    ]);
                    $created++;
                } else {
                    $skipped++;
                }
            }
        }
        $currentDate->addDay();
    }

    $message = "Berhasil membuat {$created} jadwal!";
    if ($skipped > 0) {
        $message .= " ({$skipped} jadwal dilewati karena bentrok)";
    }

    return redirect()->route('admin.jadwal.index')->with('success', $message);
}
}
