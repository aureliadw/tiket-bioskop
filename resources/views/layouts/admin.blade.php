<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | HappyCine</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a2d04b5e2c.js" crossorigin="anonymous"></script>
    
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Animasi dan efek glow halus */
        .nav-item {
            transition: all 0.25s ease;
            position: relative;
        }
        .nav-item:hover {
            background: rgba(239,68,68,0.1);
            transform: translateX(2px);
        }
        .active-link {
            background: linear-gradient(to right, rgba(239,68,68,0.15), transparent);
            border-left: 3px solid #ef4444;
            color: #f87171 !important;
        }

        /* ✅ LOGO STYLE WITH STAR */
        .logo-text {
            font-family: 'Righteous', cursive;
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            transition: all 0.3s ease;
        }
        
        .logo-text:hover {
            letter-spacing: 0.05em;
            filter: drop-shadow(0 0 8px rgba(239, 68, 68, 0.6));
        }
        
        .star-icon {
            animation: starPulse 2s ease-in-out infinite;
        }
        
        @keyframes starPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        /* Submenu Animation */
        .submenu-item {
            transition: all 0.2s ease;
        }
        .submenu-item:hover {
            background: rgba(239,68,68,0.08);
            transform: translateX(2px);
        }

        /* Scrollbar Custom */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #171717;
        }
        ::-webkit-scrollbar-thumb {
            background: #404040;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #525252;
        }
    </style>
</head>
<body class="bg-neutral-950 text-white flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="w-72 bg-gradient-to-b from-neutral-900 to-neutral-950 border-r border-neutral-800 flex flex-col shadow-2xl">
        {{-- Logo Header --}}
        <div class="px-6 py-6 border-b border-neutral-800">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1 group">
                <h1 class="logo-text flex items-center justify-center gap-1">
                    <span class="text-red-600">H</span>
                    <svg xmlns="http://www.w3.org/2000/svg" 
                         class="w-6 h-6 text-white star-icon inline-block" 
                         fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l2.39 7.26h7.63l-6.18 4.49 2.36 7.25L12 16.77l-6.2 4.23 2.36-7.25L2 9.26h7.61z"/>
                    </svg>
                    <span class="text-red-600">PPY</span>
                    <span class="text-white">CINE</span>
                </h1>
            </a>
            <p class="text-xs text-gray-500 mt-2 ml-1 font-medium">Admin Panel</p>
        </div>

        {{-- Navigation Menu --}}
        <nav class="flex-1 px-4 py-6 space-y-1 text-sm overflow-y-auto">

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl nav-item {{ request()->routeIs('admin.dashboard') ? 'active-link' : 'text-gray-300 hover:text-white' }}">
                <i class="fa-solid fa-house w-5"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            {{-- Divider --}}
            <div class="pt-4 pb-2 px-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelola Data</p>
            </div>

            {{-- Kelola Film --}}
            <a href="{{ route('admin.film.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl nav-item {{ request()->routeIs('admin.film*') ? 'active-link' : 'text-gray-300 hover:text-white' }}">
                <i class="fa-solid fa-film w-5"></i>
                <span class="font-medium">Kelola Film</span>
            </a>

            {{-- Kelola Jadwal --}}
            <a href="{{ route('admin.jadwal.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl nav-item {{ request()->routeIs('admin.jadwal*') ? 'active-link' : 'text-gray-300 hover:text-white' }}">
                <i class="fa-solid fa-calendar-days w-5"></i>
                <span class="font-medium">Kelola Jadwal</span>
            </a>

            {{-- Kelola Studio --}}
            <a href="{{ route('admin.studio.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl nav-item {{ request()->routeIs('admin.studio*') ? 'active-link' : 'text-gray-300 hover:text-white' }}">
                <i class="fa-solid fa-couch w-5"></i>
                <span class="font-medium">Kelola Studio</span>
            </a>

            {{-- Divider --}}
            <div class="pt-4 pb-2 px-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Transaksi</p>
            </div>

            {{-- Pemesanan --}}
            <a href="{{ route('admin.pemesanan') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl nav-item {{ request()->routeIs('admin.pemesanan') ? 'active-link' : 'text-gray-300 hover:text-white' }}">
                <i class="fa-solid fa-ticket w-5"></i>
                <span class="font-medium">Pemesanan</span>
            </a>

            {{-- ✅ LAPORAN (NEW) --}}
            <div x-data="{ open: {{ request()->routeIs('admin.laporan*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open" 
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl nav-item text-gray-300 hover:text-white {{ request()->routeIs('admin.laporan*') ? 'active-link' : '' }}">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-chart-bar w-5"></i>
                        <span class="font-medium">Laporan</span>
                    </span>
                    <i :class="open ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'" class="transition text-xs"></i>
                </button>

                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="pl-12 space-y-1 mt-1">
                    <a href="{{ route('admin.laporan.transaksi') }}" 
                       class="block px-4 py-2.5 rounded-lg submenu-item {{ request()->routeIs('admin.laporan.transaksi*') ? 'text-red-400 bg-red-950/20' : 'text-gray-400 hover:text-white' }}">
                        <span class="font-medium text-sm">Laporan Transaksi</span>
                    </a>
                    <a href="{{ route('admin.laporan.pendapatan') }}" 
                       class="block px-4 py-2.5 rounded-lg submenu-item {{ request()->routeIs('admin.laporan.pendapatan*') ? 'text-red-400 bg-red-950/20' : 'text-gray-400 hover:text-white' }}">
                        <span class="font-medium text-sm">Laporan Pendapatan</span>
                    </a>
                    <a href="{{ route('admin.laporan.film') }}" 
                       class="block px-4 py-2.5 rounded-lg submenu-item {{ request()->routeIs('admin.laporan.film*') ? 'text-red-400 bg-red-950/20' : 'text-gray-400 hover:text-white' }}">
                        <span class="font-medium text-sm">Laporan Film</span>
                    </a>
                </div>
            </div>

            {{-- Divider --}}
            <div class="pt-4 pb-2 px-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengguna</p>
            </div>

            {{-- Submenu: Data Pengguna --}}
            <div x-data="{ open: {{ request()->routeIs('admin.pelanggan*', 'admin.kasir*', 'admin.owner*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open" 
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl nav-item text-gray-300 hover:text-white {{ request()->routeIs('admin.pelanggan*', 'admin.kasir*', 'admin.owner*') ? 'active-link' : '' }}">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-users w-5"></i>
                        <span class="font-medium">Data Pengguna</span>
                    </span>
                    <i :class="open ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'" class="transition text-xs"></i>
                </button>

                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="pl-12 space-y-1 mt-1">
                    <a href="{{ route('admin.pelanggan.index') }}" 
                       class="block px-4 py-2.5 rounded-lg submenu-item {{ request()->routeIs('admin.pelanggan*') ? 'text-red-400 bg-red-950/20' : 'text-gray-400 hover:text-white' }}">
                        <span class="font-medium text-sm">Pelanggan</span>
                    </a>
                    <a href="{{ route('admin.kasir.index') }}" 
                       class="block px-4 py-2.5 rounded-lg submenu-item {{ request()->routeIs('admin.kasir*') ? 'text-red-400 bg-red-950/20' : 'text-gray-400 hover:text-white' }}">
                        <span class="font-medium text-sm">Kasir</span>
                    </a>
                    <a href="{{ route('admin.owner.index') }}" 
                       class="block px-4 py-2.5 rounded-lg submenu-item {{ request()->routeIs('admin.owner*') ? 'text-red-400 bg-red-950/20' : 'text-gray-400 hover:text-white' }}">
                        <span class="font-medium text-sm">Owner</span>
                    </a>
                </div>
            </div>
        </nav>

        {{-- Sidebar Footer --}}
        <div class="px-6 py-4 border-t border-neutral-800">
            <div class="flex items-center gap-3 text-xs text-gray-500">
                <i class="fa-solid fa-circle-info"></i>
                <span>Version 1.0.0</span>
            </div>
        </div>
    </aside>

    {{-- Main Content Area --}}
    <main class="flex-1 overflow-y-auto bg-neutral-950">
        {{-- Header --}}
        <header class="bg-gradient-to-r from-neutral-900 to-neutral-950 border-b border-neutral-800 px-8 py-5 flex items-center justify-between sticky top-0 z-10 shadow-lg backdrop-blur-sm">
            <div>
                <h2 class="text-xl font-bold tracking-tight">
                    @yield('page-title', 'Dashboard')
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    @yield('page-subtitle', 'Manage your cinema system')
                </p>
            </div>
            
            <div class="flex items-center space-x-5">
                {{-- Notifications Icon (Optional) --}}
                <button class="relative text-gray-400 hover:text-white transition">
                    <i class="fa-solid fa-bell text-lg"></i>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-600 rounded-full"></span>
                </button>

                {{-- Profile Section --}}
                <div class="flex items-center space-x-3 pl-5 border-l border-neutral-800">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-600 to-red-700 rounded-full flex items-center justify-center font-bold text-sm shadow-lg">
                        {{ strtoupper(substr(Auth::user()->nama_lengkap ?? 'A', 0, 1)) }}
                    </div>
                    <div class="hidden md:block">
                        <p class="text-sm font-semibold">{{ Auth::user()->nama_lengkap ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-500">Administrator</p>
                    </div>
                </div>

                {{-- Logout Button --}}
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button 
                        class="px-5 py-2.5 text-sm rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold transition-all flex items-center gap-2 shadow-lg hover:shadow-red-600/30 hover:scale-105">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </header>

        {{-- Page Content --}}
        <section class="p-8">
            @yield('content')
        </section>
    </main>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>