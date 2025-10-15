<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kasir') - HappyCine</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a2d04b5e2c.js" crossorigin="anonymous"></script>
    @stack('styles')
    <style>
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
    </style>
</head>
<body class="bg-neutral-950 text-white flex min-h-screen font-inter">

    {{-- Sidebar --}}
    <aside class="w-64 bg-neutral-900 border-r border-neutral-800 flex flex-col">
        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-neutral-800 flex items-center space-x-2">
            <span class="text-2xl font-extrabold tracking-wide flex items-center">
                <span class="text-red-600">H</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white mx-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l2.39 7.26h7.63l-6.18 4.49 2.36 7.25L12 16.77l-6.2 4.23 2.36-7.25L2 9.26h7.61z"/>
                </svg>
                <span class="text-red-600">APPY</span><span class="text-red-600">CINE</span>
            </span>
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
        <nav class="flex-1 px-4 py-6 space-y-2 text-sm">
            <a href="{{ route('kasir.dashboard') }}" 
               class="flex items-center gap-3 px-4 py-2 rounded-lg nav-item {{ request()->routeIs('kasir.dashboard') ? 'active-link text-red-400' : 'text-gray-300 hover:text-white' }}">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>

            <a href="{{ route('kasir.checkin') }}" 
               class="flex items-center gap-3 px-4 py-2 rounded-lg nav-item {{ request()->routeIs('kasir.checkin') ? 'active-link text-red-400' : 'text-gray-300 hover:text-white' }}">
                <i class="fa-solid fa-qrcode"></i> Check-In Tiket
            </a>

            <a href="{{ route('kasir.jual-tiket') }}" 
               class="flex items-center gap-3 px-4 py-2 rounded-lg nav-item {{ request()->routeIs('kasir.jual-tiket') ? 'active-link text-red-400' : 'text-gray-300 hover:text-white' }}">
                <i class="fa-solid fa-ticket"></i> Jual Tiket Offline
            </a>

            <div class="border-t border-neutral-800 my-4"></div>

            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" 
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
    <main class="flex-1 overflow-y-auto">
        {{-- Header --}}
        <header class="bg-neutral-900 border-b border-neutral-800 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
            <h2 class="text-lg font-semibold tracking-wide">Kasir Panel</h2>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="text-sm text-gray-300">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <section class="p-6">
            @yield('content')
        </section>
    </main>

    @stack('scripts')
</body>
</html>
