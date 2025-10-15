<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Studio;

class KelolaStudioController extends Controller
{
    // List semua studio
    public function index()
    {
        $studios = Studio::withCount(['jadwals', 'kursis'])->get();
        return view('admin.studio.index', compact('studios'));
    }

    // Form tambah studio
    public function create()
    {
        return view('admin.studio.create');
    }

    // Proses tambah studio
    public function store(Request $request)
    {
        $request->validate([
            'nama_studio' => 'required|string|max:50|unique:studios,nama_studio',
            'total_kursi' => 'required|integer|min:1|max:200',
            'deskripsi' => 'nullable|string|max:500',
            'status_aktif' => 'required|boolean',
        ]);

        Studio::create($request->all());

        return redirect()->route('admin.studio.index')
            ->with('success', 'Studio berhasil ditambahkan!');
    }

    // Form edit studio
    public function edit($id)
    {
        $studio = Studio::findOrFail($id);
        return view('admin.studio.edit', compact('studio'));
    }

    // Proses update studio
    public function update(Request $request, $id)
    {
        $studio = Studio::findOrFail($id);

        $request->validate([
            'nama_studio' => 'required|string|max:50|unique:studios,nama_studio,' . $id,
            'total_kursi' => 'required|integer|min:1|max:200',
            'deskripsi' => 'nullable|string|max:500',
            'status_aktif' => 'required|boolean',
        ]);

        $studio->update($request->all());

        return redirect()->route('admin.studio.index')
            ->with('success', 'Studio berhasil diupdate!');
    }

    // Hapus studio
    public function destroy($id)
    {
        $studio = Studio::findOrFail($id);
        
        // Cek apakah ada jadwal aktif
        if ($studio->jadwals()->where('status_aktif', true)->exists()) {
            return redirect()->back()
                ->with('error', 'Studio tidak dapat dihapus karena masih ada jadwal aktif!');
        }

        $studio->delete();

        return redirect()->route('admin.studio.index')
            ->with('success', 'Studio berhasil dihapus!');
    }
}
