@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-brand">{{ $category->exists ? 'Upravit kategorii' : 'Nová kategorie' }}</h2>
        <a href="{{ route('admin.categories.index') }}" class="text-gray-400 hover:text-white transition-colors">
            Zpět na přehled
        </a>
    </div>

    <div class="bg-gray-900 rounded-lg shadow-lg p-6 max-w-3xl">
        <form action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST">
            @csrf
            @if($category->exists)
                @method('PUT')
            @endif

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-400 mb-2">Název</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required maxlength="50" autofocus
                       class="w-full bg-gray-800 border @error('name') border-red-500 @else border-gray-700 @enderror rounded-lg px-4 py-2 text-white focus:outline-none focus:border-yellow-500">
                <p class="text-gray-500 text-sm mt-1">Zobrazuje se v hlavním menu webu, držte ho krátký.</p>
            </div>

            <div class="mb-8">
                <label for="description" class="block text-sm font-medium text-gray-400 mb-2">Popis</label>
                <textarea name="description" id="description" rows="8" required maxlength="5000"
                          class="w-full bg-gray-800 border @error('description') border-red-500 @else border-gray-700 @enderror rounded-lg px-4 py-2 text-white focus:outline-none focus:border-yellow-500">{{ old('description', $category->description) }}</textarea>
                <p class="text-gray-500 text-sm mt-1">Zobrazuje se v záhlaví stránky kategorie. Odstavce oddělte novým řádkem.</p>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 rounded-lg font-medium bg-brand text-black">
                    {{ $category->exists ? 'Uložit změny' : 'Vytvořit kategorii' }}
                </button>
            </div>
        </form>
    </div>
@endsection
