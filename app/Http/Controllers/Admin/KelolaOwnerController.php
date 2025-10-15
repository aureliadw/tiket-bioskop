<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class KelolaOwnerController extends Controller
{
    public function index()
    {
        $owner = User::where('role', 'owner')
            ->orderBy('nama_lengkap')
            ->paginate(10);

        return view('admin.kelola-owner.index', compact('owner'));
    }

    public function create()
    {
        return view('admin.kelola-owner.create');
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
            'role' => 'owner',
            'status_aktif' => true,
        ]);

        return redirect()->route('admin.owner.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(User $owner)
    {
        return view('admin.kelola-owner.edit', compact('owner'));
    }

    public function update(Request $request, User $owner)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$owner->id,
            'phone' => 'required|string|max:20',
        ]);

        $owner->update($request->only('nama_lengkap', 'email', 'phone', 'status_aktif'));

        return redirect()->route('admin.owner.index')->with('success', 'Data owner berhasil diperbarui.');
    }

    public function destroy(User $owner)
    {
        $owner->delete();
        return redirect()->route('admin.owner.index')->with('success', 'Owner berhasil dihapus.');
    }
}
