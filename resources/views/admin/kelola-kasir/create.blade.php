@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-neutral-950 text-white px-6 py-10">
    <div class="max-w-3xl mx-auto">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-red-500">Tambah Kasir</h1>
            <a href="{{ route('admin.kasir.index') }}" 
               class="px-5 py-2 bg-neutral-800 hover:bg-neutral-700 rounded-lg text-gray-300 font-semibold transition">
                ← Kembali
            </a>
        </div>

        {{-- Form --}}
        <div class="bg-neutral-900 border border-neutral-800 rounded-xl p-8 shadow-lg">
            <form action="{{ route('admin.kasir.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                               class="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500 text-sm text-white"
                               required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500 text-sm text-white"
                               required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Nomor Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500 text-sm text-white">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Password</label>
                        <input type="password" name="password"
                               class="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500 text-sm text-white"
                               required>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm text-gray-400 mb-2">Status Akun</label>
                    <select name="status_aktif"
                            class="w-full px-4 py-2 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500 text-sm text-white">
                        <option value="1" selected>Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.kasir.index') }}" 
                       class="px-5 py-2 bg-neutral-800 hover:bg-neutral-700 text-gray-300 rounded-lg font-semibold transition">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-lg font-semibold shadow-lg shadow-red-600/30 transition transform hover:scale-105">
                        Simpan
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
