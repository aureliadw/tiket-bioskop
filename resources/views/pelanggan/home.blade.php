@include('layouts.app')

{{-- HERO SLIDER --}}
<section 
  x-data="{
    slides: [
      { 
        img: '{{ asset('images/hero.png') }}', 
        title: 'Pesan Tiket Bioskop Online', 
        desc: 'Nonton film favoritmu, pilih kursi sendiri, bayar online, langsung dapat e-ticket.' 
      },
      { 
        img: '{{ asset('images/kangsolahbanner.jpg') }}', 
        title: 'Pengalaman Nonton Terbaik', 
        desc: 'Sistem booking modern untuk kenyamanan maksimal.' 
      },
      { 
        img: '{{ asset('images/bannermaryam.jpg') }}', 
        title: 'Kursi Terbaik Untukmu', 
        desc: 'Pilih kursi favorit langsung dari smartphone atau laptop kamu.' 
      }
    ],
    active: 0,
    next() { 
      this.active = (this.active + 1) % this.slides.length 
    },
    prev() { 
      this.active = (this.active - 1 + this.slides.length) % this.slides.length 
    }
  }"
  x-init="setInterval(() => next(), 7000)"
  class="relative overflow-hidden bg-black h-screen"
>
  {{-- Background Slides --}}
  <template x-for="(slide, index) in slides" :key="index">
    <div 
      x-show="active === index" 
      x-transition:enter="transition-opacity ease-in-out duration-1000"
      x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-100"
      x-transition:leave="transition-opacity ease-in-out duration-1000"
      x-transition:leave-start="opacity-100"
      x-transition:leave-end="opacity-0"
      class="absolute inset-0 bg-cover bg-center"
      :style="`background-image: url('${slide.img}'); filter: brightness(0.4);`"
    ></div>
  </template>

  {{-- Gradient Overlay --}}
  <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
  <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>

  {{-- Content --}}
  <div class="relative z-10 h-full max-w-6xl mx-auto px-4 flex items-center">
    <div class="w-full lg:w-3/5 space-y-6">
      
      {{-- Title --}}
      <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black leading-tight">
        <span 
          class="block bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent drop-shadow-2xl" 
          x-text="slides[active].title">
        </span>
      </h1>
      
      {{-- Description --}}
      <p 
        class="text-base sm:text-lg lg:text-xl text-gray-300 max-w-2xl leading-relaxed" 
        x-text="slides[active].desc">
      </p>

      {{-- CTA Buttons --}}
      <div class="flex flex-col sm:flex-row gap-4 pt-4">
        <a href="#now-playing" 
           class="group inline-flex items-center justify-center gap-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800
                  text-white font-bold px-6 sm:px-8 py-3 sm:py-4 rounded-xl shadow-2xl shadow-red-600/40 transition-all duration-300 hover:scale-105">
          <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
            <path d="M5 3v18l15-9z"></path>
          </svg> 
          <span class="text-base sm:text-lg">Pesan Sekarang</span>
        </a>
        
        <a href="#panduan-booking" 
           class="inline-flex items-center justify-center gap-2 text-white font-bold px-6 sm:px-8 py-3 sm:py-4 rounded-xl border-2 border-white/30 
                  hover:border-white/60 backdrop-blur-md hover:bg-white/10 transition-all duration-300 hover:scale-105">
          <span class="text-base sm:text-lg">Pelajari Lebih Lanjut</span>
          <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
          </svg>
        </a>
      </div>
    </div>
  </div>

  {{-- Navigation Arrows (Hidden on Mobile) --}}
  <button @click="prev" 
          class="hidden sm:flex absolute left-4 lg:left-6 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-red-600/80 backdrop-blur-md p-3 rounded-full 
          border border-white/10 hover:border-red-500/50 transition-all duration-300 hover:scale-110">
    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path>
    </svg>
  </button>
  
  <button @click="next" 
          class="hidden sm:flex absolute right-4 lg:right-6 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-red-600/80 backdrop-blur-md p-3 rounded-full 
          border border-white/10 hover:border-red-500/50 transition-all duration-300 hover:scale-110">
    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
    </svg>
  </button>

  {{-- Slide Indicators --}}
  <div class="absolute bottom-8 sm:bottom-10 left-1/2 -translate-x-1/2 flex gap-2 sm:gap-3 z-20">
    <template x-for="(slide, index) in slides" :key="index">
      <button @click="active = index" class="group">
        <div class="h-1 sm:h-1.5 rounded-full transition-all duration-300" 
             :class="active === index ? 'w-12 sm:w-16 bg-red-500 shadow-lg shadow-red-500/50' : 'w-8 sm:w-10 bg-gray-500/50 group-hover:bg-gray-400/70'">
        </div>
      </button>
    </template>
  </div>
</section>

{{-- MAIN CONTENT --}}
<main class="max-w-6xl mx-auto px-4">

  {{-- Now Playing --}}
  <section id="now-playing" class="py-12 sm:py-16 lg:py-20">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <div class="inline-block mb-2 px-3 py-1 bg-red-600/10 border border-red-500/30 rounded-full">
          <span class="text-red-500 text-xs font-bold">NOW PLAYING</span>
        </div>
        <h2 class="text-2xl sm:text-3xl lg:text-5xl font-black">Sedang Tayang</h2>
      </div>
      <a href="{{ route('pelanggan.now-playing') }}" 
         class="group flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
        <span class="text-sm font-medium">Lihat Semua</span>
        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
      @foreach ($nowPlaying as $film)
        <div class="group relative bg-neutral-900 rounded-xl lg:rounded-2xl overflow-hidden border border-neutral-800 hover:border-red-500/50 transition-all 
        duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-red-500/20">
          <a href="{{ route('pelanggan.detail', $film->id) }}">
            <div class="aspect-[2/3] bg-cover bg-center relative"
                 style="background-image:url('{{ Storage::url($film->poster_image) }}')">
              <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
              <div class="absolute top-2 sm:top-3 right-2 sm:right-3 px-2 py-0.5 sm:py-1 bg-red-600 rounded-lg">
                <span class="text-xs font-bold">{{ $film->durasi }}M</span>
              </div>
            </div>
          </a>
          
          <div class="p-3 sm:p-4">
            <a href="{{ route('pelanggan.detail', $film->id) }}">
              <h3 class="font-bold text-xs sm:text-sm lg:text-base line-clamp-1 group-hover:text-red-500 transition-colors">
                {{ $film->judul }}
              </h3>
            </a>
            <p class="text-xs text-gray-400 mt-1 line-clamp-1">{{ $film->genre }}</p>
            
            <a href="{{ route('pelanggan.detail', $film->id) }}" 
               class="mt-2 sm:mt-3 block w-full text-center py-1.5 sm:py-2 bg-red-600 hover:bg-red-700 rounded-lg text-xs sm:text-sm font-semibold transition-colors">
              Pesan Tiket
            </a>
          </div>
        </div>
      @endforeach
    </div>
  </section>

  {{-- Coming Soon --}}
  <section id="upcoming" class="py-12 sm:py-16 lg:py-20 border-t border-neutral-800">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <div class="inline-block mb-2 px-3 py-1 bg-blue-600/10 border border-blue-500/30 rounded-full">
          <span class="text-blue-500 text-xs font-bold">COMING SOON</span>
        </div>
        <h2 class="text-2xl sm:text-3xl lg:text-5xl font-black">Segera Hadir</h2>
      </div>
      <a href="{{ route('pelanggan.coming-soon') }}" 
         class="group flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
        <span class="text-sm font-medium">Lihat Semua</span>
        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
      @foreach ($comingSoon as $film)
        <div class="group relative bg-neutral-900 rounded-xl lg:rounded-2xl overflow-hidden border border-neutral-800 hover:border-blue-500/50 transition-all 
        duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-blue-500/20">
          <a href="{{ route('pelanggan.detail', $film->id) }}">
            <div class="aspect-[2/3] bg-cover bg-center relative"
                 style="background-image:url('{{ Storage::url($film->poster_image) }}')">
              <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
              
              <div class="absolute top-2 sm:top-3 left-2 sm:left-3 px-2 sm:px-3 py-1 sm:py-1.5 bg-blue-600 rounded-lg">
                <span class="text-xs font-bold">SOON</span>
              </div>

              <div class="absolute bottom-2 sm:bottom-3 left-2 sm:left-3 right-2 sm:right-3 px-2 sm:px-3 py-1.5 sm:py-2 bg-black/80 backdrop-blur-sm rounded-lg">
                <span class="text-xs font-bold">{{ \Carbon\Carbon::parse($film->tanggal_rilis)->format('d M Y') }}</span>
              </div>
            </div>
          </a>
          
          <div class="p-3 sm:p-4">
            <a href="{{ route('pelanggan.detail', $film->id) }}">
              <h3 class="font-bold text-xs sm:text-sm lg:text-base line-clamp-1 group-hover:text-blue-500 transition-colors">
                {{ $film->judul }}
              </h3>
            </a>
            <p class="text-xs text-gray-400 mt-1 line-clamp-1">{{ $film->genre }}</p>
            
            <a href="{{ route('pelanggan.detail', $film->id) }}" 
               class="mt-2 sm:mt-3 block w-full text-center py-1.5 sm:py-2 bg-neutral-800 hover:bg-neutral-700 rounded-lg text-xs sm:text-sm font-semibold transition-colors">
              Lihat Detail
            </a>
          </div>
        </div>
      @endforeach
    </div>
  </section>

  {{-- Panduan Booking --}}
  <section id="panduan-booking" class="py-12 sm:py-16 lg:py-20 border-t border-neutral-800">
    <div class="text-center mb-12 sm:mb-16">
      <div class="inline-block mb-4 px-4 py-2 bg-red-600/10 border border-red-500/30 rounded-full">
        <span class="text-red-500 text-sm font-bold">CARA BOOKING</span>
      </div>
      <h2 class="text-2xl sm:text-3xl lg:text-5xl font-black mb-4">
        Mudah & Praktis
      </h2>
      <p class="text-gray-400 text-sm sm:text-base lg:text-lg max-w-2xl mx-auto px-4">
        Booking tiket bioskop jadi lebih mudah dengan 4 langkah sederhana
      </p>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
      {{-- Step 1 --}}
      <div class="group relative p-6 sm:p-8 rounded-xl sm:rounded-2xl bg-gradient-to-br from-neutral-900 to-neutral-950 border border-neutral-800 hover:border-red-500/50 transition-all duration-300 hover:scale-105">
        <div class="absolute -top-3 -left-3 w-10 h-10 sm:w-12 sm:h-12 bg-red-600 rounded-xl flex items-center justify-center font-black text-lg sm:text-xl shadow-lg">
          1
        </div>
        <div class="mb-4 p-3 sm:p-4 bg-red-600/10 rounded-xl inline-block">
          <svg class="w-6 h-6 sm:w-8 sm:h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>
          </svg>
        </div>
        <h3 class="font-bold text-lg sm:text-xl mb-3">Pilih Film</h3>
        <p class="text-gray-400 text-sm leading-relaxed">
          Pilih film favorit dari katalog Now Playing atau Coming Soon
        </p>
      </div>

      {{-- Step 2 --}}
      <div class="group relative p-6 sm:p-8 rounded-xl sm:rounded-2xl bg-gradient-to-br from-neutral-900 to-neutral-950 border border-neutral-800 hover:border-red-500/50 transition-all duration-300 hover:scale-105">
        <div class="absolute -top-3 -left-3 w-10 h-10 sm:w-12 sm:h-12 bg-red-600 rounded-xl flex items-center justify-center font-black text-lg sm:text-xl shadow-lg">
          2
        </div>
        <div class="mb-4 p-3 sm:p-4 bg-red-600/10 rounded-xl inline-block">
          <svg class="w-6 h-6 sm:w-8 sm:h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
        </div>
        <h3 class="font-bold text-lg sm:text-xl mb-3">Pilih Jadwal</h3>
        <p class="text-gray-400 text-sm leading-relaxed">
          Tentukan tanggal dan jam tayang yang sesuai dengan waktumu
        </p>
      </div>

      {{-- Step 3 --}}
      <div class="group relative p-6 sm:p-8 rounded-xl sm:rounded-2xl bg-gradient-to-br from-neutral-900 to-neutral-950 border border-neutral-800 hover:border-red-500/50 transition-all duration-300 hover:scale-105">
        <div class="absolute -top-3 -left-3 w-10 h-10 sm:w-12 sm:h-12 bg-red-600 rounded-xl flex items-center justify-center font-black text-lg sm:text-xl shadow-lg">
          3
        </div>
        <div class="mb-4 p-3 sm:p-4 bg-red-600/10 rounded-xl inline-block">
          <svg class="w-6 h-6 sm:w-8 sm:h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
          </svg>
        </div>
        <h3 class="font-bold text-lg sm:text-xl mb-3">Bayar</h3>
        <p class="text-gray-400 text-sm leading-relaxed">
          Selesaikan pembayaran dengan metode yang kamu pilih
        </p>
      </div>

      {{-- Step 4 --}}
      <div class="group relative p-6 sm:p-8 rounded-xl sm:rounded-2xl bg-gradient-to-br from-neutral-900 to-neutral-950 border border-neutral-800 hover:border-red-500/50 transition-all duration-300 hover:scale-105">
        <div class="absolute -top-3 -left-3 w-10 h-10 sm:w-12 sm:h-12 bg-red-600 rounded-xl flex items-center justify-center font-black text-lg sm:text-xl shadow-lg">
          4
        </div>
        <div class="mb-4 p-3 sm:p-4 bg-red-600/10 rounded-xl inline-block">
          <svg class="w-6 h-6 sm:w-8 sm:h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
          </svg>
        </div>
        <h3 class="font-bold text-lg sm:text-xl mb-3">Dapatkan E-Ticket</h3>
        <p class="text-gray-400 text-sm leading-relaxed">
          E-ticket langsung terkirim dan siap digunakan
        </p>
      </div>
    </div>

    <div class="text-center mt-8 sm:mt-12">
      <a href="#now-playing" 
         class="inline-flex items-center gap-3 px-6 sm:px-8 py-3 sm:py-4 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-xl font-bold shadow-lg shadow-red-600/30 transition-all hover:scale-105">
        Mulai Booking
        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
        </svg>
      </a>
    </div>
  </section>

</main>

@include('layouts.footer')

</body>
</html>