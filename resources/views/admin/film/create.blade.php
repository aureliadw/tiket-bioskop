@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-neutral-950 text-white p-6">
    <div class="max-w-4xl mx-auto">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold mb-2">Tambah Film</h1>
                <p class="text-gray-400">Masukkan data film baru ke sistem HappyCine</p>
            </div>
            <a href="{{ route('admin.film.index') }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-neutral-800 hover:bg-neutral-700 rounded-xl font-bold transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        {{-- Alert Error --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('admin.film.store') }}" method="POST" enctype="multipart/form-data" 
      class="bg-neutral-900 border border-neutral-800 rounded-2xl p-8 shadow-lg shadow-red-600/10">
    @csrf

    {{-- Judul --}}
    <div class="mb-5">
        <label for="judul" class="block text-sm font-semibold mb-2">Judul Film</label>
        <input type="text" id="judul" name="judul" value="{{ old('judul') }}"
               class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500">
    </div>

    {{-- Deskripsi --}}
    <div class="mb-5">
        <label for="deskripsi" class="block text-sm font-semibold mb-2">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" rows="4"
                  class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500">{{ old('deskripsi') }}</textarea>
    </div>

    {{-- Genre & Durasi --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
        <div>
            <label for="genre" class="block text-sm font-semibold mb-2">Genre</label>
            <input type="text" id="genre" name="genre" value="{{ old('genre') }}"
                   class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500">
        </div>
        <div>
            <label for="durasi" class="block text-sm font-semibold mb-2">Durasi (menit)</label>
            <input type="number" id="durasi" name="durasi" value="{{ old('durasi') }}"
                   class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500">
        </div>
    </div>

    {{-- Sutradara & Produser --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
        <div>
            <label for="sutradara" class="block text-sm font-semibold mb-2">Sutradara</label>
            <input type="text" id="sutradara" name="sutradara" value="{{ old('sutradara') }}"
                   class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500">
        </div>
        <div>
            <label for="produser" class="block text-sm font-semibold mb-2">Produser</label>
            <input type="text" id="produser" name="produser" value="{{ old('produser') }}"
                   class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500">
        </div>
    </div>

    {{-- Produksi & Penulis --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
        <div>
            <label for="produksi" class="block text-sm font-semibold mb-2">Rumah Produksi</label>
            <input type="text" id="produksi" name="produksi" value="{{ old('produksi') }}"
                   class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500">
        </div>
        <div>
            <label for="penulis" class="block text-sm font-semibold mb-2">Penulis Naskah</label>
            <input type="text" id="penulis" name="penulis" value="{{ old('penulis') }}"
                   class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500">
        </div>
    </div>

    {{-- Pemain --}}
    <div class="mb-5">
        <label for="pemain" class="block text-sm font-semibold mb-2">Daftar Pemain</label>
        <textarea id="pemain" name="pemain" rows="3"
                  class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500"
                  placeholder="Pisahkan nama dengan koma, contoh: Iqbaal Ramadhan, Jefri Nichol, Maudy Ayunda">{{ old('pemain') }}</textarea>
    </div>

    {{-- Trailer --}}
    <div class="mb-5">
        <label for="trailer_video" class="block text-sm font-semibold mb-2">Link Trailer</label>
        <input type="text" id="trailer_video" name="trailer_video" value="{{ old('trailer_video') }}"
               class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500"
               placeholder="URL YouTube atau link video trailer">
    </div>

    {{-- Tanggal Rilis & Rating --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
        <div>
            <label for="tanggal_rilis" class="block text-sm font-semibold mb-2">Tanggal Rilis</label>
            <input type="date" id="tanggal_rilis" name="tanggal_rilis" value="{{ old('tanggal_rilis') }}"
                   class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500">
        </div>
            <div>
                <label for="umur" class="block text-sm font-semibold mb-2">Umur</label>
                <input type="text" id="umur" name="umur" value="{{ old('umur') }}"
                    placeholder="Contoh: SU, 13+, 17+"
                    class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500">
            </div>
    </div>

    {{-- Status --}}
    <div class="mb-5">
        <label for="status" class="block text-sm font-semibold mb-2">Status Tayang</label>
        <select id="status" name="status"
                class="w-full px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500">
            <option value="">-- Pilih Status --</option>
            <option value="akan_tayang" {{ old('status') == 'akan_tayang' ? 'selected' : '' }}>Akan Tayang</option>
            <option value="sedang_tayang" {{ old('status') == 'sedang_tayang' ? 'selected' : '' }}>Sedang Tayang</option>
            <option value="tidak_tayang" {{ old('status') == 'tidak_tayang' ? 'selected' : '' }}>Tidak Tayang</option>
        </select>
    </div>

    {{-- Upload Poster --}}
    <div class="mb-8">
        <label for="poster_image" class="block text-sm font-semibold mb-2">Poster Film</label>
        <input type="file" id="poster_image" name="poster_image" 
               class="w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 
                      file:text-sm file:font-semibold file:bg-red-600 file:text-white hover:file:bg-red-700">
    </div>

    {{-- Tombol Simpan --}}
    <div class="flex justify-end">
        <button type="submit" 
                class="px-8 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-xl font-bold shadow-lg shadow-red-600/30 transition-all transform hover:scale-105">
            Simpan Film
        </button>
    </div>
</form>


    </div>
</div>
@endsection
