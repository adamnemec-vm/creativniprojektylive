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

        /* Vnořené seznamy */
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
    </style>
</head>
<body class="min-h-screen bg-gray-100">
    <header class="fixed top-0 left-0 right-0 z-50 overflow-hidden transition-all duration-300"
            id="main-header"
            style="background-color: #fed501; border-bottom: 1px solid black;">
        <!-- Zkosené pozadí jako samostatný element -->
        <div class="absolute inset-0 z-0" 
             style="background-color: #fed501; clip-path: polygon(0 0, 100% 0, 100% 60%, 0 100%);">
        </div>
        
        <!-- Obsah hlavičky -->
        <div class="relative z-10 container mx-auto px-4">
            <nav class="relative">
                <div class="flex flex-col md:flex-row justify-between items-center" 
                     style="padding: 0.9rem 0 1.8rem;">
                    <!-- Logo vlevo -->
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-black flex items-center mb-4 md:mb-0 md:w-1/4">
                        <img src="{{ asset('images/defaults/Logo.png') }}" 
                             alt="Logo CHC" 
                             class="h-10 w-auto">
                    </a>

                    <!-- Menu uprostřed -->
                    <div class="hidden md:flex items-center justify-center space-x-8 md:w-2/4">
                        @foreach(\App\Models\Category::all() as $category)
                            <a href="{{ route('categories.show', $category) }}" 
                               class="text-black hover:text-gray-800 text-lg font-bold">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Pravá část -->
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
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                <!-- Mobilní menu -->
                <div id="mobile-menu" 
                     class="md:hidden fixed inset-0 transform transition-transform duration-300 ease-in-out z-50"
                     style="background-color: #fed501; top: 0;">
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

    <main class="pt-24">
        @yield('content')
    </main>

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
            let lastScrollTop = 0;
            const header = document.getElementById('main-header');
            const scrollThreshold = 100;
            const burgerMenu = document.getElementById('burger-menu');
            const mobileMenu = document.getElementById('mobile-menu');
            const burgerIcon = burgerMenu.querySelector('svg');
            let menuOpen = false;
    
            const toggleMobileMenu = (event) => {
                if (event) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                menuOpen = !menuOpen;
                burgerMenu.setAttribute('aria-expanded', String(menuOpen));
                
                if (menuOpen) {
                    mobileMenu.style.transform = 'translateY(0)';
                    mobileMenu.style.visibility = 'visible';
                    mobileMenu.style.opacity = '1';
                    burgerIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
                    document.body.style.overflow = 'hidden';
                } else {
                    mobileMenu.style.transform = 'translateY(-100%)';
                    mobileMenu.style.visibility = 'hidden';
                    mobileMenu.style.opacity = '0';
                    burgerIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
                    document.body.style.overflow = '';
                }
            };
    
            burgerMenu.addEventListener('click', toggleMobileMenu);
            burgerMenu.addEventListener('touchend', (e) => {
                e.preventDefault();
                toggleMobileMenu();
            });
    
            document.addEventListener('click', (event) => {
                if (menuOpen && !mobileMenu.contains(event.target) && !burgerMenu.contains(event.target)) {
                    toggleMobileMenu();
                }
            });
    
            const mobileMenuLinks = mobileMenu.getElementsByTagName('a');
            Array.from(mobileMenuLinks).forEach(link => {
                link.addEventListener('click', () => {
                    toggleMobileMenu();
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>