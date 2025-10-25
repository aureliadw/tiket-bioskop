@extends('layouts.app')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="relative min-h-screen bg-neutral-950 text-white py-12">
    
    {{-- Midtrans Snap Script --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <div class="max-w-6xl mx-auto px-4">

        {{-- Hitung Harga Tiket --}}
        @php
            use Carbon\Carbon;
            $tanggalTayang = Carbon::parse($jadwal->tanggal_tayang);
            $isWeekend = $tanggalTayang->isWeekend();
            $hargaFinal = $isWeekend ? 45000 : $jadwal->harga_dasar;
        @endphp

        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl lg:text-4xl font-extrabold mb-2 bg-gradient-to-r from-white via-gray-200 to-gray-400 bg-clip-text text-transparent">
                {{ strtoupper($jadwal->film->judul) }}
            </h1>
            <p class="text-gray-400">
                {{ $jadwal->studio->nama_studio ?? 'Studio 1' }} • 
                {{ Carbon::parse($jadwal->tanggal_tayang)->translatedFormat('d M Y') }} • 
                {{ Carbon::parse($jadwal->jam_tayang)->format('H:i') }} WIB
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Kolom Kiri: Area Kursi --}}
            <div class="lg:col-span-2">
                
                {{-- Layar --}}
                <div class="relative mb-12">
                    <div class="absolute inset-x-0 top-0 h-32 bg-gradient-to-b from-yellow-500/20 via-yellow-500/5 to-transparent blur-3xl"></div>
                    <div class="relative w-4/5 mx-auto h-3 bg-gradient-to-r from-gray-800 via-gray-600 to-gray-800 rounded-t-2xl shadow-2xl border-t-2 border-x-2 border-gray-700/50">
                        <div class="absolute inset-0 bg-gradient-to-b from-white/10 to-transparent rounded-t-2xl"></div>
                    </div>
                    <div class="w-full h-8 bg-gradient-to-r from-transparent via-gray-700/30 to-transparent flex items-center justify-center">
                        <span class="text-xs font-bold tracking-[0.3em] text-gray-400">LAYAR</span>
                    </div>
                </div>

                {{-- Grid Kursi --}}
                <div class="bg-gradient-to-b from-neutral-900/50 to-neutral-950 rounded-2xl border border-neutral-800/50 p-8">
                    @php
                        $kursiByRow = $kursis->groupBy('baris');
                        $colsPerSide = 5;
                    @endphp

                    @foreach($kursiByRow as $baris => $kursisBaris)
                        <div class="flex items-center justify-center gap-4 mb-3">
                            
                            <div class="w-6 text-center text-xs font-bold text-gray-500">{{ $baris }}</div>

                            {{-- Kursi Kiri --}}
                            <div class="flex gap-2">
                                @foreach($kursisBaris->sortBy('kolom')->take($colsPerSide) as $kursi)
                                    @php
                                        $isTerjual = in_array($kursi->id, $kursiTerjual ?? []);
                                        $isPending = in_array($kursi->id, $kursiPending ?? []);
                                    @endphp
                                    
                                    <div class="relative group">
                                        <input type="checkbox" 
                                               name="kursi[]" 
                                               value="{{ $kursi->id }}" 
                                               class="hidden kursi-checkbox" 
                                               id="kursi-{{ $kursi->id }}"
                                               data-nomor="{{ $kursi->nomor_kursi }}"
                                               @if($isTerjual || $isPending) disabled @endif>
                                        
                                        <div data-id="{{ $kursi->id }}"
                                             data-nomor="{{ $kursi->nomor_kursi }}"
                                             class="kursi w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-lg text-xs font-bold transition-all duration-300 cursor-pointer
                                             @if($isTerjual)
                                                 bg-red-600/80 text-white cursor-not-allowed opacity-60
                                             @elseif($isPending)
                                                 bg-yellow-500/80 text-black cursor-not-allowed opacity-60
                                             @else
                                                 bg-blue-600 text-white hover:bg-blue-500 hover:scale-110 hover:shadow-lg hover:shadow-blue-500/50
                                             @endif">
                                            {{ $kursi->kolom }}
                                        </div>
                                      
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-black/90 text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10 shadow-lg">
                                            @if($isTerjual) Terjual
                                            @elseif($isPending) Terpesan
                                            @else {{ $kursi->nomor_kursi }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Gang Tengah --}}
                            <div class="w-12 flex items-center justify-center">
                                <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-600/30 to-transparent"></div>
                            </div>

                            {{-- Kursi Kanan --}}
                            <div class="flex gap-2">
                                @foreach($kursisBaris->sortBy('kolom')->skip($colsPerSide) as $kursi)
                                    @php
                                        $isTerjual = in_array($kursi->id, $kursiTerjual ?? []);
                                        $isPending = in_array($kursi->id, $kursiPending ?? []);
                                    @endphp
                                    
                                    <div class="relative group">
                                        <input type="checkbox" 
                                               name="kursi[]" 
                                               value="{{ $kursi->id }}" 
                                               class="hidden kursi-checkbox" 
                                               id="kursi-{{ $kursi->id }}"
                                               data-nomor="{{ $kursi->nomor_kursi }}"
                                               @if($isTerjual || $isPending) disabled @endif>
                                        
                                        <div data-id="{{ $kursi->id }}"
                                             data-nomor="{{ $kursi->nomor_kursi }}"
                                             class="kursi w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-lg text-xs font-bold transition-all duration-300 cursor-pointer
                                             @if($isTerjual)
                                                 bg-red-600/80 text-white cursor-not-allowed opacity-60
                                             @elseif($isPending)
                                                 bg-yellow-500/80 text-black cursor-not-allowed opacity-60
                                             @else
                                                 bg-blue-600 text-white hover:bg-blue-500 hover:scale-110 hover:shadow-lg hover:shadow-blue-500/50
                                             @endif">
                                            {{ $kursi->kolom }}
                                        </div>
                                        
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-black/90 text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10 shadow-lg">
                                            @if($isTerjual) Terjual
                                            @elseif($isPending) Terpesan
                                            @else {{ $kursi->nomor_kursi }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="w-6 text-center text-xs font-bold text-gray-500">{{ $baris }}</div>
                        </div>
                    @endforeach
                </div>

                {{-- Legend --}}
                <div class="mt-6 flex flex-wrap justify-center gap-4 sm:gap-6 text-xs sm:text-sm bg-neutral-900/80 backdrop-blur-sm rounded-xl p-4 border border-neutral-800/50">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-blue-600 shadow-md"></div>
                        <span class="text-gray-300">Tersedia</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-green-500 shadow-md"></div>
                        <span class="text-gray-300">Dipilih</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-yellow-500 shadow-md"></div>
                        <span class="text-gray-300">Terpesan</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-red-600 shadow-md"></div>
                        <span class="text-gray-300">Terjual</span>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Summary --}}
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-neutral-900 to-neutral-950 rounded-2xl shadow-2xl border border-neutral-800/50 sticky top-24 p-6">
                    
                    {{-- Poster & Info --}}
                    <div class="flex gap-4 mb-6 pb-6 border-b border-neutral-800">
                        <div class="flex-shrink-0">
                            <div class="relative overflow-hidden rounded-lg shadow-xl w-20 h-28">
                                <img src="{{ asset('storage/' . $jadwal->film->poster_image) }}" 
                                     alt="{{ $jadwal->film->judul }}" 
                                     class="w-full h-full object-cover">
                            </div>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-sm font-bold mb-1 line-clamp-2">{{ $jadwal->film->judul }}</h2>
                            <div class="space-y-1 text-xs text-gray-400">
                                <p>{{ $jadwal->studio->nama_studio ?? 'Studio 1' }}</p>
                                <p>{{ Carbon::parse($jadwal->tanggal_tayang)->format('d M Y') }}</p>
                                <p>{{ Carbon::parse($jadwal->jam_tayang)->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Detail Kursi --}}
                    <div class="mb-6 p-4 bg-neutral-800/50 rounded-xl space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-400">Kursi Dipilih</span>
                            <span id="kursiDipilih" class="text-sm font-bold text-green-400">-</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-400">Jumlah Tiket</span>
                            <span id="jumlahTiket" class="text-sm font-bold text-white">0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-400">Harga/Tiket</span>
                            <span class="text-sm font-semibold text-white">
                                Rp {{ number_format($hargaFinal, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-400">Biaya Admin</span>
                            <span class="text-sm font-semibold text-white">Rp 3.500</span>
                        </div>
                    </div>

                    {{-- Total --}}
                    <div class="mb-6 p-5 bg-gradient-to-br from-green-500/10 to-emerald-600/5 rounded-xl border border-green-500/20">
                        <p class="text-xs text-gray-400 mb-1">Total Pembayaran</p>
                        <p class="text-3xl font-black text-green-400">
                            Rp <span id="totalHarga">0</span>
                        </p>
                    </div>

                    {{-- Button Checkout --}}
                    <button type="button" 
                            onclick="prosesCheckout()"
                            class="w-full py-4 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-xl font-bold shadow-lg shadow-red-600/30 transition-all duration-300 disabled:from-gray-700 disabled:to-gray-800 disabled:cursor-not-allowed disabled:shadow-none hover:scale-105 disabled:hover:scale-100"
                            id="btnCheckout"
                            disabled>
                        Lanjut ke Pembayaran
                    </button>
                    
                    <p class="text-xs text-center text-gray-500 mt-3" id="pesanKursi">
                        Pilih minimal 1 kursi
                    </p>

                    <p class="text-xs text-gray-400 text-center mt-4">
                        💳 Pembayaran aman dengan Midtrans<br>
                        QRIS • GoPay • ShopeePay • Transfer Bank
                    </p>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
// Konstanta
const hargaTiket = parseInt("{{ $hargaFinal }}") || 0;
const biayaAdmin = 3500;
const jadwalId = "{{ $jadwal->id }}";
let selectedSeats = [];

document.addEventListener("DOMContentLoaded", () => {
    const kursiDivs = document.querySelectorAll(".kursi");

    // Event: Klik Kursi
    kursiDivs.forEach(div => {
        div.addEventListener("click", function() {
            if (this.classList.contains("bg-red-600/80") || this.classList.contains("bg-yellow-500/80")) {
                return;
            }

            const kursiId = this.getAttribute("data-id");
            const checkbox = document.getElementById("kursi-" + kursiId);
            if (!checkbox) return;

            checkbox.checked = !checkbox.checked;

            if (checkbox.checked) {
                this.classList.remove("bg-blue-600", "hover:bg-blue-500", "hover:shadow-blue-500/50");
                this.classList.add("bg-green-500", "scale-110", "shadow-xl", "shadow-green-500/50");
            } else {
                this.classList.remove("bg-green-500", "scale-110", "shadow-xl", "shadow-green-500/50");
                this.classList.add("bg-blue-600", "hover:bg-blue-500", "hover:shadow-blue-500/50");
            }

            updateTotal();
        });
    });

    function updateTotal() {
        const checkboxes = document.querySelectorAll(".kursi-checkbox:checked");
        const selected = checkboxes.length;
        
        // Simpan ID kursi yang dipilih
        selectedSeats = Array.from(checkboxes).map(cb => cb.value);
        
        // Ambil nomor kursi
        const kursiTerpilih = Array.from(checkboxes).map(cb => {
            return cb.getAttribute('data-nomor') || '';
        }).filter(k => k);
        
        const kursiText = kursiTerpilih.length > 0 ? kursiTerpilih.join(', ') : '-';
        const totalTiket = selected * hargaTiket;
        const totalBayar = totalTiket + biayaAdmin;
        
        document.getElementById("kursiDipilih").innerText = kursiText;
        document.getElementById("jumlahTiket").innerText = selected;
        document.getElementById("totalHarga").innerText = totalBayar.toLocaleString("id-ID");

        const btnCheckout = document.getElementById("btnCheckout");
        const pesanKursi = document.getElementById("pesanKursi");
        
        if (selected > 0) {
            btnCheckout.disabled = false;
            pesanKursi.style.display = "none";
        } else {
            btnCheckout.disabled = true;
            pesanKursi.style.display = "block";
        }
    }

    updateTotal();
});

// Proses Checkout dengan Midtrans
function prosesCheckout() {
    if (selectedSeats.length === 0) {
        alert('Pilih kursi terlebih dahulu!');
        return;
    }

    const btnCheckout = document.getElementById('btnCheckout');
    btnCheckout.disabled = true;
    btnCheckout.innerHTML = '<span class="animate-spin">⏳</span> Memproses...';

    console.log('Step 1: Proses kursi...'); // DEBUG

    fetch(`/jadwal/${jadwalId}/kursi`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            kursi: selectedSeats
        })
    })
    .then(res => {
        console.log('Response status:', res.status); // DEBUG
        return res.json();
    })
    .then(data => {
        console.log('Response data:', data); // DEBUG
        
        if (data.success) {
            console.log('Step 2: Generate snap token...'); // DEBUG
            generateSnapToken(data.pemesanan_id);
        } else {
            alert('Error: ' + data.message);
            resetButton();
        }
    })
    .catch(err => {
        console.error('Fetch Error:', err); // DEBUG
        alert('Terjadi kesalahan: ' + err.message);
        resetButton();
    });
}

function generateSnapToken(pemesananId) {
    const user = @json(auth()->user());
    
    fetch(`/booking/payment/${pemesananId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            nama_lengkap: user.nama_lengkap,
            email: user.email,
            phone: user.phone || '08123456789'
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Open Midtrans Snap
            snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    checkPaymentStatus(data.order_id);
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    checkPaymentStatus(data.order_id);
                },
                onError: function(result) {
                    console.log('Payment error:', result);
                    alert('Pembayaran gagal. Silakan coba lagi.');
                    resetButton();
                },
                onClose: function() {
                    console.log('Payment popup closed');
                    resetButton();
                }
            });
        } else {
            alert('Gagal generate pembayaran: ' + data.message);
            resetButton();
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan. Silakan coba lagi.');
        resetButton();
    });
}

function checkPaymentStatus(orderId) {
    // Show loading modal
    const modal = document.createElement('div');
    modal.id = 'paymentCheckModal';
    modal.className = 'fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-neutral-900 rounded-2xl p-8 max-w-md w-full mx-4 border border-neutral-800 text-center">
            <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                <svg class="w-10 h-10 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold mb-2 text-white">Memeriksa Pembayaran...</h3>
            <p class="text-gray-400 text-sm" id="checkCounter">Checking...</p>
        </div>
    `;
    document.body.appendChild(modal);

    let checkCount = 0;
    const maxChecks = 30; // 30 x 3 detik = 90 detik (1.5 menit)

    const interval = setInterval(() => {
        checkCount++;
        
        // Update counter
        document.getElementById('checkCounter').innerText = 
            `Checking payment... (${checkCount}/${maxChecks})`;

        fetch(`/booking/check-status/${orderId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            console.log('Check #' + checkCount + ':', data); // DEBUG
            
            if (data.success && data.is_paid) {
                // ✅ PEMBAYARAN BERHASIL
                clearInterval(interval);
                modal.remove();
                
                // Success modal
                const successModal = document.createElement('div');
                successModal.className = 'fixed inset-0 bg-black/90 backdrop-blur-sm flex items-center justify-center z-50';
                successModal.innerHTML = `
                    <div class="bg-gradient-to-br from-green-900 to-neutral-900 rounded-2xl p-8 max-w-md w-full mx-4 border border-green-600 text-center">
                        <div class="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-2 text-white">Pembayaran Berhasil!</h3>
                        <p class="text-green-300 mb-6">Tiket Anda sudah siap</p>
                        <p class="text-gray-400 text-sm">Redirecting...</p>
                    </div>
                `;
                document.body.appendChild(successModal);
                
                setTimeout(() => {
                    window.location.href = '/akun?tab=riwayat';
                }, 2000);
                
            } else if (checkCount >= maxChecks) {
                // ⏰ TIMEOUT
                clearInterval(interval);
                modal.remove();
                
                alert('⏰ Verifikasi pembayaran memakan waktu.\n\nSilakan cek riwayat pemesanan Anda di menu Akun.');
                window.location.href = '/akun?tab=riwayat';
            }
        })
        .catch(err => {
            console.error('Check error:', err);
            
            if (checkCount >= maxChecks) {
                clearInterval(interval);
                modal.remove();
                alert('Terjadi kesalahan saat memeriksa status. Silakan cek riwayat pemesanan.');
                window.location.href = '/akun?tab=riwayat';
            }
        });
    }, 3000); // Check setiap 3 detik
}

function resetButton() {
    const btnCheckout = document.getElementById('btnCheckout');
    btnCheckout.disabled = false;
    btnCheckout.innerHTML = 'Lanjut ke Pembayaran';
}
</script>
@endsection