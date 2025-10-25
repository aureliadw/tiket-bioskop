@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-neutral-950 text-white">

    {{-- Midtrans Snap Script --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

    {{-- Header --}}
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
        <form id="formPembayaran" onsubmit="submitPayment(event)">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_420px] gap-6">
                
                {{-- Kolom Kiri --}}
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

                    {{-- Countdown Timer --}}
                    @if($pemesanan->status_pembayaran === 'belum_bayar' || $pemesanan->status_pembayaran === 'pending')
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

                    {{-- Detail Pesanan --}}
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

                    {{-- Informasi Pelanggan --}}
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
                                       class="w-full px-4 py-3 rounded-xl bg-neutral-800 border-2 border-neutral-700 focus:border-red-500 focus:ring-4 focus:ring-red-500/20 outline-none transition" 
                                       required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-400 mb-2">Email <span class="text-red-500">*</span></label>
                                <input type="email" 
                                       name="email" 
                                       value="{{ $user->email }}"
                                       class="w-full px-4 py-3 rounded-xl bg-neutral-800 border-2 border-neutral-700 focus:border-red-500 focus:ring-4 focus:ring-red-500/20 outline-none transition" 
                                       required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-400 mb-2">Nomor HP <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       name="phone" 
                                       value="{{ $user->phone ?? '' }}"
                                       class="w-full px-4 py-3 rounded-xl bg-neutral-800 border-2 border-neutral-700 focus:border-red-500 focus:ring-4 focus:ring-red-500/20 outline-none transition" 
                                       required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Summary --}}
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
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Harga Tiket</span>
                                    <span class="font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Biaya Layanan</span>
                                    <span class="font-semibold">Rp 3.500</span>
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

                            {{-- Button Bayar --}}
                            <button type="submit" id="btnBayar"
                                    class="w-full py-4 bg-gradient-to-r from-red-600 via-red-600 to-pink-600 hover:from-red-700 hover:via-red-700 hover:to-pink-700 rounded-xl font-bold text-lg shadow-lg shadow-red-600/40 transition-all hover:scale-[1.02]">
                                <span class="flex items-center justify-center gap-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Bayar Sekarang
                                </span>
                            </button>

                            <p class="text-xs text-gray-400 text-center mt-4">
                                💳 Pembayaran aman dengan Midtrans<br>
                                Tersedia: QRIS, GoPay, ShopeePay, Transfer Bank
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let paymentCheckInterval = null;

function submitPayment(e) {
    e.preventDefault();
    
    const form = document.getElementById('formPembayaran');
    const formData = new FormData(form);
    const btnBayar = document.getElementById('btnBayar');
    
    // Disable button
    btnBayar.disabled = true;
    btnBayar.innerHTML = '<span class="animate-spin">⏳</span> Memproses...';
    
    fetch('{{ route("pelanggan.proses-pembayaran", $pemesanan->id) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Open Midtrans Snap
            snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    startPaymentCheck(data.order_id);
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    startPaymentCheck(data.order_id);
                },
                onError: function(result) {
                    console.log('Payment error:', result);
                    alert('Pembayaran gagal. Silakan coba lagi.');
                    btnBayar.disabled = false;
                    btnBayar.innerHTML = '<span class="flex items-center justify-center gap-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>Bayar Sekarang</span>';
                },
                onClose: function() {
                    console.log('Payment popup closed');
                    btnBayar.disabled = false;
                    btnBayar.innerHTML = '<span class="flex items-center justify-center gap-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>Bayar Sekarang</span>';
                }
            });
        } else {
            alert('Gagal: ' + data.message);
            btnBayar.disabled = false;
            btnBayar.innerHTML = '<span class="flex items-center justify-center gap-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>Bayar Sekarang</span>';
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan. Silakan coba lagi.');
        btnBayar.disabled = false;
        btnBayar.innerHTML = '<span class="flex items-center justify-center gap-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>Bayar Sekarang</span>';
    });
}

// Polling payment status
function startPaymentCheck(orderId) {
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
            <h3 class="text-xl font-bold mb-2">Memeriksa Pembayaran...</h3>
            <p class="text-gray-400 text-sm">Mohon tunggu, kami sedang memverifikasi pembayaran Anda</p>
        </div>
    `;
    document.body.appendChild(modal);

    let checkCount = 0;
    const maxChecks = 60; // 60 checks x 2 seconds = 2 minutes

    paymentCheckInterval = setInterval(() => {
        checkCount++;

        fetch(`{{ url('/pembayaran/check-status') }}/${orderId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.is_paid) {
                clearInterval(paymentCheckInterval);
                modal.remove();
                
                // Show success
                alert('✅ Pembayaran berhasil! Anda akan diarahkan ke halaman tiket.');
                window.location.href = '{{ route("profile.index") }}?tab=riwayat';
            } else if (checkCount >= maxChecks) {
                clearInterval(paymentCheckInterval);
                modal.remove();
                alert('⏰ Verifikasi pembayaran memakan waktu lebih lama. Silakan cek riwayat pemesanan Anda.');
                window.location.href = '{{ route("profile.index") }}?tab=riwayat';
            }
        })
        .catch(err => {
            console.error('Check error:', err);
        });
    }, 2000); // Check every 2 seconds
}
</script>

@include('layouts.footer')
@endsection