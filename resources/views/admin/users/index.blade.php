@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold" style="color: #fed501;">Správa uživatelů</h2>
        <a href="{{ route('admin.users.create') }}" 
           class="px-4 py-2 rounded-lg transition-colors"
           style="background-color: #fed501; color: black;">
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
                        <th class="p-4">Vytvořeno</th>
                        <th class="p-4 text-right">Akce</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-800 transition-colors">
                            <td class="p-4 font-medium">{{ $user->name }}</td>
                            <td class="p-4 text-gray-400">{{ $user->username }}</td>
                            <td class="p-4">{{ $user->email }}</td>
                            <td class="p-4 text-gray-400">{{ $user->created_at->format('d.m.Y H:i') }}</td>
                            <td class="p-4 text-right space-x-2">
                                @if($user->username === 'admin' || $user->id === 1)
                                    <span class="text-gray-500 text-sm font-medium">Hlavní admin</span>
                                @elseif(auth()->id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Opravdu chcete tohoto uživatele smazat?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-400 transition-colors">
                                            Smazat
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-500 text-sm">Váš účet</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-400">
                                Nebyli nalezeni žádní uživatelé.
                            </td>
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
