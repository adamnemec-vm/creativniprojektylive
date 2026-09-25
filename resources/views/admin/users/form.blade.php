@extends('layouts.admin')

@php
    $inputClass = fn (string $field) => 'w-full bg-gray-800 border rounded-lg px-4 py-2 text-white focus:outline-none focus:border-yellow-500 '
        .($errors->has($field) ? 'border-red-500' : 'border-gray-700');
    $isSelf = $user->exists && $user->is(auth()->user());
@endphp

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-brand">{{ $user->exists ? 'Upravit uživatele' : 'Přidat uživatele' }}</h2>
        <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-white transition-colors">
            Zpět na přehled
        </a>
    </div>

    <div class="bg-gray-900 rounded-lg shadow-lg p-6 max-w-2xl">
        <form action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
            @csrf
            @if($user->exists)
                @method('PUT')
            @endif

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-400 mb-2">Jméno</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required autofocus class="{{ $inputClass('name') }}">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label for="username" class="block text-sm font-medium text-gray-400 mb-2">Uživatelské jméno</label>
                <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required class="{{ $inputClass('username') }}">
                @error('username') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-400 mb-2">E-mail</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="{{ $inputClass('email') }}">
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <fieldset class="mb-6">
                <legend class="block text-sm font-medium text-gray-400 mb-2">Role</legend>
                <div class="space-y-2">
                    <label class="flex items-start gap-3 p-3 rounded-lg bg-gray-800 {{ $isSelf ? 'opacity-60' : 'cursor-pointer' }}">
                        <input type="radio" name="role" value="editor" class="mt-1 accent-yellow-500"
                               @checked(old('role', $user->role) === 'editor') @disabled($isSelf)>
                        <span>
                            <span class="font-medium">Editor</span>
                            <span class="block text-sm text-gray-400">Vytváří příspěvky a upravuje, publikuje a maže jen ty své.</span>
                        </span>
                    </label>
                    <label class="flex items-start gap-3 p-3 rounded-lg bg-gray-800 {{ $isSelf ? 'opacity-60' : 'cursor-pointer' }}">
                        <input type="radio" name="role" value="admin" class="mt-1 accent-yellow-500"
                               @checked(old('role', $user->role) === 'admin') @disabled($isSelf)>
                        <span>
                            <span class="font-medium">Administrátor</span>
                            <span class="block text-sm text-gray-400">Plný přístup: všechny příspěvky, kategorie a správa uživatelů.</span>
                        </span>
                    </label>
                </div>
                @if($isSelf)
                    <input type="hidden" name="role" value="{{ $user->role }}">
                    <p class="text-gray-500 text-sm mt-1">Vlastní roli si změnit nemůžete.</p>
                @endif
                @error('role') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </fieldset>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-400 mb-2">
                    {{ $user->exists ? 'Nové heslo' : 'Heslo' }}
                </label>
                <input type="password" name="password" id="password" @required(! $user->exists) autocomplete="new-password" class="{{ $inputClass('password') }}">
                @if($user->exists)
                    <p class="text-gray-500 text-sm mt-1">Vyplňte jen pokud chcete heslo změnit.</p>
                @endif
                @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-8">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-400 mb-2">Potvrzení hesla</label>
                <input type="password" name="password_confirmation" id="password_confirmation" @required(! $user->exists) autocomplete="new-password"
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-yellow-500">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 rounded-lg font-medium transition-colors bg-brand text-black">
                    {{ $user->exists ? 'Uložit změny' : 'Vytvořit uživatele' }}
                </button>
            </div>
        </form>
    </div>
@endsection
