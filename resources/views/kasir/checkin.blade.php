<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Check-In Portal - Kasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-3xl">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-6 mb-6 shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">🎬 Check-In Portal</h1>
                    <p class="text-blue-100">Validasi tiket pelanggan untuk masuk studio</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-blue-200">Petugas</p>
                    <p class="font-bold text-lg">{{ auth()->user()->name }}</p>
                    <a href="/logout" class="text-xs text-blue-300 hover:underline">Logout</a>
                </div>
            </div>
        </div>

        <!-- Method Switch -->
        <div class="bg-gray-800 rounded-2xl p-6 mb-6 shadow-xl">
            <div class="flex gap-3 mb-6">
                <button onclick="showManual()" id="manualBtn" 
                        class="flex-1 py-3 bg-blue-600 rounded-xl font-semibold text-lg transition hover:bg-blue-700 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Input Manual
                </button>
                <button onclick="showScanner()" id="scanBtn"
                        class="flex-1 py-3 bg-gray-700 rounded-xl font-semibold text-lg transition hover:bg-gray-600 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    Scan QR
                </button>
            </div>

            <!-- Manual Input Section -->
            <div id="manualSection">
                <div class="bg-blue-900/20 border border-blue-500 rounded-xl p-4 mb-4">
                    <p class="text-sm text-blue-200 text-center">
                        💡 Minta pelanggan tunjukkan <strong>Booking Code</strong> dari tiket digital mereka
                    </p>
                </div>

                <form onsubmit="checkByCode(event)" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-2 font-semibold">Booking Code</label>
                        <input type="text" 
                               id="bookingCodeInput"
                               placeholder="Contoh: HPC-20250114-0001"
                               class="w-full px-4 py-4 bg-gray-900 border-2 border-gray-700 rounded-xl focus:border-blue-500 focus:outline-none font-mono text-lg text-center tracking-wider"
                               required
                               autocomplete="off">
                        <p class="text-xs text-gray-500 mt-2 text-center">Format: HPC-YYYYMMDD-XXXX</p>
                    </div>
                    <button type="submit" 
                            class="w-full py-4 bg-blue-600 hover:bg-blue-700 rounded-xl font-bold text-lg transition flex items-center justify-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Cek Tiket
                    </button>
                </form>
            </div>

            <!-- Scanner Section -->
            <div id="scannerSection" class="hidden">
                <div class="bg-yellow-900/20 border border-yellow-500 rounded-xl p-4 mb-4">
                    <p class="text-sm text-yellow-200 text-center">
                        📱 Minta pelanggan tunjukkan <strong>QR Code</strong> dari tiket digital mereka
                    </p>
                </div>

                <div id="reader" class="w-full mb-4 rounded-xl overflow-hidden bg-black"></div>
                
                <div class="flex gap-3">
                    <button onclick="startScanner()" id="startScanBtn"
                            class="flex-1 py-4 bg-green-600 hover:bg-green-700 rounded-xl font-bold text-lg transition flex items-center justify-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Mulai Scan
                    </button>
                    <button onclick="stopScanner()" id="stopScanBtn"
                            class="flex-1 py-4 bg-red-600 hover:bg-red-700 rounded-xl font-bold text-lg transition hidden flex items-center justify-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                        </svg>
                        Stop
                    </button>
                </div>

                <p class="text-xs text-gray-500 text-center mt-3">
                    💡 Tips: Pastikan cahaya cukup terang dan QR Code jelas terlihat
                </p>
            </div>
        </div>

        <!-- Result Container -->
        <div id="resultContainer" class="hidden">
            <!-- Success Result -->
            <div id="successResult" class="bg-gradient-to-br from-green-900 to-green-950 border-2 border-green-500 rounded-2xl p-8 shadow-2xl hidden">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                        <svg class="w-12 h-12" fill="white" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-green-400 mb-2">✓ Tiket Valid!</h2>
                    <p class="text-green-200">Tiket siap digunakan</p>
                </div>

                <div id="ticketInfo" class="space-y-3 mb-6">
                    <!-- Akan di-generate oleh JavaScript -->
                </div>

                <button onclick="confirmCheckIn()" 
                        class="w-full py-5 bg-green-600 hover:bg-green-700 rounded-xl font-bold text-xl transition flex items-center justify-center gap-3 shadow-lg">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Konfirmasi Check-In & Izinkan Masuk
                </button>

                <button onclick="resetForm()" 
                        class="w-full mt-3 py-3 bg-gray-700 hover:bg-gray-600 rounded-xl font-semibold transition">
                    Batal / Cek Tiket Lain
                </button>
            </div>

            <!-- Error Result -->
            <div id="errorResult" class="bg-gradient-to-br from-red-900 to-red-950 border-2 border-red-500 rounded-2xl p-8 shadow-2xl hidden">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12" fill="white" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-red-400 mb-3">✗ Tiket Tidak Valid</h2>
                    <div id="errorMessage" class="bg-red-800/30 rounded-xl p-4">
                        <p class="text-red-200 text-lg"></p>
                    </div>
                </div>

                <div id="errorTicketInfo" class="space-y-3 mb-6 hidden">
                    <!-- Info tiket yang error -->
                </div>

                <button onclick="resetForm()" 
                        class="w-full py-4 bg-gray-700 hover:bg-gray-600 rounded-xl font-bold text-lg transition">
                    Cek Tiket Lain
                </button>
            </div>
        </div>

    </div>

    <script>
        let html5QrCode;
        let currentPemesanan = null;

        // === TAB SWITCHING ===
        function showManual() {
            document.getElementById('manualSection').classList.remove('hidden');
            document.getElementById('scannerSection').classList.add('hidden');
            document.getElementById('manualBtn').classList.remove('bg-gray-700');
            document.getElementById('manualBtn').classList.add('bg-blue-600');
            document.getElementById('scanBtn').classList.remove('bg-blue-600');
            document.getElementById('scanBtn').classList.add('bg-gray-700');
            
            // Stop scanner jika nyala
            if (html5QrCode) {
                stopScanner();
            }
        }

        function showScanner() {
            document.getElementById('manualSection').classList.add('hidden');
            document.getElementById('scannerSection').classList.remove('hidden');
            document.getElementById('scanBtn').classList.remove('bg-gray-700');
            document.getElementById('scanBtn').classList.add('bg-blue-600');
            document.getElementById('manualBtn').classList.remove('bg-blue-600');
            document.getElementById('manualBtn').classList.add('bg-gray-700');
        }

        // === MANUAL INPUT ===
        function checkByCode(event) {
            event.preventDefault();
            
            const bookingCode = document.getElementById('bookingCodeInput').value.trim().toUpperCase();
            
            fetch('{{ route("kasir.checkin.code") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ booking_code: bookingCode })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data.pemesanan);
                } else {
                    showError(data.message, data.pemesanan);
                }
            })
            .catch(err => {
                showError('Gagal terhubung ke server. Coba lagi.');
            });
        }

        // === QR SCANNER ===
        function startScanner() {
            html5QrCode = new Html5Qrcode("reader");
            
            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess,
                onScanFailure
            ).then(() => {
                document.getElementById('startScanBtn').classList.add('hidden');
                document.getElementById('stopScanBtn').classList.remove('hidden');
            }).catch(err => {
                alert('Gagal mengakses kamera: ' + err);
            });
        }

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    document.getElementById('startScanBtn').classList.remove('hidden');
                    document.getElementById('stopScanBtn').classList.add('hidden');
                }).catch(err => {
                    console.error('Error stopping scanner:', err);
                });
            }
        }

        function onScanSuccess(decodedText) {
            stopScanner();
            
            fetch('{{ route("kasir.checkin.scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ qr_data: decodedText })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data.pemesanan);
                } else {
                    showError(data.message, data.pemesanan);
                }
            })
            .catch(err => {
                showError('Gagal memvalidasi QR Code. Coba lagi.');
            });
        }

        function onScanFailure(error) {
            // Scan gagal, ignore (normal behavior)
        }

        // === SHOW RESULTS ===
        function showSuccess(pemesanan) {
            currentPemesanan = pemesanan;
            
            document.getElementById('resultContainer').classList.remove('hidden');
            document.getElementById('successResult').classList.remove('hidden');
            document.getElementById('errorResult').classList.add('hidden');

            // Format kursi
            const kursiList = pemesanan.kursi.map(k => k.nomor_kursi).join(', ');
            
            // Render ticket info
            const info = `
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2 bg-gray-800/50 rounded-xl p-4 border border-green-500/30">
                        <p class="text-xs text-gray-400 mb-1">Booking Code</p>
                        <p class="text-2xl font-mono font-bold text-green-400">${pemesanan.kode_pemesanan}</p>
                    </div>
                    
                    <div class="col-span-2 bg-gray-800/50 rounded-xl p-4">
                        <p class="text-xs text-gray-400 mb-1">🎬 Film</p>
                        <p class="text-xl font-bold">${pemesanan.jadwal.film.judul}</p>
                    </div>
                    
                    <div class="bg-gray-800/50 rounded-xl p-4">
                        <p class="text-xs text-gray-400 mb-1">🏢 Studio</p>
                        <p class="text-lg font-bold">${pemesanan.jadwal.studio.nama_studio}</p>
                    </div>
                    
                    <div class="bg-gray-800/50 rounded-xl p-4">
                        <p class="text-xs text-gray-400 mb-1">💺 Kursi</p>
                        <p class="text-lg font-bold">${kursiList}</p>
                    </div>
                    
                    <div class="bg-gray-800/50 rounded-xl p-4">
                        <p class="text-xs text-gray-400 mb-1">👤 Pelanggan</p>
                        <p class="text-lg font-bold">${pemesanan.user.nama_lengkap}</p>
                    </div>
                    
                    <div class="bg-gray-800/50 rounded-xl p-4">
                        <p class="text-xs text-gray-400 mb-1">💰 Total Bayar</p>
                        <p class="text-lg font-bold">Rp ${parseInt(pemesanan.total_bayar).toLocaleString('id-ID')}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('ticketInfo').innerHTML = info;
        }

        function showError(message, pemesanan = null) {
            document.getElementById('resultContainer').classList.remove('hidden');
            document.getElementById('successResult').classList.add('hidden');
            document.getElementById('errorResult').classList.remove('hidden');
            
            document.getElementById('errorMessage').querySelector('p').textContent = message;

            // Jika ada data pemesanan, tampilkan info (untuk kasus sudah digunakan)
            if (pemesanan) {
                const kursiList = pemesanan.kursi ? pemesanan.kursi.map(k => k.nomor_kursi).join(', ') : '-';
                
                const errorInfo = `
                    <div class="bg-red-800/20 border border-red-600/50 rounded-xl p-4 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-400 text-sm">Booking Code:</span>
                            <span class="font-mono font-bold">${pemesanan.kode_pemesanan}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 text-sm">Film:</span>
                            <span class="font-semibold">${pemesanan.jadwal.film.judul}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 text-sm">Studio:</span>
                            <span class="font-semibold">${pemesanan.jadwal.studio.nama_studio}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 text-sm">Kursi:</span>
                            <span class="font-semibold">${kursiList}</span>
                        </div>
                    </div>
                `;
                
                document.getElementById('errorTicketInfo').innerHTML = errorInfo;
                document.getElementById('errorTicketInfo').classList.remove('hidden');
            } else {
                document.getElementById('errorTicketInfo').classList.add('hidden');
            }
        }

        // === CHECK-IN CONFIRMATION ===
        function confirmCheckIn() {
    if (!currentPemesanan) return;

    if (!confirm('Yakin ingin check-in tiket ini? Tiket akan dicetak.')) {
        return;
    }

    fetch(`{{ url('/kasir/check-in/use') }}/${currentPemesanan.id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // ✅ Buka halaman print
            window.open(data.print_url, '_blank');
            
            alert('✓ ' + data.message);
            resetForm();
        } else {
            alert('✗ ' + data.message);
        }
    })
    .catch(err => {
        alert('Gagal melakukan check-in. Coba lagi.');
    });
}

        // === RESET FORM ===
        function resetForm() {
            document.getElementById('resultContainer').classList.add('hidden');
            document.getElementById('bookingCodeInput').value = '';
            currentPemesanan = null;
            
            // Focus kembali ke input
            document.getElementById('bookingCodeInput').focus();
        }

        // Auto-focus input saat load
        window.onload = function() {
            document.getElementById('bookingCodeInput').focus();
        };
    </script>
</body>
</html>