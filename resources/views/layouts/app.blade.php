<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projekty CHC</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Styly pro TinyMCE seznamy */
        .prose ol {
            list-style-type: decimal !important;
            padding-left: 1.5em !important;
            margin-top: 1em !important;
            margin-bottom: 1em !important;
        }

        /* Mobile menu styles */
        #mobile-menu {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 99999;
            background-color: #fed501;
            transform: translateY(-100%);
            transition: transform 0.3s ease-in-out;
            visibility: hidden;
            opacity: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        #mobile-menu.active {
            transform: translateY(0);
            visibility: visible;
            opacity: 1;
            display: flex;
        }

        #burger-menu {
            z-index: 100000;
            position: relative;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease-in-out;
        }

        /* Rest of existing styles */

        .prose ul {
            list-style-type: disc !important;
            padding-left: 1.5em !important;
            margin-top: 1em !important;
            margin-bottom: 1em !important;
        }

        .prose li {
            margin-top: 0.5em !important;
            margin-bottom: 0.5em !important;
        }

        .prose ol ol {
            list-style-type: lower-alpha !important;
        }

        .prose ol ol ol {
            list-style-type: lower-roman !important;
        }

        .prose ul ul {
            list-style-type: circle !important;
        }

        .prose ul ul ul {
            list-style-type: square !important;
        }

        /* Overlay pro zakrytí obsahu při otevřeném menu */
        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999; /* Overlay bude pod menu, ale nad obsahem */
            visibility: hidden;
            opacity: 0;
            transition: visibility 0.3s, opacity 0.3s;
        }

        #overlay.active {
            visibility: visible;
            opacity: 1;
        }

        /* Skrytí obsahu při otevřeném menu */
        body.menu-open {
            overflow: hidden; /* Zamezí posouvání stránky */
        }

        /* Roztáhnutí hlavičky při otevřeném menu */
        #main-header.active {
            height: 100vh; /* Roztáhne hlavičku, aby se zobrazilo menu */
        }

        /* Ujistíme se, že menu bude nad overlay */
        #mobile-menu {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 10000; /* Menu nad overlay */
            background-color: #fed501;
            transform: translateY(-100%);
            visibility: hidden;
            opacity: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease-in-out;
        }

        #mobile-menu.active {
            transform: translateY(0);
            visibility: visible;
            opacity: 1;
        }

        /* Odkazy v menu jsou vždy viditelné a klikatelné */
        #mobile-menu a {
            z-index: 10001;
        }

        /* Zabráníme kliknutí na overlay, aby neinterferovalo s klikáním na menu */
        #overlay.active {
            pointer-events: none; /* Zamezí interakci s overlay */
        }

        /* Oprava pro ikonu X */
        .burger-icon {
            display: block;
            width: 24px;
            height: 24px;
            position: relative;
        }

        .burger-icon span {
            position: absolute;
            background-color: black;
            width: 100%;
            height: 3px;
            transition: all 0.3s ease;
        }

        .burger-icon span:nth-child(1) {
            top: 0;
        }

        .burger-icon span:nth-child(2) {
            top: 10px;
        }

        .burger-icon span:nth-child(3) {
            top: 20px;
        }

        .burger-icon.x span:nth-child(1) {
            transform: rotate(45deg);
            top: 10px;
        }

        .burger-icon.x span:nth-child(2) {
            opacity: 0;
        }

        .burger-icon.x span:nth-child(3) {
            transform: rotate(-45deg);
            top: 10px;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100">
    <header class="fixed top-0 left-0 right-0 z-50 overflow-hidden transition-all duration-300"
            id="main-header"
            style="background-color: #fed501; border-bottom: 1px solid black;">
        <div class="absolute inset-0 z-0" 
             style="background-color: #fed501; clip-path: polygon(0 0, 100% 0, 100% 60%, 0 100%);">
        </div>
        
        <div class="relative z-10 container mx-auto px-4">
            <nav class="relative">
                <div class="flex flex-col md:flex-row justify-between items-center" 
                     style="padding: 0.9rem 0 1.8rem;">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-black flex items-center mb-4 md:mb-0 md:w-1/4">
                        <img src="{{ asset('images/defaults/Logo.png') }}" alt="Logo CHC" class="h-10 w-auto">
                    </a>

                    <div class="hidden md:flex items-center justify-center space-x-8 md:w-2/4">
                        @foreach(\App\Models\Category::all() as $category)
                            <a href="{{ route('categories.show', $category) }}" 
                               class="text-black hover:text-gray-800 text-lg font-bold">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>

                    <div class="hidden md:flex items-center justify-end space-x-6 md:w-1/4">
                        <a href="{{ route('cooperation') }}" 
                           class="text-black hover:text-gray-800 text-lg font-bold">
                            Spolupráce
                        </a>
                        <a href="https://creativehill.cz" 
                           target="_blank" 
                           class="text-black hover:text-gray-800"
                           title="CreativeHill">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M10 6v2H5v11h11v-5h2v6a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1h6zm11-3v8h-2V6.413l-7.293 7.294-1.414-1.414L17.585 5H13V3h8z"/>
                            </svg>
                        </a>
                    </div>

                    <!-- Burger menu tlačítko -->
                    <button id="burger-menu" class="md:hidden absolute top-6 right-4 text-black hover:text-gray-800 z-30">
                        <div class="burger-icon">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </button>
                </div>

                <!-- Mobilní menu -->
                <div id="mobile-menu" class="md:hidden fixed inset-0 transform transition-transform duration-300 ease-in-out z-50" style="background-color: #fed501; top: 0;">
                    <div class="flex flex-col space-y-4 p-4 pt-8">
                        @foreach(\App\Models\Category::all() as $category)
                            <a href="{{ route('categories.show', $category) }}" 
                               class="text-black hover:text-gray-800 px-4 text-lg font-bold text-center">
                                {{ $category->name }}
                            </a>
                        @endforeach
                        <div class="flex items-center justify-center space-x-4 pt-4 border-t border-black">
                            <a href="{{ route('cooperation') }}" 
                               class="text-black hover:text-gray-800 text-lg font-bold">
                                Spolupráce
                            </a>
                            <a href="https://creativehill.cz" 
                               target="_blank" 
                               class="text-black hover:text-gray-800">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M10 6v2H5v11h11v-5h2v6a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1h6zm11-3v8h-2V6.413l-7.293 7.294-1.414-1.414L17.585 5H13V3h8z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <main class="pt-24" id="page-content">
        @yield('content')
    </main>

    <!-- Overlay pro zašednutí obsahu -->
    <div id="overlay"></div>

    <footer class="bg-gray-900 border-t border-yellow-600 py-6">
        <div class="container mx-auto px-4 text-center text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Všechna práva vyhrazena.
            @auth
                <div class="mt-2">
                    <a href="{{ route('admin.posts.index') }}" class="text-yellow-600 hover:text-yellow-500 mr-4">
                        Administrace
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-yellow-600 hover:text-yellow-500">
                            Odhlásit
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const burgerMenu = document.getElementById('burger-menu');
            const mobileMenu = document.getElementById('mobile-menu');
            const overlay = document.getElementById('overlay');
            const pageContent = document.getElementById('page-content');
            const burgerIcon = burgerMenu.querySelector('.burger-icon');
            let menuOpen = false;

            const toggleMobileMenu = () => {
                menuOpen = !menuOpen;

                if (menuOpen) {
                    mobileMenu.classList.add('active');
                    overlay.classList.add('active');
                    pageContent.classList.add('content-hidden'); // Skryje obsah stránky
                    document.body.classList.add('menu-open'); // Zamezí scrollování
                    document.getElementById('main-header').classList.add('active'); // Roztáhne hlavičku
                    burgerIcon.classList.add('x'); // Změní burger na X
                } else {
                    mobileMenu.classList.remove('active');
                    overlay.classList.remove('active');
                    pageContent.classList.remove('content-hidden'); // Obnoví viditelnost obsahu
                    document.body.classList.remove('menu-open');
                    document.getElementById('main-header').classList.remove('active'); // Reset hlavičky
                    burgerIcon.classList.remove('x'); // Vrátí burger zpět
                }
            };

            burgerMenu.addEventListener('click', toggleMobileMenu);
            overlay.addEventListener('click', toggleMobileMenu);
        });
    </script>
</body>
</html>
