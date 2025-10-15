<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class KelolaKasirController extends Controller
{
    public function index()
    {
        $kasir = User::where('role', 'kasir')
            ->orderBy('nama_lengkap')
            ->paginate(10);

        return view('admin.kelola-kasir.index', compact('kasir'));
    }

    public function create()
    {
        return view('admin.kelola-kasir.create');
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
            'role' => 'kasir',
            'status_aktif' => true,
        ]);

        return redirect()->route('admin.kasir.index')->with('success', 'Kasir berhasil ditambahkan.');
    }

    public function edit(User $kasir)
    {
        return view('admin.kelola-kasir.edit', compact('kasir'));
    }

    public function update(Request $request, User $kasir)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$kasir->id,
            'phone' => 'required|string|max:20',
        ]);

        $kasir->update($request->only('nama_lengkap', 'email', 'phone', 'status_aktif'));

        return redirect()->route('admin.kasir.index')->with('success', 'Data kasir berhasil diperbarui.');
    }

    public function destroy(User $kasir)
    {
        $kasir->delete();
        return redirect()->route('admin.kasir.index')->with('success', 'Kasir berhasil dihapus.');
    }
}
