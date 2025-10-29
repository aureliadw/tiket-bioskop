@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-neutral-950 text-white p-4 sm:p-6">
    <div class="max-w-7xl mx-auto">
        
        {{-- Header dengan Greeting & Time --}}
        <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 mb-2">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight">
                        Dashboard Admin
                    </h1>
                    <span class="px-3 py-1 bg-red-600/20 text-red-400 rounded-full text-xs font-bold animate-pulse">
                        LIVE
                    </span>
                </div>
                <p class="text-sm sm:text-base text-gray-400">
                    {{ now()->format('l, d F Y') }} • 
                    <span class="text-white font-semibold">{{ auth()->user()->nama_lengkap }}</span>
                </p>
            </div>
            
            {{-- Quick Stats Mini --}}
            <div class="w-full sm:w-auto flex sm:hidden md:flex items-center gap-4">
                <div class="flex-1 sm:flex-none text-center px-4 py-2 bg-neutral-900 rounded-lg border border-neutral-800">
                    <p class="text-xl sm:text-2xl font-bold text-green-400">{{ $stats['pemesanan_hari_ini'] }}</p>
                    <p class="text-xs text-gray-500">Hari Ini</p>
                </div>
            </div>
        </div>

        {{-- Stats Grid dengan Animasi --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">

            {{-- Total Pemesanan --}}
            <div class="group relative bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl sm:rounded-2xl p-5 sm:p-6 overflow-hidden hover:scale-105 transition-all duration-300 cursor-pointer">
                {{-- Decorative Circle --}}
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-lg sm:rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                        </div>
                        <span class="px-2 py-1 bg-white/20 rounded-full text-xs font-semibold">
                            +{{ $stats['pemesanan_hari_ini'] }}
                        </span>
                    </div>
                    <p class="text-blue-100 text-xs sm:text-sm mb-1">Total Pemesanan</p>
                    <h3 class="text-2xl sm:text-3xl font-black">{{ $stats['total_pemesanan'] }}</h3>
                </div>
            </div>

            {{-- Total Revenue --}}
            <div class="group relative bg-gradient-to-br from-green-600 to-emerald-700 rounded-xl sm:rounded-2xl p-5 sm:p-6 overflow-hidden hover:scale-105 transition-all duration-300 cursor-pointer">
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-lg sm:rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-green-100 text-xs sm:text-sm mb-1">Total Revenue</p>
                    <h3 class="text-lg sm:text-2xl font-black">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
                    <p class="text-xs text-green-200/80 mt-1">Terverifikasi</p>
                </div>
            </div>

            {{-- Quick Action - Kelola Film --}}
            <div class="group relative bg-gradient-to-br from-red-600 to-red-700 rounded-xl sm:rounded-2xl p-5 sm:p-6 overflow-hidden hover:scale-105 transition-all duration-300 sm:col-span-2 lg:col-span-1">
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                
                <a href="{{ route('admin.film.index') }}" class="relative z-10 flex flex-col items-center justify-center h-full text-center">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 rounded-lg sm:rounded-xl flex items-center justify-center backdrop-blur-sm mb-3">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <p class="font-bold text-base sm:text-lg mb-1">Kelola Film</p>
                    <p class="text-xs sm:text-sm text-red-100">{{ $stats['total_film'] }} film tersedia</p>
                </a>
            </div>

        </div>

        {{-- Secondary Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
            <div class="bg-neutral-900 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-neutral-800 hover:border-neutral-700 transition">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-purple-600/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl sm:text-2xl font-bold">{{ $stats['total_film'] }}</p>
                        <p class="text-xs text-gray-500">Total Film</p>
                    </div>
                </div>
            </div>

            <div class="bg-neutral-900 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-neutral-800 hover:border-neutral-700 transition">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-600/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl sm:text-2xl font-bold">{{ $stats['total_pelanggan'] }}</p>
                        <p class="text-xs text-gray-500">Pelanggan</p>
                    </div>
                </div>
            </div>

            <div class="bg-neutral-900 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-neutral-800 hover:border-neutral-700 transition">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-yellow-600/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl sm:text-2xl font-bold text-yellow-400">{{ $stats['pending_pembayaran'] }}</p>
                        <p class="text-xs text-gray-500">Pending</p>
                    </div>
                </div>
            </div>

            <div class="bg-neutral-900 rounded-lg sm:rounded-xl p-3 sm:p-4 border border-neutral-800 hover:border-neutral-700 transition">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-600/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg sm:text-lg font-bold text-green-400">+12.5%</p>
                        <p class="text-xs text-gray-500">Growth</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Orders dengan Design Baru --}}
        <div class="bg-neutral-900 rounded-xl sm:rounded-2xl border border-neutral-800 overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-neutral-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-0">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold mb-1">Pemesanan Terbaru</h2>
                    <p class="text-xs sm:text-sm text-gray-500">Activity terkini dari pelanggan</p>
                </div>
                <a href="{{ route('admin.pemesanan') }}" 
                   class="w-full sm:w-auto px-4 py-2 bg-neutral-800 hover:bg-neutral-700 rounded-lg text-sm font-semibold transition flex items-center justify-center gap-2">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            {{-- Mobile Card View --}}
            <div class="block lg:hidden divide-y divide-neutral-800">
                @forelse($recentOrders as $order)
                <div class="p-4 hover:bg-neutral-800/50 transition">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-red-600 to-red-700 rounded-full flex items-center justify-center text-xs font-bold shrink-0">
                                {{ substr($order->user->nama_lengkap ?? 'N', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold">{{ $order->user->nama_lengkap ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400 font-mono">#{{ $order->kode_pemesanan ?? str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                        @if($order->pembayaran && $order->pembayaran->status_pembayaran == 'berhasil')
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-semibold shrink-0">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Lunas
                            </span>
                        @elseif($order->pembayaran && $order->pembayaran->status_pembayaran == 'gagal')
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-500/20 text-red-400 rounded-full text-xs font-semibold shrink-0">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                Gagal
                            </span>
                        @elseif($order->pembayaran && $order->pembayaran->status_pembayaran == 'pending')
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-xs font-semibold shrink-0">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                Pending
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-500/20 text-gray-400 rounded-full text-xs font-semibold shrink-0">
                                Belum Bayar
                            </span>
                        @endif
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Film:</span>
                            <span class="font-medium">{{ Str::limit($order->jadwal->film->judul ?? 'N/A', 25) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Tanggal:</span>
                            <span class="text-gray-300">{{ $order->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-neutral-800">
                            <span class="text-gray-400">Total:</span>
                            <span class="font-bold text-green-400">Rp {{ number_format($order->total_bayar, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-gray-500 font-medium">Belum ada pemesanan</p>
                    </div>
                </div>
                @endforelse
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-neutral-800 bg-neutral-800/50">
                            <th class="py-4 px-6 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Kode</th>
                            <th class="py-4 px-6 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                            <th class="py-4 px-6 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Film</th>
                            <th class="py-4 px-6 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="py-4 px-6 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="py-4 px-6 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-800">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-neutral-800/50 transition group">
                            <td class="py-4 px-6">
                                <span class="font-mono text-sm font-semibold text-blue-400">
                                    #{{ $order->kode_pemesanan ?? str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-red-600 to-red-700 rounded-full flex items-center justify-center text-xs font-bold">
                                        {{ substr($order->user->nama_lengkap ?? 'N', 0, 1) }}
                                    </div>
                                    <span class="text-sm font-medium">{{ $order->user->nama_lengkap ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-sm">{{ Str::limit($order->jadwal->film->judul ?? 'N/A', 20) }}</td>
                            <td class="py-4 px-6 text-sm text-gray-400">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="py-4 px-6 text-sm font-bold text-green-400">
                                Rp {{ number_format($order->total_bayar, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6">
                                @if($order->pembayaran && $order->pembayaran->status_pembayaran == 'berhasil')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-semibold">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Lunas
                                    </span>
                                @elseif($order->pembayaran && $order->pembayaran->status_pembayaran == 'gagal')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-red-500/20 text-red-400 rounded-full text-xs font-semibold">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Gagal
                                    </span>
                                @elseif($order->pembayaran && $order->pembayaran->status_pembayaran == 'pending')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-xs font-semibold">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-500/20 text-gray-400 rounded-full text-xs font-semibold">
                                        Belum Bayar
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-gray-500 font-medium">Belum ada pemesanan</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection