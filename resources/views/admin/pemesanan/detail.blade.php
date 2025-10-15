@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-neutral-950 text-white p-6">
    <div class="max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-1">Detail Pemesanan</h1>
                <p class="text-gray-400 text-sm">
                    Kode Booking:
                    <span class="font-mono text-red-500 font-semibold">
                        #{{ $pemesanan->kode_pemesanan ?? str_pad($pemesanan->id, 6, '0', STR_PAD_LEFT) }}
                    </span>
                </p>
            </div>
            <a href="{{ route('admin.pemesanan') }}"
               class="px-5 py-2 bg-neutral-800 hover:bg-neutral-700 rounded-lg font-semibold text-sm transition">
                ← Kembali
            </a>
        </div>

        {{-- Data Pelanggan & Pembayaran --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            
            {{-- Data Pelanggan --}}
            <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-6 shadow-lg shadow-black/30">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2 border-b border-neutral-800 pb-2">
                    👤 <span>Data Pelanggan</span>
                </h2>
                <div class="space-y-2 text-sm">
                    <p><span class="text-gray-400">Nama:</span> {{ $pemesanan->user->nama_lengkap ?? '-' }}</p>
                    <p><span class="text-gray-400">Email:</span> {{ $pemesanan->user->email ?? '-' }}</p>
                    <p><span class="text-gray-400">No. Telepon:</span> {{ $pemesanan->user->phone ?? '-' }}</p>
                </div>
            </div>

            {{-- Pembayaran --}}
            <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-6 shadow-lg shadow-black/30">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2 border-b border-neutral-800 pb-2">
                    💳 <span>Informasi Pembayaran</span>
                </h2>
                <div class="space-y-2 text-sm">
                    <p><span class="text-gray-400">Metode:</span> {{ $pemesanan->pembayaran->metode ?? '-' }}</p>
                    <p>
                        <span class="text-gray-400">Total Bayar:</span>
                        <span class="text-green-400 font-bold">Rp {{ number_format($pemesanan->total_bayar ?? 0, 0, ',', '.') }}</span>
                    </p>
                    <p>
                        <span class="text-gray-400">Status:</span>
                        @php
                            $status = $pemesanan->pembayaran->status_pembayaran ?? 'unpaid';
                        @endphp
                        @if($status === 'berhasil')
                            <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded-lg text-xs font-bold">Confirmed</span>
                        @elseif($status === 'pending')
                            <span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded-lg text-xs font-bold">Pending</span>
                        @elseif($status === 'gagal')
                            <span class="px-2 py-1 bg-red-500/20 text-red-400 rounded-lg text-xs font-bold">Cancelled</span>
                        @else
                            <span class="px-2 py-1 bg-gray-600/20 text-gray-400 rounded-lg text-xs font-bold">Unpaid</span>
                        @endif
                    </p>
                    <p>
                        <span class="text-gray-400">Tanggal Bayar:</span>
                        {{ $pemesanan->pembayaran->updated_at ? $pemesanan->pembayaran->updated_at->format('d M Y, H:i') : '-' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Jadwal & Film --}}
        <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-6 mb-8 shadow-lg shadow-black/30">
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2 border-b border-neutral-800 pb-2">
                🎬 <span>Film & Jadwal</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div>
                    <p><span class="text-gray-400">Judul Film:</span> {{ $pemesanan->jadwal->film->judul ?? '-' }}</p>
                    <p><span class="text-gray-400">Genre:</span> {{ $pemesanan->jadwal->film->genre ?? '-' }}</p>
                    <p><span class="text-gray-400">Durasi:</span> {{ $pemesanan->jadwal->film->durasi ?? '-' }} menit</p>
                </div>
                <div>
                    <p><span class="text-gray-400">Studio:</span> {{ $pemesanan->jadwal->studio->nama_studio ?? '-' }}</p>
                    <p><span class="text-gray-400">Tanggal Tayang:</span> {{ \Carbon\Carbon::parse($pemesanan->jadwal->tanggal_tayang)->format('d M Y') }}</p>
                    <p><span class="text-gray-400">Jam Tayang:</span> {{ \Carbon\Carbon::parse($pemesanan->jadwal->jam_tayang)->format('H:i') }} WIB</p>
                </div>
            </div>
        </div>

        {{-- Kursi --}}
        <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-6 mb-8 shadow-lg shadow-black/30">
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2 border-b border-neutral-800 pb-2">
                💺 <span>Kursi Dipesan</span>
            </h2>
            @if($pemesanan->kursi && count($pemesanan->kursi) > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($pemesanan->kursi as $kursi)
                        <span class="px-3 py-1.5 bg-red-600/20 text-red-400 rounded-lg font-mono text-sm border border-red-700/50">
                            {{ $kursi->kode_kursi }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">Tidak ada kursi tercatat.</p>
            @endif
        </div>

        {{-- Catatan & Aksi --}}
        <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-6 shadow-lg shadow-black/30">
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2 border-b border-neutral-800 pb-2">
                📄 <span>Catatan & Aksi</span>
            </h2>

            <p class="text-gray-400 mb-5 text-sm">
                Pemesanan dibuat pada {{ $pemesanan->created_at->format('d M Y, H:i') }}.
            </p>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.pemesanan') }}"
                   class="px-5 py-2 bg-neutral-800 hover:bg-neutral-700 rounded-lg font-semibold text-sm transition">
                    Kembali
                </a>

                @if($status === 'pending')
                    <form action="{{ route('admin.pembayaran.konfirmasi', $pemesanan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                                class="px-6 py-2 bg-green-600 hover:bg-green-700 rounded-lg font-semibold text-sm transition">
                            Konfirmasi Pembayaran
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
