@extends('layouts.app')

@section('content')
<div class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Náhledový obrázek nebo galerie -->
            @if($post->thumbnail_path)
                <!-- Pouze náhledový obrázek -->
                <img src="{{ asset('storage/' . $post->thumbnail_path) }}" 
                     alt="{{ $post->title }}"
                     class="w-full h-96 object-cover">
            @endif

            @if($post->images->isNotEmpty())
                <!-- Galerie -->
                <div class="relative" x-data="{ activeSlide: 0, totalSlides: {{ count($post->images) }} }">
                    <!-- Hlavní obrázek -->
                    <div class="relative h-96 mb-4">
                        @foreach($post->images as $index => $image)
                            <div x-show.transition.opacity="activeSlide === {{ $index }}"
                                 class="absolute inset-0">
                                <img src="{{ asset('storage/' . $image->image_path) }}" 
                                     alt="Obrázek {{ $index + 1 }}"
                                     class="w-full h-full object-contain">
                            </div>
                        @endforeach

                        <!-- Ovládací prvky -->
                        <button @click="activeSlide = (activeSlide - 1 + totalSlides) % totalSlides"
                                class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 p-2 rounded-r-lg text-white hover:bg-opacity-75">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button @click="activeSlide = (activeSlide + 1) % totalSlides"
                                class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 p-2 rounded-l-lg text-white hover:bg-opacity-75">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Náhledy -->
                    <div class="grid grid-cols-6 gap-2">
                        @foreach($post->images as $index => $image)
                            <button @click="activeSlide = {{ $index }}"
                                    :class="{ 'ring-2': activeSlide === {{ $index }} }"
                                    style="ring-color: #fed501;"
                                    class="rounded-lg overflow-hidden focus:outline-none">
                                <img src="{{ asset('storage/' . $image->image_path) }}" 
                                     alt="Náhled {{ $index + 1 }}"
                                     class="w-full h-16 object-cover">
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-2xl font-bold" style="color: #fed501;">{{ $post->title }}</h1>
                    <span class="text-gray-600">{{ $post->category->name }}</span>
                </div>
                
                <div class="prose prose-lg max-w-none mb-8 text-gray-800">
                    {!! $post->content !!}
                </div>

                <div class="mt-6">
                    <a href="{{ url()->previous() }}" 
                       class="px-4 py-2 rounded-lg transition-colors"
                       style="background-color: #fed501; color: black;">
                        Zpět
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
@endpush
@endsection 