{{-- resources/views/pelanggan/app.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>HappyCine - @yield('title', 'Home')</title>

  {{-- Tailwind CDN --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Google Fonts - Premium Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Righteous&family=Orbitron:wght@900&family=Bebas+Neue&family=Rubik:wght@400;700&display=swap" rel="stylesheet">
  
  <style>
    .hero-bg { background-size: cover; background-position: center; }
    .hero-gradient::after {
      content: ""; position: absolute; left: 0; right: 0; bottom: 0;
      height: 48%;
      background: linear-gradient(180deg, rgba(10,11,13,0) 0%, rgba(3,7,10,0.85) 100%);
      pointer-events: none;
    }
    .hero-blur::before {
      content:""; position:absolute; inset:0;
      backdrop-filter: blur(2px);
      opacity: 0.12; pointer-events: none;
    }
    .nav-link { @apply text-gray-300 hover:text-red-500 transition; }
    .nav-link.active { @apply text-red-500 font-semibold; }
    
    /* ✅ NEW LOGO STYLE WITH STAR */
    .logo-text {
      font-family: 'Righteous', cursive;
      font-size: 1.5rem;
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
  </style>
</head>
<body class="bg-[#0b0b0c] text-white font-sans">

  {{-- NAVBAR --}}
  <header class="bg-black/40 backdrop-blur-md fixed top-0 left-0 right-0 z-40 border-b border-white/10">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">

      {{-- ✅ NEW LOGO WITH STAR --}}
      <a href="{{ route('home') }}" class="flex items-center gap-1 group">
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

      {{-- Menu --}}
      @php $current = url()->current(); @endphp
      <nav class="hidden md:flex items-center gap-6 text-sm">
        <a href="{{ route('pelanggan.now-playing') }}" 
           class="nav-link {{ str_contains($current, 'now-playing') ? 'active' : '' }}">
           Now Playing
        </a>
        <a href="{{ route('pelanggan.coming-soon') }}" 
           class="nav-link {{ str_contains($current, 'coming-soon') ? 'active' : '' }}">
           Coming Soon
        </a>

        @guest
          <a href="{{ route('login') }}" class="nav-link {{ str_contains($current, 'login') ? 'active' : '' }}">Sign In</a>
          <a href="{{ route('register') }}" class="px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-lg font-bold text-sm transition-all hover:scale-105 shadow-lg shadow-red-500/30 hover:shadow-red-500/50">
            Sign Up
          </a>
        @endguest

        @auth
          <a href="{{ route('profile.index') }}" 
            class="nav-link {{ str_contains($current, 'akun') ? 'active' : '' }}">
              My Account
          </a>
        @endauth
      </nav>

      {{-- Search Bar --}}
      <form action="{{ route('pelanggan.search') }}" method="GET" class="relative">
        <input type="text" name="q" placeholder="Search movies..."
               value="{{ request('q') }}"
               class="bg-white/5 backdrop-blur-sm text-sm placeholder:text-gray-500 rounded-lg px-4 py-2.5 pr-10 outline-none w-64 focus:ring-2 focus:ring-red-500 border border-white/10 focus:border-red-500 transition-all">
        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
          </svg>
        </button>
      </form>
    </div>
  </header>

  {{-- MAIN CONTENT --}}
  <main class="pt-20">
    @yield('content')
  </main>

  {{-- FOOTER --}}
  <footer class="mt-16 bg-gradient-to-br from-black via-neutral-950 to-black relative overflow-hidden border-t border-white/10">
    {{-- Decorative elements --}}
    <div class="absolute inset-0 opacity-5">
      <div class="absolute top-0 left-0 w-96 h-96 bg-red-600 rounded-full blur-3xl"></div>
      <div class="absolute bottom-0 right-0 w-96 h-96 bg-red-600 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-6xl mx-auto px-6 py-16">
      {{-- Main Footer Content --}}
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
        
        {{-- Brand Section --}}
        <div class="lg:col-span-2">
          <a href="{{ route('home') }}" class="inline-block mb-4">
            <span class="logo-text flex items-center gap-1">
              <span class="text-red-600">H</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l2.39 7.26h7.63l-6.18 4.49 2.36 7.25L12 16.77l-6.2 4.23 2.36-7.25L2 9.26h7.61z"/>
              </svg>
              <span class="text-red-600">PPY</span>
              <span class="text-white">CINE</span>
            </span>
          </a>
          <p class="text-gray-400 mb-6 text-sm leading-relaxed max-w-md">
            Your Happy Movie Experience - Nikmati pengalaman menonton terbaik dengan teknologi canggih dan kenyamanan maksimal.
          </p>
          
          {{-- Social Media Icons --}}
          <div class="flex gap-3">
            <a href="#" class="w-10 h-10 bg-white/5 hover:bg-red-600 border border-white/10 hover:border-red-600 rounded-lg flex items-center justify-center transition-all duration-300 group backdrop-blur-sm">
              <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </a>
            <a href="#" class="w-10 h-10 bg-white/5 hover:bg-red-600 border border-white/10 hover:border-red-600 rounded-lg flex items-center justify-center transition-all duration-300 group backdrop-blur-sm">
              <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
              </svg>
            </a>
            <a href="#" class="w-10 h-10 bg-white/5 hover:bg-red-600 border border-white/10 hover:border-red-600 rounded-lg flex items-center justify-center transition-all duration-300 group backdrop-blur-sm">
              <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005 20.1a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1-.1z"/>
              </svg>
            </a>
            <a href="#" class="w-10 h-10 bg-white/5 hover:bg-red-600 border border-white/10 hover:border-red-600 rounded-lg flex items-center justify-center transition-all duration-300 group backdrop-blur-sm">
              <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
              </svg>
            </a>
          </div>
        </div>

        {{-- Film & Jadwal --}}
        <div>
          <h3 class="font-bold text-white mb-4 text-lg">Film & Jadwal</h3>
          <ul class="space-y-3">
            <li>
              <a href="{{ route('pelanggan.now-playing') }}" class="text-gray-400 hover:text-red-500 transition-colors duration-200 flex items-center group text-sm">
                <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                Now Playing
              </a>
            </li>
            <li>
              <a href="{{ route('pelanggan.coming-soon') }}" class="text-gray-400 hover:text-red-500 transition-colors duration-200 flex items-center group text-sm">
                <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                Coming Soon
              </a>
            </li>
          </ul>
        </div>

        {{-- Panduan --}}
        <div>
          <h3 class="font-bold text-white mb-4 text-lg">Panduan</h3>
          <ul class="space-y-3">
            <li>
              <a href="#panduan-booking" class="text-gray-400 hover:text-red-500 transition-colors duration-200 flex items-center group text-sm">
                <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                Panduan Booking
              </a>
            </li>
          </ul>
        </div>
      </div>

      {{-- Bottom Footer --}}
      <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="text-gray-400 text-sm">
          © {{ date('Y') }} HappyCine. All rights reserved.
        </div>
        <div class="text-gray-400 text-sm flex items-center gap-1.5">
          Made with <span class="text-red-500 animate-pulse">♥</span> in Indonesia
        </div>
      </div>
    </div>
  </footer>

  @stack('scripts')
</body>
</html>