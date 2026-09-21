@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold" style="color: #fed501;">Správa příspěvků</h2>
        <a href="{{ route('admin.posts.create') }}" 
           class="px-4 py-2 rounded-lg transition-colors"
           style="background-color: #fed501; color: black;">
            Nový příspěvek
        </a>
    </div>

    <div class="mb-6 bg-gray-900 rounded-lg shadow-lg p-4">
        <form method="GET" action="{{ route('admin.posts.index') }}" class="flex items-center space-x-4">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Hledat podle názvu..." 
                   class="flex-1 px-4 py-2 bg-gray-800 border border-yellow-600 rounded-lg text-white focus:outline-none focus:border-yellow-400">
            <!-- Skryté pole pro zachování řazení při vyhledávání -->
            @if(request('sort_by'))
                <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                <input type="hidden" name="sort_direction" value="{{ request('sort_direction') }}">
            @endif
            <button type="submit" class="px-6 py-2 rounded-lg transition-colors" style="background-color: #fed501; color: black;">
                Hledat
            </button>
            @if(request('search') || request('sort_by'))
                <a href="{{ route('admin.posts.index') }}" class="px-6 py-2 rounded-lg border border-yellow-600 text-yellow-600 hover:bg-yellow-600 hover:text-black transition-colors">
                    Zrušit filtry
                </a>
            @endif
        </form>
    </div>

    <div class="bg-gray-900 rounded-lg shadow-lg overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-800">
                    <th class="px-6 py-3 text-left">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'title', 'sort_direction' => request('sort_direction') === 'asc' && request('sort_by') === 'title' ? 'desc' : 'asc']) }}" class="text-yellow-600 hover:text-yellow-500 flex items-center">
                            Název
                            @if(request('sort_by') === 'title')
                                <span class="ml-1">{!! request('sort_direction') === 'asc' ? '&uarr;' : '&darr;' !!}</span>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'category', 'sort_direction' => request('sort_direction') === 'asc' && request('sort_by') === 'category' ? 'desc' : 'asc']) }}" class="text-yellow-600 hover:text-yellow-500 flex items-center">
                            Kategorie
                            @if(request('sort_by') === 'category')
                                <span class="ml-1">{!! request('sort_direction') === 'asc' ? '&uarr;' : '&darr;' !!}</span>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_direction' => request('sort_direction', 'desc') === 'asc' && (request('sort_by') === 'created_at' || !request('sort_by')) ? 'desc' : 'asc']) }}" class="text-yellow-600 hover:text-yellow-500 flex items-center">
                            Vytvořeno
                            @if(request('sort_by') === 'created_at' || !request('sort_by'))
                                <span class="ml-1">{!! request('sort_direction', 'desc') === 'asc' ? '&uarr;' : '&darr;' !!}</span>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-yellow-600">Akce</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse($posts as $post)
                    <tr>
                        <td class="px-6 py-4">{{ $post->title }}</td>
                        <td class="px-6 py-4">{{ $post->category->name }}</td>
                        <td class="px-6 py-4">{{ $post->created_at->format('d.m.Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.posts.edit', $post) }}" 
                                   class="text-yellow-600 hover:text-yellow-500">
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
                        <td colspan="4" class="px-6 py-4 text-center text-gray-400">
                            Zatím zde nejsou žádné příspěvky.
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