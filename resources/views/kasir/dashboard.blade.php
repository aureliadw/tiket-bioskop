@extends('layouts.kasir')

@section('title', 'Dashboard')

@section('content')

<div class="space-y-6">
    
    <!-- Header -->
    <div>
        <h1 class="text-4xl font-bold mb-2 bg-gradient-to-r from-red-500 to-red-700 bg-clip-text text-transparent">Dashboard Kasir</h1>
        <p class="text-gray-300">Selamat datang, {{ auth()->user()->name }}! 👋</p>
        <p class="text-sm text-gray-500">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Check-In Hari Ini -->
        <div class="bg-gradient-to-br from-gray-900 to-black border border-red-900/30 rounded-2xl p-6 shadow-2xl hover:shadow-red-900/50 transition-all duration-300 hover:scale-105">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-red-600/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold mb-1 text-white">{{ $stats['total_checkin'] }}</p>
            <p class="text-red-400 text-sm font-medium">Check-In Hari Ini</p>
        </div>

        <!-- Penjualan Hari Ini -->
        <div class="bg-gradient-to-br from-gray-900 to-black border border-red-900/30 rounded-2xl p-6 shadow-2xl hover:shadow-red-900/50 transition-all duration-300 hover:scale-105">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-green-600/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold mb-1 text-white">Rp {{ number_format($stats['total_penjualan'], 0, ',', '.') }}</p>
            <p class="text-green-400 text-sm font-medium">Penjualan Hari Ini</p>
        </div>

        <!-- Tiket Pending -->
        <div class="bg-gradient-to-br from-gray-900 to-black border border-red-900/30 rounded-2xl p-6 shadow-2xl hover:shadow-red-900/50 transition-all duration-300 hover:scale-105">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-600/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold mb-1 text-white">{{ $stats['tiket_pending'] }}</p>
            <p class="text-yellow-400 text-sm font-medium">Tiket Belum Check-In</p>
        </div>

        <!-- Jadwal Hari Ini -->
        <div class="bg-gradient-to-br from-gray-900 to-black border border-red-900/30 rounded-2xl p-6 shadow-2xl hover:shadow-red-900/50 transition-all duration-300 hover:scale-105">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-red-600/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold mb-1 text-white">{{ $stats['jadwal_hari_ini'] }}</p>
            <p class="text-red-400 text-sm font-medium">Jadwal Hari Ini</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('kasir.checkin') }}" 
           class="bg-gradient-to-br from-gray-900 to-black border-2 border-red-600 rounded-2xl p-6 hover:border-red-500 hover:shadow-2xl hover:shadow-red-900/50 transition-all duration-300 group hover:scale-105">
            <div class="flex items-center gap-4 mb-3">
                <div class="w-14 h-14 bg-gradient-to-br from-red-600 to-red-700 rounded-xl flex items-center justify-center group-hover:scale-110 transition shadow-lg shadow-red-900/50">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-1 text-white">Check-In Tiket</h3>
                    <p class="text-sm text-gray-400">Scan atau input booking code</p>
                </div>
            </div>
            <div class="flex items-center text-red-400 text-sm font-semibold">
                <span>Mulai Check-In</span>
                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        <a href="{{ route('kasir.jual-tiket') }}" 
           class="bg-gradient-to-br from-gray-900 to-black border-2 border-red-600 rounded-2xl p-6 hover:border-red-500 hover:shadow-2xl hover:shadow-red-900/50 transition-all duration-300 group hover:scale-105">
            <div class="flex items-center gap-4 mb-3">
                <div class="w-14 h-14 bg-gradient-to-br from-red-600 to-red-700 rounded-xl flex items-center justify-center group-hover:scale-110 transition shadow-lg shadow-red-900/50">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-1 text-white">Jual Tiket Offline</h3>
                    <p class="text-sm text-gray-400">Walk-in customer langsung bayar</p>
                </div>
            </div>
            <div class="flex items-center text-red-400 text-sm font-semibold">
                <span>Buat Transaksi Baru</span>
                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>
    </div>

   <!-- 📅 Jadwal Hari Ini Section -->
<div class="bg-gradient-to-br from-gray-900 to-black border border-red-900/30 rounded-2xl shadow-2xl">
    <div class="p-6 border-b border-red-900/30">
        <h2 class="text-2xl font-bold text-white">📅 Jadwal Hari Ini</h2>
        <p class="text-sm text-gray-400 mt-1">Daftar film yang tayang hari ini</p>
    </div>

    <div class="p-6">
        @if($jadwals->isEmpty())
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-gray-400">Tidak ada jadwal hari ini</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($jadwals as $jadwal)
                    @php
                        $tanggal = $jadwal->tanggal_tayang instanceof \Carbon\Carbon 
                            ? $jadwal->tanggal_tayang->format('Y-m-d') 
                            : $jadwal->tanggal_tayang;
                        
                        $jam = $jadwal->jam_tayang instanceof \Carbon\Carbon 
                            ? $jadwal->jam_tayang->format('H:i:s') 
                            : $jadwal->jam_tayang;
                        
                        $jadwalDateTime = \Carbon\Carbon::parse($tanggal . ' ' . $jam);
                        $isPast = $jadwalDateTime->isPast();
                        $minutesUntil = now()->diffInMinutes($jadwalDateTime, false);
                    @endphp

                    <!-- 🎬 Card Jadwal -->
                    <div class="relative bg-gradient-to-br from-gray-800 to-black rounded-xl p-4 transition-all duration-300 border-2
                                {{ $isPast ? 'border-red-900/50 opacity-60' : 'border-red-900/30 hover:border-red-600 hover:shadow-lg hover:shadow-red-900/50' }}">
                        
                        {{-- Overlay untuk Jadwal Lewat --}}
                        @if($isPast)
                            <div class="absolute inset-0 bg-black/50 rounded-xl backdrop-blur-[2px] z-20 flex items-center justify-center pointer-events-none">
                                <div class="bg-red-900/95 px-6 py-3 rounded-lg border-2 border-red-500 shadow-xl">
                                    <span class="text-white font-bold text-base">🚫 Jadwal Sudah Lewat</span>
                                </div>
                            </div>
                        @endif

                        {{-- 🏷️ Badge --}}
                        <div class="mb-3 flex gap-2 flex-wrap">
                            {{-- WEEKEND --}}
                            @if($jadwal->is_weekend)
                                <span class="inline-block bg-gradient-to-r from-red-600 to-red-700 text-white text-xs font-bold px-3 py-1 rounded-full border border-red-500 animate-pulse shadow-lg shadow-red-900/50">
                                    🎉 WEEKEND +Rp 10K
                                </span>
                            @endif

                            {{-- STATUS JADWAL --}}
                            @if($isPast)
                                <span class="inline-block bg-red-950 text-red-300 text-xs font-bold px-3 py-1 rounded-full border border-red-800">
                                    ⏱️ LEWAT
                                </span>
                            @elseif($minutesUntil <= 30 && $minutesUntil > 0)
                                <span class="inline-block bg-gradient-to-r from-yellow-600 to-yellow-700 text-white text-xs font-bold px-3 py-1 rounded-full animate-pulse border border-yellow-500 shadow-lg shadow-yellow-900/50">
                                    🔔 {{ $minutesUntil }} MENIT LAGI
                                </span>
                            @else
                                <span class="inline-block bg-gray-800 text-gray-300 text-xs font-bold px-3 py-1 rounded-full border border-gray-700">
                                    ✅ AKTIF
                                </span>
                            @endif
                        </div>

                        {{-- 🎞️ Info Film --}}
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-14 h-14 bg-gradient-to-br from-red-600 to-red-800 rounded-lg flex items-center justify-center flex-shrink-0 shadow-lg shadow-red-900/50">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-base truncate mb-1 text-white">{{ $jadwal->film->judul }}</h3>
                                <p class="text-sm text-gray-400">{{ $jadwal->studio->nama_studio }}</p>
                            </div>
                        </div>

                        {{-- 📋 Detail Jadwal --}}
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 mr-2 {{ $isPast ? 'text-red-400' : 'text-red-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="{{ $isPast ? 'text-red-400 line-through' : 'text-gray-300' }}">
                                    {{ \Carbon\Carbon::parse($jadwal->jam_tayang)->format('H:i') }} WIB
                                </span>
                            </div>

                            {{-- 💰 Harga --}}
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8
                                                 c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1
                                                 m0-1c-1.11 0-2.08-.402-2.599-1
                                                 M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-gray-300 font-semibold">
                                        Rp {{ number_format($jadwal->harga_final, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- 🎟️ Tombol Aksi --}}
                        @if($isPast)
                            <button disabled
                                    class="w-full bg-gray-900 text-gray-600 py-3 px-4 rounded-lg cursor-not-allowed flex items-center justify-center gap-2 border-2 border-gray-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span class="font-semibold">Tidak Tersedia</span>
                            </button>
                        @else
                            <a href="{{ route('kasir.jual-tiket') }}?jadwal_id={{ $jadwal->id }}"
                               class="block w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800
                                      text-white py-3 px-4 rounded-lg transition-all duration-300 flex items-center justify-center
                                      gap-2 font-semibold shadow-lg shadow-red-900/50 hover:scale-[1.02]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                                <span>Pilih Jadwal</span>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

    <!-- Recent Check-Ins -->
<div class="bg-gradient-to-br from-gray-900 to-black border border-red-900/30 rounded-2xl shadow-2xl">
    <div class="p-6 border-b border-red-900/30">
        <h2 class="text-2xl font-bold text-white">✅ Check-In Terbaru</h2>
        <p class="text-sm text-gray-400 mt-1">10 check-in terakhir hari ini</p>
    </div>
    <div class="overflow-x-auto">
        @if($recentCheckins->isEmpty())
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-gray-400">Belum ada check-in hari ini</p>
            </div>
        @else
            <table class="w-full">
                <thead class="bg-black/50 border-b border-red-900/30">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Booking Code</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Film</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-red-900/20">
                    @foreach($recentCheckins as $checkin)
                    <tr class="hover:bg-red-950/20 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($checkin->used_at)
                                <span class="text-sm font-mono text-gray-300">{{ $checkin->used_at->format('H:i:s') }}</span>
                            @else
                                <span class="text-sm text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm bg-red-950/50 text-red-300 px-3 py-1 rounded border border-red-800/50 shadow-sm">
                                {{ $checkin->kode_pemesanan }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-white truncate max-w-xs">
                                {{ $checkin->jadwal->film->judul ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-300">{{ $checkin->user->nama_lengkap ?? 'Walk-in' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-400">
                                {{ $checkin->usedBy->nama_lengkap ?? 'System' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

</div>
@endsection