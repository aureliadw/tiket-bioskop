@extends('layouts.kasir')

@section('content')
<div class="min-h-screen bg-neutral-950 text-white p-6">
    <div class="max-w-7xl mx-auto">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2">🔍 Verifikasi Pembayaran Online</h1>
            <p class="text-gray-400">Konfirmasi pembayaran yang telah diupload pelanggan</p>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 rounded-xl text-green-400 flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- List Pembayaran Pending --}}
        @forelse($pembayarans as $pembayaran)
        <div class="bg-neutral-900 rounded-2xl border border-neutral-800 p-6 mb-6 hover:border-neutral-700 transition" x-data="{ showImage: false, showReject: false }">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Info Pemesanan --}}
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-xl font-bold mb-1">{{ $pembayaran->pemesanan->jadwal->film->judul ?? 'N/A' }}</h3>
                            <p class="text-sm text-gray-400">Kode: <span class="font-mono">#{{ $pembayaran->pemesanan->kode_pemesanan ?? str_pad($pembayaran->pemesanan->id, 6, '0', STR_PAD_LEFT) }}</span></p>
                        </div>
                        <span class="px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-xs font-semibold">
                            Pending
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 mb-1">Pelanggan</p>
                            <p class="font-semibold">{{ $pembayaran->pemesanan->user->nama_lengkap ?? 'N/A' }}</p>
                            <p class="text-gray-400 text-xs">{{ $pembayaran->pemesanan->user->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Tanggal Tayang</p>
                            <p class="font-semibold">{{ \Carbon\Carbon::parse($pembayaran->pemesanan->jadwal->tanggal_tayang)->format('d M Y') }}</p>
                            <p class="text-gray-400 text-xs">{{ \Carbon\Carbon::parse($pembayaran->pemesanan->jadwal->jam_tayang)->format('H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Kursi</p>
                            <p class="font-semibold">{{ $pembayaran->pemesanan->kursi->pluck('nomor_kursi')->implode(', ') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Metode Pembayaran</p>
                            <p class="font-semibold uppercase">{{ str_replace('_', ' ', $pembayaran->metode_pembayaran) }}</p>
                        </div>
                    </div>

                    <div class="bg-neutral-800 rounded-lg p-4 flex items-center justify-between">
                        <span class="text-gray-400">Total Pembayaran</span>
                        <span class="text-2xl font-bold text-green-400">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</span>
                    </div>

                    {{-- Catatan --}}
                    @if($pembayaran->detail_pembayaran && isset(json_decode($pembayaran->detail_pembayaran, true)['catatan']))
                    <div class="bg-blue-900/20 border border-blue-700/30 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-1">Catatan dari pelanggan:</p>
                        <p class="text-sm">{{ json_decode($pembayaran->detail_pembayaran, true)['catatan'] }}</p>
                    </div>
                    @endif

                    <p class="text-xs text-gray-500">
                        Diupload: {{ $pembayaran->updated_at->diffForHumans() }}
                    </p>
                </div>

                {{-- Bukti Transfer & Action --}}
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-400 mb-2">Bukti Transfer:</p>
                        <div class="relative group cursor-pointer" @click="showImage = true">
                            <img src="{{ Storage::url($pembayaran->bukti_transfer) }}" 
                                 alt="Bukti Transfer" 
                                 class="w-full h-48 object-cover rounded-lg border border-neutral-700">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="space-y-2">
                        <form action="{{ route('kasir.pembayaran.konfirmasi', $pembayaran->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Konfirmasi pembayaran ini?')"
                                    class="w-full py-3 bg-green-600 hover:bg-green-700 rounded-lg font-bold transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Konfirmasi Pembayaran
                            </button>
                        </form>

                        <button @click="showReject = !showReject" 
                                class="w-full py-3 bg-red-600/20 hover:bg-red-600/30 text-red-400 rounded-lg font-semibold transition">
                            Tolak Pembayaran
                        </button>
                    </div>

                    {{-- Form Tolak --}}
                    <div x-show="showReject" x-transition class="bg-neutral-800 rounded-lg p-4">
                        <form action="{{ route('kasir.pembayaran.tolak', $pembayaran->id) }}" method="POST">
                            @csrf
                            <label class="block text-sm mb-2">Alasan Penolakan:</label>
                            <textarea name="alasan" required rows="3" 
                                      class="w-full bg-neutral-900 border border-neutral-700 rounded-lg p-3 text-sm mb-3 focus:border-red-500 outline-none"
                                      placeholder="Contoh: Nominal tidak sesuai, bukti transfer tidak jelas, dll"></textarea>
                            <button type="submit" 
                                    onclick="return confirm('Yakin tolak pembayaran ini?')"
                                    class="w-full py-2 bg-red-600 hover:bg-red-700 rounded-lg text-sm font-semibold transition">
                                Konfirmasi Penolakan
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            {{-- Modal Image Full --}}
            <div x-show="showImage" 
                 @click.away="showImage = false"
                 class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4" 
                 x-transition>
                <div class="relative max-w-4xl w-full">
                    <button @click="showImage = false" class="absolute -top-10 right-0 text-white hover:text-gray-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <img src="{{ Storage::url($pembayaran->bukti_transfer) }}" 
                         alt="Bukti Transfer Full" 
                         class="w-full h-auto rounded-lg">
                </div>
            </div>

        </div>
        @empty
        <div class="bg-neutral-900 rounded-2xl border border-neutral-800 p-12 text-center">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-xl font-bold mb-2 text-gray-400">Tidak Ada Pembayaran Pending</h3>
            <p class="text-gray-500">Semua pembayaran sudah diverifikasi</p>
        </div>
        @endforelse

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $pembayarans->links() }}
        </div>

    </div>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
@endsection