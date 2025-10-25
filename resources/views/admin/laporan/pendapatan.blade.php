@extends('layouts.admin')

@section('page-title', 'Laporan Pendapatan')
@section('page-subtitle', 'Analisis revenue dan trend pendapatan')

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
            <div class="w-8 h-8 bg-green-600/20 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-filter text-green-400"></i>
            </div>
            <h3 class="text-lg font-bold">Filter Periode</h3>
        </div>

        <form method="GET" action="{{ route('admin.laporan.pendapatan') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Tanggal Dari --}}
            <div>
                <label class="block text-xs text-gray-500 mb-2">Tanggal Dari</label>
                <input type="date" name="tanggal_dari" value="{{ $tanggalDari }}"
                       class="w-full bg-neutral-900 border border-neutral-800 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-600 focus:border-green-600 outline-none">
            </div>

            {{-- Tanggal Sampai --}}
            <div>
                <label class="block text-xs text-gray-500 mb-2">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai" value="{{ $tanggalSampai }}"
                       class="w-full bg-neutral-900 border border-neutral-800 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-600 focus:border-green-600 outline-none">
            </div>

            {{-- Button --}}
            <div class="flex items-end gap-3">
                <button type="submit"
                        class="px-6 py-2.5 bg-green-600 hover:bg-green-700 rounded-lg font-semibold text-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-search"></i>
                    Tampilkan
                </button>
                <a href="{{ route('admin.laporan.pendapatan') }}"
                   class="px-6 py-2.5 bg-neutral-800 hover:bg-neutral-700 rounded-lg font-semibold text-sm transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Total Pendapatan --}}
        <div class="bg-gradient-to-br from-green-950/50 to-neutral-900 border border-green-900/30 rounded-2xl p-6 shadow-xl">
            <div class="flex items-start justify-between mb-3">
                <div class="w-12 h-12 bg-green-600/20 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-money-bill-wave text-2xl text-green-400"></i>
                </div>
                <span class="px-2 py-1 bg-green-950/50 border border-green-900/50 rounded-lg text-xs text-green-400 font-bold">
                    +100%
                </span>
            </div>
            <p class="text-xs text-gray-500 mb-1">Total Pendapatan</p>
            <p class="text-3xl font-black text-green-400 mb-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500">Dari transaksi yang sudah dibayar</p>
        </div>

        {{-- Total Transaksi --}}
        <div class="bg-gradient-to-br from-blue-950/50 to-neutral-900 border border-blue-900/30 rounded-2xl p-6 shadow-xl">
            <div class="flex items-start justify-between mb-3">
                <div class="w-12 h-12 bg-blue-600/20 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-receipt text-2xl text-blue-400"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mb-1">Total Transaksi</p>
            <p class="text-3xl font-black text-blue-400 mb-1">{{ number_format($totalTransaksi) }}</p>
            <p class="text-xs text-gray-500">Transaksi berhasil</p>
        </div>

        {{-- Rata-rata Transaksi --}}
        <div class="bg-gradient-to-br from-purple-950/50 to-neutral-900 border border-purple-900/30 rounded-2xl p-6 shadow-xl">
            <div class="flex items-start justify-between mb-3">
                <div class="w-12 h-12 bg-purple-600/20 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-2xl text-purple-400"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mb-1">Rata-rata per Transaksi</p>
            <p class="text-3xl font-black text-purple-400 mb-1">Rp {{ number_format($rataRataTransaksi, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500">Average order value</p>
        </div>
    </div>

    {{-- Grafik Pendapatan Harian --}}
    <div class="bg-gradient-to-br from-neutral-900 to-neutral-950 border border-neutral-800 rounded-2xl p-6 mb-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-green-600/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-chart-area text-green-400"></i>
                </div>
                <h3 class="text-lg font-bold">Trend Pendapatan Harian</h3>
            </div>
        </div>

        {{-- Chart Canvas --}}
        <canvas id="pendapatanChart" class="w-full" style="max-height: 300px;"></canvas>
    </div>

    {{-- Grid: Pendapatan per Metode & Top Films --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        {{-- Pendapatan per Metode Pembayaran --}}
        <div class="bg-gradient-to-br from-neutral-900 to-neutral-950 border border-neutral-800 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 bg-blue-600/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-credit-card text-blue-400"></i>
                </div>
                <h3 class="text-lg font-bold">Pendapatan per Metode</h3>
            </div>

            <div class="space-y-3">
                @forelse($pendapatanPerMetode as $metode)
                    <div class="p-4 bg-neutral-950/50 rounded-xl border border-neutral-800">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold capitalize">{{ str_replace('_', ' ', $metode->metode_pembayaran) }}</span>
                            <span class="text-xs text-gray-500">{{ $metode->jumlah }} transaksi</span>
                        </div>
                        <p class="text-2xl font-bold text-green-400">Rp {{ number_format($metode->total, 0, ',', '.') }}</p>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">Tidak ada data</p>
                @endforelse
            </div>
        </div>

        {{-- Top 5 Film --}}
        <div class="bg-gradient-to-br from-neutral-900 to-neutral-950 border border-neutral-800 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 bg-red-600/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-trophy text-red-400"></i>
                </div>
                <h3 class="text-lg font-bold">Top 5 Film Terlaris</h3>
            </div>

            <div class="space-y-3">
                @forelse($topFilms as $index => $film)
                    <div class="flex items-start gap-3 p-3 bg-neutral-950/50 rounded-xl border border-neutral-800 hover:border-red-600/30 transition">
                        <div class="w-8 h-8 bg-red-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-red-400">#{{ $index + 1 }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm mb-1 truncate">{{ $film->judul }}</p>
                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                <span>{{ $film->jumlah_transaksi }} transaksi</span>
                                <span class="text-green-400 font-bold">Rp {{ number_format($film->total_pendapatan, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">Tidak ada data</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Export PDF Button --}}
    <div class="flex justify-end">
        <form method="GET" action="{{ route('admin.laporan.pendapatan.pdf') }}" class="inline">
            <input type="hidden" name="tanggal_dari" value="{{ $tanggalDari }}">
            <input type="hidden" name="tanggal_sampai" value="{{ $tanggalSampai }}">
            <button type="submit"
                    class="px-6 py-3 bg-red-600 hover:bg-red-700 rounded-xl font-bold shadow-lg hover:shadow-red-600/30 transition-all flex items-center gap-2 hover:scale-105">
                <i class="fa-solid fa-file-pdf text-lg"></i>
                Download Laporan PDF
            </button>
        </form>
    </div>

</div>

{{-- Chart.js Script --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('pendapatanChart').getContext('2d');
    
    const chartData = {
        labels: [
            @foreach($pendapatanHarian as $item)
                '{{ \Carbon\Carbon::parse($item->tanggal)->format("d M") }}',
            @endforeach
        ],
        datasets: [{
            label: 'Pendapatan Harian',
            data: [
                @foreach($pendapatanHarian as $item)
                    {{ $item->total }},
                @endforeach
            ],
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            borderColor: 'rgba(34, 197, 94, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    };

    new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    displayColors: false,
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
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)'
                    },
                    ticks: {
                        color: '#9ca3af',
                        callback: function(value) {
                            return 'Rp ' + (value / 1000) + 'k';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#9ca3af'
                    }
                }
            }
        }
    });
</script>
@endsection