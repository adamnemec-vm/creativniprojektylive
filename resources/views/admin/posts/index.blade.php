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

    <div class="bg-gray-900 rounded-lg shadow-lg overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-800">
                    <th class="px-6 py-3 text-left text-yellow-600">Název</th>
                    <th class="px-6 py-3 text-left text-yellow-600">Kategorie</th>
                    <th class="px-6 py-3 text-left text-yellow-600">Vytvořeno</th>
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
@endsection 