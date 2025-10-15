@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-neutral-950 text-white px-6 py-10">
    <div class="max-w-5xl mx-auto">
        
        {{-- Header --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-red-500 mb-2">Bulk Input Jadwal</h1>
                <p class="text-gray-400">Buat banyak jadwal sekaligus dengan cepat</p>
            </div>
            <a href="{{ route('admin.jadwal.index') }}" 
               class="bg-neutral-800 hover:bg-neutral-700 text-gray-300 px-5 py-2 rounded-lg font-semibold transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        {{-- Alert Error --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-600/20 border border-red-600 text-red-300 px-4 py-3 rounded-lg">
                <strong>Terjadi kesalahan:</strong>
                <ul class="list-disc list-inside mt-2 text-sm text-red-400">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Form Input --}}
            <div class="lg:col-span-2">
                <form action="{{ route('admin.jadwal.bulk.store') }}" method="POST" id="bulkForm"
                      class="bg-neutral-900 border border-neutral-800 rounded-2xl p-8 shadow-xl space-y-6">
                    @csrf

                    {{-- Film --}}
                    <div>
                        <label for="film_id" class="block text-sm font-semibold text-gray-300 mb-2">
                            1. Pilih Film <span class="text-red-500">*</span>
                        </label>
                        <select name="film_id" id="film_id" required
                                class="w-full bg-neutral-800 border border-neutral-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-red-600"
                                onchange="updatePreview()">
                            <option value="">-- Pilih Film --</option>
                            @foreach ($films as $film)
                                <option value="{{ $film->id }}" data-judul="{{ $film->judul }}">
                                    {{ $film->judul }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Periode Tanggal --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            2. Periode Tanggal <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Dari</label>
                                <input type="date" name="tanggal_dari" id="tanggal_dari" required
                                       class="w-full bg-neutral-800 border border-neutral-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-red-600"
                                       onchange="updatePreview()">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Sampai</label>
                                <input type="date" name="tanggal_sampai" id="tanggal_sampai" required
                                       class="w-full bg-neutral-800 border border-neutral-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-red-600"
                                       onchange="updatePreview()">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Total: <span id="totalHari" class="font-bold text-white">0</span> hari
                        </p>
                    </div>

                    {{-- Studio (Multiple Checkbox) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-3">
                            3. Pilih Studio <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($studios as $studio)
                                <label class="flex items-center gap-3 p-3 bg-neutral-800 rounded-lg border border-neutral-700 hover:border-neutral-600 cursor-pointer transition">
                                    <input type="checkbox" name="studio_ids[]" value="{{ $studio->id }}" 
                                           class="studio-checkbox w-5 h-5 text-red-600 bg-neutral-700 border-neutral-600 rounded focus:ring-red-600"
                                           onchange="updatePreview()">
                                    <div class="flex-1">
                                        <span class="font-semibold text-sm">{{ $studio->nama_studio }}</span>
                                        <span class="text-xs text-gray-500 block">{{ $studio->total_kursi }} kursi</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Jam Tayang (Multiple Checkbox) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-3">
                            4. Pilih Jam Tayang <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                            @foreach ($jamTersedia as $jam)
                                <label class="flex items-center justify-center p-3 bg-neutral-800 rounded-lg border border-neutral-700 hover:border-neutral-600 cursor-pointer transition text-center">
                                    <input type="checkbox" name="jam_tayang[]" value="{{ $jam }}" 
                                           class="jam-checkbox hidden"
                                           onchange="updatePreview(); this.parentElement.classList.toggle('bg-red-600', this.checked); this.parentElement.classList.toggle('border-red-500', this.checked);">
                                    <span class="font-mono font-semibold text-sm">{{ \Carbon\Carbon::parse($jam)->format('H:i') }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Harga Dasar --}}
                    <div>
                        <label for="harga_dasar" class="block text-sm font-semibold text-gray-300 mb-2">
                            5. Harga Tiket (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="harga_dasar" id="harga_dasar" value="35000" min="0" required
                               class="w-full bg-neutral-800 border border-neutral-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-red-600"
                               placeholder="35000">
                    </div>

                    {{-- Tombol --}}
                    <div class="flex justify-end gap-3 pt-6 border-t border-neutral-800">
                        <a href="{{ route('admin.jadwal.index') }}" 
                           class="px-5 py-3 bg-neutral-700 hover:bg-neutral-600 rounded-lg text-gray-200 font-semibold transition">
                            Batal
                        </a>
                        <button type="submit" id="submitBtn"
                                class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-lg font-semibold shadow-lg transition hover:scale-105">
                            <i class="fas fa-magic mr-2"></i> Generate Jadwal
                        </button>
                    </div>
                </form>
            </div>

            {{-- Preview Panel --}}
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-neutral-900 to-neutral-950 border border-neutral-800 rounded-2xl p-6 sticky top-6">
                    <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                        </svg>
                        Preview
                    </h3>

                    <div class="space-y-4 text-sm">
                        <div class="p-3 bg-neutral-800/50 rounded-lg">
                            <p class="text-gray-500 text-xs mb-1">Film</p>
                            <p id="previewFilm" class="font-semibold">-</p>
                        </div>

                        <div class="p-3 bg-neutral-800/50 rounded-lg">
                            <p class="text-gray-500 text-xs mb-1">Periode</p>
                            <p id="previewPeriode" class="font-semibold">-</p>
                        </div>

                        <div class="p-3 bg-neutral-800/50 rounded-lg">
                            <p class="text-gray-500 text-xs mb-1">Studio</p>
                            <p id="previewStudio" class="font-semibold">-</p>
                        </div>

                        <div class="p-3 bg-neutral-800/50 rounded-lg">
                            <p class="text-gray-500 text-xs mb-1">Jam Tayang</p>
                            <p id="previewJam" class="font-semibold">-</p>
                        </div>

                        <div class="border-t border-neutral-700 pt-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Total Jadwal</span>
                                <span id="totalJadwal" class="text-3xl font-black text-green-400">0</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                = <span id="formula">0 studio × 0 hari × 0 jam</span>
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 p-3 bg-blue-900/20 border border-blue-700/30 rounded-lg">
                        <p class="text-xs text-blue-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            Jadwal yang bentrok akan otomatis dilewati
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function updatePreview() {
    // Film
    const filmSelect = document.getElementById('film_id');
    const filmText = filmSelect.options[filmSelect.selectedIndex]?.dataset.judul || '-';
    document.getElementById('previewFilm').textContent = filmText;

    // Tanggal
    const dari = document.getElementById('tanggal_dari').value;
    const sampai = document.getElementById('tanggal_sampai').value;
    
    let totalHari = 0;
    if (dari && sampai) {
        const date1 = new Date(dari);
        const date2 = new Date(sampai);
        totalHari = Math.ceil((date2 - date1) / (1000 * 60 * 60 * 24)) + 1;
        document.getElementById('previewPeriode').textContent = 
            `${new Date(dari).toLocaleDateString('id-ID', {day: 'numeric', month: 'short'})} - ${new Date(sampai).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'})}`;
    } else {
        document.getElementById('previewPeriode').textContent = '-';
    }
    document.getElementById('totalHari').textContent = totalHari;

    // Studio
    const studioCheckboxes = document.querySelectorAll('.studio-checkbox:checked');
    const studioCount = studioCheckboxes.length;
    document.getElementById('previewStudio').textContent = 
        studioCount > 0 ? `${studioCount} studio` : '-';

    // Jam
    const jamCheckboxes = document.querySelectorAll('.jam-checkbox:checked');
    const jamCount = jamCheckboxes.length;
    const jamList = Array.from(jamCheckboxes).map(cb => cb.value.substring(0, 5)).join(', ');
    document.getElementById('previewJam').textContent = 
        jamCount > 0 ? jamList : '-';

    // Total Jadwal
    const total = studioCount * totalHari * jamCount;
    document.getElementById('totalJadwal').textContent = total;
    document.getElementById('formula').textContent = 
        `${studioCount} studio × ${totalHari} hari × ${jamCount} jam`;

    // Enable/disable submit button
    document.getElementById('submitBtn').disabled = total === 0;
}

// Init
document.addEventListener('DOMContentLoaded', () => {
    updatePreview();
});
</script>
@endsection