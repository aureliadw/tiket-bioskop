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

  {{-- Heroicons CDN --}}
  <script src="https://unpkg.com/@heroicons/vue@2.0.18/dist/heroicons.js"></script>

  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Rubik:wght@400;700&display=swap" rel="stylesheet">
  
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
  </style>
</head>
<body class="bg-[#0b0b0c] text-white font-sans">

  {{-- NAVBAR --}}
  <header class="bg-black/30 backdrop-blur-sm fixed top-0 left-0 right-0 z-40">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">

      {{-- Logo --}}
      <a href="{{ route('home') }}" class="flex items-center gap-2">
        <span class="font-extrabold text-2xl tracking-wide flex items-center">
          <span class="text-red-600">H</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white-500 mx-0.5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2l2.39 7.26h7.63l-6.18 4.49 2.36 7.25L12 16.77l-6.2 4.23 2.36-7.25L2 9.26h7.61z"/>
          </svg>
          <span class="text-red-600">PPY</span>
          <span class="text-red-600">CINE</span>
        </span>
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
          <a href="{{ route('login') }}" class="nav-link {{ str_contains($current, 'login') ? 'active' : '' }}">Signin</a>
          <a href="{{ route('register') }}" class="nav-link {{ str_contains($current, 'register') ? 'active' : '' }}">Signup</a>
        @endguest

        @auth
          <a href="{{ route('profile.index') }}" 
            class="nav-link {{ str_contains($current, 'akun') ? 'active' : '' }}">
              Akun
          </a>
        @endauth
      </nav>

      <form action="{{ route('pelanggan.search') }}" method="GET" class="relative">
  <input type="text" name="q" placeholder="Search movies..."
         value="{{ request('q') }}"
         class="bg-black/40 text-sm placeholder:text-gray-400 rounded-full px-4 py-2 outline-none w-64 focus:ring-2 focus:ring-red-500">
  <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-red-500 hover:text-red-600">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
    </svg>
  </button>
</form>
            {{-- Heroicon search --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </header>

  {{-- MAIN CONTENT --}}
  <main class="pt-20">
    @yield('content')
  </main>
  @stack('scripts')
</body>
</html>
