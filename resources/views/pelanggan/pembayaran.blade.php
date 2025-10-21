@extends('layouts.app')

@section('content')
<div 
    x-data="{
        showModal: false, 
        selectedPayment: null,
        showUploadForm: false,
        countdown: 600,
        interval: null,
        startCountdown() {
            clearInterval(this.interval)
            this.interval = setInterval(() => {
                if (this.countdown > 0) this.countdown--
                else this.showModal = false
            }, 1000)
        },
        formatTime(sec) {
            const m = Math.floor(sec / 60).toString().padStart(2, '0')
            const s = (sec % 60).toString().padStart(2, '0')
            return `${m}:${s}`
        },
        async submitPayment() {
            const paymentMethod = document.querySelector('input[name=metode_pembayaran]:checked')?.value
            if (!paymentMethod) {
                alert('Pilih metode pembayaran terlebih dahulu')
                return
            }
            
            const formData = new FormData(document.getElementById('formPembayaran'))
            
            try {
                const response = await fetch('/pembayaran/proses/{{ $pemesanan->id }}', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                
                if (!response.ok) {
                    const errorText = await response.text()
                    console.error('Server error:', response.status, errorText)
                    alert('Server error: ' + response.status)
                    return
                }
                
                const data = await response.json()
                
                if (data.success) {
                    this.selectedPayment = paymentMethod
                    this.showModal = true
                    this.startCountdown()
                } else {
                    alert(data.message || 'Terjadi kesalahan')
                }
            } catch (error) {
                console.error('Fetch error:', error)
                alert('Kesalahan: ' + error.message)
            }
        }
    }"
    class="min-h-screen bg-neutral-950 text-white">

    {{-- ============================================
         HEADER: BACK BUTTON
         ============================================ 
    --}}
    <div class="bg-neutral-900 border-b border-neutral-800 py-4">
        <div class="max-w-6xl mx-auto px-4">
            <a href="{{ route('pelanggan.pilih-kursi', $pemesanan->jadwal_id) }}" 
               class="inline-flex items-center gap-2 text-gray-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Pemilihan Kursi
            </a>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 py-8">
        <form id="formPembayaran" @submit.prevent="submitPayment()">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_420px] gap-6">
                
                {{-- ============================================
                     KOLOM KIRI: DETAIL PESANAN
                     ============================================ 
                --}}
                <div class="space-y-5">
                    
                    {{-- Alert Warning --}}
                    <div class="bg-gradient-to-r from-yellow-900/30 to-yellow-800/20 border-l-4 border-yellow-500 rounded-lg p-4">
                        <div class="flex gap-3">
                            <svg class="w-6 h-6 text-yellow-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="font-bold text-yellow-200 mb-1">Perhatian Penting</p>
                                <p class="text-sm text-yellow-100">Tiket yang dibeli tidak dapat diubah atau di refund.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Countdown Timer (jika belum bayar) --}}
                    @if($pemesanan->status_pembayaran === 'belum_bayar')
                        @php
                            $expiredAt = \Carbon\Carbon::parse($pemesanan->created_at)->addMinutes(10);
                        @endphp
                        
                        <div class="bg-red-900/20 border border-red-500 rounded-xl p-4">
                            <p class="text-sm text-red-200 text-center">
                                ⏰ Selesaikan pembayaran dalam <strong id="countdown" class="text-red-400 text-lg"></strong>
                            </p>
                        </div>

                        <script>
                            const expiredAt = new Date("{{ $expiredAt->toIso8601String() }}").getTime();
                            
                            const countdownInterval = setInterval(() => {
                                const now = new Date().getTime();
                                const distance = expiredAt - now;
                                
                                if (distance < 0) {
                                    clearInterval(countdownInterval);
                                    document.getElementById('countdown').innerHTML = "EXPIRED!";
                                    alert('Waktu pembayaran habis! Pemesanan akan dibatalkan.');
                                    window.location.href = "{{ route('home') }}";
                                    return;
                                }
                                
                                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                
                                document.getElementById('countdown').innerHTML = minutes + "m " + seconds + "s";
                            }, 1000);
                        </script>
                    @endif

                    {{-- Detail Pesanan Card --}}
                    <div class="bg-gradient-to-br from-neutral-900 via-neutral-900 to-neutral-950 rounded-2xl border border-neutral-800 shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-red-600/10 to-pink-600/10 border-b border-neutral-800 px-6 py-4">
                            <h2 class="text-xl font-bold flex items-center gap-2">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                                Detail Pesanan
                            </h2>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-[140px_1fr] gap-x-6 gap-y-4 text-sm">
                                <div class="text-gray-500">Judul Film</div>
                                <div class="font-bold text-lg">{{ $pemesanan->jadwal->film->judul }}</div>
                                
                                <div class="text-gray-500">Lokasi Bioskop</div>
                                <div class="font-semibold">HappyCine Cinema</div>
                                
                                <div class="text-gray-500">Jadwal Tayang</div>
                                <div class="font-semibold">
                                    {{ \Carbon\Carbon::parse($pemesanan->jadwal->tanggal_tayang)->translatedFormat('l, d F Y') }} - 
                                    <span class="text-red-400">{{ \Carbon\Carbon::parse($pemesanan->jadwal->jam_tayang)->format('H:i') }} WIB</span>
                                </div>
                                
                                <div class="text-gray-500">Studio</div>
                                <div class="font-semibold">
                                    <span class="px-3 py-1 bg-neutral-800 border border-neutral-700 rounded-full text-xs">
                                        {{ $pemesanan->jadwal->studio->nama_studio ?? 'REGULAR' }}
                                    </span>
                                </div>
                                
                                <div class="text-gray-500">Kursi Dipilih</div>
                                <div class="font-semibold">{{ $kursis->pluck('nomor_kursi')->implode(', ') }}</div>
                                
                                <div class="col-span-2 pt-4 mt-2 border-t border-neutral-800"></div>
                                
                                <div class="text-gray-500">Total Tiket</div>
                                <div class="font-bold text-lg">
                                    {{ $kursis->count() }} Tiket × Rp {{ number_format($subtotal / $kursis->count(), 0, ',', '.') }} = 
                                    <span class="text-red-500">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Pelanggan Card --}}
                    <div class="bg-gradient-to-br from-neutral-900 via-neutral-900 to-neutral-950 rounded-2xl border border-neutral-800 shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600/10 to-indigo-600/10 border-b border-neutral-800 px-6 py-4">
                            <h2 class="text-xl font-bold flex items-center gap-2">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Informasi Pelanggan
                            </h2>
                        </div>
                        
                        <div class="p-6 space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-400 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       name="nama_lengkap" 
                                       value="{{ $user->nama_lengkap }}"
                                       class="w-full px-4 py-3 rounded-xl bg-neutral-800 border-2 border-neutral-700 focus:border-red-500 focus:ring-4 focus:ring-red-500/20 outline-none transition placeholder-gray-500" 
                                       placeholder="Masukkan nama lengkap"
                                       required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-400 mb-2">Email <span class="text-red-500">*</span></label>
                                <input type="email" 
                                       name="email" 
                                       value="{{ $user->email }}"
                                       class="w-full px-4 py-3 rounded-xl bg-neutral-800 border-2 border-neutral-700 focus:border-red-500 focus:ring-4 focus:ring-red-500/20 outline-none transition placeholder-gray-500" 
                                       placeholder="email@example.com"
                                       required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-400 mb-2">Nomor HP <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       name="phone" 
                                       value="{{ $user->phone ?? '' }}"
                                       class="w-full px-4 py-3 rounded-xl bg-neutral-800 border-2 border-neutral-700 focus:border-red-500 focus:ring-4 focus:ring-red-500/20 outline-none transition placeholder-gray-500" 
                                       placeholder="08xxxxxxxxxx"
                                       required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ============================================
                     KOLOM KANAN: DETAIL TRANSAKSI (STICKY)
                     ============================================ 
                --}}
                <div class="lg:sticky lg:top-6 h-fit">
                    <div class="bg-gradient-to-br from-neutral-900 via-neutral-900 to-neutral-950 rounded-2xl border border-neutral-800 shadow-2xl overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-600/10 to-pink-600/10 border-b border-neutral-800 px-6 py-4">
                            <h2 class="text-xl font-bold flex items-center gap-2">
                                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Detail Transaksi
                            </h2>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            {{-- Ringkasan Harga --}}
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Harga Tiket</span>
                                    <span class="font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Biaya Layanan</span>
                                    <span class="font-semibold">Rp 3.500</span>
                                </div>
                                
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Diskon/Promo</span>
                                    <span class="font-semibold text-green-400">- Rp {{ number_format($diskon, 0, ',', '.') }}</span>
                                </div>
                                
                                <div class="pt-3 mt-3 border-t border-neutral-700">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-400 font-semibold">Total Pembayaran</span>
                                        <span class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-pink-500">
                                            Rp {{ number_format($total + 3500, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Voucher/Promo Button --}}
                            <button type="button" 
                                    class="w-full px-4 py-3 bg-gradient-to-r from-neutral-800 to-neutral-900 hover:from-neutral-700 hover:to-neutral-800 border-2 border-dashed border-neutral-700 hover:border-red-500/50 rounded-xl flex items-center justify-between transition-all group">
                                <div class="flex items-center gap-3">
                                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                    </svg>
                                    <span class="font-semibold">Gunakan Voucher/Promo</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-500 group-hover:text-red-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>

                            {{-- Metode Pembayaran --}}
                            <div>
                                <h3 class="font-bold text-sm text-gray-300 mb-3">Pilih Metode Pembayaran</h3>
                                
                                <div class="space-y-2">
                                    @foreach ([
                                        ['id'=>'dana','label'=>'DANA','icon'=>'/images/logodana.jpg'],
                                        ['id'=>'gopay','label'=>'GoPay','icon'=>'/images/logogopay.png'],
                                        ['id'=>'ovo','label'=>'OVO','icon'=>'/images/logoovo2.png'],
                                        ['id'=>'bca','label'=>'Transfer BCA','icon'=>'/images/bankBCA.png'],
                                        ['id'=>'mandiri','label'=>'Transfer Mandiri','icon'=>'/images/bankBCA.png'],
                                    ] as $m)
                                    <label class="cursor-pointer block group">
                                        <input type="radio" 
                                               name="metode_pembayaran" 
                                               value="{{ $m['id'] }}" 
                                               data-rekening="081234567890" 
                                               data-label="{{ $m['label'] }}"
                                               class="hidden peer" 
                                               required>
                                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 border-neutral-700 bg-neutral-800/30 group-hover:bg-neutral-800 peer-checked:border-red-500 peer-checked:bg-gradient-to-r peer-checked:from-red-900/20 peer-checked:to-pink-900/20 transition-all">
                                            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center p-1">
                                                <img src="{{ asset($m['icon']) }}" class="w-full h-full object-contain">
                                            </div>
                                            <span class="font-semibold text-sm flex-1">{{ $m['label'] }}</span>
                                            <div class="w-5 h-5 rounded-full border-2 border-neutral-600 peer-checked:border-red-500 flex items-center justify-center">
                                                <div class="w-2.5 h-2.5 rounded-full bg-red-500 hidden peer-checked:block"></div>
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Button Bayar --}}
                            <button type="submit" 
                                    class="w-full py-4 bg-gradient-to-r from-red-600 via-red-600 to-pink-600 hover:from-red-700 hover:via-red-700 hover:to-pink-700 rounded-xl font-bold text-lg shadow-lg shadow-red-600/40 transition-all hover:scale-[1.02] hover:shadow-xl hover:shadow-red-600/60">
                                <span class="flex items-center justify-center gap-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Bayar Sekarang
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ============================================
         MODAL: INSTRUKSI PEMBAYARAN
         ============================================ 
    --}}
    <div x-show="showModal" 
         class="fixed inset-0 flex items-center justify-center bg-black/70 backdrop-blur-sm z-50 p-4" 
         x-transition>
        <div class="bg-neutral-900 w-full max-w-lg rounded-2xl p-6 relative text-white max-h-[90vh] overflow-y-auto border border-neutral-800" 
             @click.away="showModal=false">
            
            {{-- Close Button --}}
            <button @click="showModal=false" 
                    class="absolute top-4 right-4 text-gray-400 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- INSTRUKSI PEMBAYARAN --}}
            <div x-show="!showUploadForm">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold mb-2">Instruksi Pembayaran</h2>
                    <p class="text-sm text-gray-400">Selesaikan pembayaran dalam waktu:</p>
                    <p class="text-4xl font-mono font-bold text-red-400 mt-3" x-text="formatTime(countdown)"></p>
                </div>

                {{-- E-WALLET (DANA, GOPAY, OVO) --}}
                <template x-if="['dana', 'gopay', 'ovo'].includes(selectedPayment)">
                    <div>
                        <div class="bg-neutral-800 rounded-xl p-4 mb-4 text-center">
                            <p class="text-sm text-gray-400 mb-3">Scan QR Code dengan aplikasi:</p>
                            <p class="text-xl font-bold mb-4" x-text="document.querySelector('input[name=metode_pembayaran]:checked')?.dataset.label"></p>
                            
                            <div class="flex justify-center mb-4">
                                <div class="p-4 bg-white rounded-xl">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=PAYMENT-{{ $pemesanan->id }}-{{ $total }}" 
                                         alt="QR Code" 
                                         class="w-48 h-48">
                                </div>
                            </div>

                            <div class="bg-neutral-900 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-400 text-sm">Total Transfer:</span>
                                    <span class="text-xl font-bold text-green-400">Rp {{ number_format($total + 3500, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- TRANSFER BANK (BCA, MANDIRI) --}}
                <template x-if="['bca', 'mandiri'].includes(selectedPayment)">
                    <div>
                        <div class="bg-neutral-800 rounded-xl p-4 mb-4">
                            <p class="text-sm text-gray-400 mb-3 text-center">Transfer ke nomor Virtual Account:</p>
                            
                            <div class="bg-neutral-900 rounded-lg p-4 mb-3 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-400 text-sm">Bank:</span>
                                    <span class="font-bold text-lg" x-text="document.querySelector('input[name=metode_pembayaran]:checked')?.dataset.label"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-400 text-sm">Virtual Account:</span>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-xl text-green-400" x-text="'8808' + '{{ str_pad($pemesanan->id, 6, '0', STR_PAD_LEFT) }}'"></span>
                                        <button type="button" 
                                                onclick="navigator.clipboard.writeText(this.previousElementSibling.textContent)" 
                                                class="text-xs bg-neutral-700 hover:bg-neutral-600 px-2 py-1 rounded">
                                            Copy
                                        </button>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-400 text-sm">Total Transfer:</span>
                                    <span class="text-xl font-bold text-green-400">Rp {{ number_format($total + 3500, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Action Buttons --}}
                <div class="space-y-3">
                    <button @click="showUploadForm = true" 
                            class="w-full py-3 bg-green-600 hover:bg-green-500 rounded-lg font-bold transition">
                        Saya Sudah Transfer
                    </button>
                    <button @click="showModal = false" 
                            class="w-full py-3 bg-neutral-700 hover:bg-neutral-600 rounded-lg font-semibold transition">
                        Transfer Nanti
                    </button>
                </div>
            </div>

            {{-- FORM UPLOAD BUKTI TRANSFER --}}
            <div x-show="showUploadForm">
                <h2 class="text-2xl font-bold mb-4 text-center">Upload Bukti Transfer</h2>
                
                <form action="{{ route('pelanggan.upload-bukti', $pemesanan->id) }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm mb-2">Bukti Transfer</label>
                        <input type="file" 
                               name="bukti_transfer" 
                               accept="image/*" 
                               required
                               class="w-full p-3 rounded-lg bg-neutral-800 border border-neutral-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-600 file:text-white file:font-semibold hover:file:bg-green-500">
                    </div>

                    <div>
                        <label class="block text-sm mb-2">Catatan (Opsional)</label>
                        <textarea name="catatan" 
                                  rows="3" 
                                  class="w-full p-3 rounded-lg bg-neutral-800 border border-neutral-700" 
                                  placeholder="Catatan pembayaran..."></textarea>
                    </div>

                    <div class="space-y-3">
                        <button type="submit" 
                                class="w-full py-3 bg-green-600 hover:bg-green-500 rounded-lg font-bold">
                            Upload & Selesai
                        </button>
                        <button type="button" 
                                @click="showUploadForm = false" 
                                class="w-full py-3 bg-neutral-700 hover:bg-neutral-600 rounded-lg">
                            Kembali
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')
@endsection