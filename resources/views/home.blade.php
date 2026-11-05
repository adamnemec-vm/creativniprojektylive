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
                            Projekty Creative Hill College
                        </p>
                        <h2 class="text-4xl md:text-5xl font-extrabold mb-16 text-center font-display text-brand">
                            Naše zaměření
                        </h2>
                        
                        @php
                            // Karty se k oborům (kategoriím) přiřazují podle názvu.
                            $fields = [
                                [
                                    'name' => 'Vývojáři',
                                    'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
                                    'text' => 'Naši vývojáři se zaměřují na tvorbu moderních webových a mobilních aplikací s důrazem na efektivitu, bezpečnost a skvělý uživatelský zážitek. Využíváme nejnovější technologie a dbáme na čistý, kvalitní kód.',
                                ],
                                [
                                    'name' => 'Grafici',
                                    'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
                                    'text' => 'Náš grafický tým tvoří originální vizuální identity a designová řešení – od loga a webdesignu až po komplexní branding. Spojujeme kreativitu s funkčností, aby každý projekt vynikl a oslovil cílovou skupinu.',
                                ],
                                [
                                    'name' => 'Filmaři',
                                    'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
                                    'text' => 'Vyprávíme příběhy prostřednictvím obrazu a zvuku. Dokážeme stvořit reklamní spot, dokumentární snímek či třeba krátký film o vaší firmě. Naši mladí filmaři jsou plní neotřelých nápadů.',
                                ],
                            ];
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                            @foreach($fields as $field)
                                @php($category = $categoriesByName->get($field['name']))
                                {{-- Bez odpovídající kategorie (např. po přejmenování) zůstane karta bez odkazu. --}}
                                <{{ $category ? 'a' : 'div' }}
                                    @if($category) href="{{ route('categories.show', $category) }}" aria-label="{{ $field['name'] }} – zobrazit práce oboru" @endif
                                    class="group h-full block rounded-lg focus:outline-none focus-visible:ring-4 focus-visible:ring-brand focus-visible:ring-offset-2">
                                    <div class="bg-gray-900 bg-opacity-90 backdrop-blur-sm rounded-lg p-8 shadow-xl transform group-hover:-translate-y-2 group-hover:shadow-2xl transition-all duration-300 border-t-4 h-full flex flex-col border-brand">
                                        <div class="flex items-center mb-6">
                                            <svg class="w-8 h-8 mr-3 flex-shrink-0 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $field['icon'] }}"/>
                                            </svg>
                                            <h3 class="text-2xl font-bold text-brand">{{ $field['name'] }}</h3>
                                        </div>
                                        <p class="leading-relaxed text-black flex-grow">
                                            {{ $field['text'] }}
                                        </p>
                                        @if($category)
                                            <span class="mt-6 inline-flex items-center gap-2 font-semibold text-black">
                                                Prohlédnout práce
                                                <svg class="w-4 h-4 text-brand transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6l6 6-6 6"/>
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                </{{ $category ? 'a' : 'div' }}>
                            @endforeach
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

    @php($filtering = \App\Models\FilterOption::isFiltering(request()->query()))

    <!-- Sekce s příspěvky -->
    <div class="relative bg-gray-100" id="prispevky">
        <!-- Nadpis -->
        <div class="absolute top-0 left-0 right-0 transform -translate-y-1/2 flex justify-center">
            <h2 class="text-3xl font-bold text-center px-8 py-4 rounded-full shadow-lg text-brand bg-slate-50">
                {{ $filtering ? 'Výsledky hledání' : 'Nejnovější příspěvky' }}
            </h2>
        </div>

        <!-- Obsah -->
        <div class="container mx-auto px-4 pt-16 pb-16">
            <x-post-filters />

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($posts as $post)
                    <x-post-card :post="$post" />
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-600">
                            {{ $filtering ? 'Zvoleným filtrům neodpovídá žádný příspěvek.' : 'Zatím zde nejsou žádné příspěvky.' }}
                        </p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
@endsection