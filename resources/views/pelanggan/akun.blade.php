@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-neutral-950 text-white">
    <div class="max-w-6xl mx-auto px-4 py-12">
        
        {{-- ============================================
             HEADER: TABS & LOGOUT
             ============================================ 
        --}}
        <div class="flex items-center gap-6 border-b border-neutral-800 mb-10">
            <a href="{{ route('profile.index') }}" 
               class="relative pb-4 px-2 font-bold transition-colors {{ $tab == 'profile' ? 'text-red-500' : 'text-gray-400 hover:text-white' }}">
                Profile
                @if($tab == 'profile')
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-red-600 to-red-500"></span>
                @endif
            </a>
            <a href="{{ route('profile.index') }}?tab=riwayat" 
               class="relative pb-4 px-2 font-bold transition-colors {{ $tab == 'riwayat' ? 'text-red-500' : 'text-gray-400 hover:text-white' }}">
                Riwayat
                @if($tab == 'riwayat')
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-red-600 to-red-500"></span>
                @endif
            </a>
            <form action="{{ route('logout') }}" method="POST" class="ml-auto">
                @csrf
                <button type="submit" class="group flex items-center gap-2 pb-4 px-4 py-2 font-bold text-red-400 hover:text-red-500 transition-all">
                    <svg class="w-4 h-4 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>

        {{-- ============================================
             TAB 1: PROFILE
             ============================================ 
        --}}
        @if($tab == 'profile')
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                {{-- PROFILE CARD (SIDEBAR) --}}
                <div class="lg:col-span-1">
                    <div class="bg-gradient-to-br from-neutral-900 via-neutral-900 to-neutral-950 rounded-2xl p-6 border border-neutral-800/50 shadow-2xl">
                        <div class="flex flex-col items-center text-center">
                            {{-- Avatar --}}
                            <div class="relative mb-4">
                                <div class="w-32 h-32 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center border-4 border-neutral-800 shadow-lg">
                                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                </div>
                                <div class="absolute bottom-0 right-0 w-8 h-8 bg-green-500 rounded-full border-4 border-neutral-900"></div>
                            </div>
                            
                            {{-- Name & Email --}}
                            <h3 class="text-xl font-black mb-1">{{ $user->nama_lengkap }}</h3>
                            <p class="text-sm text-gray-400 mb-4">{{ $user->email }}</p>
                            
                            {{-- Stats --}}
                            <div class="w-full mt-4 pt-4 border-t border-neutral-800 grid grid-cols-2 gap-4 text-center">
                                <div>
                                    <p class="text-2xl font-black text-red-500">{{ $riwayat->count() }}</p>
                                    <p class="text-xs text-gray-400">Total Booking</p>
                                </div>
                                <div>
                                    <p class="text-2xl font-black text-red-500">
                                        {{ $riwayat->filter(fn($p) => $p->pembayaran && $p->pembayaran->status_pembayaran === 'berhasil')->count() }}
                                    </p>
                                    <p class="text-xs text-gray-400">Completed</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- EDIT PROFILE FORM --}}
                <div class="lg:col-span-3">
                    <div class="bg-gradient-to-br from-neutral-900 via-neutral-900 to-neutral-950 rounded-2xl p-8 border border-neutral-800/50 shadow-2xl">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-black">Account Information</h2>
                            <span class="px-3 py-1 bg-red-600/20 border border-red-500/30 rounded-full text-red-400 text-xs font-bold">VERIFIED</span>
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

                        {{-- Form Update Profile --}}
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            <div class="space-y-6">
                                {{-- Name --}}
                                <div>
                                    <label class="block text-sm text-gray-400 mb-2 font-semibold">Name</label>
                                    <input type="text" 
                                           name="nama_lengkap" 
                                           value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
                                           class="w-full px-4 py-3 bg-neutral-800/50 border-2 border-neutral-700 rounded-xl focus:outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all"
                                           placeholder="Masukkan nama lengkap"
                                           required>
                                    @error('nama_lengkap')
                                        <p class="text-red-400 text-xs mt-2 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label class="block text-sm text-gray-400 mb-2 font-semibold">Email Address</label>
                                    <input type="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}"
                                           class="w-full px-4 py-3 bg-neutral-800/50 border-2 border-neutral-700 rounded-xl focus:outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all"
                                           required>
                                    @error('email')
                                        <p class="text-red-400 text-xs mt-2 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Phone --}}
                                <div>
                                    <label class="block text-sm text-gray-400 mb-2 font-semibold">Phone Number</label>
                                    <input type="tel" 
                                           name="phone" 
                                           value="{{ old('phone', $user->phone ?? '') }}"
                                           placeholder="+62 xxx xxxx xxxx"
                                           class="w-full px-4 py-3 bg-neutral-800/50 border-2 border-neutral-700 rounded-xl focus:outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all">
                                    @error('phone')
                                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Action Buttons --}}
                                <div class="flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-neutral-800">
                                    <div class="flex gap-4">
                                        <button type="button" 
                                                onclick="openPasswordModal()" 
                                                class="group flex items-center gap-2 text-gray-400 hover:text-white text-sm font-semibold transition-all">
                                            <svg class="w-4 h-4 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                            </svg>
                                            Change Password
                                        </button>
                                        <button type="submit" 
                                                name="delete_account" 
                                                value="1"
                                                class="text-red-500 hover:text-red-600 text-sm font-semibold transition-colors"
                                                onclick="return confirm('Yakin ingin menghapus akun ini?')">
                                            Delete Account
                                        </button>
                                    </div>
                                    <button type="submit" 
                                            class="px-8 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-xl font-bold shadow-lg shadow-red-600/30 transition-all hover:scale-105">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        {{-- ============================================
             TAB 2: RIWAYAT (BOOKING HISTORY)
             ============================================ 
        --}}
        @else
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-3xl font-black">Booking History</h2>
                    <span class="px-3 py-1 bg-neutral-800 rounded-full text-sm font-semibold">
                        {{ $riwayat->count() }} Total
                    </span>
                </div>

                {{-- EMPTY STATE --}}
                @if($riwayat->isEmpty())
                    <div class="bg-gradient-to-br from-neutral-900 to-neutral-950 rounded-2xl p-16 border border-neutral-800/50 text-center">
                        <div class="w-24 h-24 bg-neutral-800 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">No Booking History</h3>
                        <p class="text-gray-400 mb-6">Start booking your favorite movies now!</p>
                        <a href="{{ route('pelanggan.index') }}" 
                           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-xl font-bold shadow-lg shadow-red-600/30 transition-all hover:scale-105">
                            Browse Movies
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    </div>
                @else
                    {{-- BOOKING LIST --}}
                    <div class="space-y-4">
                        @foreach($riwayat as $pemesanan)
                            <div class="group bg-gradient-to-br from-neutral-900 to-neutral-950 rounded-2xl border border-neutral-800/50 overflow-hidden hover:border-red-500/30 transition-all hover:shadow-xl hover:shadow-red-500/10">
                                <div class="flex flex-col sm:flex-row gap-4 p-5">
                                    
                                    {{-- Poster Film --}}
                                    <div class="flex-shrink-0">
                                        <div class="relative overflow-hidden rounded-xl">
                                            <img src="{{ asset('storage/' . $pemesanan->jadwal->film->poster_image) }}" 
                                                 alt="{{ $pemesanan->jadwal->film->judul }}"
                                                 class="w-full sm:w-24 h-36 object-cover group-hover:scale-110 transition-transform duration-300">
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                        </div>
                                    </div>

                                    {{-- Detail Pemesanan --}}
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-black mb-3 truncate">{{ $pemesanan->jadwal->film->judul }}</h3>
                                        
                                        {{-- Info Grid --}}
                                        <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm mb-4">
                                            <div>
                                                <p class="text-gray-500 text-xs mb-1">Booked On</p>
                                                <p class="font-bold">{{ $pemesanan->created_at->format('d M Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500 text-xs mb-1">Showtime</p>
                                                <p class="font-bold">{{ \Carbon\Carbon::parse($pemesanan->jadwal->tanggal_tayang)->format('d M Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500 text-xs mb-1">Studio</p>
                                                <p class="font-bold">{{ $pemesanan->jadwal->studio->nama_studio ?? 'Studio 1' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500 text-xs mb-1">Time</p>
                                                <p class="font-bold">{{ \Carbon\Carbon::parse($pemesanan->jadwal->jam_tayang)->format('H:i') }} WIB</p>
                                            </div>
                                            <div class="col-span-2">
                                                <p class="text-gray-500 text-xs mb-1">Seats</p>
                                                <p class="font-bold">{{ $pemesanan->kursis->pluck('nomor_kursi')->implode(', ') }}</p>
                                            </div>
                                        </div>

                                        {{-- STATUS & ACTION BUTTONS --}}
                                        <div class="flex flex-wrap items-center gap-2">
                                            @php
                                                $pembayaran = $pemesanan->pembayaran;
                                                $tanggalTayang = \Carbon\Carbon::parse($pemesanan->jadwal->tanggal_tayang);
                                                $jamTayang = \Carbon\Carbon::parse($pemesanan->jadwal->jam_tayang);
                                                $waktuTayang = $tanggalTayang->setTimeFromTimeString($jamTayang->format('H:i:s'));
                                                $sudahLewat = now()->greaterThan($waktuTayang);
                                            @endphp

                                            {{-- 1. SUKSES & SUDAH LEWAT (Expired) --}}
                                            @if($pembayaran && $pembayaran->status_pembayaran == 'berhasil' && $sudahLewat)
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-500/20 border border-gray-500/30 rounded-lg text-gray-400 text-xs font-bold">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414 0l-2 2a1 1 0 000 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 000-1.414l-2-2z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Expired
                                                </span>

                                            {{-- 2. SUKSES & BELUM LEWAT (View Ticket) --}}
                                            @elseif($pembayaran && $pembayaran->status_pembayaran == 'berhasil' && !$sudahLewat)
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400 text-xs font-bold">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Success
                                                </span>
                                                <a href="{{ route('pelanggan.tiket', $pemesanan->id) }}" 
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 rounded-lg text-white text-xs font-bold transition-colors">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 100 4v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2a2 2 0 100-4V6z"/>
                                                    </svg>
                                                    View Ticket
                                                </a>

                                            {{-- 3. GAGAL (Rejected) --}}
                                            @elseif($pembayaran && $pembayaran->status_pembayaran == 'gagal')
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 text-xs font-bold">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Rejected
                                                </span>

                                            {{-- 4. PENDING VERIFICATION (Sudah upload, nunggu admin) --}}
                                            @elseif($pembayaran && $pembayaran->status_pembayaran == 'pending' && $pembayaran->bukti_transfer)
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500/20 border border-blue-500/30 rounded-lg text-blue-400 text-xs font-bold">
                                                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    Pending Verification
                                                </span>
                                                <p class="text-xs text-gray-500">Menunggu konfirmasi</p>

                                            {{-- 5. BELUM BAYAR (Belum upload bukti) --}}
                                            @else
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-yellow-500/20 border border-yellow-500/30 rounded-lg text-yellow-400 text-xs font-bold">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Pending
                                                </span>
                                                
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Total Harga --}}
                                    <div class="flex flex-col justify-between items-end">
                                        <div class="text-right">
                                            <p class="text-gray-500 text-xs mb-1">Total</p>
                                            <p class="text-2xl font-black text-red-500">
                                                Rp {{ number_format($pemesanan->total_bayar ?? 0, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>

{{-- ============================================
     MODAL: CHANGE PASSWORD
     ============================================ 
--}}
<div id="passwordModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
    <div class="bg-gradient-to-br from-neutral-900 to-neutral-950 rounded-2xl p-8 border border-neutral-800 shadow-2xl max-w-md w-full mx-4 transform transition-all">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-black">Change Password</h3>
            <button onclick="closePasswordModal()" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('profile.change-password') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-2 font-semibold">Current Password</label>
                    <input type="password" 
                           name="current_password" 
                           class="w-full px-4 py-3 bg-neutral-800/50 border-2 border-neutral-700 rounded-xl focus:outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all"
                           required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2 font-semibold">New Password</label>
                    <input type="password" 
                           name="new_password" 
                           class="w-full px-4 py-3 bg-neutral-800/50 border-2 border-neutral-700 rounded-xl focus:outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all"
                           required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2 font-semibold">Confirm New Password</label>
                    <input type="password" 
                           name="new_password_confirmation" 
                           class="w-full px-4 py-3 bg-neutral-800/50 border-2 border-neutral-700 rounded-xl focus:outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all"
                           required>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" 
                            onclick="closePasswordModal()"
                            class="flex-1 px-4 py-3 bg-neutral-800 hover:bg-neutral-700 rounded-xl font-semibold transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-xl font-bold shadow-lg shadow-red-600/30 transition-all hover:scale-105">
                        Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ============================================
     JAVASCRIPT: MODAL FUNCTIONS
     ============================================ 
--}}
<script>
function openPasswordModal() {
    document.getElementById('passwordModal').classList.remove('hidden');
}

function closePasswordModal() {
    document.getElementById('passwordModal').classList.add('hidden');
}
</script>

@include('layouts.footer')
@endsection