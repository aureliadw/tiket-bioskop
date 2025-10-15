@include('layouts.app')

<main class="max-w-6xl mx-auto px-6 py-10">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-3xl font-bold mb-8">Coming Soon</h1>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @foreach ($comingSoon as $film)
      <div class="bg-[#0f0f10] rounded-lg overflow-hidden shadow-md transition-transform hover:scale-105">
        
        {{-- Poster --}}
        <a href="{{ route('pelanggan.detail', $film->id) }}">
          <div class="aspect-[3/4] bg-cover bg-center rounded-t-lg"
               style="background-image: url('{{ Storage::url($film->poster_image) }}')">
          </div>
        </a>
        
        {{-- Info --}}
        <div class="p-3"> 
          <a href="{{ route('pelanggan.detail', $film->id) }}" 
             class="block font-semibold text-sm truncate hover:text-red-500">
            {{ $film->judul }}
          </a>
          
          <div class="flex items-center justify-between mt-2 text-xs text-gray-400">
            <span class="truncate">{{ $film->genre }}</span>
            <span class="px-1.5 py-0.5 bg-gray-600 text-white rounded text-[10px] ml-2 flex-shrink-0">
              {{ \Carbon\Carbon::parse($film->tanggal_rilis)->format('d M Y') }}
            </span>
          </div>
          
          <div class="mt-2 flex items-center justify-between">
            <div class="text-xs text-gray-400">Segera hadir</div>
            <a href="{{ route('pelanggan.detail', $film->id) }}" 
               class="text-xs px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">
              Detail
            </a>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</main>

@include('layouts.footer')

</body>
</html>
