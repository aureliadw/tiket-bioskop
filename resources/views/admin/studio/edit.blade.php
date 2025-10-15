@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-neutral-950 text-white p-6">
    <div class="max-w-3xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold mb-2">Edit Studio</h1>
                <p class="text-gray-400">Perbarui informasi studio</p>
            </div>
            <a href="{{ route('admin.studio.index') }}" 
               class="px-5 py-2 bg-neutral-800 hover:bg-neutral-700 rounded-lg font-semibold text-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>

        {{-- Alert Messages --}}
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('admin.studio.update', $studio->id) }}" method="POST" class="space-y-6 bg-gradient-to-br from-neutral-900 to-neutral-950 border border-neutral-800 rounded-2xl p-8 shadow-xl">
            @csrf
            @method('PUT')

            {{-- Nama Studio --}}
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Nama Studio</label>
                <input type="text" name="nama_studio" value="{{ old('nama_studio', $studio->nama_studio) }}"
                       class="w-full px-4 py-3 bg-neutral-800 border border-neutral-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-600"
                       placeholder="Contoh: Studio 1" required>
                @error('nama_studio')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Total Kursi --}}
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Total Kursi</label>
                <input type="number" name="total_kursi" value="{{ old('total_kursi', $studio->total_kursi) }}"
                       class="w-full px-4 py-3 bg-neutral-800 border border-neutral-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-600"
                       placeholder="Contoh: 40" required>
                @error('total_kursi')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Deskripsi (Opsional)</label>
                <textarea name="deskripsi" rows="4"
                          class="w-full px-4 py-3 bg-neutral-800 border border-neutral-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-600"
                          placeholder="Contoh: Studio dengan kursi recliner dan audio Dolby Atmos">{{ old('deskripsi', $studio->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status Aktif --}}
            <div class="flex items-center">
                <input type="checkbox" name="status_aktif" id="status_aktif" value="1"
                       class="w-5 h-5 text-red-600 bg-neutral-800 border-neutral-700 rounded focus:ring-red-600"
                       {{ old('status_aktif', $studio->status_aktif) ? 'checked' : '' }}>
                <label for="status_aktif" class="ml-2 text-sm text-gray-300 font-medium">
                    Aktifkan Studio
                </label>
            </div>

            {{-- Tombol --}}
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('admin.studio.index') }}" 
                   class="px-5 py-2 bg-neutral-700 hover:bg-neutral-600 rounded-lg text-gray-200 font-semibold transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-lg font-semibold shadow-lg transition hover:scale-105">
                    <i class="fas fa-save mr-2"></i> Perbarui
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
