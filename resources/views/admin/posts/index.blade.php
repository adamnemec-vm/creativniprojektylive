@extends('layouts.admin')

@php
    $isAdmin = auth()->user()->isAdmin();
    $sortBy = request('sort_by', 'created_at');
    $sortDir = request('sort_direction') === 'asc' ? 'asc' : 'desc';
    $sortUrl = fn (string $column) => request()->fullUrlWithQuery([
        'sort_by' => $column,
        'sort_direction' => $sortBy === $column && $sortDir === 'asc' ? 'desc' : 'asc',
        'page' => null,
    ]);
    $arrow = fn (string $column) => $sortBy === $column ? ($sortDir === 'asc' ? '↑' : '↓') : '';
    $statusColors = [
        'Publikováno' => 'bg-green-700 text-white',
        'Naplánováno' => 'bg-blue-700 text-white',
        'Koncept' => 'bg-gray-700 text-gray-200',
    ];
@endphp

@section('content')
    <div class="flex justify-between items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-brand">{{ $isAdmin ? 'Správa příspěvků' : 'Moje příspěvky' }}</h2>
        <a href="{{ route('admin.posts.create') }}" class="px-4 py-2 rounded-lg transition-colors bg-brand text-black whitespace-nowrap">
            Nový příspěvek
        </a>
    </div>

    <div class="mb-6 bg-gray-900 rounded-lg shadow-lg p-4">
        <form method="GET" action="{{ route('admin.posts.index') }}" class="flex flex-wrap items-center gap-4">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Hledat podle názvu..."
                   class="flex-1 min-w-[12rem] px-4 py-2 bg-gray-800 border border-yellow-600 rounded-lg text-white focus:outline-none focus:border-yellow-400">
            <select name="status" class="px-4 py-2 bg-gray-800 border border-yellow-600 rounded-lg text-white focus:outline-none">
                <option value="">Všechny stavy</option>
                <option value="published" @selected(request('status') === 'published')>Publikované</option>
                <option value="scheduled" @selected(request('status') === 'scheduled')>Naplánované</option>
                <option value="draft" @selected(request('status') === 'draft')>Koncepty</option>
            </select>
            @if(request('sort_by'))
                <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                <input type="hidden" name="sort_direction" value="{{ request('sort_direction') }}">
            @endif
            <button type="submit" class="px-6 py-2 rounded-lg transition-colors bg-brand text-black">
                Filtrovat
            </button>
            @if(request()->hasAny(['search', 'status', 'sort_by']))
                <a href="{{ route('admin.posts.index') }}" class="px-6 py-2 rounded-lg border border-yellow-600 text-yellow-600 hover:bg-yellow-600 hover:text-black transition-colors">
                    Zrušit filtry
                </a>
            @endif
        </form>
    </div>

    <div class="bg-gray-900 rounded-lg shadow-lg overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-800 text-left">
                    <th class="px-6 py-3">
                        <a href="{{ $sortUrl('title') }}" class="text-yellow-600 hover:text-yellow-500">Název {{ $arrow('title') }}</a>
                    </th>
                    <th class="px-6 py-3">
                        <a href="{{ $sortUrl('category') }}" class="text-yellow-600 hover:text-yellow-500">Kategorie {{ $arrow('category') }}</a>
                    </th>
                    @if($isAdmin)
                        <th class="px-6 py-3 text-yellow-600">Autor</th>
                    @endif
                    <th class="px-6 py-3 text-yellow-600">Stav</th>
                    <th class="px-6 py-3">
                        <a href="{{ $sortUrl('created_at') }}" class="text-yellow-600 hover:text-yellow-500">Vytvořeno {{ $arrow('created_at') }}</a>
                    </th>
                    <th class="px-6 py-3 text-yellow-600">Akce</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse($posts as $post)
                    <tr>
                        <td class="px-6 py-4">{{ $post->title }}</td>
                        <td class="px-6 py-4">{{ $post->category->name }}</td>
                        @if($isAdmin)
                            <td class="px-6 py-4 text-gray-400">{{ $post->author?->name ?? '—' }}</td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 rounded text-xs {{ $statusColors[$post->statusLabel()] }}"
                                  @if($post->published_at) title="{{ $post->published_at->format('d.m.Y H:i') }}" @endif>
                                {{ $post->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $post->created_at->format('d.m.Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-3 whitespace-nowrap">
                                <a href="{{ route('posts.show', $post) }}" target="_blank" class="text-gray-300 hover:text-white">
                                    {{ $post->isPublished() ? 'Zobrazit' : 'Náhled' }}
                                </a>
                                <a href="{{ route('admin.posts.edit', $post) }}" class="text-yellow-600 hover:text-yellow-500">
                                    Upravit
                                </a>
                                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}"
                                      class="inline"
                                      onsubmit="return confirm('Opravdu chcete smazat tento příspěvek?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-500">
                                        Smazat
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 6 : 5 }}" class="px-6 py-4 text-center text-gray-400">
                            Žádné příspěvky neodpovídají filtru.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $posts->links() }}
    </div>
@endsection
