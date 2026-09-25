@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-brand">Kategorie</h2>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 rounded-lg bg-brand text-black whitespace-nowrap">
            Nová kategorie
        </a>
    </div>

    <div class="bg-gray-900 rounded-lg shadow-lg overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-800 text-gray-300">
                <tr>
                    <th class="p-4">Název</th>
                    <th class="p-4">Popis</th>
                    <th class="p-4">Příspěvků</th>
                    <th class="p-4 text-right">Akce</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-800 transition-colors">
                        <td class="p-4 font-medium">{{ $category->name }}</td>
                        <td class="p-4 text-gray-400">{{ Str::limit($category->description, 90) }}</td>
                        <td class="p-4">{{ $category->posts_count }}</td>
                        <td class="p-4 text-right whitespace-nowrap space-x-3">
                            <a href="{{ route('categories.show', $category) }}" target="_blank" class="text-gray-300 hover:text-white">Zobrazit</a>
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-yellow-600 hover:text-yellow-500">Upravit</a>
                            @if($category->posts_count === 0)
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Opravdu chcete smazat tuto kategorii?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-400">Smazat</button>
                                </form>
                            @else
                                <span class="text-gray-500 text-sm" title="Kategorii s příspěvky nelze smazat">Smazat</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-400">Zatím nejsou žádné kategorie.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
