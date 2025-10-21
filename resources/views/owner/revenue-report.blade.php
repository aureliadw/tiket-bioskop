@extends('layouts.owner')

@section('title', 'Laporan Revenue - Owner Dashboard')
@section('page-title', 'Laporan Revenue Detail')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header & Filter -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Laporan Revenue Detail</h1>
                <p class="text-gray-600">Analisis pendapatan harian dengan breakdown lengkap</p>
            </div>
            <a href="{{ route('owner.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">
                ← Kembali ke Dashboard
            </a>
        </div>

        <!-- Date Filter -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('owner.revenue.report') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition font-semibold">
                    🔍 Filter
                </button>
                <a href="{{ route('owner.revenue.report') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition font-semibold">
                    Reset
                </a>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="text-sm opacity-90 mb-2">Total Revenue</div>
            <div class="text-3xl font-bold">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="text-sm opacity-90 mb-2">Total Kursi Terjual</div>
            <div class="text-3xl font-bold">{{ number_format($summary['total_tickets']) }}</div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="text-sm opacity-90 mb-2">Total Transaksi</div>
            <div class="text-3xl font-bold">{{ number_format($summary['total_transactions']) }}</div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="text-sm opacity-90 mb-2">Rata-rata Transaksi</div>
            <div class="text-3xl font-bold">Rp {{ number_format($summary['avg_transaction'], 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Online vs Offline Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Revenue by Channel</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center pb-3 border-b">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-blue-500 rounded mr-3"></div>
                        <span class="font-medium text-gray-700">Online</span>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-gray-800">Rp {{ number_format($summary['online_revenue'], 0, ',', '.') }}</div>
                        <div class="text-sm text-gray-500">
                            {{ $summary['total_revenue'] > 0 ? number_format(($summary['online_revenue'] / $summary['total_revenue']) * 100, 1) : 0 }}%
                        </div>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-gray-400 rounded mr-3"></div>
                        <span class="font-medium text-gray-700">Offline</span>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-gray-800">Rp {{ number_format($summary['offline_revenue'], 0, ',', '.') }}</div>
                        <div class="text-sm text-gray-500">
                            {{ $summary['total_revenue'] > 0 ? number_format(($summary['offline_revenue'] / $summary['total_revenue']) * 100, 1) : 0 }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Quick Statistics</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Periode</span>
                    <span class="font-semibold text-gray-800">
                        {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Hari</span>
                    <span class="font-semibold text-gray-800">
                        {{ \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1 }} hari
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Avg. Revenue/Hari</span>
                    <span class="font-semibold text-gray-800">
                        Rp {{ number_format($summary['total_revenue'] / max(1, $dailyRevenue->count()), 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Avg. Kursi/Transaksi</span>
                    <span class="font-semibold text-gray-800">
                        {{ $summary['total_transactions'] > 0 ? number_format($summary['total_tickets'] / $summary['total_transactions'], 1) : 0 }} kursi
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Revenue Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-800">Revenue Harian</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Online</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Offline</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Kursi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Transaksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($dailyRevenue as $day)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($day->date)->locale('id')->isoFormat('dddd') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-bold text-gray-900">Rp {{ number_format($day->revenue, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm text-blue-600">Rp {{ number_format($day->online_revenue, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm text-gray-600">Rp {{ number_format($day->offline_revenue, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm text-gray-900">{{ number_format($day->tickets) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm text-gray-900">{{ number_format($day->transactions) }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data untuk periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($dailyRevenue->count() > 0)
                <tfoot class="bg-gray-100 font-bold">
                    <tr>
                        <td class="px-6 py-4 text-gray-900">TOTAL</td>
                        <td class="px-6 py-4 text-right text-gray-900">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right text-blue-700">Rp {{ number_format($summary['online_revenue'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right text-gray-700">Rp {{ number_format($summary['offline_revenue'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right text-gray-900">{{ number_format($summary['total_tickets']) }}</td>
                        <td class="px-6 py-4 text-right text-gray-900">{{ number_format($summary['total_transactions']) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Export Buttons -->
    <div class="mt-6 flex gap-4">
        <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition font-semibold">
            🖨️ Print Laporan
        </button>
        <a href="{{ route('owner.export.pdf') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
           class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition font-semibold">
            📄 Export PDF
        </a>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .container, .container * {
            visibility: visible;
        }
        .container {
            position: absolute;
            left: 0;
            top: 0;
        }
        button, a[href*="export"] {
            display: none !important;
        }
    }
</style>
@endsection