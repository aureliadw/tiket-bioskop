<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Tiket - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center p-4">
    
    <div class="max-w-md w-full">
        @if($valid)
        <!-- ✅ TIKET VALID -->
        <div class="bg-gradient-to-br from-green-900 to-green-950 border-2 border-green-500 rounded-2xl p-8 shadow-2xl">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                    <svg class="w-12 h-12" fill="white" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-green-400 mb-2">✓ Tiket Valid!</h2>
                <p class="text-green-200 text-sm">Tiket dapat digunakan</p>
            </div>

            <div class="space-y-3 mb-6">
                <div class="bg-gray-800/50 rounded-xl p-4 border border-green-500/30">
                    <p class="text-xs text-gray-400 mb-1">Booking Code</p>
                    <p class="text-2xl font-mono font-bold text-green-400">{{ $pemesanan->kode_pemesanan }}</p>
                </div>
                
                <div class="bg-gray-800/50 rounded-xl p-4">
                    <p class="text-xs text-gray-400 mb-1">🎬 Film</p>
                    <p class="text-xl font-bold">{{ $pemesanan->jadwal->film->judul }}</p>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-800/50 rounded-xl p-4">
                        <p class="text-xs text-gray-400 mb-1">🏢 Studio</p>
                        <p class="font-bold">{{ $pemesanan->jadwal->studio->nama_studio }}</p>
                    </div>
                    <div class="bg-gray-800/50 rounded-xl p-4">
                        <p class="text-xs text-gray-400 mb-1">💺 Kursi</p>
                        <p class="font-bold">{{ $pemesanan->kursi->pluck('nomor_kursi')->join(', ') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-800/50 rounded-xl p-4">
                        <p class="text-xs text-gray-400 mb-1">📅 Tanggal</p>
                        <p class="font-bold">{{ date('d M Y', strtotime($pemesanan->jadwal->tanggal)) }}</p>
                    </div>
                    <div class="bg-gray-800/50 rounded-xl p-4">
                        <p class="text-xs text-gray-400 mb-1">🕐 Waktu</p>
                        <p class="font-bold">{{ date('H:i', strtotime($pemesanan->jadwal->waktu_mulai)) }}</p>
                    </div>
                </div>

                <div class="bg-gray-800/50 rounded-xl p-4">
                    <p class="text-xs text-gray-400 mb-1">👤 Pelanggan</p>
                    <p class="font-bold">{{ $pemesanan->user->name }}</p>
                </div>
            </div>

            @if($isKasir ?? false)
            <div class="bg-blue-900/30 border border-blue-500 rounded-xl p-4 mb-4 text-center">
                <p class="text-sm text-blue-200">
                    <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Anda login sebagai <strong>Kasir/Petugas</strong>
                </p>
            </div>
            <a href="{{ route('kasir.checkin') }}" 
               class="block w-full py-4 bg-green-600 hover:bg-green-700 rounded-xl font-bold text-center transition">
                🎫 Lanjut ke Portal Check-In
            </a>
            @else
            <div class="bg-blue-900/30 border border-blue-500 rounded-xl p-4 text-center">
                <p class="text-sm text-blue-200">
                    <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    Tunjukkan tiket ini ke <strong>petugas check-in</strong> di pintu masuk
                </p>
            </div>
            @endif

            <a href="{{ route('home') }}" 
               class="block w-full py-3 bg-gray-700 hover:bg-gray-600 rounded-xl font-semibold text-center transition mt-3">
                Kembali ke Beranda
            </a>
        </div>
        
        @else
        <!-- ❌ TIKET TIDAK VALID -->
        <div class="bg-gradient-to-br from-red-900 to-red-950 border-2 border-red-500 rounded-2xl p-8 shadow-2xl">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12" fill="white" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-red-400 mb-3">✗ Tiket Tidak Valid</h2>
                <div class="bg-red-800/30 rounded-xl p-4">
                    <p class="text-red-200 text-lg">{{ $message }}</p>
                </div>
            </div>

            @if(isset($pemesanan))
            <div class="bg-red-800/20 border border-red-600/50 rounded-xl p-4 space-y-2 mb-6">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Booking Code:</span>
                    <span class="font-mono font-bold">{{ $pemesanan->kode_pemesanan }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Film:</span>
                    <span class="font-semibold">{{ $pemesanan->jadwal->film->judul }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Studio:</span>
                    <span class="font-semibold">{{ $pemesanan->jadwal->studio->nama_studio }}</span>
                </div>
            </div>
            @endif

            <a href="{{ route('home') }}" 
               class="block w-full py-4 bg-gray-700 hover:bg-gray-600 rounded-xl font-bold text-center transition">
                Kembali ke Beranda
            </a>
        </div>
        @endif
    </div>

</body>
</html>