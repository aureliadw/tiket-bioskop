@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-neutral-950 text-white p-6">
    <div class="max-w-7xl mx-auto">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold mb-2">Kelola Studio</h1>
                <p class="text-gray-400">Manajemen studio bioskop</p>
            </div>
            <a href="{{ route('admin.studio.create') }}" 
               class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-xl font-bold shadow-lg transition-all hover:scale-105">
                <i class="fas fa-plus mr-2"></i>
                Tambah Studio
            </a>
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 rounded-xl text-green-400 flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Grid Studio Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($studios as $studio)
                <div class="bg-gradient-to-br from-neutral-900 to-neutral-950 rounded-2xl border border-neutral-800 p-6 hover:border-neutral-700 transition-all hover:scale-105">
                    
                    {{-- Header Card --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-red-600/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold">{{ $studio->nama_studio }}</h3>
                                <p class="text-xs text-gray-500">ID: {{ $studio->id }}</p>
                            </div>
                        </div>
                        
                        {{-- Status Badge --}}
                        @if($studio->status_aktif)
                            <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-semibold">
                                Aktif
                            </span>
                        @else
                            <span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded-full text-xs font-semibold">
                                Nonaktif
                            </span>
                        @endif
                    </div>

                    {{-- Deskripsi --}}
                    @if($studio->deskripsi)
                        <p class="text-sm text-gray-400 mb-4 line-clamp-2">{{ $studio->deskripsi }}</p>
                    @endif

                    {{-- Stats --}}
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="bg-neutral-800/50 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-blue-400">{{ $studio->total_kursi }}</p>
                            <p class="text-xs text-gray-500">Kursi</p>
                        </div>
                        <div class="bg-neutral-800/50 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-purple-400">{{ $studio->kursis_count ?? 0 }}</p>
                            <p class="text-xs text-gray-500">Layout</p>
                        </div>
                        <div class="bg-neutral-800/50 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-green-400">{{ $studio->jadwals_count ?? 0 }}</p>
                            <p class="text-xs text-gray-500">Jadwal</p>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-2">
                        <a href="{{ route('admin.studio.edit', $studio->id) }}" 
                           class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-semibold text-center transition">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        
                        <form action="{{ route('admin.studio.destroy', $studio->id) }}" 
                              method="POST" 
                              onsubmit="return confirm('Yakin hapus studio ini?')"
                              class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full py-2 bg-red-600/20 hover:bg-red-600/30 text-red-400 rounded-lg text-sm font-semibold transition">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </button>
                        </form>
                    </div>

                    {{-- Warning jika layout kursi belum ada --}}
                    @if($studio->kursis_count == 0)
                        <div class="mt-3 p-2 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
                            <p class="text-xs text-yellow-400 text-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Layout kursi belum dibuat
                            </p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full bg-neutral-900 rounded-2xl border border-neutral-800 p-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <h3 class="text-xl font-bold mb-2 text-gray-400">Belum Ada Studio</h3>
                    <p class="text-gray-500 mb-4">Tambahkan studio pertama untuk memulai</p>
                    <a href="{{ route('admin.studio.create') }}" 
                       class="inline-block px-6 py-3 bg-red-600 hover:bg-red-700 rounded-lg font-semibold transition">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Studio
                    </a>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection