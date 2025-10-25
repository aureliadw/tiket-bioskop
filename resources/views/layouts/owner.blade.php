<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Owner Dashboard - HappyCine')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        body { background: linear-gradient(to bottom right, #0f0f0f, #1a1a1a); }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased" x-data="{ sidebarOpen: true, profileOpen: false }">
    
    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-black via-gray-900 to-black border-r border-red-900/30 text-white transition-transform duration-300 ease-in-out lg:translate-x-0">
        
        <!-- Logo & Brand -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-red-900/30 bg-black/50">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-red-600 to-red-800 rounded-lg flex items-center justify-center shadow-lg shadow-red-900/50">
                    <span class="text-2xl">🎬</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold bg-gradient-to-r from-red-500 to-red-700 bg-clip-text text-transparent">HappyCine</h1>
                    <p class="text-xs text-red-400">Owner Panel</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-red-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- User Info -->
        <div class="px-6 py-4 bg-gradient-to-r from-red-950/50 to-black/50">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center shadow-lg shadow-red-900/50">
                    <span class="text-xl font-bold">{{ substr(auth()->user()->nama_lengkap, 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate">{{ auth()->user()->nama_lengkap }}</p>
                    <p class="text-xs text-red-400">Owner</p>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="px-3 py-4 space-y-1 overflow-y-auto" style="max-height: calc(100vh - 220px)">
            
            <!-- Dashboard -->
            <a href="{{ route('owner.dashboard') }}" 
               class="flex items-center space-x-3 px-3 py-3 rounded-lg transition {{ request()->routeIs('owner.dashboard') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-900/50' : 'text-gray-300 hover:bg-red-950/30 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <!-- Revenue Report -->
            <a href="{{ route('owner.revenue.report') }}" 
               class="flex items-center space-x-3 px-3 py-3 rounded-lg transition {{ request()->routeIs('owner.revenue.report') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-900/50' : 'text-gray-300 hover:bg-red-950/30 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span class="font-medium">Laporan Revenue</span>
            </a>

            <!-- Film Performance -->
            <a href="{{ route('owner.film.performance') }}" 
               class="flex items-center space-x-3 px-3 py-3 rounded-lg transition {{ request()->routeIs('owner.film.performance') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-900/50' : 'text-gray-300 hover:bg-red-950/30 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                </svg>
                <span class="font-medium">Performa Film</span>
            </a>

            <div class="border-t border-red-900/30 my-3"></div>

            <!-- Export PDF -->
            <a href="{{ route('owner.export.pdf') }}" 
               class="flex items-center space-x-3 px-3 py-3 rounded-lg transition text-gray-300 hover:bg-red-950/30 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                </svg>
                <span class="font-medium">Export Laporan</span>
            </a>

            <div class="border-t border-red-900/30 my-3"></div>

            <!-- Settings (future) -->
            <a href="#" class="flex items-center space-x-3 px-3 py-3 rounded-lg transition text-gray-500 hover:bg-red-950/30 opacity-50 cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="font-medium">Pengaturan</span>
                <span class="ml-auto text-xs bg-red-900/50 px-2 py-1 rounded">Soon</span>
            </a>

        </nav>

        <!-- Logout Button -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-red-900/30 bg-black/50">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-lg transition font-medium shadow-lg shadow-red-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="lg:pl-64">
        
        <!-- Top Navbar -->
        <header class="sticky top-0 z-40 bg-gradient-to-r from-black via-gray-900 to-black border-b border-red-900/30 shadow-lg">
            <div class="flex items-center justify-between px-4 py-4">
                
                <!-- Mobile Menu Button -->
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-red-400 hover:text-red-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Page Title -->
                <div class="flex-1 ml-4 lg:ml-0">
                    <h2 class="text-xl font-bold bg-gradient-to-r from-red-500 to-red-700 bg-clip-text text-transparent">@yield('page-title', 'Dashboard')</h2>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center space-x-4">
                    
                    <!-- Current Date & Time -->
                    <div class="hidden md:flex items-center space-x-2 text-sm text-gray-400">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                    </div>

                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-400 hover:text-red-400 hover:bg-red-950/30 rounded-lg transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-600 rounded-full animate-pulse"></span>
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 p-2 hover:bg-red-950/30 rounded-lg transition">
                            <div class="w-8 h-8 bg-gradient-to-br from-red-600 to-red-800 rounded-full flex items-center justify-center text-white font-semibold shadow-lg shadow-red-900/50">
                                {{ substr(auth()->user()->nama_lengkap, 0, 1) }}
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-cloak
                             class="absolute right-0 mt-2 w-48 bg-gray-900 border border-red-900/30 rounded-lg shadow-xl py-2">
                            <div class="px-4 py-2 border-b border-red-900/30">
                                <p class="text-sm font-semibold text-white">{{ auth()->user()->nama_lengkap }}</p>
                                <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-300 hover:bg-red-950/30 hover:text-white">
                                Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-950/30 hover:text-red-400">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="min-h-screen">
            
            <!-- Flash Messages -->
            @if(session('success'))
            <div class="mx-4 mt-4">
                <div class="bg-green-950/50 border border-green-700/50 text-green-300 px-4 py-3 rounded-lg flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-green-400 hover:text-green-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mx-4 mt-4">
                <div class="bg-red-950/50 border border-red-700/50 text-red-300 px-4 py-3 rounded-lg flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
            @endif

            @if(session('info'))
            <div class="mx-4 mt-4">
                <div class="bg-blue-950/50 border border-blue-700/50 text-blue-300 px-4 py-3 rounded-lg flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('info') }}</span>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-blue-400 hover:text-blue-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
            @endif

            <!-- Main Content -->
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-black border-t border-red-900/30 mt-12">
            <div class="px-4 py-6">
                <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                    <p>&copy; {{ date('Y') }} HappyCine. All rights reserved.</p>
                    <p class="mt-2 md:mt-0">Owner Dashboard v1.0</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false"
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-75 z-40 lg:hidden"></div>

    @stack('scripts')
</body>
</html>