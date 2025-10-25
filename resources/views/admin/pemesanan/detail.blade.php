@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-neutral-950 text-white p-6">
    <div class="max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-black tracking-tight bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent mb-2">
                        Detail Pemesanan
                    </h1>
                    <div class="flex items-center gap-3 text-sm">
                        <span class="text-gray-500">Kode Booking:</span>
                        <span class="px-3 py-1 bg-red-950/50 border border-red-900/50 rounded-lg font-mono text-red-400 font-bold tracking-wider">
                            #{{ $pemesanan->kode_pemesanan ?? str_pad($pemesanan->id, 6, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('admin.pemesanan') }}"
                   class="px-6 py-2.5 bg-neutral-900 hover:bg-neutral-800 border border-neutral-800 hover:border-neutral-700 rounded-xl font-semibold text-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
            
            {{-- Status Badge Besar --}}
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border
                @php
                    $status = $pemesanan->pembayaran->status_pembayaran ?? 'unpaid';
                @endphp
                @if($status === 'berhasil')
                    bg-green-950/30 border-green-900/50 text-green-400
                @elseif($status === 'pending')
                    bg-yellow-950/30 border-yellow-900/50 text-yellow-400
                @elseif($status === 'gagal')
                    bg-red-950/30 border-red-900/50 text-red-400
                @else
                    bg-gray-950/30 border-gray-900/50 text-gray-400
                @endif">
                <div class="w-2 h-2 rounded-full
                    @if($status === 'berhasil') bg-green-400 animate-pulse
                    @elseif($status === 'pending') bg-yellow-400 animate-pulse
                    @elseif($status === 'gagal') bg-red-400
                    @else bg-gray-400
                    @endif">
                </div>
                <span class="font-bold text-sm uppercase tracking-wide">
                    @if($status === 'berhasil') Pembayaran Berhasil
                    @elseif($status === 'pending') Menunggu Konfirmasi
                    @elseif($status === 'gagal') Pembayaran Gagal
                    @else Belum Dibayar
                    @endif
                </span>
            </div>
        </div>

        {{-- Grid Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            
            {{-- Left Column: Film & Jadwal (Lebih Besar) --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Film & Jadwal Card --}}
                <div class="bg-gradient-to-br from-neutral-900 to-neutral-950 border border-neutral-800 rounded-2xl p-6 shadow-xl">
                    <div class="flex items-center gap-2 mb-5 pb-3 border-b border-neutral-800">
                        <div class="w-8 h-8 bg-red-600/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold">Film & Jadwal Tayang</h2>
                    </div>
                    
                    <div class="space-y-4">
                        {{-- Film Info --}}
                        <div class="p-4 bg-neutral-950/50 rounded-xl border border-neutral-800/50">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-16 h-20 bg-neutral-800 rounded-lg overflow-hidden">
                                    @if(isset($pemesanan->jadwal->film->poster_image))
                                        <img src="{{ asset('storage/' . $pemesanan->jadwal->film->poster_image) }}" 
                                             alt="{{ $pemesanan->jadwal->film->judul }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-600">
                                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-lg mb-1">{{ $pemesanan->jadwal->film->judul ?? '-' }}</h3>
                                    <div class="flex flex-wrap gap-2 mb-2">
                                        @if(isset($pemesanan->jadwal->film->genre))
                                            @foreach(explode(',', $pemesanan->jadwal->film->genre) as $genre)
                                                <span class="px-2 py-0.5 bg-neutral-800 border border-neutral-700 rounded text-xs text-gray-400">
                                                    {{ trim($genre) }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3 text-xs text-gray-400">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $pemesanan->jadwal->film->durasi ?? '-' }} menit
                                        </span>
                                        @if(isset($pemesanan->jadwal->film->rating))
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                                {{ $pemesanan->jadwal->film->rating }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Schedule Info --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 bg-neutral-950/50 rounded-lg border border-neutral-800/50">
                                <span class="text-xs text-gray-500 block mb-1">Studio</span>
                                <span class="font-bold">{{ $pemesanan->jadwal->studio->nama_studio ?? '-' }}</span>
                            </div>
                            <div class="p-3 bg-neutral-950/50 rounded-lg border border-neutral-800/50">
                                <span class="text-xs text-gray-500 block mb-1">Tanggal Tayang</span>
                                <span class="font-bold">{{ \Carbon\Carbon::parse($pemesanan->jadwal->tanggal_tayang)->format('d M Y') }}</span>
                            </div>
                            <div class="p-3 bg-neutral-950/50 rounded-lg border border-neutral-800/50 col-span-2">
                                <span class="text-xs text-gray-500 block mb-1">Jam Tayang</span>
                                <span class="font-bold text-red-400">{{ \Carbon\Carbon::parse($pemesanan->jadwal->jam_tayang)->format('H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kursi Card --}}
                <div class="bg-gradient-to-br from-neutral-900 to-neutral-950 border border-neutral-800 rounded-2xl p-6 shadow-xl">
                    <div class="flex items-center gap-2 mb-5 pb-3 border-b border-neutral-800">
                        <div class="w-8 h-8 bg-red-600/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold">Kursi Dipesan</h2>
                        @if($pemesanan->kursi && count($pemesanan->kursi) > 0)
                            <span class="ml-auto px-2 py-1 bg-red-950/50 border border-red-900/50 rounded-lg text-xs font-bold text-red-400">
                                {{ count($pemesanan->kursi) }} Kursi
                            </span>
                        @endif
                    </div>
                    
                    @if($pemesanan->kursi && count($pemesanan->kursi) > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($pemesanan->kursi as $kursi)
                                <div class="px-4 py-2.5 bg-red-950/30 border border-red-900/50 rounded-lg font-mono text-sm font-bold text-red-400 hover:bg-red-900/30 transition-colors">
                                    {{ $kursi->kode_kursi }}
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-gray-500 text-sm">Tidak ada kursi tercatat</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Column: Data Pelanggan & Pembayaran --}}
            <div class="space-y-6">
                
                {{-- Data Pelanggan --}}
                <div class="bg-gradient-to-br from-neutral-900 to-neutral-950 border border-neutral-800 rounded-2xl p-6 shadow-xl">
                    <div class="flex items-center gap-2 mb-5 pb-3 border-b border-neutral-800">
                        <div class="w-8 h-8 bg-red-600/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold">Data Pelanggan</h2>
                    </div>
                    <div class="space-y-3">
                        <div class="p-3 bg-neutral-950/50 rounded-lg border border-neutral-800/50">
                            <span class="text-xs text-gray-500 block mb-1">Nama Lengkap</span>
                            <span class="font-semibold">{{ $pemesanan->user->nama_lengkap ?? '-' }}</span>
                        </div>
                        <div class="p-3 bg-neutral-950/50 rounded-lg border border-neutral-800/50">
                            <span class="text-xs text-gray-500 block mb-1">Email</span>
                            <span class="font-semibold text-sm break-all">{{ $pemesanan->user->email ?? '-' }}</span>
                        </div>
                        <div class="p-3 bg-neutral-950/50 rounded-lg border border-neutral-800/50">
                            <span class="text-xs text-gray-500 block mb-1">No. Telepon</span>
                            <span class="font-semibold">{{ $pemesanan->user->phone ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Pembayaran --}}
                <div class="bg-gradient-to-br from-neutral-900 to-neutral-950 border border-neutral-800 rounded-2xl p-6 shadow-xl">
                    <div class="flex items-center gap-2 mb-5 pb-3 border-b border-neutral-800">
                        <div class="w-8 h-8 bg-red-600/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold">Pembayaran</h2>
                    </div>
                    <div class="space-y-3">
                        <div class="p-3 bg-neutral-950/50 rounded-lg border border-neutral-800/50">
                            <span class="text-xs text-gray-500 block mb-1">Metode Pembayaran</span>
                            <span class="font-semibold uppercase">{{ $pemesanan->pembayaran->metode ?? '-' }}</span>
                        </div>
                        <div class="p-4 bg-gradient-to-r from-red-950/50 to-neutral-950/50 border border-red-900/50 rounded-lg">
                            <span class="text-xs text-gray-500 block mb-1">Total Pembayaran</span>
                            <span class="text-2xl font-black text-red-400">Rp {{ number_format($pemesanan->total_bayar ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="p-3 bg-neutral-950/50 rounded-lg border border-neutral-800/50">
                            <span class="text-xs text-gray-500 block mb-2">Status Pembayaran</span>
                            @if($status === 'berhasil')
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-950/30 border border-green-900/50 text-green-400 rounded-lg text-xs font-bold">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    BERHASIL
                                </span>
                            @elseif($status === 'pending')
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-yellow-950/30 border border-yellow-900/50 text-yellow-400 rounded-lg text-xs font-bold">
                                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    PENDING
                                </span>
                            @elseif($status === 'gagal')
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-950/30 border border-red-900/50 text-red-400 rounded-lg text-xs font-bold">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    GAGAL
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-950/30 border border-gray-900/50 text-gray-400 rounded-lg text-xs font-bold">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    BELUM DIBAYAR
                                </span>
                            @endif
                        </div>
                        <div class="p-3 bg-neutral-950/50 rounded-lg border border-neutral-800/50">
                            <span class="text-xs text-gray-500 block mb-1">Tanggal Pembayaran</span>
                            <span class="font-semibold text-sm">
                                {{ $pemesanan->pembayaran->updated_at ? $pemesanan->pembayaran->updated_at->format('d M Y, H:i') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Timeline --}}
                <div class="bg-gradient-to-br from-neutral-900 to-neutral-950 border border-neutral-800 rounded-2xl p-6 shadow-xl">
                    <div class="flex items-center gap-2 mb-5 pb-3 border-b border-neutral-800">
                        <div class="w-8 h-8 bg-red-600/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold">Timeline</h2>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-red-500 rounded-full mt-1.5"></div>
                            <div>
                                <p class="text-gray-500 text-xs">Pemesanan Dibuat</p>
                                <p class="font-semibold">{{ $pemesanan->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @if($pemesanan->pembayaran && $pemesanan->pembayaran->updated_at)
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 
                                    @if($status === 'berhasil') bg-green-500
                                    @elseif($status === 'pending') bg-yellow-500
                                    @else bg-gray-500
                                    @endif rounded-full mt-1.5">
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs">Status Terakhir</p>
                                    <p class="font-semibold">{{ $pemesanan->pembayaran->updated_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        @if($status === 'pending')
            <div class="bg-gradient-to-br from-yellow-950/20 to-neutral-950 border border-yellow-900/30 rounded-2xl p-6 shadow-xl">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-yellow-600/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-yellow-400 mb-1">Menunggu Konfirmasi</h3>
                        <p class="text-sm text-gray-400 mb-4">
                            Pembayaran pelanggan sedang menunggu konfirmasi dari admin. Silakan periksa bukti pembayaran dan konfirmasi.
                        </p>
                        <form action="{{ route('admin.pembayaran.konfirmasi', $pemesanan->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit"
                                    class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 rounded-xl font-bold shadow-lg shadow-green-600/30 transition-all transform hover:scale-105 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Konfirmasi Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection