<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Administrace | Projekty CHC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white">
    <header class="p-4 bg-brand">
        <div class="container mx-auto flex justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-black">Administrace</h1>
            <div class="flex items-center gap-4">
                <span class="text-black text-sm hidden sm:inline">
                    {{ auth()->user()->name }} · {{ auth()->user()->roleLabel() }}
                </span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-black text-yellow-600 px-4 py-2 rounded-lg hover:bg-gray-900">
                        Odhlásit
                    </button>
                </form>
            </div>
        </div>
    </header>

    <nav class="bg-gray-900 p-4 border-b border-brand">
        <div class="container mx-auto flex flex-wrap gap-x-6 gap-y-2">
            <a href="{{ route('home') }}" class="text-brand hover:opacity-75 font-medium">
                Zpět na web
            </a>
            @php($navLink = fn (string $pattern) => request()->routeIs($pattern) ? 'text-white font-semibold' : 'text-gray-300 hover:text-white')
            <a href="{{ route('admin.posts.index') }}" class="{{ $navLink('admin.posts.*') }} transition-colors">
                {{ auth()->user()->isAdmin() ? 'Správa příspěvků' : 'Moje příspěvky' }}
            </a>
            @can('admin')
                <a href="{{ route('admin.categories.index') }}" class="{{ $navLink('admin.categories.*') }} transition-colors">
                    Kategorie
                </a>
                <a href="{{ route('admin.users.index') }}" class="{{ $navLink('admin.users.*') }} transition-colors">
                    Uživatelé
                </a>
            @endcan
            <a href="{{ route('admin.profile.edit') }}" class="{{ $navLink('admin.profile.*') }} transition-colors">
                Můj profil
            </a>
        </div>
    </nav>

    <main class="container mx-auto p-4 sm:p-8">
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

    @stack('scripts')
</body>
</html>
