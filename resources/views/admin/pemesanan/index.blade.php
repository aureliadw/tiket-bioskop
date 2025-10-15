@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-neutral-950 text-white p-6">
    <div class="max-w-7xl mx-auto">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2">Kelola Pemesanan</h1>
            <p class="text-gray-400">Semua transaksi pemesanan tiket bioskop</p>
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

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-neutral-900 border border-neutral-800 rounded-xl p-4">
                <p class="text-gray-400 text-sm mb-1">Total Pemesanan</p>
                <p class="text-2xl font-bold">{{ $pemesanans->total() }}</p>
            </div>
            <div class="bg-green-900/20 border border-green-700 rounded-xl p-4">
                <p class="text-green-400 text-sm mb-1">Confirmed</p>
                <p class="text-2xl font-bold text-green-400">
                    {{ $pemesanans->where('pembayaran.status_pembayaran', 'berhasil')->count() }}
                </p>
            </div>
            <div class="bg-yellow-900/20 border border-yellow-700 rounded-xl p-4">
                <p class="text-yellow-400 text-sm mb-1">Pending</p>
                <p class="text-2xl font-bold text-yellow-400">
                    {{ $pemesanans->where('pembayaran.status_pembayaran', 'pending')->count() }}
                </p>
            </div>
            <div class="bg-red-900/20 border border-red-700 rounded-xl p-4">
                <p class="text-red-400 text-sm mb-1">Cancelled</p>
                <p class="text-2xl font-bold text-red-400">
                    {{ $pemesanans->where('pembayaran.status_pembayaran', 'gagal')->count() }}
                </p>
            </div>
        </div>

        {{-- Filter & Search --}}
        <div class="mb-6 bg-neutral-900 border border-neutral-800 rounded-xl p-4">
            <form method="GET" class="flex flex-wrap gap-3">
                {{-- Search --}}
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari kode booking atau nama pelanggan..." 
                       class="flex-1 min-w-[250px] px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500 text-sm">
                
                {{-- Filter Status --}}
                <select name="status" 
                        class="px-4 py-2.5 bg-neutral-800 border border-neutral-700 rounded-lg focus:outline-none focus:border-red-500 text-sm">
                    <option value="">Semua Status</option>
                    <option value="berhasil" {{ request('status') == 'berhasil' ? 'selected' : '' }}>✅ Confirmed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                    <option value="gagal" {{ request('status') == 'gagal' ? 'selected' : '' }}>❌ Cancelled</option>
                </select>
                
                {{-- Button Filter --}}
                <button type="submit" 
                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 rounded-lg font-semibold transition">
                    Filter
                </button>
                
                {{-- Button Reset --}}
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.pemesanan') }}" 
                       class="px-6 py-2.5 bg-neutral-700 hover:bg-neutral-600 rounded-lg font-semibold transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Tabel Pemesanan --}}
        <div class="bg-neutral-900 rounded-2xl border border-neutral-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-neutral-800 bg-neutral-800/50">
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-400">Kode Booking</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-400">Pelanggan</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-400">Film</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-400">Studio</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-400">Showtime</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-400">Total</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-400">Status</th>
                            <th class="text-center py-4 px-4 text-sm font-semibold text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pemesanans as $order)
                        <tr class="border-b border-neutral-800 hover:bg-neutral-800/30 transition">
                            {{-- Kode Booking --}}
                            <td class="py-4 px-4">
                                <span class="font-mono font-bold text-red-400">
                                    #{{ $order->kode_pemesanan ?? str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </p>
                            </td>

                            {{-- Pelanggan --}}
                            <td class="py-4 px-4">
                                <p class="font-semibold">{{ $order->user->nama_lengkap ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $order->user->email ?? '-' }}</p>
                            </td>

                            {{-- Film --}}
                            <td class="py-4 px-4">
                                <p class="font-semibold">{{ $order->jadwal->film->judul ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $order->jadwal->film->genre ?? '-' }}</p>
                            </td>

                            {{-- Studio --}}
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 bg-neutral-800 border border-neutral-700 rounded text-xs font-semibold">
                                    {{ $order->jadwal->studio->nama_studio ?? 'N/A' }}
                                </span>
                            </td>

                            {{-- Showtime --}}
                            <td class="py-4 px-4">
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($order->jadwal->tanggal_tayang)->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($order->jadwal->jam_tayang)->format('H:i') }} WIB</p>
                            </td>

                            {{-- Total --}}
                            <td class="py-4 px-4">
                                <p class="font-bold text-green-400">Rp {{ number_format($order->total_bayar ?? 0, 0, ',', '.') }}</p>
                            </td>

                            {{-- Status --}}
                            <td class="py-4 px-4">
                                @if($order->pembayaran && $order->pembayaran->status_pembayaran == 'berhasil')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-bold">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Confirmed
                                    </span>
                                @elseif($order->pembayaran && $order->pembayaran->status_pembayaran == 'gagal')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-500/20 text-red-400 rounded text-xs font-bold">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Cancelled
                                    </span>
                                @elseif($order->pembayaran && $order->pembayaran->status_pembayaran == 'pending')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded text-xs font-bold">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-bold">
                                        Unpaid
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-4 px-4 text-center">
                                <a href="{{ route('admin.pemesanan.detail', $order->id) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 rounded-lg text-xs font-bold transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-gray-400 font-semibold mb-2">Belum Ada Pemesanan</p>
                                    <p class="text-gray-500 text-sm">Pemesanan akan muncul di sini setelah pelanggan melakukan booking</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($pemesanans->hasPages())
                <div class="border-t border-neutral-800 px-4 py-4">
                    {{ $pemesanans->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection