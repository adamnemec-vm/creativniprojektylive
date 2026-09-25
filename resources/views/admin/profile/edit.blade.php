@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-brand">Můj profil</h2>
    </div>

    <div class="bg-gray-900 rounded-lg shadow-lg p-6 max-w-2xl">
        <h3 class="text-xl font-bold text-white mb-6">Změna hesla</h3>
        
        <form action="{{ route('admin.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Současné heslo -->
            <div class="mb-6">
                <label for="current_password" class="block text-sm font-medium text-gray-400 mb-2">Současné heslo</label>
                <input type="password" name="current_password" id="current_password" required autofocus
                       class="w-full bg-gray-800 border @error('current_password') border-red-500 @else border-gray-700 @enderror rounded-lg px-4 py-2 text-white focus:outline-none focus:border-yellow-500">
                @error('current_password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nové heslo -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-400 mb-2">Nové heslo</label>
                <input type="password" name="password" id="password" required
                       class="w-full bg-gray-800 border @error('password') border-red-500 @else border-gray-700 @enderror rounded-lg px-4 py-2 text-white focus:outline-none focus:border-yellow-500">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Potvrzení nového hesla -->
            <div class="mb-8">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-400 mb-2">Potvrzení nového hesla</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-yellow-500">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 rounded-lg font-medium transition-colors bg-brand text-black">
                    Změnit heslo
                </button>
            </div>
        </form>
    </div>
@endsection
