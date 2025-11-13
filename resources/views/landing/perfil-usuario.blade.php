<x-layouts.public>
    <div class="py-24 min-h-screen bg-gradient-to-br from-white via-blue-50 to-blue-200 overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-bold text-blue-500 dark:text-blue mb-2">Perfil de Usuario</h1>
                <p class="text-lg text-zinc-600 dark:text-zinc-700">Gestiona tu cuenta y visualiza tu información</p>
            </div>

            @php
                $user = auth()->user();
                $total = $user?->reservations()->count() ?? 0;
                $activas = $user?->reservations()->activas()->count() ?? 0;
                $completadas = $user?->reservations()->completadas()->count() ?? 0;
                $canceladas = $user?->reservations()->canceladas()->count() ?? 0;
                $recent = $user?->reservations()->with(['vehicle', 'branch'])->orderByDesc('created_at')->take(5)->get() ?? collect();
            @endphp

            @if(session('status'))
                <div class="mb-6 rounded-lg border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-950/30 px-4 py-3 text-green-800 dark:text-green-300 flex items-start gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">{{ session('status') }}</span>
                </div>
            @endif

            <div class="grid lg:grid-cols-4 gap-6">
                <div class="md:col-span-1">
                    <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden p-6 text-center">
                        <div class="mx-auto mb-4">
                            <span class="relative flex h-24 w-24 shrink-0 overflow-hidden rounded-2xl mx-auto ring-4 ring-blue-200 dark:ring-blue-900">
                                <span class="flex h-full w-full items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white text-3xl font-bold">
                                    {{ $user->initials() }}
                                </span>
                            </span>
                        </div>
                        <div class="space-y-1">
                            <div class="text-xl font-semibold text-zinc-900 dark:text-white">{{ $user->name }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $user->email }}</div>
                            <div class="mt-2 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $user->email_verified_at ? 'bg-green-100 text-green-700 dark:bg-green-950/50 dark:text-green-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-950/50 dark:text-yellow-300' }}">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                                </svg>
                                {{ $user->email_verified_at ? 'Email verificado' : 'Email no verificado' }}
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col gap-3">
                            <a href="{{ route('mis-reservas') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">Mis Reservas</a>
                            <a href="{{ route('editar-perfil-publico') }}" class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 text-zinc-800 dark:bg-zinc-700 dark:hover:bg-zinc-600 dark:text-zinc-100 rounded-lg font-medium transition-colors">Editar Perfil</a>
                            <a href="{{ route('editar-password-publico') }}" class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 text-zinc-800 dark:bg-zinc-700 dark:hover:bg-zinc-600 dark:text-zinc-100 rounded-lg font-medium transition-colors">Cambiar Contraseña</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-950/30 dark:hover:bg-red-950/50 dark:text-red-400 rounded-lg font-medium transition-colors">Cerrar Sesión</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden p-6">
                        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-gradient-to-br from-blue-50 to-white dark:from-blue-950/20 dark:to-zinc-800">
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">Total Reservas</div>
                                <div class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $total }}</div>
                            </div>
                            <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-gradient-to-br from-green-50 to-white dark:from-green-950/20 dark:to-zinc-800">
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">Activas</div>
                                <div class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $activas }}</div>
                            </div>
                            <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-gradient-to-br from-indigo-50 to-white dark:from-indigo-950/20 dark:to-zinc-800">
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">Completadas</div>
                                <div class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $completadas }}</div>
                            </div>
                            <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-gradient-to-br from-red-50 to-white dark:from-red-950/20 dark:to-zinc-800">
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">Canceladas</div>
                                <div class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $canceladas }}</div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Últimas Reservas</h3>
                                <a href="{{ route('mis-reservas') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Ver todas</a>
                            </div>
                            <div class="space-y-3">
                                @forelse($recent as $res)
                                    @php
                                        $isDom = $res->isDomicilio();
                                        $badgeColor = $isDom ? $res->tipo->color() : ($res->vehicle?->tipo?->color() ?? 'blue');
                                    @endphp
                                    <div class="flex items-center justify-between p-4 rounded-xl border border-zinc-200 dark:border-zinc-700">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $badgeColor }}-100 text-{{ $badgeColor }}-700 dark:bg-{{ $badgeColor }}-950/50 dark:text-{{ $badgeColor }}-400">
                                                {{ $isDom ? 'Domicilio' : ($res->vehicle?->tipo?->label() ?? 'Vehículo') }}
                                            </span>
                                            <div class="text-sm text-zinc-700 dark:text-zinc-300">
                                                {{ $isDom ? ($res->origen_direccion.' → '.$res->destino_direccion) : (optional($res->fecha_inicio)->format('d/m/Y - H:i')) }}
                                            </div>
                                        </div>
                                        <div class="text-sm font-semibold text-zinc-900 dark:text-white">${{ number_format($res->monto_final, 0, ',', '.') }}</div>
                                    </div>
                                @empty
                                    <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400">Aún no tienes reservas.</div>
                                @endforelse
                            </div>

                            <div class="mt-8">
                                <a href="{{ route('catalogo') }}" class="inline-flex items-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-all hover:scale-[1.02] shadow-lg">
                                    Explorar Vehículos
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>
