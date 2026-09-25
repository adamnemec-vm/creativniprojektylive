@extends('layouts.app')

@section('title', $category->name)
@section('description', Str::limit(trim(preg_replace('/\s+/', ' ', $category->description)), 160))

@section('content')
<div class="bg-gray-50 min-h-screen">
    
    <!-- Prémiová Hero sekce kategorie s interaktivním roztahováním -->
    <!-- Používáme group, max-h a transition pro plynulý efekt. Přidáno Alpine.js pro mobilní rozbalení na klik. -->
    <div x-data="{ expanded: false }"
         @click="expanded = !expanded"
         class="relative bg-white overflow-hidden mb-8 shadow-lg group transition-all duration-700 ease-in-out cursor-pointer"
         :class="expanded ? 'max-h-[1500px]' : 'max-h-[140px] md:max-h-[160px] hover:max-h-[1500px]'">
        
        <!-- Fade out efekt (viditelný jen když není hover, naznačuje že text pokračuje) -->
        <div class="absolute bottom-1 left-0 right-0 h-20 bg-gradient-to-t from-white to-transparent transition-opacity duration-500 pointer-events-none z-20"
             :class="expanded ? 'opacity-0' : 'opacity-100 group-hover:opacity-0'"></div>
        
        <!-- Malá animovaná šipečka indikující možnost rozbalení -->
        <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 text-yellow-500 transition-opacity duration-500 z-30"
             :class="expanded ? 'opacity-0' : 'opacity-100 group-hover:opacity-0'">
            <svg class="w-6 h-6 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>

        <!-- Dekorativní prvky (blobs) na pozadí pro dynamiku -->
        <div class="absolute top-0 right-0 -mr-32 -mt-32 w-96 h-96 rounded-full bg-yellow-500 opacity-20 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-32 -mb-32 w-80 h-80 rounded-full bg-yellow-500 opacity-20 blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 container mx-auto px-6 py-6 max-w-6xl text-center flex flex-col items-center">
            <!-- Ikonka nebo badge nad nadpisem -->
            <div class="inline-flex items-center justify-center p-2 mb-3 rounded-full" style="background-color: rgba(254, 213, 1, 0.1);">
                <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            
            <!-- Hlavní nadpis -->
            <h1 class="text-3xl md:text-4xl font-extrabold text-black mb-6 tracking-tight">
                {{ $category->name }}
            </h1>
            
            <!-- Popis kategorie s elegantním formátováním (odhalí se na hover nebo klik na mobilu) -->
            <div class="text-base md:text-lg text-black leading-relaxed font-light space-y-3 w-full max-w-5xl mx-auto pb-4 transition-opacity duration-700"
                 :class="expanded ? 'opacity-100' : 'opacity-40 group-hover:opacity-100'">
                @foreach(explode("\n", $category->description) as $paragraph)
                    @if(trim($paragraph))
                        <p>
                            {{ trim($paragraph) }}
                        </p>
                    @endif
                @endforeach
            </div>
        </div>
        
        <!-- Spodní barevný akcent -->
        <div class="absolute bottom-0 left-0 right-0 h-1.5 z-40 bg-brand"></div>
    </div>

    <!-- Výpis příspěvků -->
    <div class="container mx-auto px-6 lg:px-8 pb-24 max-w-7xl">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($posts as $post)
                <x-post-card :post="$post" />
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-600">V této kategorii zatím nejsou žádné příspěvky.</p>
                </div>
            @endforelse
            </div>
            
            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
@endsection
