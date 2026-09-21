@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold" style="color: #fed501;">Přidat uživatele</h2>
        <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-white transition-colors">
            Zpět na přehled
        </a>
    </div>

    <div class="bg-gray-900 rounded-lg shadow-lg p-6 max-w-2xl">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            
            <!-- Jméno -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-400 mb-2">Jméno</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                       class="w-full bg-gray-800 border @error('name') border-red-500 @else border-gray-700 @enderror rounded-lg px-4 py-2 text-white focus:outline-none focus:border-yellow-500">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Uživatelské jméno -->
            <div class="mb-6">
                <label for="username" class="block text-sm font-medium text-gray-400 mb-2">Uživatelské jméno</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" required
                       class="w-full bg-gray-800 border @error('username') border-red-500 @else border-gray-700 @enderror rounded-lg px-4 py-2 text-white focus:outline-none focus:border-yellow-500">
                @error('username')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- E-mail -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-400 mb-2">E-mail</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       class="w-full bg-gray-800 border @error('email') border-red-500 @else border-gray-700 @enderror rounded-lg px-4 py-2 text-white focus:outline-none focus:border-yellow-500">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Heslo -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-400 mb-2">Heslo</label>
                <input type="password" name="password" id="password" required
                       class="w-full bg-gray-800 border @error('password') border-red-500 @else border-gray-700 @enderror rounded-lg px-4 py-2 text-white focus:outline-none focus:border-yellow-500">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Potvrzení hesla -->
            <div class="mb-8">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-400 mb-2">Potvrzení hesla</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-yellow-500">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 rounded-lg font-medium transition-colors"
                        style="background-color: #fed501; color: black;">
                    Vytvořit uživatele
                </button>
            </div>
        </form>
    </div>
@endsection
