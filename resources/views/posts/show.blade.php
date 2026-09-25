@extends('layouts.app')

@php($cover = $post->coverImagePath())

@section('title', $post->title)
@section('description', $post->excerpt(160))
@section('og_type', 'article')
@section('og_image', $cover ? asset('storage/'.$cover) : asset('images/defaults/default.jpg'))

@section('content')
<div class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4">
        @unless($post->isPublished())
            <div class="mb-4 rounded-lg bg-black text-brand px-4 py-3 text-sm">
                Náhled – příspěvek není veřejný ({{ mb_strtolower($post->statusLabel()) }}{{ $post->isScheduled() ? ' na '.$post->published_at->format('d.m.Y H:i') : '' }}).
                <a href="{{ route('admin.posts.edit', $post) }}" class="underline ml-2">Upravit</a>
            </div>
        @endunless

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">

            <!-- Hlavička příspěvku -->
            <div class="p-6 flex justify-between items-start gap-6 bg-gray-50 border-b">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold mb-3 text-brand">{{ $post->title }}</h1>
                    <div class="flex items-center gap-4 text-gray-600 text-sm mb-2">
                        <span>{{ $post->created_at->format('d.m.Y') }}</span>
                        <span>{{ $post->category->name }}</span>
                    </div>
                </div>
                @if($post->thumbnail_path)
                    <div class="flex-shrink-0">
                        <img src="{{ asset('storage/' . $post->thumbnail_path) }}" 
                             alt="{{ $post->title }}"
                             class="w-32 h-32 object-cover rounded-lg shadow-sm">
                    </div>
                @endif
            </div>
    
            <!-- Obsah příspěvku -->
            <div class="px-6 pb-6">
                <div class="prose prose-lg max-w-none mb-8 text-gray-800">
                    {!! $post->content !!}
                </div>
            </div>



            @if($post->images->isNotEmpty())
                <!-- Galerie s Lightboxem -->
                <div class="relative mt-8" 
                     x-data="{ 
                         activeSlide: 0, 
                         totalSlides: {{ count($post->images) }}, 
                         lightboxOpen: false 
                     }"
                     @keydown.escape.window="lightboxOpen = false"
                     @keydown.right.window="if(lightboxOpen) activeSlide = (activeSlide + 1) % totalSlides"
                     @keydown.left.window="if(lightboxOpen) activeSlide = (activeSlide - 1 + totalSlides) % totalSlides">
                     
                    <!-- Hlavní obrázek -->
                    <div class="relative h-96 mb-4 cursor-pointer group rounded-lg overflow-hidden" @click="lightboxOpen = true" title="Klikněte pro zvětšení">
                        @foreach($post->images as $index => $image)
                            <div x-show.transition.opacity="activeSlide === {{ $index }}"
                                 class="absolute inset-0">
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                     alt="{{ $image->alt ?: $post->title.' – obrázek '.($index + 1) }}"
                                     @if($index > 0) loading="lazy" @endif
                                     class="w-full h-full object-contain bg-gray-100">
                            </div>
                        @endforeach

                        <!-- Ovládací prvky -->
                        <button @click.stop="activeSlide = (activeSlide - 1 + totalSlides) % totalSlides"
                                class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 p-2 rounded-r-lg text-white hover:bg-opacity-75 z-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button @click.stop="activeSlide = (activeSlide + 1) % totalSlides"
                                class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 p-2 rounded-l-lg text-white hover:bg-opacity-75 z-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        
                        <!-- Lupa ikonka naznačující kliknutí -->
                        <div class="absolute bottom-4 right-4 bg-black bg-opacity-60 p-2 rounded-full text-white pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                        </div>
                    </div>

                    <!-- Náhledy -->
                    <div class="grid grid-cols-6 gap-2">
                        @foreach($post->images as $index => $image)
                            <button @click="activeSlide = {{ $index }}"
                                    :class="{ 'ring-2': activeSlide === {{ $index }} }"
                                    aria-label="Zobrazit obrázek {{ $index + 1 }}"
                                    class="rounded-lg overflow-hidden focus:outline-none bg-gray-100 ring-brand">
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                     alt="" loading="lazy"
                                     class="w-full h-16 object-cover hover:opacity-75 transition-opacity">
                            </button>
                        @endforeach
                    </div>

                    <!-- Lightbox Modal -->
                    <div x-show="lightboxOpen" 
                         style="display: none;"
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-95 p-4 sm:p-6 backdrop-blur-sm"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0">
                         
                        <!-- Zavřít tlačítko -->
                        <button @click="lightboxOpen = false" class="absolute top-4 right-4 sm:top-6 sm:right-6 text-gray-300 hover:text-white z-50 bg-black bg-opacity-50 p-2 rounded-full">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <!-- Zvětšený obrázek -->
                        @foreach($post->images as $index => $image)
                            <div x-show="activeSlide === {{ $index }}" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 class="relative w-full h-full max-w-7xl flex items-center justify-center">
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                     alt="{{ $image->alt ?: $post->title.' – obrázek '.($index + 1) }}"
                                     loading="lazy"
                                     @click.stop
                                     class="max-w-full max-h-full object-contain drop-shadow-2xl rounded-sm">
                            </div>
                        @endforeach

                        <!-- Ovládací prvky v lightboxu -->
                        <button @click.stop="activeSlide = (activeSlide - 1 + totalSlides) % totalSlides"
                                class="absolute left-2 sm:left-8 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-80 p-3 rounded-full text-white transition z-50">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button @click.stop="activeSlide = (activeSlide + 1) % totalSlides"
                                class="absolute right-2 sm:right-8 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-80 p-3 rounded-full text-white transition z-50">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <div class="p-6">

                <div class="mt-6">
                    <a href="{{ url()->previous() }}"
                       class="px-4 py-2 rounded-lg transition-colors bg-brand text-black">
                        Zpět
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection