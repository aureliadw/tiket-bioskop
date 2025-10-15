@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-neutral-950 text-white px-6 py-10">
    <div class="max-w-4xl mx-auto">
        
        {{-- Header --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-red-500 mb-2">Tambah Jadwal Film</h1>
                <p class="text-gray-400">Isi data jadwal penayangan film baru</p>
            </div>
            <a href="{{ route('admin.jadwal.index') }}" 
               class="bg-neutral-800 hover:bg-neutral-700 text-gray-300 px-5 py-2 rounded-lg font-semibold transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        {{-- Alert Error --}}
@if ($errors->any())
    <div class="mb-6 bg-red-600/20 border border-red-600 text-red-300 px-4 py-3 rounded-lg">
        <strong>⚠️ Terjadi kesalahan:</strong>
        <ul class="list-disc list-inside mt-2 text-sm text-red-400">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ✅ TAMPILKAN JADWAL EXISTING (Agar admin tau jam mana yang sudah terpakai) --}}
<div class="mb-6 bg-blue-900/20 border border-blue-600/50 rounded-xl p-6">
    <div class="flex items-start gap-3 mb-4">
        <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        <div>
            <h3 class="text-blue-300 font-bold mb-1">💡 Jadwal yang Sudah Ada</h3>
            <p class="text-blue-200 text-sm">Cek jadwal di bawah untuk menghindari bentrok jam tayang</p>
        </div>
    </div>

    <div id="jadwalPreview" class="space-y-3">
        <p class="text-gray-400 text-sm italic">Pilih studio dan tanggal untuk melihat jadwal yang sudah ada...</p>
    </div>
</div>

        {{-- Form Tambah Jadwal --}}
        <form action="{{ route('admin.jadwal.store') }}" method="POST" 
              class="bg-neutral-900 border border-neutral-800 rounded-2xl p-8 shadow-xl space-y-6">
            @csrf

            {{-- Film --}}
            <div>
                <label for="film_id" class="block text-sm font-semibold text-gray-300 mb-2">Film</label>
                <select name="film_id" id="film_id" required
                        class="w-full bg-neutral-800 border border-neutral-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-red-600">
                    <option value="">-- Pilih Film --</option>
                    @foreach ($films as $film)
                        <option value="{{ $film->id }}" {{ old('film_id') == $film->id ? 'selected' : '' }}>
                            {{ $film->judul }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Studio --}}
<div>
    <label for="studio_id" class="block text-sm font-semibold text-gray-300 mb-2">Studio</label>
    <select name="studio_id" id="studio_id" required
            class="w-full bg-neutral-800 border border-neutral-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-red-600">
        <option value="">-- Pilih Studio --</option>
        @foreach ($studios as $studio)
            <option value="{{ $studio->id }}" 
                    data-kursi="{{ $studio->total_kursi }}"
                    {{ old('studio_id') == $studio->id ? 'selected' : '' }}>
                {{ $studio->nama_studio }} ({{ $studio->total_kursi }} kursi)
            </option>
        @endforeach
    </select>
</div>

{{-- Kursi Tersedia (Auto-fill & Readonly) --}}
<div>
    <label for="kursi_tersedia" class="block text-sm font-semibold text-gray-300 mb-2">
        Kursi Tersedia
        <span class="text-xs text-gray-500 font-normal ml-2">(Otomatis dari studio)</span>
    </label>
    <input type="number" name="kursi_tersedia" id="kursi_tersedia" 
           value="{{ old('kursi_tersedia') }}" 
           min="1" 
           readonly
           required
           class="w-full bg-neutral-800 border border-neutral-700 rounded-lg px-4 py-3 text-white focus:outline-none cursor-not-allowed"
           placeholder="Pilih studio terlebih dahulu">
    <p class="text-xs text-gray-500 mt-1">
        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        Jumlah kursi mengikuti kapasitas studio
    </p>
</div>

            {{-- Tanggal Tayang --}}
            <div>
                <label for="tanggal_tayang" class="block text-sm font-semibold text-gray-300 mb-2">Tanggal Tayang</label>
                <input type="date" name="tanggal_tayang" id="tanggal_tayang" value="{{ old('tanggal_tayang') }}" required
                       class="w-full bg-neutral-800 border border-neutral-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-red-600">
            </div>

            {{-- Jam Tayang --}}
            <div>
                <label for="jam_tayang" class="block text-sm font-semibold text-gray-300 mb-2">Jam Tayang</label>
                <input type="time" name="jam_tayang" id="jam_tayang" value="{{ old('jam_tayang') }}" required
                       class="w-full bg-neutral-800 border border-neutral-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-red-600">
            </div>

            {{-- Harga Dasar --}}
            <div>
                <label for="harga_dasar" class="block text-sm font-semibold text-gray-300 mb-2">Harga Dasar (Rp)</label>
                <input type="number" name="harga_dasar" id="harga_dasar" value="{{ old('harga_dasar') }}" min="0" required
                       class="w-full bg-neutral-800 border border-neutral-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-red-600"
                       placeholder="Contoh: 35000">
            </div>

            {{-- Status Aktif --}}
            <div class="flex items-center">
                <input type="checkbox" name="status_aktif" id="status_aktif" value="1" 
                       class="w-5 h-5 text-red-600 bg-neutral-800 border-neutral-700 rounded focus:ring-red-600"
                       {{ old('status_aktif', true) ? 'checked' : '' }}>
                <label for="status_aktif" class="ml-2 text-sm text-gray-300 font-medium">
                    Aktifkan jadwal ini
                </label>
            </div>

            {{-- Tombol --}}
            <div class="flex justify-end gap-3 pt-6">
                <a href="{{ route('admin.jadwal.index') }}" 
                   class="px-5 py-2 bg-neutral-700 hover:bg-neutral-600 rounded-lg text-gray-200 font-semibold transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-lg font-semibold shadow-lg transition hover:scale-105">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- JavaScript --}}
<script>
    // Data jadwal dari backend
    const jadwalsData = @json($existingJadwals);

    // Auto-fill kursi saat studio dipilih
    const studioSelect = document.getElementById('studio_id');
    const tanggalInput = document.getElementById('tanggal_tayang');
    const kursiInput = document.getElementById('kursi_tersedia');
    const jadwalPreview = document.getElementById('jadwalPreview');

    studioSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const kursi = selectedOption.getAttribute('data-kursi');
        
        if (kursi) {
            kursiInput.value = kursi;
            kursiInput.classList.remove('border-neutral-700');
            kursiInput.classList.add('border-green-600', 'bg-green-900/20');
        } else {
            kursiInput.value = '';
            kursiInput.classList.remove('border-green-600', 'bg-green-900/20');
            kursiInput.classList.add('border-neutral-700');
        }

        // Update preview jadwal
        updateJadwalPreview();
    });

    tanggalInput.addEventListener('change', updateJadwalPreview);

    // Fungsi untuk menampilkan jadwal existing
    function updateJadwalPreview() {
        const studioId = studioSelect.value;
        const tanggal = tanggalInput.value;

        if (!studioId || !tanggal) {
            jadwalPreview.innerHTML = '<p class="text-gray-400 text-sm italic">Pilih studio dan tanggal untuk melihat jadwal yang sudah ada...</p>';
            return;
        }

        const key = studioId + '-' + tanggal;
        const jadwals = jadwalsData[key] || [];

        if (jadwals.length === 0) {
            jadwalPreview.innerHTML = `
                <div class="bg-green-900/20 border border-green-600/50 rounded-lg p-4">
                    <p class="text-green-300 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <strong>Jadwal kosong!</strong> Semua jam tayang tersedia untuk tanggal ini.
                    </p>
                </div>
            `;
            return;
        }

        const studioName = studioSelect.options[studioSelect.selectedIndex].text.split(' (')[0];
        const jadwalHTML = jadwals.map(j => `
            <div class="flex items-center gap-3 px-4 py-2 bg-neutral-800/50 border border-neutral-700 rounded-lg">
                <span class="text-red-400 font-mono font-bold">${j.jam_tayang.substring(0, 5)}</span>
                <span class="text-gray-400">→</span>
                <span class="text-white font-semibold">${j.film.judul}</span>
            </div>
        `).join('');

        jadwalPreview.innerHTML = `
            <div class="bg-neutral-800/30 border border-neutral-700 rounded-lg p-4">
                <p class="text-gray-300 font-semibold mb-3">📅 ${studioName} - ${new Date(tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</p>
                <div class="space-y-2">
                    ${jadwalHTML}
                </div>
            </div>
        `;
    }

    // Trigger auto-fill jika ada old value
    window.addEventListener('DOMContentLoaded', function() {
        if (studioSelect.value) {
            studioSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
