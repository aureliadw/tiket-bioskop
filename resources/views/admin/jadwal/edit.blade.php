@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-[#0F1419] text-white px-6 py-10">
    <div class="max-w-4xl mx-auto">
        
        {{-- Header --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#D4AF37] mb-2">Edit Jadwal Tayang</h1>
                <p class="text-slate-400">Ubah informasi jadwal yang sudah ada</p>
            </div>
            <a href="{{ route('admin.jadwal.index') }}" 
               class="bg-[#1A2332] hover:bg-slate-700 text-slate-300 px-5 py-2 rounded-lg font-semibold transition border border-slate-700/50">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        {{-- Alert Error --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-600/20 border border-red-500/50 text-red-300 px-4 py-3 rounded-lg">
                <strong class="font-bold">Terjadi kesalahan:</strong>
                <ul class="list-disc list-inside mt-2 text-sm text-red-400">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Alert Success --}}
        @if (session('success'))
            <div class="mb-6 bg-green-600/20 border border-green-500/50 text-green-300 px-4 py-3 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Form Edit --}}
        <form action="{{ route('admin.jadwal.update', $jadwal->id) }}" method="POST"
              class="bg-gradient-to-br from-[#1A2332] to-[#0F1419] border border-slate-700/50 rounded-2xl p-8 shadow-2xl space-y-6">
            @csrf
            @method('PUT')

            {{-- Info Current --}}
            <div class="p-4 bg-[#D4AF37]/10 border border-[#D4AF37]/30 rounded-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-[#D4AF37] flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm text-[#D4AF37] font-semibold mb-1">Jadwal Saat Ini</p>
                        <p class="text-sm text-slate-300">
                            <strong>{{ $jadwal->film->judul }}</strong> • 
                            {{ $jadwal->studio->nama_studio }} • 
                            {{ \Carbon\Carbon::parse($jadwal->tanggal_tayang)->translatedFormat('d F Y') }} • 
                            {{ \Carbon\Carbon::parse($jadwal->jam_tayang)->format('H:i') }} WIB
                        </p>
                    </div>
                </div>
            </div>

            {{-- Film --}}
            <div>
                <label for="film_id" class="block text-sm font-semibold text-slate-300 mb-2">
                    Pilih Film <span class="text-red-500">*</span>
                </label>
                <select name="film_id" id="film_id" required
                        class="w-full bg-[#0A1929] border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition">
                    <option value="">-- Pilih Film --</option>
                    @foreach ($films as $film)
                        <option value="{{ $film->id }}" {{ $jadwal->film_id == $film->id ? 'selected' : '' }}>
                            {{ $film->judul }}
                        </option>
                    @endforeach
                </select>
                @error('film_id')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Studio --}}
            <div>
                <label for="studio_id" class="block text-sm font-semibold text-slate-300 mb-2">
                    Studio <span class="text-red-500">*</span>
                </label>
                <select name="studio_id" id="studio_id" required
                        class="w-full bg-[#0A1929] border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition">
                    <option value="">-- Pilih Studio --</option>
                    @foreach ($studios as $studio)
                        <option value="{{ $studio->id }}" {{ $jadwal->studio_id == $studio->id ? 'selected' : '' }}>
                            {{ $studio->nama_studio }} ({{ $studio->total_kursi }} kursi)
                        </option>
                    @endforeach
                </select>
                @error('studio_id')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal & Jam Tayang --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Tanggal --}}
                <div>
                    <label for="tanggal_tayang" class="block text-sm font-semibold text-slate-300 mb-2">
                        Tanggal Tayang <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="tanggal_tayang" 
                           id="tanggal_tayang" 
                           value="{{ $jadwal->tanggal_tayang }}"
                           required
                           class="w-full bg-[#0A1929] border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition">
                    @error('tanggal_tayang')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jam Tayang --}}
                <div>
                    <label for="jam_tayang" class="block text-sm font-semibold text-slate-300 mb-2">
                        Jam Tayang <span class="text-red-500">*</span>
                    </label>
                    <input type="time" 
                           name="jam_tayang" 
                           id="jam_tayang" 
                           value="{{ \Carbon\Carbon::parse($jadwal->jam_tayang)->format('H:i') }}"
                           required
                           class="w-full bg-[#0A1929] border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition">
                    @error('jam_tayang')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Harga Dasar --}}
            <div>
                <label for="harga_dasar" class="block text-sm font-semibold text-slate-300 mb-2">
                    Harga Tiket (Rp) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-semibold">Rp</span>
                    <input type="number" 
                           name="harga_dasar" 
                           id="harga_dasar" 
                           value="{{ $jadwal->harga_dasar }}"
                           min="0" 
                           step="1000"
                           required
                           class="w-full bg-[#0A1929] border border-slate-700 rounded-lg pl-12 pr-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition"
                           placeholder="35000">
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    <i class="fas fa-info-circle"></i> Harga ini untuk hari biasa (Senin-Jumat). Weekend otomatis Rp 45.000
                </p>
                @error('harga_dasar')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status Aktif --}}
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-3">
                    Status Jadwal <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-3 p-4 bg-[#0A1929] rounded-lg border border-slate-700 hover:border-[#D4AF37]/50 cursor-pointer transition flex-1">
                        <input type="radio" 
                               name="status_aktif" 
                               value="1" 
                               {{ $jadwal->status_aktif ? 'checked' : '' }}
                               class="w-5 h-5 text-[#D4AF37] bg-[#0A1929] border-slate-600 focus:ring-[#D4AF37]">
                        <div>
                            <span class="font-semibold text-sm block">Aktif</span>
                            <span class="text-xs text-slate-500">Jadwal bisa dibooking</span>
                        </div>
                    </label>
                    
                    <label class="flex items-center gap-3 p-4 bg-[#0A1929] rounded-lg border border-slate-700 hover:border-red-500/50 cursor-pointer transition flex-1">
                        <input type="radio" 
                               name="status_aktif" 
                               value="0" 
                               {{ !$jadwal->status_aktif ? 'checked' : '' }}
                               class="w-5 h-5 text-red-500 bg-[#0A1929] border-slate-600 focus:ring-red-500">
                        <div>
                            <span class="font-semibold text-sm block">Nonaktif</span>
                            <span class="text-xs text-slate-500">Jadwal tidak muncul</span>
                        </div>
                    </label>
                </div>
                @error('status_aktif')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Action --}}
            <div class="flex flex-col sm:flex-row justify-between gap-4 pt-6 border-t border-slate-700/50">
                <button type="button" 
                        onclick="confirmDelete()"
                        class="px-5 py-3 bg-red-600/20 hover:bg-red-600/30 border border-red-500/50 text-red-400 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Jadwal
                </button>

                <div class="flex gap-3">
                    <a href="{{ route('admin.jadwal.index') }}" 
                       class="px-5 py-3 bg-slate-700 hover:bg-slate-600 rounded-lg text-slate-200 font-semibold transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-[#D4AF37] to-[#C9A02C] hover:from-[#E4C466] hover:to-[#D4AF37] text-[#0A1929] rounded-lg font-bold shadow-lg shadow-[#D4AF37]/30 transition hover:scale-105 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

        {{-- Form Delete (Hidden) --}}
        <form id="deleteForm" action="{{ route('admin.jadwal.destroy', $jadwal->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

    </div>
</div>

{{-- Script Konfirmasi Delete --}}
<script>
function confirmDelete() {
    if (confirm('⚠️ Yakin ingin menghapus jadwal ini?\n\nJadwal: {{ $jadwal->film->judul }}\nTanggal: {{ \Carbon\Carbon::parse($jadwal->tanggal_tayang)->translatedFormat("d F Y") }}\nJam: {{ \Carbon\Carbon::parse($jadwal->jam_tayang)->format("H:i") }} WIB\n\nData yang sudah dihapus tidak bisa dikembalikan!')) {
        document.getElementById('deleteForm').submit();
    }
}

// Auto-format harga dengan separator ribuan
document.getElementById('harga_dasar').addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, '');
    this.value = value;
});

// Validasi tanggal tidak boleh di masa lalu
document.getElementById('tanggal_tayang').addEventListener('change', function() {
    const selectedDate = new Date(this.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (selectedDate < today) {
        alert('⚠️ Tanggal tayang tidak boleh di masa lalu!');
        this.value = '';
    }
});
</script>
@endsection