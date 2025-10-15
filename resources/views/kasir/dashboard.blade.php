@extends('layouts.kasir')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold mb-2">Dashboard Kasir</h1>
        <p class="text-gray-400">Selamat datang, {{ auth()->user()->name }}! 👋</p>
        <p class="text-sm text-gray-500">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Check-In Hari Ini -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-6 shadow-xl">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold mb-1">{{ $stats['total_checkin'] }}</p>
            <p class="text-blue-100 text-sm">Check-In Hari Ini</p>
        </div>

        <!-- Penjualan Hari Ini -->
        <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-2xl p-6 shadow-xl">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold mb-1">Rp {{ number_format($stats['total_penjualan'], 0, ',', '.') }}</p>
            <p class="text-green-100 text-sm">Penjualan Hari Ini</p>
        </div>

        <!-- Tiket Pending -->
        <div class="bg-gradient-to-br from-yellow-600 to-yellow-700 rounded-2xl p-6 shadow-xl">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold mb-1">{{ $stats['tiket_pending'] }}</p>
            <p class="text-yellow-100 text-sm">Tiket Belum Check-In</p>
        </div>

        <!-- Jadwal Hari Ini -->
        <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-2xl p-6 shadow-xl">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold mb-1">{{ $stats['jadwal_hari_ini'] }}</p>
            <p class="text-purple-100 text-sm">Jadwal Hari Ini</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('kasir.checkin') }}" 
           class="bg-gradient-to-br from-gray-800 to-gray-900 border-2 border-blue-500 rounded-2xl p-6 hover:border-blue-400 transition group">
            <div class="flex items-center gap-4 mb-3">
                <div class="w-14 h-14 bg-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-1">Check-In Tiket</h3>
                    <p class="text-sm text-gray-400">Scan atau input booking code</p>
                </div>
            </div>
            <div class="flex items-center text-blue-400 text-sm font-semibold">
                <span>Mulai Check-In</span>
                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        <a href="{{ route('kasir.jual-tiket') }}" 
           class="bg-gradient-to-br from-gray-800 to-gray-900 border-2 border-green-500 rounded-2xl p-6 hover:border-green-400 transition group">
            <div class="flex items-center gap-4 mb-3">
                <div class="w-14 h-14 bg-green-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-1">Jual Tiket Offline</h3>
                    <p class="text-sm text-gray-400">Walk-in customer langsung bayar</p>
                </div>
            </div>
            <div class="flex items-center text-green-400 text-sm font-semibold">
                <span>Buat Transaksi Baru</span>
                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>
    </div>

    <!-- Jadwal Hari Ini -->
    <div class="bg-gray-800 rounded-2xl shadow-xl">
        <div class="p-6 border-b border-gray-700">
            <h2 class="text-2xl font-bold">📅 Jadwal Hari Ini</h2>
            <p class="text-sm text-gray-400 mt-1">Daftar film yang tayang hari ini</p>
        </div>
        <div class="p-6">
            @if($jadwals->isEmpty())
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-gray-400">Tidak ada jadwal hari ini</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($jadwals as $jadwal)
                    <div class="bg-gray-900 border border-gray-700 rounded-xl p-4 hover:border-blue-500 transition">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-lg truncate mb-1">{{ $jadwal->film->judul }}</h3>
                                <p class="text-sm text-gray-400">{{ $jadwal->studio->nama_studio }}</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-gray-300">{{ date('H:i', strtotime($jadwal->waktu_mulai)) }} - {{ date('H:i', strtotime($jadwal->waktu_selesai)) }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-gray-300">Rp {{ number_format($jadwal->harga, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Check-Ins -->
    <div class="bg-gray-800 rounded-2xl shadow-xl">
        <div class="p-6 border-b border-gray-700">
            <h2 class="text-2xl font-bold">✅ Check-In Terbaru</h2>
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
                    <thead class="bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Booking Code</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Film</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($recentCheckins as $checkin)
                        <tr class="hover:bg-gray-900/50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono">{{ $checkin->used_at->format('H:i:s') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-sm bg-blue-900/30 px-2 py-1 rounded border border-blue-500/50">
                                    {{ $checkin->kode_pemesanan }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold truncate max-w-xs">{{ $checkin->jadwal->film->judul }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm">{{ $checkin->user->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-400">{{ $checkin->usedBy->name ?? 'System' }}</span>
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