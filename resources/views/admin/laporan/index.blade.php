@extends('layouts.admin')

@section('page-title', 'Laporan')
@section('page-subtitle', 'Pilih jenis laporan yang ingin dilihat')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-black tracking-tight bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent mb-2">
            Laporan & Statistik
        </h1>
        <p class="text-gray-400 text-sm">
            Lihat berbagai laporan dan analisis data bioskop Anda
        </p>
    </div>

    {{-- Grid Jenis Laporan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Laporan Transaksi --}}
        <a href="{{ route('admin.laporan.transaksi') }}" 
           class="group relative overflow-hidden bg-gradient-to-br from-blue-950/50 to-neutral-950 border border-blue-900/30 rounded-2xl p-6 hover:border-blue-600/50 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-blue-600/20">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/10 rounded-full blur-3xl group-hover:bg-blue-600/20 transition"></div>
            
            <div class="relative">
                <div class="w-14 h-14 bg-blue-600/20 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-600/30 transition">
                    <i class="fa-solid fa-file-invoice text-2xl text-blue-400"></i>
                </div>
                
                <h3 class="text-xl font-bold mb-2 group-hover:text-blue-400 transition">
                    Laporan Transaksi
                </h3>
                <p class="text-gray-400 text-sm mb-4">
                    Detail pemesanan, status pembayaran, metode bayar, dan riwayat transaksi lengkap
                </p>
                
                <div class="flex items-center gap-2 text-blue-400 text-sm font-semibold">
                    <span>Lihat Laporan</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
                </div>
            </div>
        </a>

        {{-- Laporan Pendapatan --}}
        <a href="{{ route('admin.laporan.pendapatan') }}" 
           class="group relative overflow-hidden bg-gradient-to-br from-green-950/50 to-neutral-950 border border-green-900/30 rounded-2xl p-6 hover:border-green-600/50 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-green-600/20">
            <div class="absolute top-0 right-0 w-32 h-32 bg-green-600/10 rounded-full blur-3xl group-hover:bg-green-600/20 transition"></div>
            
            <div class="relative">
                <div class="w-14 h-14 bg-green-600/20 rounded-xl flex items-center justify-center mb-4 group-hover:bg-green-600/30 transition">
                    <i class="fa-solid fa-chart-line text-2xl text-green-400"></i>
                </div>
                
                <h3 class="text-xl font-bold mb-2 group-hover:text-green-400 transition">
                    Laporan Pendapatan
                </h3>
                <p class="text-gray-400 text-sm mb-4">
                    Total revenue, trend pendapatan harian, analisis metode pembayaran, dan grafik
                </p>
                
                <div class="flex items-center gap-2 text-green-400 text-sm font-semibold">
                    <span>Lihat Laporan</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
                </div>
            </div>
        </a>

        {{-- Laporan Film --}}
        <a href="{{ route('admin.laporan.film') }}" 
           class="group relative overflow-hidden bg-gradient-to-br from-red-950/50 to-neutral-950 border border-red-900/30 rounded-2xl p-6 hover:border-red-600/50 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-red-600/20">
            <div class="absolute top-0 right-0 w-32 h-32 bg-red-600/10 rounded-full blur-3xl group-hover:bg-red-600/20 transition"></div>
            
            <div class="relative">
                <div class="w-14 h-14 bg-red-600/20 rounded-xl flex items-center justify-center mb-4 group-hover:bg-red-600/30 transition">
                    <i class="fa-solid fa-film text-2xl text-red-400"></i>
                </div>
                
                <h3 class="text-xl font-bold mb-2 group-hover:text-red-400 transition">
                    Laporan Film
                </h3>
                <p class="text-gray-400 text-sm mb-4">
                    Performa setiap film, total penonton, pendapatan per film, dan film terlaris
                </p>
                
                <div class="flex items-center gap-2 text-red-400 text-sm font-semibold">
                    <span>Lihat Laporan</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
                </div>
            </div>
        </a>
    </div>

    {{-- Quick Stats --}}
    <div class="mt-10 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-neutral-900 border border-neutral-800 rounded-xl p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-blue-600/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-calendar text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Periode Default</p>
                    <p class="font-bold">Bulan Ini</p>
                </div>
            </div>
        </div>

        <div class="bg-neutral-900 border border-neutral-800 rounded-xl p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-green-600/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-file-pdf text-green-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Format Export</p>
                    <p class="font-bold">PDF Ready</p>
                </div>
            </div>
        </div>

        <div class="bg-neutral-900 border border-neutral-800 rounded-xl p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-purple-600/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-filter text-purple-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Filter Custom</p>
                    <p class="font-bold">Available</p>
                </div>
            </div>
        </div>

        <div class="bg-neutral-900 border border-neutral-800 rounded-xl p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-red-600/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-clock text-red-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Update</p>
                    <p class="font-bold">Real-time</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection