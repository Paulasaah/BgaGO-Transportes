@php
    $u = auth()->user();
    $name = $u?->name ?? 'Usuario';
    $parts = preg_split('/\s+/', trim($name));
    $initials = strtoupper((substr($parts[0] ?? '', 0, 1)) . (substr($parts[1] ?? '', 0, 1)));
    $role = $u?->isAdmin() ? 'Admin' : ($u?->isDriver() ? 'Conductor' : 'Usuario');
    $since = $u?->created_at?->format('M Y');

    $roleKey = $u?->isAdmin() ? 'admin' : ($u?->isDriver() ? 'driver' : 'user');
    $gradient = match($roleKey) {
        'admin' => 'from-green-600 to-emerald-600',
        'driver' => 'from-orange-500 to-amber-500',
        default => 'from-blue-600 to-blue-500',
    };
    $gradientDark = match($roleKey) {
        'admin' => 'dark:from-green-900 dark:to-emerald-800',
        'driver' => 'dark:from-orange-900 dark:to-amber-800',
        default => 'dark:from-[#0b2a3a] dark:to-[#174562]',
    };
    $avatarBg = match($roleKey) {
        'admin' => 'bg-green-600/25',
        'driver' => 'bg-orange-500/25',
        default => 'bg-blue-600/25',
    };
@endphp

<div class="relative mb-8 w-full">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $gradient }} {{ $gradientDark }} p-6 md:p-8 pt-12 md:pt-14 text-white shadow-lg">
        <div class="absolute -right-10 -top-10 opacity-30">
            <svg width="240" height="240" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="g1" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#fff" />
                        <stop offset="100%" stop-color="#e5e7eb" />
                    </linearGradient>
                </defs>
                <circle cx="120" cy="120" r="90" stroke="url(#g1)" stroke-width="8" fill="none" />
                <circle cx="120" cy="120" r="60" stroke="url(#g1)" stroke-width="6" fill="none" />
            </svg>
        </div>

        <div class="flex items-center gap-6">
            <div class="h-16 w-16 rounded-full {{ $avatarBg }} flex items-center justify-center">
                <span class="text-2xl font-bold">{{ $initials }}</span>
            </div>
            <div class="flex-1">
                <div class="text-2xl md:text-3xl font-bold">Configuración</div>
                <div class="text-sm md:text-base/relaxed opacity-90">Administra tu perfil y ajustes de cuenta</div>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-md bg-white/20 text-white/90 text-xs md:text-sm">{{ $name }} • {{ $role }}</span>
                    @if($since)
                        <span class="inline-flex items-center px-3 py-1 rounded-md bg-white/10 text-white/80 text-xs md:text-sm">Miembro desde {{ $since }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <flux:separator variant="subtle" class="mt-6" />
</div>
