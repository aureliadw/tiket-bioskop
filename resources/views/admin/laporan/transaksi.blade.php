@extends('layouts.admin')

@section('page-title', 'Laporan Transaksi')
@section('page-subtitle', 'Detail pemesanan dan transaksi')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- Back Button --}}
    <div class="mb-6">
        <a href="{{ route('admin.laporan.index') }}" 
           class="inline-flex items-center gap-2 text-gray-400 hover:text-white transition text-sm">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Laporan</span>
        </a>
    </div>

    {{-- Filter Section --}}
    <div class="bg-gradient-to-br from-neutral-900 to-neutral-950 border border-neutral-800 rounded-2xl p-6 mb-6 shadow-xl">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 bg-blue-600/20 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-filter text-blue-400"></i>
            </div>
            <h3 class="text-lg font-bold">Filter Laporan</h3>
        </div>

        <form method="GET" action="{{ route('admin.laporan.transaksi') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Tanggal Dari --}}
            <div>
                <label class="block text-xs text-gray-500 mb-2">Tanggal Dari</label>
                <input type="date" name="tanggal_dari" value="{{ $tanggalDari }}"
                       class="w-full bg-neutral-900 border border-neutral-800 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none">
            </div>

            {{-- Tanggal Sampai --}}
            <div>
                <label class="block text-xs text-gray-500 mb-2">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai" value="{{ $tanggalSampai }}"
                       class="w-full bg-neutral-900 border border-neutral-800 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none">
            </div>

            {{-- Status Pembayaran --}}
            <div>
                <label class="block text-xs text-gray-500 mb-2">Status Pembayaran</label>
                <select name="status_pembayaran"
                        class="w-full bg-neutral-900 border border-neutral-800 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none">
                    <option value="all" {{ $statusPembayaran == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="belum_bayar" {{ $statusPembayaran == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="sudah_bayar" {{ $statusPembayaran == 'sudah_bayar' ? 'selected' : '' }}>Sudah Bayar</option>
                    <option value="refund" {{ $statusPembayaran == 'refund' ? 'selected' : '' }}>Refund</option>
                </select>
            </div>

            {{-- Tipe Pemesanan --}}
            <div>
                <label class="block text-xs text-gray-500 mb-2">Tipe Pemesanan</label>
                <select name="tipe_pemesanan"
                        class="w-full bg-neutral-900 border border-neutral-800 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none">
                    <option value="all" {{ $tipePemesanan == 'all' ? 'selected' : '' }}>Semua Tipe</option>
                    <option value="online" {{ $tipePemesanan == 'online' ? 'selected' : '' }}>Online</option>
                    <option value="offline" {{ $tipePemesanan == 'offline' ? 'selected' : '' }}>Offline</option>
                </select>
            </div>

            {{-- Button Submit --}}
            <div class="md:col-span-4 flex gap-3">
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold text-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-search"></i>
                    Tampilkan Laporan
                </button>
                <a href="{{ route('admin.laporan.transaksi') }}"
                   class="px-6 py-2.5 bg-neutral-800 hover:bg-neutral-700 rounded-lg font-semibold text-sm transition">
                    Reset Filter
                </a>
            </div>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-950/30 to-neutral-900 border border-blue-900/30 rounded-xl p-5">
            <p class="text-xs text-gray-500 mb-1">Total Transaksi</p>
            <p class="text-3xl font-black text-blue-400">{{ number_format($totalTransaksi) }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-950/30 to-neutral-900 border border-green-900/30 rounded-xl p-5">
            <p class="text-xs text-gray-500 mb-1">Total Pendapatan</p>
            <p class="text-3xl font-black text-green-400">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-950/30 to-neutral-900 border border-yellow-900/30 rounded-xl p-5">
            <p class="text-xs text-gray-500 mb-1">Belum Dibayar</p>
            <p class="text-3xl font-black text-yellow-400">Rp {{ number_format($totalBelumBayar, 0, ',', '.') }}</p>
        </div>
        <div class="bg-gradient-to-br from-red-950/30 to-neutral-900 border border-red-900/30 rounded-xl p-5">
            <p class="text-xs text-gray-500 mb-1">Refund</p>
            <p class="text-3xl font-black text-red-400">Rp {{ number_format($totalRefund, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Export Button --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Data Transaksi</h2>
        <form method="GET" action="{{ route('admin.laporan.transaksi.pdf') }}" class="inline">
            <input type="hidden" name="tanggal_dari" value="{{ $tanggalDari }}">
            <input type="hidden" name="tanggal_sampai" value="{{ $tanggalSampai }}">
            <input type="hidden" name="status_pembayaran" value="{{ $statusPembayaran }}">
            <input type="hidden" name="tipe_pemesanan" value="{{ $tipePemesanan }}">
            <button type="submit"
                    class="px-5 py-2.5 bg-red-600 hover:bg-red-700 rounded-lg font-semibold text-sm transition flex items-center gap-2 shadow-lg hover:shadow-red-600/30">
                <i class="fa-solid fa-file-pdf"></i>
                Download PDF
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-neutral-900 border border-neutral-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-950 border-b border-neutral-800">
                    <tr>
                        <th class="px-4 py-4 text-left font-bold text-gray-400 text-xs uppercase">Kode Booking</th>
                        <th class="px-4 py-4 text-left font-bold text-gray-400 text-xs uppercase">Tanggal</th>
                        <th class="px-4 py-4 text-left font-bold text-gray-400 text-xs uppercase">Pelanggan</th>
                        <th class="px-4 py-4 text-left font-bold text-gray-400 text-xs uppercase">Film</th>
                        <th class="px-4 py-4 text-left font-bold text-gray-400 text-xs uppercase">Studio</th>
                        <th class="px-4 py-4 text-left font-bold text-gray-400 text-xs uppercase">Kursi</th>
                        <th class="px-4 py-4 text-left font-bold text-gray-400 text-xs uppercase">Total Bayar</th>
                        <th class="px-4 py-4 text-left font-bold text-gray-400 text-xs uppercase">Metode</th>
                        <th class="px-4 py-4 text-left font-bold text-gray-400 text-xs uppercase">Status</th>
                        <th class="px-4 py-4 text-left font-bold text-gray-400 text-xs uppercase">Tipe</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-800">
                    @forelse($pemesanans as $pemesanan)
                        <tr class="hover:bg-neutral-950/50 transition">
                            <td class="px-4 py-4">
                                <span class="font-mono font-bold text-blue-400">#{{ $pemesanan->kode_pemesanan }}</span>
                            </td>
                            <td class="px-4 py-4 text-gray-300">
                                {{ \Carbon\Carbon::parse($pemesanan->tanggal_pesan)->format('d M Y H:i') }}
                            </td>
                            <td class="px-4 py-4 text-gray-300">
                                {{ $pemesanan->user->nama_lengkap ?? '-' }}
                            </td>
                            <td class="px-4 py-4 text-gray-300">
                                {{ $pemesanan->jadwal->film->judul ?? '-' }}
                            </td>
                            <td class="px-4 py-4 text-gray-300">
                                {{ $pemesanan->jadwal->studio->nama_studio ?? '-' }}
                            </td>
                            <td class="px-4 py-4 text-gray-300">
                                {{ $pemesanan->jumlah_kursi }} kursi
                            </td>
                            <td class="px-4 py-4 font-bold text-green-400">
                                Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2 py-1 bg-neutral-800 rounded text-xs">
                                    {{ $pemesanan->pembayaran->metode_pembayaran ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                @if($pemesanan->status_pembayaran == 'sudah_bayar')
                                    <span class="px-2 py-1 bg-green-950/30 border border-green-900/50 text-green-400 rounded text-xs font-bold">
                                        Lunas
                                    </span>
                                @elseif($pemesanan->status_pembayaran == 'belum_bayar')
                                    <span class="px-2 py-1 bg-yellow-950/30 border border-yellow-900/50 text-yellow-400 rounded text-xs font-bold">
                                        Pending
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-red-950/30 border border-red-900/50 text-red-400 rounded text-xs font-bold">
                                        Refund
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2 py-1 bg-neutral-800 rounded text-xs capitalize">
                                    {{ $pemesanan->tipe_pemesanan }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                                <i class="fa-solid fa-inbox text-4xl mb-2 opacity-20"></i>
                                <p>Tidak ada data transaksi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection