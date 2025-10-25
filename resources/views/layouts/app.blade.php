{{-- resources/views/pelanggan/app.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
  @stack('scripts')
</body>
</html>