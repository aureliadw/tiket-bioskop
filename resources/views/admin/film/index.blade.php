@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-neutral-950 text-white p-6">
    <div class="max-w-7xl mx-auto">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold mb-2">Kelola Film</h1>
                <p class="text-gray-400">Manajemen data film di HappyCine</p>
            </div>
            <a href="{{ route('admin.film.create') }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-xl font-bold shadow-lg shadow-red-600/30 transition-all transform hover:scale-105">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Film
            </a>
        </div>

        {{-- Alert Success --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 rounded-xl text-green-400 flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter & Search --}}
        <div class="mb-6 flex flex-wrap gap-4 items-center">
            <form method="GET" class="flex-1 flex gap-3">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari film..." 
                       class="flex-1 px-4 py-2.5 bg-neutral-900 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500 text-sm">
                
                <select name="status" 
                        class="px-4 py-2.5 bg-neutral-900 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500 text-sm">
                    <option value="">Semua Status</option>
                    <option value="akan_tayang" {{ request('status') == 'akan_tayang' ? 'selected' : '' }}>Akan Tayang</option>
                    <option value="sedang_tayang" {{ request('status') == 'sedang_tayang' ? 'selected' : '' }}>Sedang Tayang</option>
                    <option value="selesai_tayang" {{ request('status') == 'selesai_tayang' ? 'selected' : '' }}>Selesai Tayang</option>
                </select>
                
                <button type="submit" 
                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 rounded-lg font-semibold transition">
                    Filter
                </button>
            </form>
        </div>

        {{-- Film Grid --}}
        @if($films->isEmpty())
            <div class="bg-neutral-900 rounded-2xl border border-neutral-800 p-16 text-center">
                <div class="w-24 h-24 bg-neutral-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Belum Ada Film</h3>
                <p class="text-gray-400 mb-6">Tambahkan film pertama Anda sekarang!</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($films as $film)
                    <div class="group bg-neutral-900 rounded-2xl border border-neutral-800 overflow-hidden hover:border-red-500/50 transition-all hover:shadow-xl hover:shadow-red-500/10">
                        
                        {{-- Poster --}}
                        <div class="relative aspect-[2/3] overflow-hidden">
                            <img src="{{ asset('storage/' . $film->poster_image) }}" 
                                 alt="{{ $film->judul }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            
                            {{-- Status Badge --}}
                            <div class="absolute top-3 right-3">
                                @if($film->status == 'sedang_tayang')
                                    <span class="px-2 py-1 bg-green-500/90 text-white rounded text-xs font-bold backdrop-blur-sm">
                                        Now Playing
                                    </span>
                                @elseif($film->status == 'akan_tayang')
                                    <span class="px-2 py-1 bg-blue-500/90 text-white rounded text-xs font-bold backdrop-blur-sm">
                                        Coming Soon
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-gray-500/90 text-white rounded text-xs font-bold backdrop-blur-sm">
                                        Ended
                                    </span>
                                @endif
                            </div>

                            {{-- Rating --}}
                            <div class="absolute top-3 left-3 flex items-center gap-1 px-2 py-1 bg-black/70 backdrop-blur-sm rounded text-xs font-bold">
                                <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                {{ $film->rating }}
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-2 line-clamp-1">{{ $film->judul }}</h3>
                            
                            <div class="flex items-center gap-3 text-xs text-gray-400 mb-3">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($film->tanggal_rilis)->format('Y') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $film->durasi }} min
                                </span>
                            </div>

                            <p class="text-xs text-gray-500 mb-4 line-clamp-2">{{ $film->deskripsi }}</p>

                            {{-- Actions --}}
                            <div class="flex gap-2">
                                <a href="{{ route('admin.film.edit', $film->id) }}" 
                                   class="flex-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-center text-xs font-semibold transition">
                                    Edit
                                </a>
                                <form action="{{ route('admin.film.destroy', $film->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Yakin ingin menghapus film ini?')"
                                      class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-xs font-semibold transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $films->links() }}
            </div>
        @endif

    </div>
</div>
@endsection