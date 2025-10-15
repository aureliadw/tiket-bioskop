@extends('layouts.app')

@section('title', 'Tiket Saya')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 py-12 px-4">
    <div class="max-w-md mx-auto">
        
        @if($pemesanan->status_pembayaran !== 'sudah_bayar')
        <!-- Peringatan Belum Bayar -->
        <div class="bg-yellow-900/30 border-2 border-yellow-500 rounded-2xl p-6 mb-6">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-yellow-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-bold text-yellow-200 mb-1">⚠️ Pembayaran Pending</p>
                    <p class="text-sm text-yellow-100">Tiket belum aktif. Selesaikan pembayaran dan upload bukti transfer untuk aktivasi tiket.</p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Tiket Card -->
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-3xl shadow-2xl overflow-hidden border-2 {{ $pemesanan->status_pembayaran === 'sudah_bayar' ? 'border-green-500/30' : 'border-yellow-500/30' }}">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-center">
                <h1 class="text-2xl font-bold text-white mb-2">🎬 Tiket Digital</h1>
                <p class="text-blue-100 text-sm">
                    @if($pemesanan->status_pembayaran === 'sudah_bayar')
                        Tunjukkan tiket ini ke petugas
                    @else
                        Menunggu Pembayaran
                    @endif
                </p>
            </div>

            @if($pemesanan->status_pembayaran === 'sudah_bayar')
            <!-- QR Code (Hanya tampil jika sudah bayar) -->
            <div class="flex justify-center py-8 bg-white">
                <div id="qrcode" class="p-4 bg-white rounded-xl shadow-lg"></div>
            </div>

            <!-- Booking Code -->
            <div class="text-center px-6 pb-6 bg-white">
                <p class="text-xs text-gray-500 mb-2">Booking Code</p>
                <p class="text-2xl font-bold font-mono tracking-wider text-gray-900">{{ $bookingCode }}</p>
            </div>
            @else
            <!-- Placeholder jika belum bayar -->
            <div class="flex justify-center py-8 bg-white">
                <div class="text-center p-8">
                    <div class="w-32 h-32 bg-gray-200 rounded-xl mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500">QR Code akan muncul setelah pembayaran dikonfirmasi</p>
                </div>
            </div>
            @endif

            <!-- Ticket Info -->
            <div class="p-6 space-y-4">
                <!-- Film -->
                <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700">
                    <p class="text-xs text-gray-400 mb-1">🎬 Film</p>
                    <p class="text-xl font-bold text-white">{{ $pemesanan->jadwal->film->judul }}</p>
                </div>

                <!-- Studio & Kursi -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700">
                        <p class="text-xs text-gray-400 mb-1">🏢 Studio</p>
                        <p class="text-lg font-bold text-white">{{ $pemesanan->jadwal->studio->nama_studio }}</p>
                    </div>
                    <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700">
    <p class="text-xs text-gray-400 mb-1">💺 Kursi</p>
    <p class="text-lg font-bold text-white">
        @if($pemesanan->kursi && $pemesanan->kursi->count() > 0)
            {{ $pemesanan->kursi->pluck('nomor_kursi')->join(', ') }}
        @else
            Belum ada kursi
        @endif
    </p>
</div>

                </div>

                <!-- Tanggal & Jam -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700">
                        <p class="text-xs text-gray-400 mb-1">📅 Tanggal</p>
                        <p class="font-bold text-white">{{ \Carbon\Carbon::parse($pemesanan->jadwal->tanggal_tayang)->format('d M Y') }}</p>
                    </div>
                    <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700">
                        <p class="text-xs text-gray-400 mb-1">🕐 Jam</p>
                        <p class="font-bold text-white">{{ \Carbon\Carbon::parse($pemesanan->jadwal->jam_tayang)->format('H:i') }}</p>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Status Tiket</p>
                            @if($pemesanan->status_pemesanan === 'digunakan')
                                <p class="font-bold text-green-400">✓ Sudah Check-In</p>
                                @if($pemesanan->used_at)
                                    <p class="text-xs text-gray-400">{{ $pemesanan->used_at->format('d M Y, H:i') }}</p>
                                @endif
                            @elseif($pemesanan->status_pembayaran === 'sudah_bayar')
                                <p class="font-bold text-blue-400">✓ Aktif - Belum Check-In</p>
                            @else
                                <p class="font-bold text-yellow-400">⏳ Menunggu Pembayaran</p>
                            @endif
                        </div>
                        @if($pemesanan->status_pemesanan === 'digunakan')
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        @elseif($pemesanan->status_pembayaran === 'sudah_bayar')
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Total -->
                <div class="bg-gradient-to-r from-blue-900 to-purple-900 rounded-xl p-4 border border-blue-500">
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-blue-200">Total Pembayaran</p>
                        <p class="text-2xl font-bold text-white">Rp {{ number_format($pemesanan->total_bayar, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Info Penting -->
            @if($pemesanan->status_pembayaran === 'sudah_bayar')
            <div class="bg-blue-900/30 border-t-2 border-blue-500 p-4 m-6 rounded-xl">
                <p class="text-xs text-blue-200 font-semibold mb-2">ℹ️ INFORMASI PENTING:</p>
                <ul class="text-xs text-blue-100 space-y-1 list-disc list-inside">
                    <li>Datang 15 menit sebelum film dimulai</li>
                    <li>Tunjukkan QR Code ke petugas check-in</li>
                    <li>Tiket tidak dapat dikembalikan</li>
                    <li>Screenshot QR Code juga berlaku</li>
                </ul>
            </div>
            @endif

            <!-- Actions -->
            <div class="p-6 space-y-3">
                @if($pemesanan->status_pembayaran === 'sudah_bayar')
                <button onclick="shareTicket()" 
                        class="w-full py-3 bg-blue-600 hover:bg-blue-700 rounded-xl font-semibold transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    Bagikan Tiket
                </button>
                @endif
                
                <a href="{{ route('home') }}" 
                   class="block w-full py-3 bg-gray-700 hover:bg-gray-600 rounded-xl font-semibold text-center transition">
                    Kembali ke Beranda
                </a>
            </div>

            <!-- Footer -->
            <div class="bg-gray-900 p-4 text-center border-t border-gray-700">
                <p class="text-xs text-gray-500">
                    Dibuat: {{ $pemesanan->created_at->format('d M Y, H:i') }}
                </p>
                @if($pemesanan->status_pembayaran === 'sudah_bayar' && isset($bookingCode))
                <p class="text-xs text-gray-600 mt-1">
                    Booking: {{ $bookingCode }}
                </p>
                @endif
            </div>

        </div>

    </div>
</div>

@if($pemesanan->status_pembayaran === 'sudah_bayar' && isset($verifyUrl))
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // Generate QR Code dengan URL verifikasi
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ $verifyUrl }}",
        width: 176,
        height: 176,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    
    // Share function
    function shareTicket() {
        const shareData = {
            title: 'Tiket Film - {{ $pemesanan->jadwal->film->judul }}',
            text: 'Booking Code: {{ $bookingCode }}\nFilm: {{ $pemesanan->jadwal->film->judul }}\nStudio: {{ $pemesanan->jadwal->studio->nama_studio }}\nTanggal: {{ \Carbon\Carbon::parse($pemesanan->jadwal->tanggal_tayang)->format("d M Y") }} {{ \Carbon\Carbon::parse($pemesanan->jadwal->jam_tayang)->format("H:i") }}',
            url: window.location.href
        };
        
        if (navigator.share) {
            navigator.share(shareData)
                .then(() => console.log('Berhasil dibagikan'))
                .catch((error) => console.log('Error sharing', error));
        } else {
            const text = shareData.text + '\n\n' + shareData.url;
            navigator.clipboard.writeText(text).then(() => {
                alert('Link tiket berhasil disalin!');
            });
        }
    }
</script>
@endif
@endsection