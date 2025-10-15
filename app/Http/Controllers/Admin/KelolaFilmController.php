<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Film;
use Illuminate\Support\Facades\Storage;

class KelolaFilmController extends Controller
{
    public function index()
    {
        $films = Film::latest()->paginate(10);
        return view('admin.film.index', compact('films'));
    }

    // Form tambah film
    public function create()
    {
        return view('admin.film.create');
    }

    // Simpan film baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'genre' => 'required|string',
            'durasi' => 'required|integer|min:1',
            'umur' => 'nullable|string|max:10',
            'tanggal_rilis' => 'required|date',
            'sutradara' => 'required|string|max:255',
            'produser' => 'nullable|string|max:255',
            'penulis' => 'nullable|string|max:255',
            'produksi' => 'nullable|string|max:255',
            'pemain' => 'nullable|string',
            'poster_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'trailer_video' => 'nullable|url',
            'status' => 'required|in:akan_tayang,sedang_tayang,selesai_tayang',
        ]);

        // Upload poster
        if ($request->hasFile('poster_image')) {
            $validated['poster_image'] = $request->file('poster_image')->store('posters', 'public');
        }

        Film::create($validated);

        return redirect()->route('admin.film.index')->with('success', 'Film berhasil ditambahkan!');
    }

    // Form edit film
    public function edit($id)
    {
        $film = Film::findOrFail($id);
        return view('admin.film.edit', compact('film'));
    }

    // Update film
    public function update(Request $request, $id)
    {
        $film = Film::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'genre' => 'required|string',
            'durasi' => 'required|integer|min:1',
            'umur' => 'nullable|string|max:10',
            'tanggal_rilis' => 'required|date',
            'sutradara' => 'required|string|max:255',
            'produser' => 'nullable|string|max:255',
            'penulis' => 'nullable|string|max:255',
            'produksi' => 'nullable|string|max:255',
            'pemain' => 'nullable|string',
            'poster_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'trailer_video' => 'nullable|url',
            'status' => 'required|in:akan_tayang,sedang_tayang,tidak_tayang',
        ]);

        // Upload poster baru
        if ($request->hasFile('poster_image')) {
            // Hapus poster lama
            if ($film->poster_image) {
                Storage::disk('public')->delete($film->poster_image);
            }
            $validated['poster_image'] = $request->file('poster_image')->store('posters', 'public');
        }

        $film->update($validated);

        return redirect()->route('admin.film.index')->with('success', 'Film berhasil diupdate!');
    }

    // Hapus film
    public function destroy($id)
    {
        $film = Film::findOrFail($id);

        // Hapus poster
        if ($film->poster_image) {
            Storage::disk('public')->delete($film->poster_image);
        }

        $film->delete();

        return redirect()->route('admin.film.index')->with('success', 'Film berhasil dihapus!');
    }
}
