@extends('layouts.app')

@section('title', 'Přihlášení')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-gray-900 p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-brand">Přihlášení</h2>

        @if ($errors->any())
        <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="username" class="block mb-2 text-brand">Uživatelské jméno</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus autocomplete="username"
                    class="w-full px-3 py-2 bg-gray-800 rounded-lg text-white focus:outline-none border border-brand">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-yellow-600 mb-2">Heslo</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-3 py-2 bg-gray-800 border border-yellow-600 rounded-lg text-white focus:outline-none focus:border-yellow-400">
            </div>

            <button type="submit" 
                class="w-full py-2 px-4 rounded-lg transition-colors bg-brand text-black">
                Přihlásit se
            </button>
        </form>
    </div>
</div>
@endsection 