@extends('layouts.owner')

@section('title', 'Owner Dashboard - HappyCine')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Executive Dashboard</h1>
        <p class="text-gray-600">Owner: {{ auth()->user()->nama_lengkap }} | {{ now()->format('d F Y') }}</p>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Today Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold uppercase opacity-90">Today</h3>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-3xl font-bold mb-1">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
            <p class="text-sm opacity-90">{{ number_format($todayTickets) }} kursi terjual</p>
        </div>

        <!-- Week Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold uppercase opacity-90">Minggu Ini</h3>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <p class="text-3xl font-bold mb-1">Rp {{ number_format($weekRevenue, 0, ',', '.') }}</p>
            <p class="text-sm opacity-90">{{ number_format($weekTickets) }} kursi terjual</p>
        </div>

        <!-- Month Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold uppercase opacity-90">Bulan Ini</h3>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <p class="text-3xl font-bold mb-1">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</p>
            <p class="text-sm opacity-90">{{ number_format($monthTickets) }} kursi terjual</p>
        </div>

        <!-- Year Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold uppercase opacity-90">Tahun Ini</h3>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
            <p class="text-3xl font-bold mb-1">Rp {{ number_format($yearRevenue, 0, ',', '.') }}</p>
            <p class="text-sm opacity-90">{{ number_format($yearTickets) }} kursi terjual</p>
        </div>
    </div>

    <!-- Revenue Trend Chart & Online/Offline Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Revenue Trend (30 Days) -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Tren Revenue (30 Hari Terakhir)</h2>
            <canvas id="revenueTrendChart" height="100"></canvas>
        </div>

        <!-- Online vs Offline -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Channel Penjualan</h2>
            <canvas id="channelChart"></canvas>
            <div class="mt-4 space-y-2">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-600">Online</span>
                    </div>
                    <span class="font-semibold text-gray-800">Rp {{ number_format($onlineRevenue, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-gray-400 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-600">Offline</span>
                    </div>
                    <span class="font-semibold text-gray-800">Rp {{ number_format($offlineRevenue, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Films & Occupancy Rate -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Performing Films -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Film Terlaris (Bulan Ini)</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 text-sm font-semibold text-gray-600">#</th>
                            <th class="text-left py-2 text-sm font-semibold text-gray-600">Film</th>
                            <th class="text-right py-2 text-sm font-semibold text-gray-600">Revenue</th>
                            <th class="text-right py-2 text-sm font-semibold text-gray-600">Kursi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topFilms as $index => $film)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 text-gray-700">{{ $index + 1 }}</td>
                            <td class="py-3 font-medium text-gray-800">{{ $film->judul }}</td>
                            <td class="py-3 text-right text-gray-700">Rp {{ number_format($film->revenue, 0, ',', '.') }}</td>
                            <td class="py-3 text-right text-gray-700">{{ number_format($film->tickets) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-gray-500">Belum ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Studio Occupancy Rate -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Occupancy Rate Studio (Bulan Ini)</h2>
            <div class="space-y-4">
                @forelse($occupancyRates as $studio)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $studio->nama_studio }}</span>
                        <span class="text-sm font-bold text-gray-800">{{ number_format($studio->occupancy_rate, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full transition-all duration-500" 
                             style="width: {{ min($studio->occupancy_rate, 100) }}%"></div>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-xs text-gray-500">{{ number_format($studio->tickets_sold) }} kursi</span>
                        <span class="text-xs text-gray-500">{{ number_format($studio->total_shows) }} shows</span>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">Belum ada data</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-8 flex flex-wrap gap-4">
        <a href="{{ route('owner.revenue.report') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow transition duration-200">
            📊 Laporan Revenue Detail
        </a>
        <a href="{{ route('owner.film.performance') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg shadow transition duration-200">
            🎬 Analisis Performa Film
        </a>
        <a href="{{ route('owner.export.pdf') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow transition duration-200">
            📄 Export ke PDF
        </a>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Revenue Trend Chart
    const revenueTrendCtx = document.getElementById('revenueTrendChart').getContext('2d');
    const revenueTrendData = {
        labels: [
            @foreach($revenueTrend as $item)
                '{{ \Carbon\Carbon::parse($item->date)->format("d M") }}',
            @endforeach
        ],
        datasets: [{
            label: 'Revenue',
            data: [
                @foreach($revenueTrend as $item)
                    {{ $item->revenue }},
                @endforeach
            ],
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    };

    new Chart(revenueTrendCtx, {
        type: 'line',
        data: revenueTrendData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                        }
                    }
                }
            }
        }
    });

    // Channel Chart (Pie)
    const channelCtx = document.getElementById('channelChart').getContext('2d');
    new Chart(channelCtx, {
        type: 'doughnut',
        data: {
            labels: ['Online', 'Offline'],
            datasets: [{
                data: [{{ $onlineRevenue }}, {{ $offlineRevenue }}],
                backgroundColor: ['rgb(59, 130, 246)', 'rgb(156, 163, 175)'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endsection