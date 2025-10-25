@extends('layouts.kasir')

@section('title', 'Check-In Portal')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-600 to-red-800 rounded-2xl p-6 mb-6 shadow-2xl shadow-red-900/50 border border-red-900/30">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">🎬 Check-In Portal</h1>
                <p class="text-red-100">Validasi tiket pelanggan untuk masuk studio</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-red-200">Kasir</p>
                <p class="font-bold text-lg">{{ auth()->user()->name }}</p>
            </div>
        </div>
    </div>

    <!-- Manual Input Section -->
    <div class="bg-gradient-to-br from-gray-900 to-black border border-red-900/30 rounded-2xl p-6 mb-6 shadow-2xl">
        <div class="bg-red-950/30 border border-red-800/50 rounded-xl p-4 mb-6">
            <p class="text-sm text-red-200 text-center">
                💡 Minta pelanggan tunjukkan <strong>Booking Code</strong> dari tiket digital mereka
            </p>
        </div>

        <form onsubmit="checkByCode(event)" class="space-y-4">
            <div>
                <label class="block text-sm text-gray-400 mb-2 font-semibold">Booking Code</label>
                <input type="text" 
                       id="bookingCodeInput"
                       placeholder="Contoh: HPC-20250114-0001"
                       class="w-full px-4 py-4 bg-black border-2 border-red-900/50 rounded-xl focus:border-red-500 focus:outline-none font-mono text-lg text-center tracking-wider text-white"
                       required
                       autocomplete="off">
                <p class="text-xs text-gray-500 mt-2 text-center">Format: HPC-YYYYMMDD-XXXX</p>
            </div>
            <button type="submit" 
                    class="w-full py-4 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-xl font-bold text-lg transition flex items-center justify-center gap-2 shadow-lg shadow-red-900/50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Cek Tiket
            </button>
        </form>
    </div>

    <!-- Result Container -->
    <div id="resultContainer" class="hidden">
        <!-- Success Result -->
        <div id="successResult" class="bg-gradient-to-br from-gray-900 to-black border-2 border-green-500 rounded-2xl p-8 shadow-2xl hidden">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse shadow-lg shadow-green-900/50">
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
                    class="w-full py-5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 rounded-xl font-bold text-xl transition flex items-center justify-center gap-3 shadow-lg shadow-green-900/50">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Konfirmasi Check-In & Izinkan Masuk
            </button>

            <button onclick="resetForm()" 
                    class="w-full mt-3 py-3 bg-gray-800 hover:bg-gray-700 rounded-xl font-semibold transition border border-red-900/30">
                Batal / Cek Tiket Lain
            </button>
        </div>

        <!-- Error Result -->
        <div id="errorResult" class="bg-gradient-to-br from-gray-900 to-black border-2 border-red-500 rounded-2xl p-8 shadow-2xl shadow-red-900/50 hidden">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg shadow-red-900/50">
                    <svg class="w-12 h-12" fill="white" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-red-400 mb-3">✗ Tiket Tidak Valid</h2>
                <div id="errorMessage" class="bg-red-950/50 border border-red-800/50 rounded-xl p-4">
                    <p class="text-red-200 text-lg"></p>
                </div>
            </div>

            <div id="errorTicketInfo" class="space-y-3 mb-6 hidden">
                <!-- Info tiket yang error -->
            </div>

            <button onclick="resetForm()" 
                    class="w-full py-4 bg-gray-800 hover:bg-gray-700 rounded-xl font-bold text-lg transition border border-red-900/30">
                Cek Tiket Lain
            </button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let currentPemesanan = null;

    // === MANUAL INPUT ===
    function checkByCode(event) {
        event.preventDefault();
        
        const bookingCode = document.getElementById('bookingCodeInput').value.trim().toUpperCase();
        
        fetch('{{ route("kasir.check-by-code") }}', {
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
                <div class="col-span-2 bg-gray-800/50 border border-green-600/30 rounded-xl p-4">
                    <p class="text-xs text-gray-400 mb-1">Booking Code</p>
                    <p class="text-2xl font-mono font-bold text-green-400">${pemesanan.kode_pemesanan}</p>
                </div>
                
                <div class="col-span-2 bg-gray-800/50 border border-red-900/30 rounded-xl p-4">
                    <p class="text-xs text-gray-400 mb-1">🎬 Film</p>
                    <p class="text-xl font-bold">${pemesanan.jadwal.film.judul}</p>
                </div>
                
                <div class="bg-gray-800/50 border border-red-900/30 rounded-xl p-4">
                    <p class="text-xs text-gray-400 mb-1">🏢 Studio</p>
                    <p class="text-lg font-bold">${pemesanan.jadwal.studio.nama_studio}</p>
                </div>
                
                <div class="bg-gray-800/50 border border-red-900/30 rounded-xl p-4">
                    <p class="text-xs text-gray-400 mb-1">💺 Kursi</p>
                    <p class="text-lg font-bold">${kursiList}</p>
                </div>
                
                <div class="bg-gray-800/50 border border-red-900/30 rounded-xl p-4">
                    <p class="text-xs text-gray-400 mb-1">👤 Pelanggan</p>
                    <p class="text-lg font-bold">${pemesanan.user.nama_lengkap}</p>
                </div>
                
                <div class="bg-gray-800/50 border border-red-900/30 rounded-xl p-4">
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
                <div class="bg-red-950/30 border border-red-800/50 rounded-xl p-4 space-y-2">
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

    // ✅ PERBAIKAN: Pakai route yang benar
    fetch(`/kasir/use-tiket/${currentPemesanan.id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.open(data.print_url, '_blank');
            alert('✓ ' + data.message);
            resetForm();
        } else {
            alert('✗ ' + data.message);
        }
    })
    .catch(err => {
        console.error('Error:', err); // ✅ Debug
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
@endpush