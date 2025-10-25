@extends('layouts.kasir')

@section('title', 'Jual Tiket Offline')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-600 to-red-800 rounded-2xl p-6 shadow-2xl shadow-red-900/50 border border-red-900/30">
        <h1 class="text-3xl font-bold mb-2">🎫 Jual Tiket Offline</h1>
        <p class="text-red-100">Untuk pelanggan walk-in yang datang langsung ke bioskop</p>
    </div>

    <!-- Step Wizard -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-2 flex-1">
            <div id="step1Indicator" class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center font-bold text-lg shadow-lg shadow-red-900/50">1</div>
            <div class="flex-1 h-1 bg-red-600" id="line1"></div>
        </div>
        <div class="flex items-center gap-2 flex-1">
            <div id="step2Indicator" class="w-10 h-10 bg-gray-800 border border-gray-700 rounded-full flex items-center justify-center font-bold text-lg">2</div>
            <div class="flex-1 h-1 bg-gray-800" id="line2"></div>
        </div>
        <div class="flex items-center">
            <div id="step3Indicator" class="w-10 h-10 bg-gray-800 border border-gray-700 rounded-full flex items-center justify-center font-bold text-lg">3</div>
        </div>
    </div>

    <!-- STEP 1: Pilih Jadwal -->
    <div id="step1" class="bg-gradient-to-br from-gray-900 to-black border border-red-900/30 rounded-2xl p-6 shadow-2xl">
        <h2 class="text-2xl font-bold mb-6 text-white">1️⃣ Pilih Film & Jadwal</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($jadwals as $jadwal)
                @php
                    $tanggal = $jadwal->tanggal_tayang instanceof \Carbon\Carbon 
                        ? $jadwal->tanggal_tayang->format('Y-m-d') 
                        : $jadwal->tanggal_tayang;

                    $jam = $jadwal->jam_tayang instanceof \Carbon\Carbon 
                        ? $jadwal->jam_tayang->format('H:i:s') 
                        : $jadwal->jam_tayang;

                    $jadwalDateTime = \Carbon\Carbon::parse($tanggal . ' ' . $jam);
                    $isPast = $jadwalDateTime->isPast();
                    $minutesUntil = now()->diffInMinutes($jadwalDateTime, false);
                @endphp

                <!-- 🎬 Card Jadwal -->
                <div class="relative bg-gradient-to-br from-gray-800 to-black rounded-xl p-5 border-2 transition-all duration-300 flex flex-col justify-between h-full
                            {{ $isPast ? 'border-red-900/50 opacity-60' : 'border-red-900/30 hover:border-red-600 hover:shadow-lg hover:shadow-red-900/50' }}">
                    
                    @if($isPast)
                        <div class="absolute inset-0 bg-black/50 rounded-xl backdrop-blur-[2px] z-20 flex items-center justify-center pointer-events-none">
                            <div class="bg-red-900/95 px-6 py-3 rounded-lg border-2 border-red-500 shadow-xl">
                                <span class="text-white font-bold text-base">🚫 Jadwal Sudah Lewat</span>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
                        @if($jadwal->is_weekend)
                            <span class="bg-gradient-to-r from-red-600 to-red-700 text-white text-xs font-bold px-3 py-1 rounded-full border border-red-500 shadow-lg shadow-red-900/50">
                                🎉 WEEKEND +Rp10K
                            </span>
                        @endif

                        @if($isPast)
                            <span class="bg-red-950 text-red-300 text-xs font-bold px-3 py-1 rounded-full border border-red-800">
                                ⏱ LEWAT
                            </span>
                        @elseif($minutesUntil <= 30 && $minutesUntil > 0)
                            <span class="bg-gradient-to-r from-yellow-600 to-yellow-700 text-white text-xs font-bold px-3 py-1 rounded-full border border-yellow-500 animate-pulse shadow-lg shadow-yellow-900/50">
                                🔔 {{ $minutesUntil }} MENIT LAGI
                            </span>
                        @else
                            <span class="bg-gray-800 text-gray-300 text-xs font-bold px-3 py-1 rounded-full border border-gray-700">
                                ✅ AKTIF
                            </span>
                        @endif
                    </div>

                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-red-600 to-red-800 rounded-lg flex items-center justify-center flex-shrink-0 shadow-lg shadow-red-900/50">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-lg text-white truncate" title="{{ $jadwal->film->judul }}">
                                {{ $jadwal->film->judul }}
                            </h3>
                            <p class="text-sm text-gray-400">{{ $jadwal->studio->nama_studio }}</p>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-300">
                            <svg class="w-4 h-4 mr-2 {{ $isPast ? 'text-red-400' : 'text-red-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="{{ $isPast ? 'text-red-400 line-through' : 'text-gray-300' }}">
                                {{ date('H:i', strtotime($jadwal->jam_tayang)) }} WIB
                            </span>
                        </div>

                        <div class="flex items-center text-sm text-gray-300">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8
                                       c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1
                                       m0-1c-1.11 0-2.08-.402-2.599-1
                                       M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Rp {{ number_format($jadwal->harga_final ?? $jadwal->harga_dasar, 0, ',', '.') }}
                        </div>
                    </div>

                    <button type="button"
                        onclick="selectJadwal(
                            '{{ $jadwal->id }}',
                            '{{ $jadwal->film->judul }}',
                            '{{ $jadwal->studio->nama_studio }}',
                            '{{ $tanggal }}',
                            '{{ date('H:i', strtotime($jadwal->jam_tayang)) }}',
                            '{{ $jadwal->harga_final ?? $jadwal->harga_dasar }}'
                        )"
                        @if($isPast) disabled @endif
                        class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800
                               text-white font-semibold py-2 rounded-lg text-sm flex items-center justify-center gap-2
                               transition-all duration-300 shadow-lg shadow-red-900/50 hover:scale-[1.02]
                               disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        Pilih Jadwal
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    <!-- STEP 2: Pilih Kursi -->
    <div id="step2" class="bg-gradient-to-br from-gray-900 to-black border border-red-900/30 rounded-2xl p-6 shadow-2xl hidden">
        <h2 class="text-2xl font-bold mb-4 text-white">2️⃣ Pilih Kursi</h2>
        
        <div class="bg-red-950/30 border border-red-800/50 rounded-xl p-4 mb-6">
            <p class="text-sm text-red-200">
                <strong>Jadwal Dipilih:</strong> <span id="selectedJadwalInfo"></span>
            </p>
        </div>

        <div class="flex gap-4 mb-6 justify-center flex-wrap">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-800 rounded border-2 border-gray-700"></div>
                <span class="text-sm text-gray-300">Tersedia</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-red-600 rounded border-2 border-red-500 shadow-lg shadow-red-900/50"></div>
                <span class="text-sm text-gray-300">Dipilih</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-red-950 rounded border-2 border-red-800 opacity-50"></div>
                <span class="text-sm text-gray-300">Terisi</span>
            </div>
        </div>

        <div class="mb-8">
            <div class="bg-gradient-to-b from-red-900/30 to-gray-900 h-3 rounded-t-3xl mx-auto max-w-2xl border-t-2 border-x-2 border-red-900/50"></div>
            <p class="text-center text-xs text-red-500 font-bold mt-2">🎬 LAYAR</p>
        </div>

        <div id="kursiGrid" class="flex flex-col items-center gap-3"></div>

        <div class="mt-6 flex justify-between items-center flex-wrap gap-4">
            <button type="button" onclick="backToStep1()" class="px-6 py-3 bg-gray-800 hover:bg-gray-700 border border-red-900/30 rounded-xl font-semibold transition">
                ← Kembali
            </button>
            <div class="text-right">
                <p class="text-sm text-gray-400">Kursi Dipilih: <span id="selectedCount" class="font-bold text-red-400">0</span></p>
                <p class="text-sm text-gray-400">Total: <span id="totalPrice" class="font-bold text-red-400">Rp 0</span></p>
            </div>
            <button type="button" onclick="goToStep3()" id="btnToStep3" class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-xl font-semibold transition shadow-lg shadow-red-900/50 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none" disabled>
                Lanjut →
            </button>
        </div>
    </div>

    <!-- STEP 3: Detail Pemesanan & Metode Pembayaran -->
    <div id="step3" class="bg-gradient-to-br from-gray-900 to-black border border-red-900/30 rounded-2xl p-6 shadow-2xl hidden">
        <h2 class="text-2xl font-bold mb-6 text-white">3️⃣ Detail Pemesanan & Pembayaran</h2>
        
        <!-- Detail Pemesanan -->
        <div class="bg-black border-2 border-red-900/50 rounded-xl p-6 mb-6">
            <h3 class="text-lg font-bold text-red-400 mb-4">📋 Detail Pemesanan</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-400">Film & Jadwal</p>
                    <p class="font-semibold text-white" id="summaryJadwal"></p>
                </div>
                <div>
                    <p class="text-gray-400">Studio</p>
                    <p class="font-semibold text-white" id="summaryStudio"></p>
                </div>
                <div>
                    <p class="text-gray-400">Kursi Dipilih</p>
                    <p class="font-semibold text-white" id="summaryKursi"></p>
                </div>
                <div>
                    <p class="text-gray-400">Jumlah Tiket</p>
                    <p class="font-semibold text-white" id="summaryJumlah"></p>
                </div>
            </div>
            
            <div class="border-t border-red-900/50 mt-4 pt-4">
                <div class="flex justify-between items-center">
                    <span class="text-xl font-bold text-white">Total Pembayaran</span>
                    <span class="text-2xl font-bold text-red-400" id="summaryTotal">Rp 0</span>
                </div>
            </div>
        </div>

        <!-- Metode Pembayaran -->
        <div class="bg-black border-2 border-red-900/50 rounded-xl p-6">
            <h3 class="text-lg font-bold text-red-400 mb-4">💳 Pilih Metode Pembayaran</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Cash -->
                <label class="relative cursor-pointer group">
                    <input type="radio" name="metode_bayar" value="tunai" class="peer sr-only" required>
                    <div class="bg-gray-900 border-2 border-red-900/50 rounded-xl p-6 text-center transition-all
                                peer-checked:border-green-500 peer-checked:bg-green-950/30 peer-checked:shadow-lg peer-checked:shadow-green-900/50
                                hover:border-red-500 hover:bg-red-950/20">
                        <div class="text-4xl mb-3">💵</div>
                        <p class="font-bold text-lg text-white mb-1">Cash (Tunai)</p>
                        <p class="text-xs text-gray-400">Bayar langsung di kasir</p>
                        <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                            <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </label>

                <!-- Transfer Bank -->
                <label class="relative cursor-pointer group">
                    <input type="radio" name="metode_bayar" value="transfer" class="peer sr-only" required>
                    <div class="bg-gray-900 border-2 border-red-900/50 rounded-xl p-6 text-center transition-all
                                peer-checked:border-blue-500 peer-checked:bg-blue-950/30 peer-checked:shadow-lg peer-checked:shadow-blue-900/50
                                hover:border-red-500 hover:bg-red-950/20">
                        <div class="text-4xl mb-3">🏦</div>
                        <p class="font-bold text-lg text-white mb-1">Transfer Bank</p>
                        <p class="text-xs text-gray-400">BCA, BNI, BRI, Mandiri</p>
                        <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                            <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </label>

                <!-- QRIS / E-Wallet -->
                <label class="relative cursor-pointer group">
                    <input type="radio" name="metode_bayar" value="qris" class="peer sr-only" required>
                    <div class="bg-gray-900 border-2 border-red-900/50 rounded-xl p-6 text-center transition-all
                                peer-checked:border-purple-500 peer-checked:bg-purple-950/30 peer-checked:shadow-lg peer-checked:shadow-purple-900/50
                                hover:border-red-500 hover:bg-red-950/20">
                        <div class="text-4xl mb-3">📱</div>
                        <p class="font-bold text-lg text-white mb-1">QRIS / E-Wallet</p>
                        <p class="text-xs text-gray-400">GoPay, ShopeePay, OVO</p>
                        <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                            <svg class="w-6 h-6 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </label>
            </div>

            <p class="text-xs text-gray-400 mt-4 text-center">
                💡 Pilih metode pembayaran yang sesuai dengan pelanggan
            </p>
        </div>

        <!-- Buttons -->
        <div class="flex justify-between pt-6 flex-wrap gap-4">
            <button type="button" onclick="backToStep2()" 
                    class="px-6 py-3 bg-gray-800 hover:bg-gray-700 border border-red-900/30 rounded-xl font-semibold transition">
                ← Kembali
            </button>
            
            <button type="button" id="btnProcessPayment" onclick="processPayment()" 
                    class="px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 
                           rounded-xl font-bold text-lg transition shadow-lg shadow-green-900/50 flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Proses Pembayaran
            </button>
        </div>
    </div>

</div>

@push('scripts')

<script src="https://app.sandbox.midtrans.com/snap/snap.js" 
        data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
// ✅ Tunggu Midtrans load
(function() {
    var checkSnapLoaded = setInterval(function() {
        if (typeof snap !== 'undefined') {
            clearInterval(checkSnapLoaded);
            console.log('✅ Midtrans Snap loaded successfully');
        }
    }, 100);
    
    // Timeout after 10 seconds
    setTimeout(function() {
        if (typeof snap === 'undefined') {
            clearInterval(checkSnapLoaded);
            console.error('❌ Midtrans Snap failed to load after 10 seconds');
            alert('Gagal memuat Midtrans. Silakan refresh halaman.');
        }
    }, 10000);
})();

// Global Variables
let selectedJadwalId = null;
let selectedKursiIds = [];
let kursiData = [];
let hargaPerKursi = 0;
let currentOrderId = null;
let snapToken = null;
let paymentCheckInterval = null;
let selectedJadwalInfo = {
    film: '',
    studio: '',
    tanggal: '',
    waktu: ''
};

// STEP 1: Select Schedule
function selectJadwal(id, film, studio, tanggal, waktu, harga) {
    console.log('selectJadwal called:', id, film, studio, tanggal, waktu, harga);
    
    selectedJadwalId = id;
    hargaPerKursi = parseInt(harga);
    selectedJadwalInfo = { film, studio, tanggal, waktu };
    
    const infoText = film + ' - ' + studio + ' | ' + tanggal + ' ' + waktu;
    document.getElementById('selectedJadwalInfo').textContent = infoText;
    
    console.log('Fetching kursi from:', '/kasir/get-kursi/' + id);
    
    // Fetch kursi data
    fetch('/kasir/get-kursi/' + id)
        .then(function(res) {
            console.log('Response status:', res.status);
            if (!res.ok) {
                throw new Error('HTTP error! status: ' + res.status);
            }
            return res.json();
        })
        .then(function(data) {
            console.log('Response data:', data);
            
            if (data.success) {
                kursiData = data.kursis;
                console.log('Total kursi loaded:', kursiData.length);
                renderKursiGrid();
                goToStep(2);
            } else {
                alert('Gagal memuat data kursi: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(function(err) {
            console.error('Error fetching kursi:', err);
            alert('Terjadi kesalahan saat memuat kursi: ' + err.message);
        });
}

// STEP 2: Render Seat Grid
function renderKursiGrid() {
    const grid = document.getElementById('kursiGrid');
    grid.innerHTML = '';
    
    // Group seats by row
    const rows = {};
    kursiData.forEach(kursi => {
        const row = kursi.nomor_kursi.charAt(0);
        if (!rows[row]) rows[row] = [];
        rows[row].push(kursi);
    });
    
    // Render each row
    Object.keys(rows).sort().forEach(row => {
        const rowDiv = document.createElement('div');
        rowDiv.className = 'flex gap-2 items-center justify-center';
        
        // Left label
        const labelLeft = document.createElement('span');
        labelLeft.className = 'w-8 text-center font-bold text-gray-400';
        labelLeft.textContent = row;
        rowDiv.appendChild(labelLeft);
        
        // Sort seats in row
        const kursiRow = rows[row].sort((a, b) => {
            const numA = parseInt(a.nomor_kursi.slice(1));
            const numB = parseInt(b.nomor_kursi.slice(1));
            return numA - numB;
        });
        
        // Split into left and right sections
        const totalKursi = kursiRow.length;
        const leftSeats = kursiRow.slice(0, Math.ceil(totalKursi / 2));
        const rightSeats = kursiRow.slice(Math.ceil(totalKursi / 2));
        
        // Render left seats
        leftSeats.forEach(kursi => rowDiv.appendChild(createSeatButton(kursi)));
        
        // Aisle
        const aisle = document.createElement('div');
        aisle.className = 'w-12';
        rowDiv.appendChild(aisle);
        
        // Render right seats
        rightSeats.forEach(kursi => rowDiv.appendChild(createSeatButton(kursi)));
        
        // Right label
        const labelRight = document.createElement('span');
        labelRight.className = 'w-8 text-center font-bold text-gray-400';
        labelRight.textContent = row;
        rowDiv.appendChild(labelRight);
        
        grid.appendChild(rowDiv);
    });
    
    updateSummary();
}

function createSeatButton(kursi) {
    const seat = document.createElement('button');
    seat.type = 'button';
    seat.className = 'w-10 h-10 rounded font-semibold text-sm transition';
    seat.textContent = kursi.nomor_kursi.slice(1);
    
    if (kursi.status === 'booked') {
        seat.className += ' bg-red-950 border-2 border-red-800 opacity-50 cursor-not-allowed text-gray-600';
        seat.disabled = true;
    } else if (selectedKursiIds.includes(kursi.id)) {
        seat.className += ' bg-red-600 border-2 border-red-500 shadow-lg shadow-red-900/50';
    } else {
        seat.className += ' bg-gray-800 border-2 border-gray-700 hover:border-red-500 hover:bg-gray-700';
    }
    
    seat.onclick = () => toggleKursi(kursi.id);
    return seat;
}

function toggleKursi(kursiId) {
    const index = selectedKursiIds.indexOf(kursiId);
    if (index > -1) {
        selectedKursiIds.splice(index, 1);
    } else {
        selectedKursiIds.push(kursiId);
    }
    renderKursiGrid();
}

function updateSummary() {
    const selected = kursiData.filter(k => selectedKursiIds.includes(k.id));
    const total = selected.length * hargaPerKursi;
    
    document.getElementById('selectedCount').textContent = selected.length;
    document.getElementById('totalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('btnToStep3').disabled = selected.length === 0;
}

// STEP 2: Render Seat Grid
function renderKursiGrid() {
    var grid = document.getElementById('kursiGrid');
    grid.innerHTML = '';
    
    // Group seats by row
    var rows = {};
    kursiData.forEach(function(kursi) {
        var row = kursi.nomor_kursi.charAt(0);
        if (!rows[row]) rows[row] = [];
        rows[row].push(kursi);
    });
    
    // Render each row
    Object.keys(rows).sort().forEach(function(row) {
        var rowDiv = document.createElement('div');
        rowDiv.className = 'flex gap-2 items-center justify-center';
        
        // Left label
        var labelLeft = document.createElement('span');
        labelLeft.className = 'w-8 text-center font-bold text-gray-400';
        labelLeft.textContent = row;
        rowDiv.appendChild(labelLeft);
        
        // Sort seats in row
        var kursiRow = rows[row].sort(function(a, b) {
            var numA = parseInt(a.nomor_kursi.slice(1));
            var numB = parseInt(b.nomor_kursi.slice(1));
            return numA - numB;
        });
        
        // Split into left and right sections
        var totalKursi = kursiRow.length;
        var leftSeats = kursiRow.slice(0, Math.ceil(totalKursi / 2));
        var rightSeats = kursiRow.slice(Math.ceil(totalKursi / 2));
        
        // Render left seats
        leftSeats.forEach(function(kursi) {
            rowDiv.appendChild(createSeatButton(kursi));
        });
        
        // Aisle
        var aisle = document.createElement('div');
        aisle.className = 'w-12';
        rowDiv.appendChild(aisle);
        
        // Render right seats
        rightSeats.forEach(function(kursi) {
            rowDiv.appendChild(createSeatButton(kursi));
        });
        
        // Right label
        var labelRight = document.createElement('span');
        labelRight.className = 'w-8 text-center font-bold text-gray-400';
        labelRight.textContent = row;
        rowDiv.appendChild(labelRight);
        
        grid.appendChild(rowDiv);
    });
    
    updateSummary();
}

function createSeatButton(kursi) {
    var seat = document.createElement('button');
    seat.type = 'button';
    seat.className = 'w-10 h-10 rounded font-semibold text-sm transition';
    seat.textContent = kursi.nomor_kursi.slice(1);
    
    if (kursi.status === 'booked') {
        seat.className += ' bg-red-950 border-2 border-red-800 opacity-50 cursor-not-allowed text-gray-600';
        seat.disabled = true;
    } else if (selectedKursiIds.includes(kursi.id)) {
        seat.className += ' bg-red-600 border-2 border-red-500 shadow-lg shadow-red-900/50';
    } else {
        seat.className += ' bg-gray-800 border-2 border-gray-700 hover:border-red-500 hover:bg-gray-700';
    }
    
    seat.onclick = function() { toggleKursi(kursi.id); };
    return seat;
}

function toggleKursi(kursiId) {
    var index = selectedKursiIds.indexOf(kursiId);
    if (index > -1) {
        selectedKursiIds.splice(index, 1);
    } else {
        selectedKursiIds.push(kursiId);
    }
    renderKursiGrid();
}

function updateSummary() {
    var selected = kursiData.filter(function(k) {
        return selectedKursiIds.includes(k.id);
    });
    var total = selected.length * hargaPerKursi;
    
    document.getElementById('selectedCount').textContent = selected.length;
    document.getElementById('totalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('btnToStep3').disabled = selected.length === 0;
}

// STEP 3: Show Summary
function goToStep3() {
    var selected = kursiData.filter(function(k) {
        return selectedKursiIds.includes(k.id);
    });
    var total = selected.length * hargaPerKursi;
    
    // Fill summary details
    var jadwalInfo = selectedJadwalInfo.film + ' | ' + selectedJadwalInfo.tanggal + ' ' + selectedJadwalInfo.waktu;
    document.getElementById('summaryJadwal').textContent = jadwalInfo;
    document.getElementById('summaryStudio').textContent = selectedJadwalInfo.studio;
    
    var kursiText = selected.map(function(k) { return k.nomor_kursi; }).join(', ');
    document.getElementById('summaryKursi').textContent = kursiText;
    document.getElementById('summaryJumlah').textContent = selected.length + ' Tiket';
    document.getElementById('summaryTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    
    // Reset payment method selection
    document.querySelectorAll('input[name="metode_bayar"]').forEach(function(radio) {
        radio.checked = false;
    });
    
    goToStep(3);
}

// STEP Navigation
function goToStep(step) {
    // Hide all steps
    for (var i = 1; i <= 3; i++) {
        document.getElementById('step' + i).classList.add('hidden');
    }
    
    // Show current step
    document.getElementById('step' + step).classList.remove('hidden');
    
    // Update step indicators
    for (var i = 1; i <= 3; i++) {
        var indicator = document.getElementById('step' + i + 'Indicator');
        var line = document.getElementById('line' + i);
        
        if (i < step) {
            // Completed step
            indicator.classList.remove('bg-gray-800', 'border', 'border-gray-700');
            indicator.classList.add('bg-red-600', 'shadow-lg', 'shadow-red-900/50');
            if (line) {
                line.classList.remove('bg-gray-800');
                line.classList.add('bg-red-600');
            }
        } else if (i === step) {
            // Current step
            indicator.classList.remove('bg-gray-800', 'border', 'border-gray-700');
            indicator.classList.add('bg-red-600', 'shadow-lg', 'shadow-red-900/50');
        } else {
            // Future step
            indicator.classList.remove('bg-red-600', 'shadow-lg', 'shadow-red-900/50');
            indicator.classList.add('bg-gray-800', 'border', 'border-gray-700');
            if (line) {
                line.classList.remove('bg-red-600');
                line.classList.add('bg-gray-800');
            }
        }
    }
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function backToStep1() {
    selectedKursiIds = [];
    selectedJadwalId = null;
    kursiData = [];
    goToStep(1);
}

function backToStep2() {
    goToStep(2);
}

// PAYMENT PROCESSING
function processPayment() {
    var selectedMetode = document.querySelector('input[name="metode_bayar"]:checked');
    
    if (!selectedMetode) {
        alert('Pilih metode pembayaran terlebih dahulu!');
        return;
    }
    
    var metodeBayar = selectedMetode.value;
    
    if (metodeBayar === 'tunai') {
        // Cash payment - direct confirmation
        confirmCashPayment();
    } else {
        // Digital payment - generate Midtrans token
        generateMidtransPayment();
    }
}

// Cash Payment Handler
function confirmCashPayment() {
    var selected = kursiData.filter(function(k) {
        return selectedKursiIds.includes(k.id);
    });
    var total = selected.length * hargaPerKursi;
    
    if (!confirm('Konfirmasi pembayaran TUNAI sebesar Rp ' + total.toLocaleString('id-ID') + '?')) {
        return;
    }
    
    var btn = document.getElementById('btnProcessPayment');
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Memproses...';
    
    var data = {
        jadwal_id: selectedJadwalId,
        kursi_ids: selectedKursiIds,
        metode_bayar: 'tunai',
        total_bayar: total
    };
    
    fetch('{{ route("kasir.store-tiket-offline") }}', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json', // ✅ TAMBAH INI!
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(data)
})
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            alert('Pembayaran Cash Berhasil! Tiket akan dicetak...');
            
            // Open print window
            if (data.print_url) {
                window.open(data.print_url, '_blank');
            }
            
            // Redirect to dashboard
            setTimeout(function() {
                window.location.href = '{{ route("kasir.dashboard") }}';
            }, 1500);
        } else {
            alert('Error: ' + (data.message || 'Gagal memproses pembayaran'));
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Proses Pembayaran';
        }
    })
    .catch(function(err) {
        console.error('Cash payment error:', err);
        alert('Gagal memproses transaksi. Silakan coba lagi.');
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Proses Pembayaran';
    });
}

// Digital Payment Handler (Midtrans)
function generateMidtransPayment() {
    var selected = kursiData.filter(function(k) {
        return selectedKursiIds.includes(k.id);
    });
    var total = selected.length * hargaPerKursi;
    
    var btn = document.getElementById('btnProcessPayment');
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Generating Payment...';
    
    var data = {
        jadwal_id: selectedJadwalId,
        kursi_ids: selectedKursiIds,
        nama_pelanggan: 'Walk-in Customer',
        email_pelanggan: 'offline@happycine.com',
        no_hp_pelanggan: '081234567890',
        total_bayar: total
    };
    
    fetch('{{ route("kasir.generate-snap-token-offline") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            snapToken = data.snap_token;
            currentOrderId = data.order_id;
            
            // ✅ CEK APAKAH SNAP SUDAH READY
            if (typeof snap === 'undefined') {
                alert('Midtrans belum ready. Refresh halaman dan coba lagi.');
                console.error('Midtrans snap is not loaded');
                resetPaymentButton();
                return;
            }
            
            // Open Midtrans payment popup
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    checkPaymentStatus();
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    alert('Menunggu pembayaran...');
                    startPaymentCheck();
                },
                onError: function(result) {
                    console.error('Payment error:', result);
                    alert('Pembayaran gagal!');
                    resetPaymentButton();
                },
                onClose: function() {
                    console.log('Payment popup closed');
                    resetPaymentButton();
                }
            });
        } else {
            alert('Error: ' + (data.message || 'Gagal generate payment'));
            resetPaymentButton();
        }
    })
    .catch(function(err) {
        console.error('Generate payment error:', err);
        alert('Gagal generate payment. Silakan coba lagi.');
        resetPaymentButton();
    });
}

function resetPaymentButton() {
    var btn = document.getElementById('btnProcessPayment');
    btn.disabled = false;
    btn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Proses Pembayaran';
}

// Payment Status Check
function startPaymentCheck() {
    // Clear existing interval if any
    if (paymentCheckInterval) {
        clearInterval(paymentCheckInterval);
    }
    
    // Check every 3 seconds
    paymentCheckInterval = setInterval(function() {
        checkPaymentStatus();
    }, 3000);
    
    // Stop checking after 5 minutes
    setTimeout(function() {
        if (paymentCheckInterval) {
            clearInterval(paymentCheckInterval);
            paymentCheckInterval = null;
        }
    }, 300000); // 5 minutes
}

function checkPaymentStatus() {
    if (!currentOrderId) {
        console.warn('No order ID to check');
        return;
    }
    
    fetch('/kasir/check-payment-status-offline/' + currentOrderId)
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.is_paid) {
                // Stop checking
                if (paymentCheckInterval) {
                    clearInterval(paymentCheckInterval);
                    paymentCheckInterval = null;
                }
                
                alert('Pembayaran Berhasil! Tiket akan dicetak...');
                
                // Open print window
                if (data.print_url) {
                    window.open(data.print_url, '_blank');
                }
                
                // Redirect to dashboard
                setTimeout(function() {
                    window.location.href = '{{ route("kasir.dashboard") }}';
                }, 1500);
            }
        })
        .catch(function(err) {
            console.error('Check payment status error:', err);
        });
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (paymentCheckInterval) {
        clearInterval(paymentCheckInterval);
    }
});
</script>
@endpush

@endsection