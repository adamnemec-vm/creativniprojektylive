@extends('layouts.app')

@section('content')
    <!-- Sekce představení oborů -->
    <div class="relative -mt-24">
        <div class="w-screen bg-white">
            <!-- Obsah sekce -->
            <div class="relative pt-40 pb-16">
                <div class="container mx-auto px-4">
                    <div class="max-w-7xl mx-auto">
                        <p class="text-xl text-gray-600 text-center mb-4">
                            Projekty našich studentů
                        </p>
                        <h2 class="text-4xl md:text-5xl font-extrabold mb-16 text-center font-display" style="color: #fed501;">
                            Naše obory
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                            <div class="group h-full">
                                <div class="bg-gray-900 bg-opacity-90 backdrop-blur-sm rounded-lg p-8 shadow-xl transform group-hover:-translate-y-2 transition-all duration-300 border-t-4 h-full flex flex-col" 
                                     style="border-color: #fed501;">
                                    <div class="flex items-center mb-6">
                                        <svg class="w-8 h-8 mr-3 flex-shrink-0" style="color: #fed501;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                        </svg>
                                        <h3 class="text-2xl font-bold" style="color: #fed501;">Vývojáři</h3>
                                    </div>
                                    <p class="leading-relaxed text-black flex-grow">
                                        Naši vývojáři se specializují na tvorbu moderních webových a mobilních aplikací. 
                                        Využíváme nejnovější technologie a postupy pro vytváření efektivních a 
                                        bezpečných řešení. Klademe důraz na kvalitní kód a uživatelskou přívětivost.
                                    </p>
                                </div>
                            </div>

                            <div class="group h-full">
                                <div class="bg-gray-900 bg-opacity-90 backdrop-blur-sm rounded-lg p-8 shadow-xl transform group-hover:-translate-y-2 transition-all duration-300 border-t-4 h-full flex flex-col" 
                                     style="border-color: #fed501;">
                                    <div class="flex items-center mb-6">
                                        <svg class="w-8 h-8 mr-3 flex-shrink-0" style="color: #fed501;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <h3 class="text-2xl font-bold" style="color: #fed501;">Grafici</h3>
                                    </div>
                                    <p class="leading-relaxed text-black flex-grow">
                                        Náš grafický tým vytváří jedinečné vizuální identity a designová řešení. 
                                        Od loga přes webdesign až po komplexní brandingové strategie. 
                                        Kombinujeme kreativitu s funkčností pro maximální efekt.
                                    </p>
                                </div>
                            </div>

                            <div class="group h-full">
                                <div class="bg-gray-900 bg-opacity-90 backdrop-blur-sm rounded-lg p-8 shadow-xl transform group-hover:-translate-y-2 transition-all duration-300 border-t-4 h-full flex flex-col" 
                                     style="border-color: #fed501;">
                                    <div class="flex items-center mb-6">
                                        <svg class="w-8 h-8 mr-3 flex-shrink-0" style="color: #fed501;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <h3 class="text-2xl font-bold" style="color: #fed501;">Filmaři</h3>
                                    </div>
                                    <p class="leading-relaxed text-black flex-grow">
                                        Zachycujeme příběhy prostřednictvím videí a filmů. Specializujeme se na 
                                        reklamní spoty, dokumentární tvorbu a firemní prezentace. Každý projekt 
                                        je pro nás jedinečnou příležitostí vytvořit něco výjimečného.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dekorativní oddělovač -->
    <div class="relative h-32 w-screen overflow-hidden -mb-16">
        <!-- Hlavní gradient -->
        <div class="absolute inset-0 bg-gradient-to-b from-white via-gray-200 to-gray-100"></div>
        
        <!-- Dekorativní prvky -->
        <div class="absolute inset-0">
            <!-- Šikmé pruhy -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0 transform -skew-y-12 bg-gradient-to-r" style="background: linear-gradient(to right, #fed501, transparent);"></div>
                <div class="absolute inset-0 transform skew-y-12 bg-gradient-to-l translate-x-1/2" style="background: linear-gradient(to left, #fed501, transparent);"></div>
            </div>
            
            <!-- Jemná mřížka -->
            <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
        </div>
        
        <!-- Spodní stínování -->
        <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-gray-100"></div>
    </div>

    <!-- Sekce s příspěvky -->
    <div class="relative bg-gray-100">
        <!-- Nadpis -->
        <div class="absolute top-0 left-0 right-0 transform -translate-y-1/2">
            <h2 class="text-3xl font-bold text-center text-black bg-gray-100 mx-auto w-max px-8 py-4 rounded-full">
                Nejnovější příspěvky
            </h2>
        </div>

        <!-- Obsah -->
        <div class="container mx-auto px-4 pt-16 pb-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($posts as $post)
                    <a href="{{ route('posts.show', $post) }}" class="block group">
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden relative h-full flex flex-col transition-transform duration-300 hover:-translate-y-2">
                            <!-- Kategorie tag -->
                            <div class="absolute top-2 right-2 z-10">
                                <span class="bg-black bg-opacity-50 px-3 py-1 rounded-full text-sm" 
                                      style="color: #fed501;">
                                    {{ $post->category->name }}
                                </span>
                            </div>

                            <!-- Obrázek -->
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

                            <!-- Obsah -->
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
                        <p class="text-gray-600">Zatím zde nejsou žádné příspěvky.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection 