<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Owner Dashboard') - HappyCine</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- Google Fonts - Righteous untuk logo --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    
    <style>
        [x-cloak] { display: none !important; }
        
        .nav-item {
            transition: all 0.25s ease;
        }
        .nav-item:hover {
            background: linear-gradient(to right, rgba(239,68,68,0.15), transparent);
            transform: translateX(4px);
        }
        .active-link {
            background: linear-gradient(to right, rgba(239,68,68,0.25), transparent);
            border-left: 3px solid #ef4444;
        }
        
        /* ✅ Logo Style - Sama dengan layouts.app & kasir */
        .logo-text {
            font-family: 'Righteous', cursive;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            transition: all 0.3s ease;
        }
        
        .star-icon {
            animation: starPulse 2s ease-in-out infinite;
        }
        
        @keyframes starPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-neutral-950 text-white flex min-h-screen" x-data="{ sidebarOpen: true }">

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 z-50 w-64 bg-neutral-900 border-r border-neutral-800 transition-transform duration-300 ease-in-out lg:translate-x-0">
        
        {{-- ✅ Logo - Disamakan dengan layouts.app & kasir --}}
        <div class="px-6 py-5 border-b border-neutral-800 flex items-center justify-between">
            <div class="logo-text flex items-center justify-center gap-1">
                <span class="text-red-600">H</span>
                <svg xmlns="http://www.w3.org/2000/svg" 
                     class="w-6 h-6 text-white star-icon inline-block" 
                     fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l2.39 7.26h7.63l-6.18 4.49 2.36 7.25L12 16.77l-6.2 4.23 2.36-7.25L2 9.26h7.61z"/>
                </svg>
                <span class="text-red-600">PPY</span>
                <span class="text-white">CINE</span>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-red-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- User Info --}}
        <div class="px-5 py-4 border-b border-neutral-800 bg-neutral-950/70">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center font-bold">
                    {{ strtoupper(substr(auth()->user()->nama_lengkap ?? auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <p class="font-semibold text-sm">{{ auth()->user()->nama_lengkap ?? auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400">Owner</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-4 py-6 space-y-2 text-sm overflow-y-auto" style="max-height: calc(100vh - 220px)">
            
            <a href="{{ route('owner.dashboard') }}" 
               class="flex items-center gap-3 px-4 py-2 rounded-lg nav-item {{ request()->routeIs('owner.dashboard') ? 'active-link text-red-400' : 'text-gray-300 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('owner.revenue.report') }}" 
               class="flex items-center gap-3 px-4 py-2 rounded-lg nav-item {{ request()->routeIs('owner.revenue.report') ? 'active-link text-red-400' : 'text-gray-300 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span>Laporan Revenue</span>
            </a>

            <a href="{{ route('owner.film.performance') }}" 
               class="flex items-center gap-3 px-4 py-2 rounded-lg nav-item {{ request()->routeIs('owner.film.performance') ? 'active-link text-red-400' : 'text-gray-300 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                </svg>
                <span>Performa Film</span>
            </a>

            <div class="border-t border-neutral-800 my-4"></div>

            <a href="{{ route('owner.export.pdf') }}" 
               class="flex items-center gap-3 px-4 py-2 rounded-lg nav-item text-gray-300 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                </svg>
                <span>Export Laporan</span>
            </a>

        </nav>

        {{-- Logout --}}
        <div class="border-t border-neutral-800 px-4 py-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 text-sm rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="lg:pl-64 flex-1">
        
        {{-- Header --}}
        <header class="bg-neutral-900 border-b border-neutral-800 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-red-400 hover:text-red-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <h2 class="text-lg font-semibold tracking-wide">@yield('page-title', 'Dashboard')</h2>

            <div class="flex items-center gap-4">
                <div class="hidden md:block text-sm text-gray-400">
                    {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                </div>

                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center font-bold">
                        {{ strtoupper(substr(auth()->user()->nama_lengkap ?? auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="text-sm text-gray-300">{{ auth()->user()->nama_lengkap ?? auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="mx-6 mt-4 bg-green-950/50 border border-green-700/50 text-green-300 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mx-6 mt-4 bg-red-950/50 border border-red-700/50 text-red-300 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        {{-- Page Content --}}
        <main class="p-6">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="border-t border-neutral-800 p-6">
            <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} HappyCine. All rights reserved.</p>
                <p class="mt-2 md:mt-0">Owner Dashboard v1.0</p>
            </div>
        </footer>
    </div>

    {{-- Mobile Sidebar Overlay --}}
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false"
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-75 z-40 lg:hidden"></div>

    @stack('scripts')
</body>
</html>