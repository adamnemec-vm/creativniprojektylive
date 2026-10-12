@extends('layouts.admin')

@php($label = $option->label())

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-brand">
            {{ $option->exists ? 'Upravit hodnotu' : 'Nová hodnota' }} – {{ $label }}
        </h2>
        <a href="{{ route('admin.filters.index') }}" class="text-gray-400 hover:text-white transition-colors">
            Zpět na přehled
        </a>
    </div>

    <div class="bg-gray-900 rounded-lg shadow-lg p-6 max-w-xl">
        <form action="{{ $option->exists ? route('admin.filters.update', $option) : route('admin.filters.store') }}" method="POST">
            @csrf
            @if($option->exists)
                @method('PUT')
            @else
                <input type="hidden" name="filter" value="{{ $option->filter }}">
            @endif

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-400 mb-2">Název</label>
                <input type="text" name="name" id="name" value="{{ old('name', $option->name) }}" required maxlength="50" autofocus
                       placeholder="{{ ['work_type' => 'např. Klauzurní práce', 'school_year' => 'např. 2028/29', 'grade' => 'např. 1. ročník'][$option->filter] }}"
                       class="w-full bg-gray-800 border @error('name') border-red-500 @else border-gray-700 @enderror rounded-lg px-4 py-2 text-white focus:outline-none focus:border-yellow-500">
                @if($option->exists && ($used = $option->postsCount()) > 0)
                    <p class="text-gray-500 text-sm mt-1">Přejmenování se projeví u všech {{ $used }} příspěvků s touto hodnotou.</p>
                @endif
            </div>

            @if($option->filter === 'school_year')
                <p class="mb-8 text-gray-500 text-sm">Školní roky se řadí automaticky od nejnovějšího. Na webu se zobrazí jen několik posledních let, starší si návštěvník rozbalí.</p>
            @else
            <div class="mb-8">
                <label for="sort_order" class="block text-sm font-medium text-gray-400 mb-2">Pořadí v nabídce</label>
                <input type="number" name="sort_order" id="sort_order" min="0" max="9999"
                       value="{{ old('sort_order', $option->exists ? $option->sort_order : '') }}"
                       class="w-32 bg-gray-800 border @error('sort_order') border-red-500 @else border-gray-700 @enderror rounded-lg px-4 py-2 text-white focus:outline-none focus:border-yellow-500">
                <p class="text-gray-500 text-sm mt-1">Menší číslo je výš. {{ $option->exists ? '' : 'Když necháte prázdné, hodnota se zařadí na konec.' }}</p>
            </div>
            @endif

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 rounded-lg font-medium bg-brand text-black">
                    {{ $option->exists ? 'Uložit změny' : 'Přidat hodnotu' }}
                </button>
            </div>
        </form>
    </div>
@endsection
