@extends('layouts.owner')

@section('title', 'Analisis Performa Film - Owner Dashboard')
@section('page-title', 'Analisis Performa Film')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Analisis Performa Film</h1>
                <p class="text-gray-600">Performa detail setiap film berdasarkan revenue dan occupancy</p>
            </div>
            <a href="{{ route('owner.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">
                ← Kembali ke Dashboard
            </a>
        </div>

        <!-- Date Filter -->
        <div class="bg-white rounded-lg shadow p-6">
            <form method="GET" action="{{ route('owner.film.performance') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition font-semibold">
                    🔍 Filter
                </button>
                <a href="{{ route('owner.film.performance') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition font-semibold">
                    Reset
                </a>
            </form>
        </div>
    </div>

    <!-- Film Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse($filmPerformance as $film)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
            <div class="flex">
                <!-- Film Poster -->
                <div class="w-32 flex-shrink-0 bg-gray-200">
                    @if($film->poster_image)
                        <img src="{{ asset('storage/' . $film->poster_image) }}" 
                             alt="{{ $film->judul }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Film Details -->
                <div class="flex-1 p-6">
                    <div class="mb-4">
                        <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $film->judul }}</h3>
                        @if($film->genre)
                        <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">{{ $film->genre }}</span>
                        @endif
                    </div>

                    <!-- Revenue Section -->
                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <div class="text-3xl font-bold text-purple-600 mb-1">
                            Rp {{ number_format($film->total_revenue, 0, ',', '.') }}
                        </div>
                        <div class="text-sm text-gray-500">Total Revenue</div>
                    </div>

                    <!-- Metrics Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="text-2xl font-bold text-gray-800">{{ number_format($film->total_tickets) }}</div>
                            <div class="text-xs text-gray-500">Kursi Terjual</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-800">{{ number_format($film->total_shows) }}</div>
                            <div class="text-xs text-gray-500">Total Shows</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-800">{{ number_format($film->total_transactions) }}</div>
                            <div class="text-xs text-gray-500">Transaksi</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-800">{{ number_format($film->avg_tickets_per_booking, 1) }}</div>
                            <div class="text-xs text-gray-500">Avg. Kursi/Booking</div>
                        </div>
                    </div>

                    <!-- Occupancy Rate -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Occupancy Rate</span>
                            <span class="text-lg font-bold {{ $film->occupancy_rate >= 70 ? 'text-green-600' : ($film->occupancy_rate >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ number_format($film->occupancy_rate, 1) }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all duration-500 {{ $film->occupancy_rate >= 70 ? 'bg-gradient-to-r from-green-400 to-green-600' : ($film->occupancy_rate >= 50 ? 'bg-gradient-to-r from-yellow-400 to-yellow-600' : 'bg-gradient-to-r from-red-400 to-red-600') }}" 
                                 style="width: {{ min($film->occupancy_rate, 100) }}%"></div>
                        </div>
                        <div class="mt-1 text-xs text-gray-500">
                            @if($film->occupancy_rate >= 70)
                                ✅ Excellent - Film sangat diminati
                            @elseif($film->occupancy_rate >= 50)
                                ⚠️ Good - Performa cukup baik
                            @else
                                ❌ Poor - Perlu evaluasi jadwal/marketing
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-2 bg-white rounded-lg shadow p-12 text-center">
            <div class="text-gray-400 mb-4">
                <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm3 2h6v4H7V5zm8 8v2h1v-2h-1zm-2-2H7v4h6v-4zm2 0h1V9h-1v2zm1-4V5h-1v2h1zM5 5v2H4V5h1zm0 4H4v2h1V9zm-1 4h1v2H4v-2z" clip-rule="evenodd" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak Ada Data Film</h3>
            <p class="text-gray-500">Belum ada data performa film untuk periode ini</p>
        </div>
        @endforelse
    </div>

    <!-- Summary Table -->
    @if($filmPerformance->count() > 0)
    <div class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-800">Ringkasan Performa</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ranking</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Film</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Kursi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Shows</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Occupancy</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($filmPerformance as $index => $film)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($index == 0)
                                    <span class="text-2xl">🥇</span>
                                @elseif($index == 1)
                                    <span class="text-2xl">🥈</span>
                                @elseif($index == 2)
                                    <span class="text-2xl">🥉</span>
                                @else
                                    <span class="text-sm font-medium text-gray-700">{{ $index + 1 }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $film->judul }}</div>
                            @if($film->genre)
                            <div class="text-xs text-gray-500">{{ $film->genre }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-semibold text-gray-900">Rp {{ number_format($film->total_revenue, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm text-gray-900">{{ number_format($film->total_tickets) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm text-gray-900">{{ number_format($film->total_shows) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $film->occupancy_rate >= 70 ? 'bg-green-100 text-green-800' : ($film->occupancy_rate >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ number_format($film->occupancy_rate, 1) }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection