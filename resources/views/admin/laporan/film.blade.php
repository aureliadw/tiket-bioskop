@extends('layouts.admin')

@section('page-title', 'Laporan Film')
@section('page-subtitle', 'Performa dan statistik setiap film')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- Back Button --}}
    <div class="mb-6">
        <a href="{{ route('admin.laporan.index') }}" 
           class="inline-flex items-center gap-2 text-gray-400 hover:text-white transition text-sm">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Laporan</span>
        </a>
    </div>

    {{-- Filter Section --}}
    <div class="bg-gradient-to-br from-neutral-900 to-neutral-950 border border-neutral-800 rounded-2xl p-6 mb-6 shadow-xl">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 bg-red-600/20 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-filter text-red-400"></i>
            </div>
            <h3 class="text-lg font-bold">Filter Laporan</h3>
        </div>

        <form method="GET" action="{{ route('admin.laporan.film') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Tanggal Dari --}}
            <div>
                <label class="block text-xs text-gray-500 mb-2">Tanggal Dari</label>
                <input type="date" name="tanggal_dari" value="{{ $tanggalDari }}"
                       class="w-full bg-neutral-900 border border-neutral-800 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-600 focus:border-red-600 outline-none">
            </div>

            {{-- Tanggal Sampai --}}
            <div>
                <label class="block text-xs text-gray-500 mb-2">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai" value="{{ $tanggalSampai }}"
                       class="w-full bg-neutral-900 border border-neutral-800 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-600 focus:border-red-600 outline-none">
            </div>

            {{-- Status Film --}}
            <div>
                <label class="block text-xs text-gray-500 mb-2">Status Film</label>
                <select name="status_film"
                        class="w-full bg-neutral-900 border border-neutral-800 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-600 focus:border-red-600 outline-none">
                    <option value="all" {{ $statusFilm == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="sedang_tayang" {{ $statusFilm == 'sedang_tayang' ? 'selected' : '' }}>Sedang Tayang</option>
                    <option value="akan_tayang" {{ $statusFilm == 'akan_tayang' ? 'selected' : '' }}>Akan Tayang</option>
                    <option value="tidak_tayang" {{ $statusFilm == 'tidak_tayang' ? 'selected' : '' }}>Tidak Tayang</option>
                </select>
            </div>

            {{-- Button --}}
            <div class="flex items-end gap-3">
                <button type="submit"
                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 rounded-lg font-semibold text-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-search"></i>
                    Tampilkan
                </button>
                <a href="{{ route('admin.laporan.film') }}"
                   class="px-6 py-2.5 bg-neutral-800 hover:bg-neutral-700 rounded-lg font-semibold text-sm transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-950/30 to-neutral-900 border border-blue-900/30 rounded-xl p-5">
            <p class="text-xs text-gray-500 mb-1">Total Film</p>
            <p class="text-3xl font-black text-blue-400">{{ $totalFilm }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-950/30 to-neutral-900 border border-green-900/30 rounded-xl p-5">
            <p class="text-xs text-gray-500 mb-1">Total Pendapatan</p>
            <p class="text-2xl font-black text-green-400">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-950/30 to-neutral-900 border border-purple-900/30 rounded-xl p-5">
            <p class="text-xs text-gray-500 mb-1">Total Penonton</p>
            <p class="text-3xl font-black text-purple-400">{{ number_format($totalPenonton) }}</p>
        </div>
        <div class="bg-gradient-to-br from-red-950/30 to-neutral-900 border border-red-900/30 rounded-xl p-5">
            <p class="text-xs text-gray-500 mb-1">Film Terlaris</p>
            <p class="text-lg font-black text-red-400 truncate">{{ $filmTerlaris->judul ?? '-' }}</p>
        </div>
    </div>

    {{-- Export Button --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Data Performa Film</h2>
        <form method="GET" action="{{ route('admin.laporan.film.pdf') }}" class="inline">
            <input type="hidden" name="tanggal_dari" value="{{ $tanggalDari }}">
            <input type="hidden" name="tanggal_sampai" value="{{ $tanggalSampai }}">
            <input type="hidden" name="status_film" value="{{ $statusFilm }}">
            <button type="submit"
                    class="px-5 py-2.5 bg-red-600 hover:bg-red-700 rounded-lg font-semibold text-sm transition flex items-center gap-2 shadow-lg hover:shadow-red-600/30">
                <i class="fa-solid fa-file-pdf"></i>
                Download PDF
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-neutral-900 border border-neutral-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-950 border-b border-neutral-800">
                    <tr>
                        <th class="px-4 py-4 text-left font-bold text-gray-400 text-xs uppercase">No</th>
                        <th class="px-4 py-4 text-left font-bold text-gray-400 text-xs uppercase">Judul Film</th>
                        <th class="px-4 py-4 text-left font-bold text-gray-400 text-xs uppercase">Genre</th>
                        <th class="px-4 py-4 text-left font-bold text-gray-400 text-xs uppercase">Status</th>
                        <th class="px-4 py-4 text-center font-bold text-gray-400 text-xs uppercase">Transaksi</th>
                        <th class="px-4 py-4 text-center font-bold text-gray-400 text-xs uppercase">Penonton</th>
                        <th class="px-4 py-4 text-right font-bold text-gray-400 text-xs uppercase">Pendapatan</th>
                        <th class="px-4 py-4 text-center font-bold text-gray-400 text-xs uppercase">Rating</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-800">
                    @forelse($films as $index => $film)
                        <tr class="hover:bg-neutral-950/50 transition">
                            <td class="px-4 py-4 text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-4 py-4">
                                <p class="font-semibold">{{ $film->judul }}</p>
                                <p class="text-xs text-gray-500">{{ $film->durasi }} menit</p>
                            </td>
                            <td class="px-4 py-4 text-gray-300 text-xs">
                                {{ $film->genre }}
                            </td>
                            <td class="px-4 py-4">
                                @if($film->status == 'sedang_tayang')
                                    <span class="px-2 py-1 bg-green-950/30 border border-green-900/50 text-green-400 rounded text-xs font-bold">
                                        Tayang
                                    </span>
                                @elseif($film->status == 'akan_tayang')
                                    <span class="px-2 py-1 bg-blue-950/30 border border-blue-900/50 text-blue-400 rounded text-xs font-bold">
                                        Akan Tayang
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-gray-950/30 border border-gray-900/50 text-gray-400 rounded text-xs font-bold">
                                        Tidak Tayang
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center font-semibold text-blue-400">
                                {{ $film->total_transaksi ?? 0 }}
                            </td>
                            <td class="px-4 py-4 text-center font-semibold text-purple-400">
                                {{ $film->total_penonton ?? 0 }}
                            </td>
                            <td class="px-4 py-4 text-right font-bold text-green-400">
                                Rp {{ number_format($film->total_pendapatan ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-star text-yellow-500 text-xs"></i>
                                    <span class="font-semibold">{{ $film->rating }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                <i class="fa-solid fa-film text-4xl mb-2 opacity-20"></i>
                                <p>Tidak ada data film</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection