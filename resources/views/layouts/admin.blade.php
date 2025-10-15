<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | HappyCine</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a2d04b5e2c.js" crossorigin="anonymous"></script>
    <style>
        /* Animasi dan efek glow halus */
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
        <div class="px-6 py-5 border-b border-neutral-800 flex items-center space-x-2">
            <span class="text-2xl font-extrabold tracking-wide flex items-center">
                <span class="text-red-600">H</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white mx-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l2.39 7.26h7.63l-6.18 4.49 2.36 7.25L12 16.77l-6.2 4.23 2.36-7.25L2 9.26h7.61z"/>
                </svg>
                <span class="text-red-600">APPY</span><span class="text-red-600">CINE</span>
            </span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2 text-sm">

    {{-- Dashboard --}}
    <a href="{{ route('admin.dashboard') }}" 
       class="flex items-center gap-3 px-4 py-2 rounded-lg nav-item {{ request()->routeIs('admin.dashboard') ? 'active-link text-red-400' : 'text-gray-300 hover:text-white' }}">
        <i class="fa-solid fa-house"></i> Dashboard
    </a>

    {{-- Kelola Data Utama --}}
    <a href="{{ route('admin.film.index') }}" 
       class="flex items-center gap-3 px-4 py-2 rounded-lg nav-item {{ request()->routeIs('admin.film*') ? 'active-link text-red-400' : 'text-gray-300 hover:text-white' }}">
        <i class="fa-solid fa-film"></i> Kelola Film
    </a>
    <a href="{{ route('admin.jadwal.index') }}" 
       class="flex items-center gap-3 px-4 py-2 rounded-lg nav-item {{ request()->routeIs('admin.jadwal*') ? 'active-link text-red-400' : 'text-gray-300 hover:text-white' }}">
        <i class="fa-solid fa-calendar-days"></i> Kelola Jadwal
    </a>
    <a href="{{ route('admin.studio.index') }}" 
       class="flex items-center gap-3 px-4 py-2 rounded-lg nav-item {{ request()->routeIs('admin.studio*') ? 'active-link text-red-400' : 'text-gray-300 hover:text-white' }}">
        <i class="fa-solid fa-couch"></i> Kelola Studio
    </a>

    {{-- Pemesanan & Pembayaran --}}
    <a href="{{ route('admin.pemesanan') }}" 
       class="flex items-center gap-3 px-4 py-2 rounded-lg nav-item {{ request()->routeIs('admin.pemesanan*') ? 'active-link text-red-400' : 'text-gray-300 hover:text-white' }}">
        <i class="fa-solid fa-ticket"></i> Pemesanan
    </a>
    <a href="{{ route('admin.pembayaran') }}" 
       class="flex items-center gap-3 px-4 py-2 rounded-lg nav-item {{ request()->routeIs('admin.pembayaran*') ? 'active-link text-red-400' : 'text-gray-300 hover:text-white' }}">
        <i class="fa-solid fa-money-bill-wave"></i> Pembayaran
    </a>

    {{-- Submenu: Data Pengguna --}}
    <div x-data="{ open: false }" class="space-y-1">
        <button @click="open = !open" 
                class="w-full flex items-center justify-between px-4 py-2 rounded-lg nav-item text-gray-300 hover:text-white">
            <span class="flex items-center gap-3">
                <i class="fa-solid fa-users"></i> Data Pengguna
            </span>
            <i :class="open ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'" class="transition"></i>
        </button>

        <div x-show="open" x-transition class="pl-8 space-y-1">
            <a href="{{ route('admin.pelanggan.index') }}" 
               class="block px-3 py-2 rounded-md text-gray-400 hover:text-white hover:bg-neutral-800/60 {{ request()->routeIs('admin.pelanggan*') ? 'text-red-400' : '' }}">
                Pelanggan
            </a>
            <a href="{{ route('admin.kasir.index') }}" 
               class="block px-3 py-2 rounded-md text-gray-400 hover:text-white hover:bg-neutral-800/60 {{ request()->routeIs('admin.kasir*') ? 'text-red-400' : '' }}">
                Kasir
            </a>
            <a href="{{ route('admin.owner.index') }}" 
               class="block px-3 py-2 rounded-md text-gray-400 hover:text-white hover:bg-neutral-800/60 {{ request()->routeIs('admin.owner*') ? 'text-red-400' : '' }}">
                Owner
            </a>
        </div>
    </div>
</nav>
    </aside>

    {{-- Konten utama --}}
    <main class="flex-1 overflow-y-auto">
        {{-- Header --}}
        <header class="bg-neutral-900 border-b border-neutral-800 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
            <h2 class="text-lg font-semibold tracking-wide">Admin Panel</h2>
            <div class="flex items-center space-x-5">
                {{-- Profil --}}
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 bg-red-600 rounded-full flex items-center justify-center font-bold">
                        {{ strtoupper(substr(Auth::user()->nama_lengkap ?? 'A', 0, 1)) }}
                    </div>
                    <span class="text-sm text-gray-300 font-medium">{{ Auth::user()->nama_lengkap ?? 'Admin' }}</span>
                </div>

                {{-- Tombol Logout --}}
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button 
                        class="px-4 py-2 text-sm rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold transition flex items-center gap-2">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </button>
                </form>
            </div>
        </header>

        {{-- Isi halaman --}}
        <section class="p-6">
            @yield('content')
        </section>
    </main>

</body>
</html>
