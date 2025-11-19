<nav x-data="{ mobileMenuOpen: false }" class="fixed top-0 left-0 right-0 z-50 bg-white/90 dark:bg-black/90 backdrop-blur border-b border-zinc-200 dark:border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="{{ auth()->check() ? ((auth()->user()->isAdmin() || auth()->user()->isDriver()) ? route('dashboard') : route('user.home')) : route('home') }}" class="flex items-center space-x-3 group" wire:navigate>
                    <div class="relative">
                        <x-app-logo-icon class="h-10 w-10 text-blue-600 dark:text-blue-400" />
                        <div class="absolute inset-0 bg-blue-500/20 blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <span class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
                        BgaGO
                    </span>
                </a>

                <!-- Desktop Navigation -->
                @auth
                <div class="hidden lg:flex items-center space-x-2">
                    <a href="{{ route('services.delivery') }}"
                    class="px-4 py-2 rounded-lg font-medium text-zinc-700 hover:text-black dark:text-zinc-300 dark:hover:text-white transition-colors" wire:navigate>
                        Servicios
                    </a>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('catalog.index') ? route('catalog.index') : '/catalog' }}"
                    class="px-4 py-2 rounded-lg font-medium text-zinc-700 hover:text-black dark:text-zinc-300 dark:hover:text-white transition-colors" wire:navigate>
                        Catálogo
                    </a>
                    <a href="{{ route('catalog.reservations') }}"
                    class="px-4 py-2 rounded-lg font-medium text-zinc-700 hover:text-black dark:text-zinc-300 dark:hover:text-white transition-colors" wire:navigate>
                        Reservas
                    </a>
                </div>
                @endauth

                <!-- Auth Buttons -->
                <div class="hidden lg:flex items-center space-x-3">
                    <div x-data="{ theme: localStorage.getItem('theme') || 'light', init(){ document.documentElement.classList.toggle('dark', this.theme==='dark') }, toggle(){ this.theme = this.theme==='dark' ? 'light' : 'dark'; localStorage.setItem('theme', this.theme); document.documentElement.classList.toggle('dark', this.theme==='dark') } }" class="flex items-center">
                        <button @click="toggle" :aria-label="theme==='dark' ? 'Cambiar a claro' : 'Cambiar a oscuro'" class="relative h-9 w-16 rounded-full transition-colors" :class="theme==='dark' ? 'bg-[#0b2a3a]' : 'bg-amber-100'">
                            <span class="absolute top-1 left-1 h-7 w-7 rounded-full flex items-center justify-center text-white transition-all" :class="theme==='dark' ? 'translate-x-7 bg-sky-500' : 'translate-x-0 bg-amber-400 text-black'">
                                <template x-if="theme==='dark'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/></svg>
                                </template>
                                <template x-if="theme==='light'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
                                </template>
                            </span>
                        </button>
                    </div>
                    @guest
                        <a href="{{ route('login') }}"
                        class="px-4 py-2 rounded-lg font-medium text-zinc-700 hover:text-black dark:text-zinc-300 dark:hover:text-white transition-colors">
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}"
                        class="px-6 py-2.5 bg-blue-600 text-white hover:bg-blue-700 dark:bg-white dark:text-black dark:hover:bg-zinc-200 rounded-lg font-medium transition-colors">
                            Registrarse
                        </a>
                    @else
                        @php
                            $u = auth()->user();
                            $name = $u?->name ?? $u?->email;
                            $parts = preg_split('/\s+/', trim($name ?? ''));
                            $initials = strtoupper((substr($parts[0] ?? '', 0, 1)) . (substr($parts[1] ?? '', 0, 1)));
                            $role = $u?->isAdmin() ? 'Admin' : ($u?->isDriver() ? 'Conductor' : 'Usuario');
                            $bgClass = $u?->isAdmin() ? 'bg-green-600' : ($u?->isDriver() ? 'bg-orange-500' : 'bg-blue-600');
                        @endphp
                        <div x-data="{ open: false, t: null, openFn(){ this.open = true; if(this.t){ clearTimeout(this.t) } }, closeFn(){ if(this.t){ clearTimeout(this.t) } this.t = setTimeout(() => { this.open = false }, 500) } }" class="relative" @mouseenter="openFn()" @mouseleave="closeFn()">
                            <button @click="open = !open" class="relative group flex items-center gap-3 px-3 py-2 rounded-lg ring-1 ring-zinc-200 dark:ring-zinc-800 hover:bg-zinc-100 dark:hover:bg-white/10 transition">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full text-white text-sm font-semibold {{ $bgClass }}">{{ $initials }}</span>
                                <span class="flex flex-col leading-tight">
                                    <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $name }}</span>
                                    <span class="text-xs text-zinc-600 dark:text-zinc-400">{{ $role }}</span>
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-zinc-600 dark:text-zinc-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                            </button>
                            <div x-show="open" x-cloak style="display:none" class="absolute right-0 mt-2 w-56 rounded-lg ring-1 ring-zinc-200 dark:ring-zinc-800 bg-white dark:bg-zinc-900 shadow-lg z-50" @mouseenter="openFn()" @mouseleave="closeFn()">
                                <div class="p-2 space-y-1">
                                    <a href="{{ \Illuminate\Support\Facades\Route::has('settings.profile') ? route('settings.profile') : '/settings/profile' }}" wire:navigate class="block px-4 py-2 text-sm text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-white/10 rounded-lg">Configuración</a>
                                    <a href="{{ \Illuminate\Support\Facades\Route::has('wallet') ? route('wallet') : '/wallet' }}" wire:navigate class="block px-4 py-2 text-sm text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-white/10 rounded-lg">Wallet</a>
                                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-white/10 rounded-lg">Cerrar Sesión</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endguest
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="lg:hidden p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-white/10 transition-colors text-zinc-900 dark:text-white">
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
            class="lg:hidden bg-white/90 dark:bg-black/90 backdrop-blur border-t border-zinc-200 dark:border-white/10">
            <div class="px-4 py-6 space-y-3">
                @auth
                <a href="{{ \Illuminate\Support\Facades\Route::has('mapa') ? route('mapa') : '/mapa' }}" class="block px-4 py-3 text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-white/10 rounded-lg font-medium transition-colors" wire:navigate>
                    Mapa
                </a>
                <a href="{{ route('services.delivery') }}" class="block px-4 py-3 text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-white/10 rounded-lg font-medium transition-colors" wire:navigate>
                    Servicios
                </a>
                <a href="{{ route('catalog.index') }}" class="block px-4 py-3 text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-white/10 rounded-lg font-medium transition-colors" wire:navigate>
                    Catálogo
                </a>
                @endauth

                <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 space-y-3">
                    @guest
                        <a href="{{ route('login') }}" class="block px-4 py-3 text-center text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-white/10 rounded-lg font-medium transition-colors">
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}" class="block px-4 py-3 text-center bg-blue-600 text-white hover:bg-blue-700 dark:bg-white dark:text-black dark:hover:bg-zinc-200 rounded-lg font-medium transition-colors">
                            Registrarse
                        </a>
                    @else
                        <a href="{{ (auth()->user()->isAdmin() || auth()->user()->isDriver()) ? route('dashboard') : route('user.home') }}" class="block px-4 py-3 text-center bg-blue-600 text-white hover:bg-blue-700 dark:bg-white dark:text-black dark:hover:bg-zinc-200 rounded-lg font-medium transition-colors" wire:navigate>
                            {{ (auth()->user()->isAdmin() || auth()->user()->isDriver()) ? 'Dashboard' : 'Inicio' }}
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>
