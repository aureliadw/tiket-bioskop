@extends('layouts.app')

@section('title', 'Search Results')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
  <h2 class="text-2xl font-semibold mb-6"> 
    Hasil Pencarian untuk: <span class="text-red-500">"{{ $query }}"</span>
  </h2>

  @if($films->isEmpty())
    <p class="text-gray-400">Tidak ada film yang cocok dengan pencarianmu.</p>
  @else
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
      @foreach($films as $film)
        <a href="{{ route('pelanggan.detail', $film->id) }}" 
           class="bg-[#111] rounded-xl overflow-hidden hover:scale-105 transition">
          <img src="{{ asset('storage/'.$film->poster_image) }}" alt="{{ $film->judul }}" class="w-full h-72 object-cover">
          <div class="p-3">
            <h3 class="font-semibold text-lg">{{ $film->judul }}</h3>
            <p class="text-sm text-gray-400">{{ $film->genre }}</p>
          </div>
        </a>
      @endforeach
    </div>
  @endif
</div>
@endsection
