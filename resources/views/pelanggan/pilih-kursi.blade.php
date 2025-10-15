@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-neutral-950 text-white py-12">
    <div class="max-w-7xl mx-auto px-4">
        
        {{-- Header Film --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl lg:text-4xl font-extrabold mb-2 bg-gradient-to-r from-white via-gray-200 to-gray-400 bg-clip-text text-transparent">
                {{ strtoupper($jadwal->film->judul) }}
            </h1>
            <p class="text-gray-400">
                {{ $jadwal->studio->nama_studio ?? 'Studio 1' }} • 
                {{ \Carbon\Carbon::parse($jadwal->tanggal_tayang)->translatedFormat('d M Y') }} • 
                {{ \Carbon\Carbon::parse($jadwal->jam_tayang)->format('H:i') }} WIB
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Kolom Kiri: Area Kursi --}}
            <div class="lg:col-span-2">
                
                {{-- Screen dengan Efek Cahaya --}}
                <div class="relative mb-12">
                    {{-- Cahaya dari Screen --}}
                    <div class="absolute inset-x-0 top-0 h-32 bg-gradient-to-b from-yellow-500/20 via-yellow-500/5 to-transparent blur-3xl"></div>
                    
                    {{-- Screen --}}
                    <div class="relative w-4/5 mx-auto h-3 bg-gradient-to-r from-gray-800 via-gray-600 to-gray-800 rounded-t-2xl shadow-2xl border-t-2 border-x-2 border-gray-700/50">
                        <div class="absolute inset-0 bg-gradient-to-b from-white/10 to-transparent rounded-t-2xl"></div>
                    </div>
                    <div class="w-full h-8 bg-gradient-to-r from-transparent via-gray-700/30 to-transparent flex items-center justify-center">
                        <span class="text-xs font-bold tracking-[0.3em] text-gray-400">LAYAR</span>
                    </div>
                </div>

                {{-- Form Kursi --}}
                <form action="{{ route('pelanggan.proses-kursi', $jadwal->id) }}" method="POST" id="formKursi">
                    @csrf
                    
                    {{-- Grid Kursi dengan Gang Tengah --}}
                    <div class="bg-gradient-to-b from-neutral-900/50 to-neutral-950 rounded-2xl border border-neutral-800/50 p-8">
                        @php
                            // Group kursi berdasarkan baris
                            $kursiByRow = $kursis->groupBy('baris');
                            $colsPerSide = 5; // 5 kursi kiri, 5 kursi kanan (untuk total 10 kolom)
                        @endphp

                        @foreach($kursiByRow as $baris => $kursisBaris)
                            <div class="flex items-center justify-center gap-4 mb-3">
                                {{-- Label Baris Kiri --}}
                                <div class="w-6 text-center text-xs font-bold text-gray-500">
                                    {{ $baris }}
                                </div>

                                {{-- Kursi Sisi Kiri (Kolom 1-5) --}}
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
                                                   @if($isTerjual || $isPending) disabled @endif>
                                            
                                            <div data-id="{{ $kursi->id }}"
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
                                            
                                            {{-- Tooltip --}}
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-black/90 text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10 shadow-lg">
                                                @if($isTerjual)
                                                    Terjual
                                                @elseif($isPending)
                                                    Terpesan
                                                @else
                                                    {{ $kursi->nomor_kursi }}
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- GANG TENGAH --}}
                                <div class="w-12 flex items-center justify-center">
                                    <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-600/30 to-transparent"></div>
                                </div>

                                {{-- Kursi Sisi Kanan (Kolom 6-10) --}}
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
                                                   @if($isTerjual || $isPending) disabled @endif>
                                            
                                            <div data-id="{{ $kursi->id }}"
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
                                            
                                            {{-- Tooltip --}}
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-black/90 text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10 shadow-lg">
                                                @if($isTerjual)
                                                    Terjual
                                                @elseif($isPending)
                                                    Terpesan
                                                @else
                                                    {{ $kursi->nomor_kursi }}
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Label Baris Kanan --}}
                                <div class="w-6 text-center text-xs font-bold text-gray-500">
                                    {{ $baris }}
                                </div>
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
                </form>
            </div>

            {{-- Kolom Kanan: Summary Card --}}
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-neutral-900 to-neutral-950 rounded-2xl shadow-2xl border border-neutral-800/50 sticky top-24 p-6">
                    
                    {{-- Poster & Info --}}
                    <div class="flex gap-4 mb-6 pb-6 border-b border-neutral-800">
                        {{-- Poster --}}
                        <div class="flex-shrink-0">
                            <div class="relative overflow-hidden rounded-lg shadow-xl w-20 h-28">
                                <img src="{{ asset('storage/' . $jadwal->film->poster_image) }}" 
                                     alt="{{ $jadwal->film->judul }}" 
                                     class="w-full h-full object-cover">
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1">
                            <h2 class="text-sm font-bold mb-1">{{ $jadwal->film->judul }}</h2>
                            <div class="space-y-1 text-xs text-gray-400">
                                <p>{{ $jadwal->studio->nama_studio ?? 'Studio 1' }}</p>
                                <p>{{ \Carbon\Carbon::parse($jadwal->tanggal_tayang)->format('d M Y') }}</p>
                                <p>{{ \Carbon\Carbon::parse($jadwal->jam_tayang)->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Kursi Terpilih --}}
                    <div class="mb-6 p-4 bg-neutral-800/50 rounded-xl">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-400">Kursi Dipilih</span>
                            <span id="kursiDipilih" class="text-sm font-bold text-green-400">-</span>
                        </div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-400">Jumlah Tiket</span>
                            <span id="jumlahTiket" class="text-sm font-bold text-white">0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-400">Harga/Tiket</span>
                            <span class="text-sm font-semibold text-white">Rp {{ number_format($jadwal->harga_dasar, 0, ',', '.') }}</span>
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
                    <button type="submit" 
                            form="formKursi" 
                            class="w-full py-4 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-xl font-bold shadow-lg shadow-red-600/30 transition-all duration-300 disabled:from-gray-700 disabled:to-gray-800 disabled:cursor-not-allowed disabled:shadow-none hover:scale-105 disabled:hover:scale-100"
                            id="btnCheckout"
                            disabled>
                        Lanjut ke Pembayaran
                    </button>
                    
                    {{-- Pesan --}}
                    <p class="text-xs text-center text-gray-500 mt-3" id="pesanKursi">
                        Pilih minimal 1 kursi
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Script --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    const hargaTiket = parseInt("{{ $jadwal->harga_dasar ?? 0 }}") || 0;
    const kursiDivs = document.querySelectorAll(".kursi");

    kursiDivs.forEach(div => {
        div.addEventListener("click", function() {
            // Abaikan kursi yang terjual atau pending
            if (this.classList.contains("bg-red-600/80") || this.classList.contains("bg-yellow-500/80")) {
                return;
            }

            const kursiId = this.getAttribute("data-id");
            const checkbox = document.getElementById("kursi-" + kursiId);
            if (!checkbox) return;

            checkbox.checked = !checkbox.checked;

            // Toggle warna kursi
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
        const total = selected * hargaTiket;
        
        // Ambil nomor kursi yang dipilih
        const kursiTerpilih = Array.from(checkboxes).map(cb => {
            const kursiDiv = document.querySelector(`[data-id="${cb.value}"]`);
            return kursiDiv ? kursiDiv.textContent.trim() : '';
        }).filter(k => k);
        
        const kursiText = kursiTerpilih.length > 0 ? kursiTerpilih.join(', ') : '-';
        
        document.getElementById("kursiDipilih").innerText = kursiText;
        document.getElementById("jumlahTiket").innerText = selected;
        document.getElementById("totalHarga").innerText = total.toLocaleString("id-ID");

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
</script>
@endsection