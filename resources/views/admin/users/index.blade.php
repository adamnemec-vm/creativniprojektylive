@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-brand">Správa uživatelů</h2>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 rounded-lg transition-colors bg-brand text-black whitespace-nowrap">
            Přidat uživatele
        </a>
    </div>

    <div class="bg-gray-900 rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-800 text-gray-300">
                    <tr>
                        <th class="p-4">Jméno</th>
                        <th class="p-4">Uživatelské jméno</th>
                        <th class="p-4">E-mail</th>
                        <th class="p-4">Role</th>
                        <th class="p-4">Příspěvků</th>
                        <th class="p-4 text-right">Akce</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-800 transition-colors">
                            <td class="p-4 font-medium">
                                {{ $user->name }}
                                @if($user->is(auth()->user()))
                                    <span class="text-gray-500 text-sm">(vy)</span>
                                @endif
                            </td>
                            <td class="p-4 text-gray-400">{{ $user->username }}</td>
                            <td class="p-4">{{ $user->email }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-xs {{ $user->isAdmin() ? 'bg-brand text-black' : 'bg-gray-700 text-gray-200' }}">
                                    {{ $user->roleLabel() }}
                                </span>
                            </td>
                            <td class="p-4 text-gray-400">{{ $user->posts_count }}</td>
                            <td class="p-4 text-right whitespace-nowrap space-x-3">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-500">Upravit</a>
                                @unless($user->is(auth()->user()))
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block"
                                          onsubmit="return confirm('Opravdu chcete tohoto uživatele smazat? Jeho příspěvky zůstanou zachované bez autora.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-400 transition-colors">Smazat</button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-400">Nebyli nalezeni žádní uživatelé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-gray-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
