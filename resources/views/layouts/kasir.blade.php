<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kasir') - HappyCine</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a2d04b5e2c.js" crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- Google Fonts - Righteous untuk logo --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    
    @stack('styles')
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
</head>
<body class="bg-neutral-950 text-white min-h-screen" x-data="{ sidebarOpen: false }">

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 z-50 w-64 bg-neutral-900 border-r border-neutral-800 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0">
        
        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-neutral-800 flex items-center justify-between">
            <div class="logo-text flex items-center gap-1">
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
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <p class="font-semibold text-sm">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 capitalize">{{ auth()->user()->role }}</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-4 py-6 space-y-2 text-sm overflow-y-auto">
            <a href="{{ route('kasir.dashboard') }}" 
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-4 py-2 rounded-lg nav-item {{ request()->routeIs('kasir.dashboard') ? 'active-link text-red-400' : 'text-gray-300 hover:text-white' }}">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>

            <a href="{{ route('kasir.checkin') }}" 
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-4 py-2 rounded-lg nav-item {{ request()->routeIs('kasir.checkin') ? 'active-link text-red-400' : 'text-gray-300 hover:text-white' }}">
                <i class="fa-solid fa-qrcode"></i> Check-In Tiket
            </a>

            <a href="{{ route('kasir.jual-tiket') }}" 
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-4 py-2 rounded-lg nav-item {{ request()->routeIs('kasir.jual-tiket') ? 'active-link text-red-400' : 'text-gray-300 hover:text-white' }}">
                <i class="fa-solid fa-ticket"></i> Jual Tiket Offline
            </a>

            <div class="border-t border-neutral-800 my-4"></div>

            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" 
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-4 py-2 rounded-lg nav-item text-gray-300 hover:text-white">
                <i class="fa-solid fa-gear"></i> Admin Panel
            </a>
            @endif
        </nav>

        {{-- Logout --}}
        <div class="border-t border-neutral-800 px-4 py-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full flex items-center justify-center gap-2 px-4 py-2 text-sm rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold transition">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="lg:pl-64 flex-1 min-h-screen flex flex-col">
        {{-- Header --}}
        <header class="bg-neutral-900 border-b border-neutral-800 px-4 lg:px-6 py-4 flex items-center justify-between sticky top-0 z-10">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-red-400 hover:text-red-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h2 class="text-lg font-semibold tracking-wide">Kasir Panel</h2>
            </div>
            
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="text-sm text-gray-300 hidden sm:block">{{ auth()->user()->name }}</span>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-4 lg:p-6">
            @yield('content')
        </main>
    </div>

    {{-- Mobile Sidebar Overlay --}}
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false"
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-75 z-40 lg:hidden"></div>

    @stack('scripts')
</body>
</html>