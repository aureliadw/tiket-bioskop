@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-neutral-950 text-white px-6 py-10">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold text-red-500">Kelola Jadwal Film</h1>
    <div class="flex gap-3">
        <a href="{{ route('admin.jadwal.bulk.create') }}" 
           class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-5 py-2 rounded-lg font-semibold transition shadow-lg">
            <i class="fas fa-magic mr-2"></i>Bulk Input
        </a>
    </div>
</div>

        {{-- Alert sukses --}}
        @if (session('success'))
            <div class="mb-6 bg-green-600/20 border border-green-600 text-green-300 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- Tabel Jadwal --}}
        <div class="overflow-x-auto bg-neutral-900 rounded-xl shadow-lg border border-neutral-800">
            <table class="min-w-full text-sm">
                <thead class="bg-neutral-800 text-gray-300 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left">#</th>
                        <th class="px-6 py-4 text-left">Film</th>
                        <th class="px-6 py-4 text-left">Studio</th>
                        <th class="px-6 py-4 text-left">Tanggal</th>
                        <th class="px-6 py-4 text-left">Jam</th>
                        <th class="px-6 py-4 text-left">Harga</th>
                        <th class="px-6 py-4 text-left">Kursi Tersedia</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-800">
                    @forelse ($jadwals as $index => $jadwal)
                        <tr class="hover:bg-neutral-800/40 transition">
                            <td class="px-6 py-4">{{ $loop->iteration + ($jadwals->currentPage() - 1) * $jadwals->perPage() }}</td>
                            <td class="px-6 py-4 font-medium">{{ $jadwal->film->judul ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $jadwal->studio->nama_studio }}</td>
                            <td class="px-6 py-4">{{ $jadwal->tanggal_tayang->format('d M Y') }}</td>
                            <td class="px-6 py-4">{{ $jadwal->jam_tayang->format('H:i') }}</td>
                            <td class="px-6 py-4">Rp {{ number_format($jadwal->harga_dasar, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ $jadwal->kursi_tersedia }}</td>
                            <td class="px-6 py-4">
                                @if ($jadwal->status_aktif)
                                    <span class="bg-green-600/20 text-green-400 px-3 py-1 rounded-full text-xs">Aktif</span>
                                @else
                                    <span class="bg-red-600/20 text-red-400 px-3 py-1 rounded-full text-xs">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.jadwal.edit', $jadwal->id) }}" 
                                       class="bg-yellow-500 hover:bg-yellow-600 text-black px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.jadwal.destroy', $jadwal->id) }}" method="POST" 
                                          onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-gray-400">
                                Belum ada jadwal film.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $jadwals->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection
