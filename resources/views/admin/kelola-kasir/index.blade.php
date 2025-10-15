@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-neutral-950 text-white px-6 py-10">
    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-red-500">Kelola Kasir</h1>
            <div class="flex gap-3">
                <a href="{{ route('admin.kasir.create') }}" 
                   class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-5 py-2 rounded-lg font-semibold transition shadow-lg">
                    <i class="fas fa-user-plus mr-2"></i>Tambah Kasir
                </a>
            </div>
        </div>

        {{-- Alert sukses --}}
        @if (session('success'))
            <div class="mb-6 bg-green-600/20 border border-green-600 text-green-300 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- Tabel Kasir --}}
        <div class="overflow-x-auto bg-neutral-900 rounded-xl shadow-lg border border-neutral-800">
            <table class="min-w-full text-sm">
                <thead class="bg-neutral-800 text-gray-300 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left">#</th>
                        <th class="px-6 py-4 text-left">Nama Lengkap</th>
                        <th class="px-6 py-4 text-left">Email</th>
                        <th class="px-6 py-4 text-left">Nomor Telepon</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-800">
                    @forelse ($kasir as $k)
                        <tr class="hover:bg-neutral-800/40 transition">
                            <td class="px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 font-medium text-gray-100">{{ $k->nama_lengkap }}</td>
                            <td class="px-6 py-4 text-gray-400">{{ $k->email }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $k->phone }}</td>
                            <td class="px-6 py-4">
                                @if ($k->status_aktif)
                                    <span class="bg-green-600/20 text-green-400 px-3 py-1 rounded-full text-xs">Aktif</span>
                                @else
                                    <span class="bg-red-600/20 text-red-400 px-3 py-1 rounded-full text-xs">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.kasir.edit', $k->id) }}" 
                                       class="bg-yellow-500 hover:bg-yellow-600 text-black px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.kasir.destroy', $k->id) }}" method="POST" 
                                          onsubmit="return confirm('Yakin ingin menghapus kasir ini?')" class="inline-block">
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
                            <td colspan="6" class="text-center py-8 text-gray-400">
                                <i class="fas fa-user-slash text-red-500 text-2xl mb-2 block"></i>
                                Belum ada data kasir.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $kasir->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection
