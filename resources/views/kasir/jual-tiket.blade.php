@extends('layouts.kasir')

@section('title', 'Jual Tiket Offline')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl p-6 shadow-xl">
        <h1 class="text-3xl font-bold mb-2">🎫 Jual Tiket Offline</h1>
        <p class="text-green-100">Untuk pelanggan walk-in yang datang langsung ke bioskop</p>
    </div>

    <!-- Step Wizard -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-2 flex-1">
            <div id="step1Indicator" class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center font-bold text-lg">1</div>
            <div class="flex-1 h-1 bg-green-600" id="line1"></div>
        </div>
        <div class="flex items-center gap-2 flex-1">
            <div id="step2Indicator" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center font-bold text-lg">2</div>
            <div class="flex-1 h-1 bg-gray-700" id="line2"></div>
        </div>
        <div class="flex items-center gap-2 flex-1">
            <div id="step3Indicator" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center font-bold text-lg">3</div>
            <div class="flex-1 h-1 bg-gray-700" id="line3"></div>
        </div>
        <div class="flex items-center">
            <div id="step4Indicator" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center font-bold text-lg">4</div>
        </div>
    </div>

    <!-- STEP 1: Pilih Jadwal -->
    <div id="step1" class="bg-gray-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-2xl font-bold mb-4">1️⃣ Pilih Film & Jadwal</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($jadwals as $jadwal)
            <div class="bg-gray-900 border-2 border-gray-700 rounded-xl p-5 hover:border-green-500 cursor-pointer transition jadwal-card"
                 onclick="selectJadwal({{ $jadwal->id }}, '{{ $jadwal->film->judul }}', '{{ $jadwal->studio->nama_studio }}', '{{ date('d M Y', strtotime($jadwal->tanggal)) }}', '{{ date('H:i', strtotime($jadwal->waktu_mulai)) }}')">
                
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-lg mb-1 truncate">{{ $jadwal->film->judul }}</h3>
                        <p class="text-sm text-gray-400">{{ $jadwal->studio->nama_studio }}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ date('d M Y', strtotime($jadwal->tanggal)) }}</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ date('H:i', strtotime($jadwal->waktu_mulai)) }} - {{ date('H:i', strtotime($jadwal->waktu_selesai)) }}</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Rp {{ number_format($jadwal->harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- STEP 2: Pilih Kursi -->
    <div id="step2" class="bg-gray-800 rounded-2xl p-6 shadow-xl hidden">
        <h2 class="text-2xl font-bold mb-4">2️⃣ Pilih Kursi</h2>
        
        <div class="bg-blue-900/20 border border-blue-500 rounded-xl p-4 mb-6">
            <p class="text-sm text-blue-200">
                <strong>Jadwal Dipilih:</strong> <span id="selectedJadwalInfo"></span>
            </p>
        </div>

        <!-- Legend -->
        <div class="flex gap-4 mb-6 justify-center">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-700 rounded border border-gray-600"></div>
                <span class="text-sm">Tersedia</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-green-600 rounded border border-green-500"></div>
                <span class="text-sm">Dipilih</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-red-900 rounded border border-red-700 opacity-50"></div>
                <span class="text-sm">Terisi</span>
            </div>
        </div>

        <!-- Layar -->
        <div class="mb-8">
            <div class="bg-gradient-to-b from-gray-700 to-gray-800 h-3 rounded-t-3xl mx-auto max-w-2xl"></div>
            <p class="text-center text-xs text-gray-500 mt-2">LAYAR</p>
        </div>

        <!-- Kursi Grid -->
        <div id="kursiGrid" class="flex flex-col items-center gap-3">
            <!-- Will be populated by JavaScript -->
        </div>

        <div class="mt-6 flex justify-between items-center">
            <button onclick="backToStep1()" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 rounded-xl font-semibold transition">
                ← Kembali
            </button>
            <div class="text-right">
                <p class="text-sm text-gray-400">Kursi Dipilih: <span id="selectedCount" class="font-bold text-green-400">0</span></p>
                <p class="text-sm text-gray-400">Total: <span id="totalPrice" class="font-bold text-green-400">Rp 0</span></p>
            </div>
            <button onclick="goToStep3()" id="btnToStep3" class="px-6 py-3 bg-green-600 hover:bg-green-700 rounded-xl font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                Lanjut →
            </button>
        </div>
    </div>

    <!-- STEP 3: Data Pelanggan -->
    <div id="step3" class="bg-gray-800 rounded-2xl p-6 shadow-xl hidden">
        <h2 class="text-2xl font-bold mb-4">3️⃣ Data Pelanggan</h2>
        
        <form id="customerForm" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama_pelanggan" 
                       class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl focus:border-green-500 focus:outline-none"
                       required>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">No. HP / WhatsApp <span class="text-red-500">*</span></label>
                <input type="tel" name="no_hp_pelanggan" 
                       placeholder="08xxxxxxxxxx"
                       class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl focus:border-green-500 focus:outline-none"
                       required>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Email (Opsional)</label>
                <input type="email" name="email_pelanggan" 
                       placeholder="email@example.com"
                       class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl focus:border-green-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Metode Pembayaran <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="flex items-center justify-center px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl cursor-pointer hover:border-green-500 transition has-[:checked]:border-green-500 has-[:checked]:bg-green-900/20">
                        <input type="radio" name="metode_bayar" value="cash" class="mr-2" required>
                        <span class="font-semibold">💵 Cash</span>
                    </label>
                    <label class="flex items-center justify-center px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl cursor-pointer hover:border-green-500 transition has-[:checked]:border-green-500 has-[:checked]:bg-green-900/20">
                        <input type="radio" name="metode_bayar" value="debit" class="mr-2" required>
                        <span class="font-semibold">💳 Debit</span>
                    </label>
                    <label class="flex items-center justify-center px-4 py-3 bg-gray-900 border-2 border-gray-700 rounded-xl cursor-pointer hover:border-green-500 transition has-[:checked]:border-green-500 has-[:checked]:bg-green-900/20">
                        <input type="radio" name="metode_bayar" value="qris" class="mr-2" required>
                        <span class="font-semibold">📱 QRIS</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-between pt-4">
                <button type="button" onclick="backToStep2()" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 rounded-xl font-semibold transition">
                    ← Kembali
                </button>
                <button type="button" onclick="showSummary()" class="px-6 py-3 bg-green-600 hover:bg-green-700 rounded-xl font-semibold transition">
                    Review Pesanan →
                </button>
            </div>
        </form>
    </div>

    <!-- STEP 4: Ringkasan & Konfirmasi -->
    <div id="step4" class="bg-gray-800 rounded-2xl p-6 shadow-xl hidden">
        <h2 class="text-2xl font-bold mb-4">4️⃣ Ringkasan Pesanan</h2>
        
        <div id="summaryContent" class="space-y-4 mb-6">
            <!-- Will be populated by JavaScript -->
        </div>

        <div class="flex justify-between pt-4">
            <button onclick="backToStep3()" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 rounded-xl font-semibold transition">
                ← Kembali
            </button>
            <button onclick="confirmTransaction()" id="btnConfirm" class="px-6 py-3 bg-green-600 hover:bg-green-700 rounded-xl font-bold text-lg transition">
                ✓ Konfirmasi & Cetak Tiket
            </button>
        </div>
    </div>

</div>

@push('scripts')
<script>
let selectedJadwalId = null;
let selectedKursiIds = [];
let kursiData = [];

function selectJadwal(id, film, studio, tanggal, waktu) {
    selectedJadwalId = id;
    document.getElementById('selectedJadwalInfo').textContent = `${film} - ${studio} | ${tanggal} ${waktu}`;
    
    // Load kursi
    fetch(`/kasir/get-kursi/${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                kursiData = data.kursis;
                renderKursiGrid();
                goToStep(2);
            }
        });
}

function renderKursiGrid() {
    const grid = document.getElementById('kursiGrid');
    grid.innerHTML = '';
    
    // Group by row
    const rows = {};
    kursiData.forEach(kursi => {
        const row = kursi.nomor_kursi.charAt(0);
        if (!rows[row]) rows[row] = [];
        rows[row].push(kursi);
    });
    
    Object.keys(rows).sort().forEach(row => {
        const rowDiv = document.createElement('div');
        rowDiv.className = 'flex gap-2 items-center';
        
        // Row label
        const label = document.createElement('span');
        label.className = 'w-8 text-center font-bold text-gray-400';
        label.textContent = row;
        rowDiv.appendChild(label);
        
        // Seats
        rows[row].forEach(kursi => {
            const seat = document.createElement('button');
            seat.type = 'button';
            seat.className = 'w-10 h-10 rounded font-semibold text-sm transition';
            seat.textContent = kursi.nomor_kursi.slice(1);
            
            if (kursi.status === 'booked') {
                seat.className += ' bg-red-900 border border-red-700 opacity-50 cursor-not-allowed';
                seat.disabled = true;
            } else if (selectedKursiIds.includes(kursi.id)) {
                seat.className += ' bg-green-600 border border-green-500';
            } else {
                seat.className += ' bg-gray-700 border border-gray-600 hover:border-green-500';
            }
            
            seat.onclick = () => toggleKursi(kursi.id);
            rowDiv.appendChild(seat);
        });
        
        grid.appendChild(rowDiv);
    });
    
    updateSummary();
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
    const total = selected.reduce((sum, k) => sum + parseInt(k.harga), 0);
    
    document.getElementById('selectedCount').textContent = selected.length;
    document.getElementById('totalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('btnToStep3').disabled = selected.length === 0;
}

function goToStep(step) {
    // Hide all steps
    document.getElementById('step1').classList.add('hidden');
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('step3').classList.add('hidden');
    document.getElementById('step4').classList.add('hidden');
    
    // Show selected step
    document.getElementById('step' + step).classList.remove('hidden');
    
    // Update indicators
    for (let i = 1; i <= 4; i++) {
        const indicator = document.getElementById('step' + i + 'Indicator');
        const line = document.getElementById('line' + i);
        
        if (i < step) {
            indicator.classList.remove('bg-gray-700');
            indicator.classList.add('bg-green-600');
            if (line) {
                line.classList.remove('bg-gray-700');
                line.classList.add('bg-green-600');
            }
        } else if (i === step) {
            indicator.classList.remove('bg-gray-700');
            indicator.classList.add('bg-green-600');
        } else {
            indicator.classList.remove('bg-green-600');
            indicator.classList.add('bg-gray-700');
            if (line) {
                line.classList.remove('bg-green-600');
                line.classList.add('bg-gray-700');
            }
        }
    }
}

function backToStep1() {
    selectedKursiIds = [];
    goToStep(1);
}

function goToStep3() {
    goToStep(3);
}

function backToStep2() {
    goToStep(2);
}

function backToStep3() {
    goToStep(3);
}

function showSummary() {
    const form = document.getElementById('customerForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    const selected = kursiData.filter(k => selectedKursiIds.includes(k.id));
    const total = selected.reduce((sum, k) => sum + parseInt(k.harga), 0);
    
    const summary = `
        <div class="bg-gray-900 border border-gray-700 rounded-xl p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-400">Film & Jadwal</p>
                    <p class="font-semibold">${document.getElementById('selectedJadwalInfo').textContent}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Kursi Dipilih</p>
                    <p class="font-semibold">${selected.map(k => k.nomor_kursi).join(', ')}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Nama Pelanggan</p>
                    <p class="font-semibold">${formData.get('nama_pelanggan')}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">No. HP</p>
                    <p class="font-semibold">${formData.get('no_hp_pelanggan')}</p>
                </div>
                ${formData.get('email_pelanggan') ? `
                <div>
                    <p class="text-sm text-gray-400">Email</p>
                    <p class="font-semibold">${formData.get('email_pelanggan')}</p>
                </div>
                ` : ''}
                <div>
                    <p class="text-sm text-gray-400">Metode Pembayaran</p>
                    <p class="font-semibold uppercase">${formData.get('metode_bayar')}</p>
                </div>
            </div>
            
            <div class="border-t border-gray-700 pt-4">
                <div class="flex justify-between items-center text-2xl font-bold">
                    <span>Total Pembayaran</span>
                    <span class="text-green-400">Rp ${total.toLocaleString('id-ID')}</span>
                </div>
            </div>
        </div>
        
        <div class="bg-yellow-900/20 border border-yellow-500 rounded-xl p-4">
            <p class="text-sm text-yellow-200">
                ⚠️ <strong>Pastikan pembayaran sudah diterima</strong> sebelum mengkonfirmasi transaksi!
            </p>
        </div>
    `;
    
    document.getElementById('summaryContent').innerHTML = summary;
    goToStep(4);
}

function confirmTransaction() {
    const btnConfirm = document.getElementById('btnConfirm');
    btnConfirm.disabled = true;
    btnConfirm.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Memproses...';
    
    const form = document.getElementById('customerForm');
    const formData = new FormData(form);
    
    const data = {
        jadwal_id: selectedJadwalId,
        kursi_ids: selectedKursiIds,
        nama_pelanggan: formData.get('nama_pelanggan'),
        email_pelanggan: formData.get('email_pelanggan'),
        no_hp_pelanggan: formData.get('no_hp_pelanggan'),
        metode_bayar: formData.get('metode_bayar')
    };
    
    fetch('{{ route("kasir.store-tiket-offline") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('✓ Transaksi berhasil! Tiket akan dicetak...');
            // Open print page in new window
            window.open(data.print_url, '_blank');
            // Redirect to dashboard
            setTimeout(() => {
                window.location.href = '{{ route("kasir.dashboard") }}';
            }, 1000);
        } else {
            alert('✗ ' + data.message);
            btnConfirm.disabled = false;
            btnConfirm.innerHTML = '✓ Konfirmasi & Cetak Tiket';
        }
    })
    .catch(err => {
        alert('Gagal memproses transaksi. Coba lagi.');
        btnConfirm.disabled = false;
        btnConfirm.innerHTML = '✓ Konfirmasi & Cetak Tiket';
    });
}
</script>
@endpush

@endsection