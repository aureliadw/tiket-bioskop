@include('layouts.app')

{{-- MAIN CONTENT --}}
<main class="max-w-6xl mx-auto px-4 py-20">
  
  {{-- Header Section --}}
  <div class="mb-12">
    <div class="inline-block mb-3 px-3 py-1 bg-blue-600/10 border border-blue-500/30 rounded-full">
      <span class="text-blue-500 text-xs font-bold">COMING SOON</span>
    </div>
    <h1 class="text-3xl md:text-5xl font-black mb-3">Segera Hadir</h1>
    <p class="text-gray-400">Film-film yang akan segera tayang di HappyCine</p>
  </div>

  {{-- Movies Grid --}}
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
    @forelse ($comingSoon as $film)
      <div class="group relative bg-neutral-900 rounded-2xl overflow-hidden border border-neutral-800 hover:border-blue-500/50 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-blue-500/20">
        <a href="{{ route('pelanggan.detail', $film->id) }}" class="block">
          <div class="aspect-[2/3] bg-cover bg-center relative overflow-hidden"
               style="background-image:url('{{ Storage::url($film->poster_image) }}')">
            {{-- Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>

            {{-- Coming Soon Label --}}
            <div class="absolute top-3 left-3 px-3 py-1.5 bg-blue-600 rounded-lg">
              <span class="text-xs font-bold">SOON</span>
            </div>

            {{-- Release Date --}}
            <div class="absolute bottom-3 left-3 right-3 px-3 py-2 bg-black/80 backdrop-blur-sm rounded-lg">
              <span class="text-xs font-bold">{{ \Carbon\Carbon::parse($film->tanggal_rilis)->format('d M Y') }}</span>
            </div>
          </div>
        </a>
        
        <div class="p-4">
          <a href="{{ route('pelanggan.detail', $film->id) }}" class="block">
            <h3 class="font-bold text-sm md:text-base line-clamp-1 group-hover:text-blue-500 transition-colors">
              {{ $film->judul }}
            </h3>
          </a>
          <p class="text-xs text-gray-400 mt-1 line-clamp-1">{{ $film->genre }}</p>
          
          <a href="{{ route('pelanggan.detail', $film->id) }}" 
             class="mt-3 block w-full text-center py-2 bg-neutral-800 hover:bg-neutral-700 rounded-lg text-sm font-semibold transition-colors">
            Lihat Detail
          </a>
        </div>
      </div>
    @empty
      <div class="col-span-full text-center py-20">
        <div class="inline-block p-6 bg-neutral-900 rounded-2xl border border-neutral-800 mb-4">
          <svg class="w-16 h-16 text-gray-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </div>
        <h3 class="text-xl font-bold mb-2">Belum Ada Film</h3>
        <p class="text-gray-400">Belum ada film yang akan datang saat ini.</p>
      </div>
    @endforelse
  </div>

</main>

@include('layouts.footer')
</body>
</html>
