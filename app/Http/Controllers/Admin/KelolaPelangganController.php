<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class KelolaPelangganController extends Controller
{
    public function index()
    {
        $pelanggan = User::where('role', 'pelanggan')
            ->orderBy('nama_lengkap')
            ->paginate(10);

        return view('admin.kelola-pelanggan.index', compact('pelanggan'));
    }

    public function create()
    {
        return view('admin.kelola-pelanggan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:6',
        ]);

        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'role' => 'pelanggan',
            'status_aktif' => true,
        ]);

        return redirect()->route('admin.pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(User $pelanggan)
    {
        return view('admin.kelola-pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, User $pelanggan)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$pelanggan->id,
            'phone' => 'required|string|max:20',
        ]);

        $pelanggan->update($request->only('nama_lengkap', 'email', 'phone', 'status_aktif'));

        return redirect()->route('admin.pelanggan.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(User $pelanggan)
    {
        $pelanggan->delete();
        return redirect()->route('admin.pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
