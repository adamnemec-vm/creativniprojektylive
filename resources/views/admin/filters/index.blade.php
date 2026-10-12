@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-brand">Filtry příspěvků</h2>
        <p class="text-gray-400 text-sm mt-1">
            Hodnoty, podle kterých návštěvníci filtrují práce na webu. Autor je u každého příspěvku musí vybrat.
            Pořadí určuje, jak se hodnoty zobrazí v nabídce; školní roky se řadí automaticky od nejnovějšího.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @foreach($groups as $filter => $options)
            <section class="bg-gray-900 rounded-lg shadow-lg overflow-hidden flex flex-col">
                <div class="flex items-center justify-between gap-2 px-4 py-3 bg-gray-800">
                    <h3 class="font-semibold">{{ \App\Models\FilterOption::FILTERS[$filter]['label'] }}</h3>
                    <a href="{{ route('admin.filters.create', ['filter' => $filter]) }}" class="px-3 py-1 rounded-lg text-sm bg-brand text-black whitespace-nowrap">
                        Přidat
                    </a>
                </div>

                <ul class="divide-y divide-gray-800 flex-1">
                    @forelse($options as $option)
                        @php($count = $counts[$option->id] ?? 0)
                        <li class="px-4 py-3 flex items-center justify-between gap-3">
                            <div>
                                <span class="font-medium">{{ $option->name }}</span>
                                <span class="block text-xs text-gray-500">
                                    @unless($filter === 'school_year') pořadí {{ $option->sort_order }} · @endunless
                                    {{ $count }} {{ $count === 1 ? 'příspěvek' : ($count >= 2 && $count <= 4 ? 'příspěvky' : 'příspěvků') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3 text-sm whitespace-nowrap">
                                <a href="{{ route('admin.filters.edit', $option) }}" class="text-yellow-600 hover:text-yellow-500">Upravit</a>
                                @if($count === 0)
                                    <form action="{{ route('admin.filters.destroy', $option) }}" method="POST" class="inline"
                                          onsubmit="return confirm(@js('Opravdu chcete smazat hodnotu „'.$option->name.'“?'));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-400">Smazat</button>
                                    </form>
                                @else
                                    <span class="text-gray-500" title="Hodnotu používají příspěvky, nelze ji smazat">Smazat</span>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-6 text-center text-gray-500 text-sm">Zatím žádné hodnoty.</li>
                    @endforelse
                </ul>
            </section>
        @endforeach
    </div>
@endsection
