@include('layouts.app')

{{-- MAIN CONTENT --}}
<main class="max-w-6xl mx-auto px-4 py-20">
  
  {{-- Header Section --}}
  <div class="mb-12">
    <div class="inline-block mb-3 px-3 py-1 bg-red-600/10 border border-red-500/30 rounded-full">
      <span class="text-red-500 text-xs font-bold">NOW PLAYING</span>
    </div>
    <h1 class="text-3xl md:text-5xl font-black mb-3">Sedang Tayang</h1>
    <p class="text-gray-400">Semua film yang sedang tayang di HappyCine</p>
  </div>

  {{-- Movies Grid --}}
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
    @forelse ($nowPlaying as $film)
      <div class="group relative bg-neutral-900 rounded-2xl overflow-hidden border border-neutral-800 hover:border-red-500/50 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-red-500/20">
        <a href="{{ route('pelanggan.detail', $film->id) }}" class="block">
          <div class="aspect-[2/3] bg-cover bg-center relative overflow-hidden"
               style="background-image:url('{{ Storage::url($film->poster_image) }}')">
            {{-- Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>

            {{-- Duration Badge --}}
            <div class="absolute top-3 right-3 px-2 py-1 bg-red-600 rounded-lg">
              <span class="text-xs font-bold">{{ $film->durasi }}M</span>
            </div>
          </div>
        </a>
        
        <div class="p-4">
          <a href="{{ route('pelanggan.detail', $film->id) }}" class="block">
            <h3 class="font-bold text-sm md:text-base line-clamp-1 group-hover:text-red-500 transition-colors">
              {{ $film->judul }}
            </h3>
          </a>
          <p class="text-xs text-gray-400 mt-1 line-clamp-1">{{ $film->genre }}</p>
          
          <a href="{{ route('pelanggan.detail', $film->id) }}" 
             class="mt-3 block w-full text-center py-2 bg-red-600 hover:bg-red-700 rounded-lg text-sm font-semibold transition-colors">
            Pesan Tiket
          </a>
        </div>
      </div>
    @empty
      <div class="col-span-full text-center py-20">
        <div class="inline-block p-6 bg-neutral-900 rounded-2xl border border-neutral-800 mb-4">
          <svg class="w-16 h-16 text-gray-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>
          </svg>
        </div>
        <h3 class="text-xl font-bold mb-2">Belum Ada Film</h3>
        <p class="text-gray-400">Belum ada film yang sedang tayang saat ini.</p>
      </div>
    @endforelse
  </div>

</main>

@include('layouts.footer')

</body>
</html>