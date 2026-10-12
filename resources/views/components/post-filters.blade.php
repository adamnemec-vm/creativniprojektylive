@props(['category' => null])

@php
    use App\Models\FilterOption;
    use App\Models\Post;

    $visibleYears = 4;
    $query = request()->query();
    $search = is_string($query['q'] ?? null) ? trim($query['q']) : '';

    // Počty publikovaných prací u každé hodnoty (v rámci kategorie, pokud jsme na její stránce).
    $counts = [];
    foreach (FilterOption::FILTERS as $config) {
        $counts = array_replace($counts, Post::published()
            ->when($category, fn ($q) => $q->where('category_id', $category->id))
            ->whereNotNull($config['column'])
            ->groupBy($config['column'])
            ->selectRaw($config['column'].' as option_id, count(*) as total')
            ->pluck('total', 'option_id')
            ->all());
    }

    $fields = collect(FilterOption::grouped())->map(function ($options, $filter) use ($query, $counts, $visibleYears) {
        $config = FilterOption::FILTERS[$filter];
        $selected = FilterOption::parseIds($query[$config['param']] ?? null);
        $isSelected = fn ($option) => in_array($option->id, $selected, true);
        // Hodnoty bez jediné práce nenabízíme (např. budoucí školní rok), vybrané ale ano.
        $visible = $options->filter(fn ($option) => ($counts[$option->id] ?? 0) > 0 || $isSelected($option))->values();
        $primary = $filter === 'school_year' ? $visible->take($visibleYears) : $visible;
        $more = $visible->slice($primary->count())->values();

        return $config + [
            'filter' => $filter,
            'primary' => $primary,
            'more' => $more,
            'expanded' => $more->contains($isSelected),
            'active' => $options->filter($isSelected)->values(),
        ];
    });

    $selection = $fields->mapWithKeys(fn ($field) => [$field['param'] => $field['active']->pluck('id')->all()])->all();
    $activeCount = collect($selection)->flatten()->count();

    // Odkaz se zadaným výběrem filtrů; hledaný text zůstává.
    $urlFor = function (array $selection) use ($search) {
        $params = array_map(fn ($ids) => implode(',', $ids), array_filter($selection));
        if ($search !== '') {
            $params['q'] = $search;
        }
        $queryString = str_replace('%2C', ',', http_build_query($params));

        return url()->current().($queryString !== '' ? '?'.$queryString : '').'#prispevky';
    };

    $pill = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-sm cursor-pointer select-none transition-colors '
        .'border-gray-300 bg-white text-gray-700 hover:border-gray-500 '
        .'peer-checked:bg-black peer-checked:border-black peer-checked:text-brand '
        .'peer-focus-visible:ring-2 peer-focus-visible:ring-brand peer-focus-visible:ring-offset-1';
@endphp

<form method="GET" action="{{ url()->current() }}#prispevky" role="search" x-data
      class="mb-6 flex flex-col-reverse md:flex-row md:items-center md:justify-between gap-3 text-sm"
      onsubmit="return window.submitPostFilters ? window.submitPostFilters(this) : true">

    {{-- Aktivní filtry: každá vybraná hodnota zvlášť --}}
    @if($activeCount > 0)
        <div class="flex flex-wrap items-center gap-2">
            @foreach($fields as $field)
                @foreach($field['active'] as $option)
                    <a href="{{ $urlFor(array_merge($selection, [$field['param'] => array_values(array_diff($selection[$field['param']], [$option->id]))])) }}"
                       class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1 rounded-full bg-black text-brand text-xs font-semibold hover:bg-gray-800"
                       title="Odebrat filtr {{ $field['label'] }}: {{ $option->name }}">
                        {{ $option->name }}
                        <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
                        <span class="sr-only">odebrat</span>
                    </a>
                @endforeach
            @endforeach
            @if($activeCount > 1)
                <a href="{{ $urlFor([]) }}" class="text-gray-500 underline hover:text-black text-xs">Zrušit vše</a>
            @endif
        </div>
    @endif

    {{-- Tlačítko a hledání --}}
    <div class="flex gap-2 md:ml-auto">
        <button type="button" @click="$refs.dialog.showModal()"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md border font-medium transition-colors
                       {{ $activeCount > 0 ? 'bg-black border-black text-brand' : 'bg-white border-gray-300 text-gray-800 hover:border-gray-500' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18l-7 8.5V19l-4 2v-8.5L3 4z"/>
            </svg>
            Filtrovat
            @if($activeCount > 0)
                <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-brand text-black text-xs">{{ $activeCount }}</span>
            @endif
        </button>

        <label for="q" class="sr-only">Hledat v příspěvcích</label>
        <input type="search" name="q" id="q" value="{{ $search }}" placeholder="Hledat v příspěvcích…"
               class="flex-1 md:flex-none md:w-56 min-w-0 px-3 py-1.5 rounded-md border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-brand">
        <button type="submit" class="px-3 py-1.5 rounded-md bg-brand text-black font-medium">Hledat</button>
    </div>

    {{-- Okno s filtry --}}
    <dialog x-ref="dialog" @click="if ($event.target === $el) $el.close()"
            aria-labelledby="filters-title"
            class="w-[calc(100%-2rem)] max-w-lg rounded-xl p-0 shadow-2xl backdrop:bg-black/60 backdrop:backdrop-blur-sm">
        <div class="flex items-start justify-between px-6 py-4 border-b">
            <div>
                <h2 id="filters-title" class="text-lg font-bold text-black">Filtrovat práce</h2>
                <p class="text-xs text-gray-500">V každé skupině můžete vybrat více možností.</p>
            </div>
            <button type="button" @click="$refs.dialog.close()" class="p-1 rounded text-gray-500 hover:text-black" aria-label="Zavřít">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="px-6 py-5 space-y-6 max-h-[65vh] overflow-y-auto">
            @foreach($fields as $field)
                <fieldset x-data="{ more: {{ $field['expanded'] ? 'true' : 'false' }} }">
                    <legend class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $field['label'] }}</legend>

                    @if($field['primary']->isEmpty())
                        <p class="text-gray-400 text-sm">Zatím žádné práce.</p>
                    @endif

                    <div class="flex flex-wrap gap-2">
                        @foreach([['options' => $field['primary'], 'hidden' => false], ['options' => $field['more'], 'hidden' => true]] as $part)
                            @foreach($part['options'] as $option)
                                <label @if($part['hidden']) x-show="more" @unless($field['expanded']) x-cloak @endunless @endif>
                                    <input type="checkbox" name="{{ $field['param'] }}[]" value="{{ $option->id }}" data-param="{{ $field['param'] }}"
                                           class="peer sr-only" @checked(in_array($option->id, $selection[$field['param']], true))>
                                    <span class="{{ $pill }}">
                                        {{ $option->name }}
                                        <span class="text-xs opacity-60">{{ $counts[$option->id] ?? 0 }}</span>
                                    </span>
                                </label>
                            @endforeach
                        @endforeach
                    </div>

                    @if($field['more']->isNotEmpty())
                        <button type="button" @click="more = !more" class="mt-3 text-xs text-gray-600 underline hover:text-black"
                                x-text="more ? 'Skrýt starší' : @js('Starší školní roky ('.$field['more']->count().')')">
                            Starší školní roky ({{ $field['more']->count() }})
                        </button>
                    @endif
                </fieldset>
            @endforeach
        </div>

        <div class="flex items-center justify-between gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-xl">
            <a href="{{ $urlFor([]) }}" class="text-gray-600 underline hover:text-black">Zrušit filtry</a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-brand text-black font-semibold hover:brightness-95">Použít filtry</button>
        </div>
    </dialog>
</form>
