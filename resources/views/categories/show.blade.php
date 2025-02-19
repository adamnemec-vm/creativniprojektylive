@extends('layouts.app')

@section('content')
<div class="bg-gray-100 min-h-screen pt-8">
    <div class="container mx-auto px-8 lg:px-16">
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-4" style="color: #fed501;">{{ $category->name }}</h1>
            <p class="text-gray-600">{{ $category->description }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($posts as $post)
                <a href="{{ route('posts.show', $post) }}" class="block group">
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden relative h-full flex flex-col transition-transform duration-300 hover:-translate-y-2">
                        <div class="absolute top-2 right-2 z-10">
                            <span class="bg-black bg-opacity-50 px-3 py-1 rounded-full text-sm" 
                                  style="color: #fed501;">
                                {{ $post->category->name }}
                            </span>
                        </div>

                        <div class="overflow-hidden h-64 flex-shrink-0">
                            @if($post->thumbnail_path)
                                <img src="{{ asset('storage/' . $post->thumbnail_path) }}" 
                                     alt="{{ $post->title }}"
                                     class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-110">
                            @elseif($post->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $post->images->first()->image_path) }}" 
                                     alt="{{ $post->title }}"
                                     class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-110">
                            @else
                                <img src="{{ asset('images/defaults/default.jpg') }}" 
                                     alt="{{ $post->title }}"
                                     class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-110">
                            @endif
                        </div>

                        <div class="p-6 flex-grow flex flex-col">
                            <h2 class="text-xl font-bold mb-2" style="color: #fed501;">{{ $post->title }}</h2>
                            <p class="text-gray-600 mb-4 flex-grow">
                                {!! Str::limit(strip_tags($post->content), 100) !!}
                            </p>
                            <div class="flex justify-end mt-auto">
                                <span class="px-4 py-2 rounded-lg transition-colors inline-flex items-center"
                                      style="background-color: #fed501; color: black;">
                                    Číst více
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-600">V této kategorii zatím nejsou žádné příspěvky.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection 