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
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            padding-top: 80px;
            pointer-events: none;
        }

        #mobile-menu.active {
            transform: translateY(0);
            pointer-events: auto;
        }

        #burger-menu {
            z-index: 100000;
            position: absolute;
            top: 1.5rem;
            right: 1rem;
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
            z-index: 40; /* Overlay bude pod hlavičkou (z-50), ale nad obsahem */
            visibility: hidden;
            opacity: 0;
            transition: visibility 0.3s, opacity 0.3s;
        }

        #overlay.active {
            visibility: visible;
            opacity: 0.5;
            pointer-events: auto;
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
        /* Responsive video styles */
        .prose iframe {
            max-width: 100%;
            width: 100%;
            aspect-ratio: 16/9;
            height: auto;
        }

        .prose .mce-content-body iframe {
            max-width: 100%;
            width: 100%;
            aspect-ratio: 16/9;
            height: auto;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100">
    <header class="fixed top-0 left-0 right-0 z-50 overflow-hidden transition-all duration-300"
            id="main-header"
            style="background-color: #fed501; border-bottom: 1px solid white;">
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

                    <!-- Burger menu container -->
                    <div class="block md:hidden">
                        <button id="burger-menu" class="absolute top-6 right-4 text-black hover:text-gray-800 z-30">
                            <div class="burger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Mobilní menu -->
                <div id="mobile-menu" class="md:hidden fixed inset-0 transform transition-transform duration-300 ease-in-out z-50" style="background-color: #fed501; top: 0; -webkit-overflow-scrolling: touch;">
                    <div class="flex flex-col space-y-4 p-4 pt-20 min-h-screen w-full">
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
            <div class="flex justify-center space-x-6 mb-4">
                <a href="https://www.instagram.com/creativehillcollege/" target="_blank" class="text-gray-400 hover:text-yellow-600 transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                    </svg>
                </a>
                <a href="https://www.facebook.com/CreativeHillCollege/" target="_blank" class="text-gray-400 hover:text-yellow-600 transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path>
                    </svg>
                </a>
                <a href="https://www.linkedin.com/school/creativehillcollege/?originalSubdomain=cz" target="_blank" class="text-gray-400 hover:text-yellow-600 transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clip-rule="evenodd"></path>
                    </svg>
                </a>
                <a href="https://creativehill.cz" target="_blank" class="text-gray-400 hover:text-yellow-600 transition-colors font-semibold">
                    creativehill.cz
                </a>
            </div>
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
                requestAnimationFrame(() => {
                    if (menuOpen) {
                        mobileMenu.classList.add('active');
                        overlay.classList.add('active');
                        document.body.classList.add('menu-open');
                        document.getElementById('main-header').classList.add('active');
                        burgerIcon.classList.add('x');
                    } else {
                        mobileMenu.classList.remove('active');
                        overlay.classList.remove('active');
                        document.body.classList.remove('menu-open');
                        document.getElementById('main-header').classList.remove('active');
                        burgerIcon.classList.remove('x');
                    }
                });
            };

            burgerMenu.addEventListener('click', toggleMobileMenu);
            overlay.addEventListener('click', toggleMobileMenu);
        });
    </script>
</body>
</html>
