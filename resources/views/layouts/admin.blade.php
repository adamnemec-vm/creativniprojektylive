<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white">
    <header style="background-color: #fed501;" class="p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-black">Administrace</h1>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="bg-black text-yellow-600 px-4 py-2 rounded-lg hover:bg-gray-900">
                    Odhlásit
                </button>
            </form>
        </div>
    </header>
    
    <nav class="bg-gray-900 p-4" style="border-bottom: 1px solid #fed501;">
        <div class="container mx-auto flex space-x-4">
            <a href="{{ route('home') }}" style="color: #fed501;" class="hover:opacity-75">
                Zpět na web
            </a>
            <a href="{{ route('admin.posts.index') }}" class="text-yellow-600 hover:text-yellow-500">
                Správa příspěvků
            </a>
        </div>
    </nav>

    <main class="container mx-auto p-8">
        @if(session('success'))
            <div class="bg-green-600 text-white p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-600 text-white p-4 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html> 