@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-neutral-950 text-white">

    {{-- ============================================
         BACKGROUND BLUR EFFECT
         ============================================
         Mengambil poster film sebagai background dengan efek blur
         untuk memberikan kesan sinematik
    --}}
    <div class="absolute inset-0 w-full h-full bg-cover bg-center bg-no-repeat opacity-30 blur-xl"
         style="background-image: url('{{ asset('storage/' . $film->poster_image) }}')">
    </div>

    {{-- ============================================
         MAIN CONTENT CONTAINER
         ============================================ 
         Container utama dengan max-width 6xl untuk consistency
    --}}
    <div class="relative z-10 max-w-6xl mx-auto px-4 py-16">
        
        {{-- ============================================
             HERO SECTION: POSTER + FILM DETAILS
             ============================================
             Grid 3 kolom: 1 untuk poster, 2 untuk detail film
        --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">

            {{-- POSTER FILM (Sticky Position) --}}
            <div class="md:sticky md:top-24">
                <div class="relative overflow-hidden rounded-2xl shadow-2xl transform transition-transform duration-300 hover:scale-105">
                    <img src="{{ asset('storage/' . $film->poster_image) }}" 
                         alt="{{ $film->judul }}" 
                         class="w-full">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                </div>
            </div>

            {{-- DETAIL FILM (2 Kolom) --}}
            <div class="md:col-span-2 space-y-6">
                
                {{-- Judul Film --}}
                <h1 class="text-4xl md:text-5xl font-black tracking-tight bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">
                    {{ strtoupper($film->judul) }}
                </h1>

                {{-- Genre & Info Cepat (Rating, Durasi, Tahun) --}}
                <div class="p-5 bg-neutral-900/80 backdrop-blur-sm rounded-xl border border-neutral-800">
                    {{-- Genre Tags --}}
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach(explode(',', $film->genre) as $genre)
                            <span class="px-3 py-1 text-xs font-semibold bg-neutral-800 border border-neutral-700 rounded-full">
                                {{ trim($genre) }}
                            </span>
                        @endforeach
                    </div>

                    {{-- Quick Info --}}
                    <div class="flex flex-wrap items-center gap-4 text-gray-300 text-sm">
                        <span class="flex items-center gap-2">
                            <i class="far fa-calendar"></i> 
                            {{ \Carbon\Carbon::parse($film->tanggal_rilis)->format('Y') }}
                        </span>
                        <span class="flex items-center gap-2">
                            <i class="far fa-clock"></i> 
                            {{ $film->durasi }} MIN
                        </span>
                        <span class="flex items-center gap-2">
                            <i class="fas fa-star text-yellow-400"></i> 
                            {{ $film->rating }}
                        </span>
                    </div>
                </div>

                {{-- Sinopsis Film --}}
                <div class="p-5 bg-neutral-900/80 backdrop-blur-sm rounded-xl border border-neutral-800">
                    <h3 class="text-sm font-bold text-gray-400 mb-2 uppercase tracking-wide">
                        Sinopsis
                    </h3>
                    <p class="text-gray-300 leading-relaxed text-sm">
                        {{ $film->deskripsi }}
                    </p>
                </div>

                {{-- Credits Grid (Sutradara, Produser, dll) --}}
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="p-3 bg-neutral-900/60 rounded-lg border border-neutral-800">
                        <span class="text-gray-500 block mb-1">Sutradara</span>
                        <span class="text-white font-semibold">{{ $film->sutradara }}</span>
                    </div>
                    <div class="p-3 bg-neutral-900/60 rounded-lg border border-neutral-800">
                        <span class="text-gray-500 block mb-1">Produser</span>
                        <span class="text-white font-semibold">{{ $film->produser }}</span>
                    </div>
                    <div class="p-3 bg-neutral-900/60 rounded-lg border border-neutral-800">
                        <span class="text-gray-500 block mb-1">Penulis</span>
                        <span class="text-white font-semibold">{{ $film->penulis }}</span>
                    </div>
                    <div class="p-3 bg-neutral-900/60 rounded-lg border border-neutral-800">
                        <span class="text-gray-500 block mb-1">Produksi</span>
                        <span class="text-white font-semibold">{{ $film->produksi }}</span>
                    </div>
                    <div class="p-3 bg-neutral-900/60 rounded-lg border border-neutral-800 col-span-2">
                        <span class="text-gray-500 block mb-1">Cast</span>
                        <span class="text-white font-semibold line-clamp-1">{{ $film->pemain }}</span>
                    </div>
                </div>

                {{-- Tombol Trailer (jika ada) --}}
                @if($film->trailer_video)
                    <a href="{{ $film->trailer_video }}" 
                       target="_blank" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-xl font-bold shadow-lg shadow-red-600/30 transition-all transform hover:scale-105">
                        <i class="fas fa-play"></i> 
                        Watch Trailer
                    </a>
                @endif
            </div>
        </div>

        {{-- ============================================
             JADWAL TAYANG (SHOWTIMES SECTION)
             ============================================
             Hanya muncul jika status film = "sedang_tayang"
        --}}
        @if($film->status === 'sedang_tayang')
            <div class="mt-12 pt-8 border-t border-neutral-800">
                
                {{-- Header Section --}}
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                    <span class="w-1 h-6 bg-red-600 rounded-full"></span>
                    Jadwal Tayang
                </h2>

                {{-- ============================================
                     DATE SELECTOR (Hari Ini & Besok)
                     ============================================ 
                --}}
                <div class="flex gap-3 mb-6">
                    @php
                        $today = now();
                        $tomorrow = now()->addDay();
                        $dates = [$today, $tomorrow];
                    @endphp

                    @foreach($dates as $date)
                        @php
                            $isSelected = $date->toDateString() === $selectedDate;
                            $label = $date->isToday() ? 'Hari Ini' : 'Besok';
                        @endphp
                        <a href="{{ route('pelanggan.detail', ['id' => $film->id, 'date' => $date->toDateString()]) }}"
                           class="flex-1 px-5 py-3 rounded-xl border font-bold text-center transition-all
                                  {{ $isSelected 
                                       ? 'bg-gradient-to-r from-red-600 to-red-700 border-red-500 text-white shadow-lg shadow-red-600/40' 
                                       : 'bg-neutral-900 border-neutral-700 text-gray-400 hover:border-neutral-600 hover:text-white' }}">
                            <div class="text-sm">{{ $label }}</div>
                            <div class="text-xs text-gray-400">
                                {{ $date->translatedFormat('d M Y') }}
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- ============================================
                     JADWAL PER STUDIO
                     ============================================
                     Loop setiap studio yang punya jadwal film ini
                --}}
                @forelse($jadwalsByStudio as $studioId => $jadwals)
                    <div class="mb-6 p-5 bg-neutral-900/80 backdrop-blur-sm rounded-xl border border-neutral-800">
                        
                        {{-- Nama Studio --}}
                        <h3 class="text-sm font-bold text-gray-400 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                            </svg>
                            {{ $jadwals->first()->studio->nama_studio ?? 'Studio ' . $studioId }}
                        </h3>
                        
                        {{-- Daftar Jam Tayang --}}
                        <div class="flex flex-wrap gap-3">
                            @foreach($jadwals as $jadwal)
                                @php
                                    // Format jam untuk tampilan (contoh: 14:30)
                                    $jamFormatted = \Carbon\Carbon::parse($jadwal->jam_tayang)->format('H:i');
                                    
                                    // Cek status jadwal
                                    $isPast = $jadwal->sudah_lewat;      // Sudah lewat waktunya
                                    $isFull = $jadwal->kursi_penuh;      // Kursi sudah penuh
                                    $isDisabled = $isPast || $isFull;    // Disabled jika salah satu TRUE
                                @endphp
                                
                                @if($isDisabled)
                                    {{-- BUTTON DISABLED (Abu-abu, tidak bisa diklik) --}}
                                    <button disabled 
                                            class="px-5 py-2.5 rounded-lg bg-gray-900 border border-gray-800 text-gray-600 font-semibold cursor-not-allowed opacity-40 relative group">
                                        {{ $jamFormatted }}
                                        
                                        {{-- Tooltip saat hover --}}
                                        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1.5 bg-black text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition whitespace-nowrap shadow-lg">
                                            @if($isPast)
                                                ⏰ Jadwal sudah lewat
                                            @else
                                                🎫 Kursi penuh (0 tersedia)
                                            @endif
                                        </span>
                                    </button>
                                @else
                                    {{-- BUTTON AKTIF (Bisa diklik untuk booking) --}}
                                    <a href="{{ route('pelanggan.pilih-kursi', ['id' => $jadwal->id]) }}"
                                       class="group relative px-5 py-2.5 rounded-lg bg-neutral-800 hover:bg-red-600 border border-neutral-700 hover:border-red-500 text-white font-bold transition-all transform hover:scale-105 shadow-lg hover:shadow-red-600/30">
                                        {{ $jamFormatted }}
                                        
                                        {{-- Tooltip info kursi tersedia --}}
                                        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1.5 bg-black text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition whitespace-nowrap shadow-lg">
                                            ✓ {{ $jadwal->kursi_tersedia }} kursi tersedia
                                        </span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @empty
                    {{-- Empty State: Tidak ada jadwal --}}
                    <div class="p-8 bg-neutral-900/80 backdrop-blur-sm rounded-xl border border-neutral-800 text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-gray-400 font-semibold">
                            Belum ada jadwal tayang untuk tanggal ini
                        </p>
                        <p class="text-gray-500 text-sm mt-1">
                            Silakan pilih tanggal lain
                        </p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</div>

@include('layouts.footer')
@endsection