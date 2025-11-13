{{-- resources/views/components/footer.blade.php --}}
<footer class="relative bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800">
    <div class="pt-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-4 gap-12">
        <!-- Logo and Description -->
        <div>
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center">
                    <x-app-logo-icon class="w-6 h-6 text-blue-600" />
                </div>
                <span class="text-2xl font-bold">BgaGO</span>
            </div>
            <p class="text-zinc-600 dark:text-zinc-300 text-sm leading-relaxed">
                Plataforma de movilidad sostenible del Área Metropolitana de Bucaramanga.
                Conecta usuarios, vehículos y domicilios con tecnología inteligente.
            </p>

            <!-- Redes Sociales -->
            <div class="flex space-x-4 mt-6">
                <a href="#" class="hover:text-blue-500 transition-colors">
                    <!-- Facebook -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M22 12a10 10 0 1 0-11.5 9.9v-7h-2v-3h2v-2.3c0-2 1.2-3.1 3-3.1.9 0 1.8.1 2 .1v2.3h-1.1c-1.1 0-1.5.7-1.5 1.4V12h2.6l-.4 3h-2.2v7A10 10 0 0 0 22 12z"/>
                    </svg>
                </a>
                <a href="#" class="hover:text-blue-500 transition-colors">
                    <!-- Instagram -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 2C4.2 2 2 4.2 2 7v10c0 2.8 2.2 5 5 5h10c2.8 0 5-2.2 5-5V7c0-2.8-2.2-5-5-5H7zm10 2c1.7 0 3 1.3 3 3v10c0 1.7-1.3 3-3 3H7c-1.7 0-3-1.3-3-3V7c0-1.7 1.3-3 3-3h10zm-5 3a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 2a3 3 0 1 1 0 6 3 3 0 0 1 0-6zm4.5-.9a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                    </svg>
                </a>
                <a href="#" class="hover:text-blue-500 transition-colors">
                    <!-- Twitter / X -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M22 5.8c-.8.3-1.6.5-2.5.6.9-.5 1.6-1.3 1.9-2.3-.8.5-1.8.8-2.8 1a4.4 4.4 0 0 0-7.6 4c-3.6-.2-6.8-1.9-9-4.6a4.5 4.5 0 0 0 1.4 6A4.2 4.2 0 0 1 2 9.6v.1a4.4 4.4 0 0 0 3.5 4.3 4.5 4.5 0 0 1-2 .1 4.4 4.4 0 0 0 4.1 3A8.8 8.8 0 0 1 2 19.5a12.4 12.4 0 0 0 6.7 2c8 0 12.4-6.7 12.4-12.4v-.6c.8-.6 1.5-1.3 2-2.2z"/>
                    </svg>
                </a>
                <a href="#" class="hover:text-blue-500 transition-colors">
                    <!-- YouTube -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21.8 8s-.2-1.5-.8-2.2c-.7-.8-1.5-.8-1.9-.8C16.8 5 12 5 12 5s-4.8 0-7.1.1c-.4 0-1.2 0-1.9.8C2.3 6.5 2 8 2 8S1.9 9.6 1.9 11.2v1.6C1.9 14.4 2 16 2 16s.2 1.5.8 2.2c.7.8 1.5.8 1.9.8 2.3.1 7.1.1 7.1.1s4.8 0 7.1-.1c.4 0 1.2 0 1.9-.8.6-.7.8-2.2.8-2.2s.1-1.6.1-3.2V11.2c0-1.6-.1-3.2-.1-3.2zM9.7 14.6V9.4l4.9 2.6-4.9 2.6z"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Links -->
        <div>
            <h3 class="text-lg font-semibold mb-4 text-blue-500">Enlaces</h3>
            <ul class="space-y-2 text-zinc-600 dark:text-zinc-300 text-sm">
                <li><a href="{{ route('home') }}" class="hover:text-blue-500 transition-colors">Inicio</a></li>
                <li><a href="{{ route('servicios') }}" class="hover:text-blue-500 transition-colors">Servicios</a></li>
                <li><a href="{{ route('mapa') }}" class="hover:text-blue-500 transition-colors">Mapa</a></li>
                <li><a href="{{ route('catalogo') }}" class="hover:text-blue-500 transition-colors">Catálogo</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-blue-500 transition-colors">Iniciar sesión</a></li>
            </ul>
        </div>

        <!-- Contact Info -->
        <div>
            <h3 class="text-lg font-semibold mb-4 text-blue-500">Contáctanos</h3>
            <ul class="space-y-3 text-zinc-600 dark:text-zinc-300 text-sm">
                <li class="flex items-start space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.1 0 2-.9 2-2s-.9-2-2-2a2 2 0 100 4zM12 22s8-4.5 8-11a8 8 0 10-16 0c0 6.5 8 11 8 11z"/>
                    </svg>
                    <span>Cra. 15 #35-17, Bucaramanga, Colombia</span>
                </li>
                <li class="flex items-start space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28l1.5 4.5L7 9l5 5 2-3.5L19 16h2a2 2 0 002 2h-1a2 2 0 01-2-2v-1"/>
                    </svg>
                    <span>+57 305 221 9913</span>
                </li>
                <li class="flex items-start space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l9 6 9-6M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z"/>
                    </svg>
                    <span>soporte@bgago.co</span>
                </li>
            </ul>
        </div>

        <!-- Newsletter -->
        <div>
            <h3 class="text-lg font-semibold mb-4 text-blue-500">Suscríbete</h3>
            <p class="text-sm text-zinc-600 dark:text-zinc-300 mb-4">
                Recibe noticias, promociones y actualizaciones sobre movilidad sostenible.
            </p>
        </div>
    </div>

    <div class="relative z-10 mt-16 border-t border-zinc-200 dark:border-zinc-800 pt-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
        <p>&copy; {{ date('Y') }} <span class="font-semibold text-zinc-900 dark:text-white">BgaGO</span>. Todos los derechos reservados.</p>
        <p class="mt-1">Diseñado con 💙 por el equipo BgaGO</p>
    </div>
</footer>
