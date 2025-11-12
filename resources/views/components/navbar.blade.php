<nav x-data="{ mobileMenuOpen: false }" class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-zinc-900 shadow-md border-b-4 border-black transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                <div class="relative">
                    <x-app-logo-icon class="h-10 w-10 text-blue-600 dark:text-blue-400" />
                    <div class="absolute inset-0 bg-blue-500/20 blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
                <span class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white transition-colors">
                    BgaGO
                </span>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center space-x-1">
                <a href="{{ route('mapa') }}"
                   class="px-4 py-2 rounded-lg font-medium text-zinc-700 hover:text-blue-600 dark:text-zinc-300 dark:hover:text-blue-400 transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-950/30">
                    Mapa
                </a>
                <a href="{{ route('servicios') }}"
                   class="px-4 py-2 rounded-lg font-medium text-zinc-700 hover:text-blue-600 dark:text-zinc-300 dark:hover:text-blue-400 transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-950/30">
                    Servicios
                </a>
                <a href="{{ route('catalogo') }}"
                   class="px-4 py-2 rounded-lg font-medium text-zinc-700 hover:text-blue-600 dark:text-zinc-300 dark:hover:text-blue-400 transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-950/30">
                    Catálogo
                </a>
            </div>

            <!-- Auth Buttons -->
            <div class="hidden lg:flex items-center space-x-3">
                @guest
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 rounded-lg font-medium text-zinc-700 hover:text-blue-600 dark:text-zinc-300 dark:hover:text-blue-400 transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-950/30">
                        Iniciar Sesión
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all duration-200 shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 hover:scale-105">
                        Registrarse
                    </a>
                @else
                    <a href="{{ route('dashboard') }}"
                       class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all duration-200 shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 hover:scale-105">
                        Dashboard
                    </a>
                @endguest
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="lg:hidden p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="lg:hidden bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 shadow-xl">
        <div class="px-4 py-6 space-y-3">
            <a href="{{ route('mapa') }}" class="block px-4 py-3 text-zinc-700 dark:text-zinc-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 rounded-lg font-medium transition-colors">
                Mapa
            </a>
            <a href="{{ route('servicios') }}" class="block px-4 py-3 text-zinc-700 dark:text-zinc-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 rounded-lg font-medium transition-colors">
                Servicios
            </a>
            <a href="{{ route('catalogo') }}" class="block px-4 py-3 text-zinc-700 dark:text-zinc-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 rounded-lg font-medium transition-colors">
                Catálogo
            </a>

            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 space-y-3">
                @guest
                    <a href="{{ route('login') }}" class="block px-4 py-3 text-center text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg font-medium transition-colors">
                        Iniciar Sesión
                    </a>
                    <a href="{{ route('register') }}" class="block px-4 py-3 text-center bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-colors shadow-lg">
                        Registrarse
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="block px-4 py-3 text-center bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-colors">
                        Dashboard
                    </a>
                @endguest
            </div>
        </div>
    </div>
</nav>
