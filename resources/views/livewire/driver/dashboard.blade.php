<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Reservation;
use App\Enums\ReservationStatus;
use Carbon\Carbon;
use App\Services\ReservationService;

new #[Layout('components.layouts.public')] class extends Component {
    public array $stats = [];
    public array $hoy = [];
    public array $futuras = [];
    public array $pasadas = [];
    public array $pendientes = [];
    public ?array $selected = null;
    public bool $showModal = false;
    public float $rating = 4.8;
    public int $ganancias = 0;

    protected ReservationService $reservationService;

    public function boot(ReservationService $reservationService): void
    {
        $this->reservationService = $reservationService;
    }

    public function mount(): void
    {
        $driverId = Auth::id();

        $baseQuery = Reservation::with(['user','vehicle','branch'])
            ->where('conductor_id', $driverId);

        // Activas
        $this->hoy = $baseQuery->clone()
            ->where('estado', ReservationStatus::Activa)
            ->orderByDesc('fecha_inicio')
            ->limit(10)
            ->get()
            ->toArray();

        // Pendientes por realizar
        $this->futuras = Reservation::with(['user','vehicle','branch'])
            ->where('estado', ReservationStatus::Pendiente)
            ->where(function($q) use ($driverId) {
                $q->whereNull('conductor_id')
                  ->orWhere('conductor_id', $driverId);
            })
            ->orderBy('fecha_inicio')
            ->limit(10)
            ->get()
            ->toArray();

        // Completadas (historial)
        $this->pasadas = $baseQuery->clone()
            ->where('estado', ReservationStatus::Completada)
            ->orderByDesc('fecha_inicio')
            ->limit(10)
            ->get()
            ->toArray();

        $this->stats = [
            'activas' => Reservation::where('conductor_id', $driverId)->whereIn('estado', [ReservationStatus::Confirmada, ReservationStatus::Activa])->count(),
            'hoy' => Reservation::where('conductor_id', $driverId)->whereDate('fecha_inicio', Carbon::today())->count(),
            'completadas' => Reservation::where('conductor_id', $driverId)->where('estado', ReservationStatus::Completada)->count(),
        ];

        $completed = Reservation::where('conductor_id', $driverId)
            ->where('estado', ReservationStatus::Completada)
            ->get();

        $avg = $completed
            ->whereNotNull('calificacion_conductor')
            ->avg('calificacion_conductor');
        $this->rating = is_null($avg) ? 4.8 : (float) $avg;

        $this->ganancias = $completed->sum(function ($r) {
            $valor = (float) ($r->monto_final ?? $r->monto ?? 0);
            $tarifa = (int) round($valor * 0.20);
            return max($tarifa, 5000);
        });

        $this->pendientes = Reservation::with(['user','vehicle','branch'])
            ->where('estado', ReservationStatus::Pendiente)
            ->where(function($q) use ($driverId) {
                $q->whereNull('conductor_id')
                  ->orWhere('conductor_id', $driverId);
            })
            ->orderByDesc('fecha_inicio')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function viewReservation(int $id): void
    {
        $res = Reservation::with(['user','vehicle','branch'])->find($id);
        if (!$res) {
            $this->dispatch('notify', type: 'error', message: 'Reserva no encontrada');
            return;
        }
        $this->selected = $res->toArray();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selected = null;
    }

    public function acceptReservation(int $id): void
    {
        $driverId = Auth::id();
        $result = $this->reservationService->driverAccept($id, $driverId);
        if (!$result['success']) {
            $this->dispatch('notify', type: 'error', message: $result['message'] ?? 'No se pudo aceptar');
            return;
        }
        $start = $this->reservationService->startReservation($id, $driverId);
        if (!$start['success']) {
            $this->dispatch('notify', type: 'warning', message: $start['message'] ?? 'Reserva aceptada pero no se pudo activar');
        }
        $this->closeModal();
        $this->mount();
        $this->dispatch('notify', type: 'success', message: 'Reserva aceptada');
    }

    public function rejectReservation(int $id, ?string $motivo = null): void
    {
        $driverId = Auth::id();
        $result = $this->reservationService->driverReject($id, $driverId, $motivo);
        if (!$result['success']) {
            $this->dispatch('notify', type: 'error', message: $result['message'] ?? 'No se pudo rechazar');
            return;
        }
        $this->closeModal();
        $this->mount();
        $this->dispatch('notify', type: 'success', message: 'Reserva rechazada');
    }

    public function completeReservation(int $id): void
    {
        $result = $this->reservationService->completeReservation($id);
        if (!$result['success']) {
            $this->dispatch('notify', type: 'error', message: $result['message'] ?? 'No se pudo completar');
            return;
        }
        $this->closeModal();
        $this->mount();
        $this->dispatch('notify', type: 'success', message: 'Reserva marcada como completada');
    }

    public function cancelActive(int $id): void
    {
        $driverId = Auth::id();
        $result = $this->reservationService->cancelReservation($id, 'Cancelada por conductor', $driverId);
        if (!$result['success']) {
            $this->dispatch('notify', type: 'error', message: $result['message'] ?? 'No se pudo cancelar');
            return;
        }
        $this->closeModal();
        $this->mount();
        $this->dispatch('notify', type: 'success', message: 'Reserva cancelada');
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 px-4 sm:px-6 lg:px-8 py-6">
    @php
        $u = Auth::user();
        $name = $u?->name ?? 'Conductor';
        $parts = preg_split('/\s+/', trim($name));
        $initials = strtoupper((substr($parts[0] ?? '', 0, 1)) . (substr($parts[1] ?? '', 0, 1)));
    @endphp

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-blue-500 dark:from-[#0b2a3a] dark:to-[#174562] p-6 md:p-8 text-white shadow-lg">
        <div class="absolute -right-10 -top-10 opacity-30">
            <svg width="240" height="240" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="dg" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#fff" />
                        <stop offset="100%" stop-color="#e5e7eb" />
                    </linearGradient>
                </defs>
                <circle cx="120" cy="120" r="90" stroke="url(#dg)" stroke-width="8" fill="none" />
                <circle cx="120" cy="120" r="60" stroke="url(#dg)" stroke-width="6" fill="none" />
            </svg>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-full bg-white/20 flex items-center justify-center">
                    <span class="text-xl font-bold">{{ $initials }}</span>
                </div>
                <div>
                    <div class="text-2xl md:text-3xl font-bold">Bienvenido, {{ $name }}</div>
                    <div class="text-sm md:text-base/relaxed opacity-90">Gestiona tus entregas y reservas asignadas</div>
                </div>
            </div>
        </div>

        <div class="mt-6 grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-5">
            <div class="rounded-xl bg-white/15 p-4 backdrop-blur">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <div class="text-sm opacity-80">Próximas</div>
                        <div class="text-lg font-semibold">{{ count($futuras) }}</div>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-white/15 p-4 backdrop-blur">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 20h10a2 2 0 002-2V7a2 2 0 00-2-2H9l-2 2H7"/></svg>
                    <div>
                        <div class="text-sm opacity-80">Activas</div>
                        <div class="text-lg font-semibold">{{ count($hoy) }}</div>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-white/15 p-4 backdrop-blur">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17l3 3m0 0l3-3m-3 3V4"/></svg>
                    <div>
                        <div class="text-sm opacity-80">Completadas</div>
                        <div class="text-lg font-semibold">{{ $stats['completadas'] }}</div>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-white/15 p-4 backdrop-blur">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M11.48 3.499a.75.75 0 011.04 0l2.055 2.044a.75.75 0 00.564.219l2.861-.205a.75.75 0 01.78.78l-.205 2.861a.75.75 0 00.219.564l2.044 2.055a.75.75 0 010 1.04l-2.044 2.055a.75.75 0 00-.219.564l.205 2.861a.75.75 0 01-.78.78l-2.861-.205a.75.75 0 00-.564.219l-2.055 2.044a.75.75 0 01-1.04 0l-2.055-2.044a.75.75 0 00-.564-.219l-2.861.205a.75.75 0 01-.78-.78l.205-2.861a.75.75 0 00-.219-.564L3.5 13.72a.75.75 0 010-1.04l2.044-2.055a.75.75 0 00.219-.564l-.205-2.861a.75.75 0 01.78-.78l2.861.205a.75.75 0 00.564-.219l2.055-2.044z"/></svg>
                    <div>
                        <div class="text-sm opacity-80">Rating</div>
                        <div class="text-lg font-semibold">{{ number_format($rating, 1) }}</div>
                        @php
                            $full = (int) floor($rating);
                            $dec = $rating - $full;
                            $half = $dec >= 0.25 && $dec < 0.75;
                            $empty = 5 - $full - ($half ? 1 : 0);
                            $color = $rating >= 4.5 ? 'text-green-500' : ($rating >= 3 ? 'text-yellow-500' : 'text-red-500');
                        @endphp
                        <div class="mt-1 flex items-center gap-1">
                            @for($i = 0; $i < $full; $i++)
                                <svg class="w-4 h-4 {{ $color }}" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927a1 1 0 011.902 0l1.065 3.282a1 1 0 00.95.69h3.451a1 1 0 01.588 1.806l-2.793 2.03a1 1 0 00-.364 1.118l1.065 3.283a1 1 0 01-1.538 1.118L10 14.347l-2.375 1.907a1 1 0 01-1.538-1.118l1.065-3.283a1 1 0 00-.364-1.118L4.995 8.705a1 1 0 01.588-1.806h3.451a1 1 0 00.95-.69l1.065-3.282z"/></svg>
                            @endfor
                            @if($half)
                                <span class="relative inline-block w-4 h-4">
                                    <svg class="absolute inset-0 w-4 h-4 text-zinc-400" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path d="M9.049 2.927l1.065 3.282a1 1 0 00.95.69h3.451l-2.793 2.03a1 1 0 00-.364 1.118l1.065 3.283L10 14.347l-2.375 1.907 1.065-3.283a1 1 0 00-.364-1.118L4.995 8.705h3.451a1 1 0 00.95-.69l1.065-3.282z"/></svg>
                                    <div class="overflow-hidden w-2 h-4">
                                        <svg class="w-4 h-4 {{ $color }}" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927a1 1 0 011.902 0l1.065 3.282a1 1 0 00.95.69h3.451a1 1 0 01.588 1.806l-2.793 2.03a1 1 0 00-.364 1.118l1.065 3.283a1 1 0 01-1.538 1.118L10 14.347l-2.375 1.907a1 1 0 01-1.538-1.118l1.065-3.283a1 1 0 00-.364-1.118L4.995 8.705a1 1 0 01.588-1.806h3.451a1 1 0 00.95-.69l1.065-3.282z"/></svg>
                                    </div>
                                </span>
                            @endif
                            @for($i = 0; $i < $empty; $i++)
                                <svg class="w-4 h-4 text-zinc-400" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path d="M9.049 2.927l1.065 3.282a1 1 0 00.95.69h3.451l-2.793 2.03a1 1 0 00-.364 1.118l1.065 3.283L10 14.347l-2.375 1.907 1.065-3.283a1 1 0 00-.364-1.118L4.995 8.705h3.451a1 1 0 00.95-.69l1.065-3.282z"/></svg>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-white/15 p-4 backdrop-blur">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3c-4.97 0-9 3.13-9 7 0 2.22 1.32 4.2 3.39 5.52A8.96 8.96 0 0012 21a8.96 8.96 0 005.61-1.48C20.68 14.2 22 12.22 22 10c0-3.87-4.03-7-9-7zm0 12a5 5 0 110-10 5 5 0 010 10z"/></svg>
                    <div>
                        <div class="text-sm opacity-80">Ganancias</div>
                        <div class="text-lg font-semibold">COP {{ number_format($ganancias) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="grid gap-6 lg:grid-cols-3">
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <flux:heading size="lg" class="mb-4">Entregas de hoy</flux:heading>
            <div class="space-y-4">
                @forelse($hoy as $r)
                    <div class="flex items-center justify-between rounded-lg p-2 ring-1 ring-zinc-200 dark:ring-zinc-700">
                        <div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $r['user']['name'] ?? 'Cliente' }}</div>
                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $r['origen_direccion'] }} → {{ $r['destino_direccion'] ?? 'Destino' }}</div>
                        </div>
                    <div class="flex items-center gap-2">
                        <flux:button size="sm" variant="ghost" wire:click="viewReservation({{ $r['id'] }})">Ver</flux:button>
                    </div>
                    </div>
                @empty
                    <div class="text-sm text-zinc-500">No tienes reservas activas</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <flux:heading size="lg" class="mb-4">Próximas</flux:heading>
            <div class="space-y-4">
                @forelse($futuras as $r)
                    <div class="flex items-center justify-between rounded-lg p-2 ring-1 ring-zinc-200 dark:ring-zinc-700">
                        <div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $r['user']['name'] ?? 'Cliente' }}</div>
                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $r['origen_direccion'] }} → {{ $r['destino_direccion'] ?? 'Destino' }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:button size="sm" variant="ghost" wire:click="viewReservation({{ $r['id'] }})">Ver</flux:button>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-zinc-500">Sin próximas asignaciones</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <flux:heading size="lg" class="mb-4">Historial reciente</flux:heading>
            <div class="space-y-4">
                @forelse($pasadas as $r)
                    <div class="flex items-center justify-between rounded-lg p-2 ring-1 ring-zinc-200 dark:ring-zinc-700">
                        <div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $r['user']['name'] ?? 'Cliente' }}</div>
                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $r['origen_direccion'] }} → {{ $r['destino_direccion'] ?? 'Destino' }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:button size="sm" variant="ghost" wire:click="viewReservation({{ $r['id'] }})">Ver</flux:button>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-zinc-500">Aún no hay historial</div>
                @endforelse
            </div>
        </div>
    </div>



    <div class="grid gap-6 md:grid-cols-3">
        <div class="rounded-xl p-6 ring-1 ring-zinc-200 dark:ring-zinc-800 bg-white dark:bg-zinc-900">
            <div class="flex items-center gap-3 mb-2">
                <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="font-semibold">Rutas óptimas</div>
            </div>
            <div class="text-sm text-zinc-600 dark:text-zinc-400">Aprovecha los atajos sugeridos para reducir tiempos de entrega.</div>
        </div>
        <div class="rounded-xl p-6 ring-1 ring-zinc-200 dark:ring-zinc-800 bg-white dark:bg-zinc-900">
            <div class="flex items-center gap-3 mb-2">
                <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 20h10a2 2 0 002-2V7a2 2 0 00-2-2H9l-2 2H7"/></svg>
                <div class="font-semibold">Checkpoints seguros</div>
            </div>
            <div class="text-sm text-zinc-600 dark:text-zinc-400">Verifica en cada punto para asegurar la cadena de custodia.</div>
        </div>
        <div class="rounded-xl p-6 ring-1 ring-zinc-200 dark:ring-zinc-800 bg-white dark:bg-zinc-900">
            <div class="flex items-center gap-3 mb-2">
                <svg class="w-6 h-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17l3 3m0 0l3-3m-3 3V4"/></svg>
                <div class="font-semibold">Desempeño</div>
            </div>
            <div class="text-sm text-zinc-600 dark:text-zinc-400">Consulta tu historial para mejorar tiempos y satisfacción.</div>
        </div>
    </div>

    @if($showModal && $selected)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-lg rounded-xl bg-white dark:bg-zinc-900 shadow-lg border border-zinc-200 dark:border-zinc-800">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                    <div class="font-semibold">Detalle de reserva</div>
                    <button class="text-zinc-500 hover:text-zinc-700" wire:click="closeModal">✕</button>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex justify-between">
                        <div>
                            <div class="text-sm text-zinc-500">Cliente</div>
                            <div class="font-medium">{{ $selected['user']['name'] ?? 'Cliente' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-zinc-500">Estado</div>
                            <div class="font-medium">{{ ucfirst($selected['estado']) }}</div>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-zinc-500">Ruta</div>
                        <div class="text-sm">{{ $selected['origen_direccion'] }} → {{ $selected['destino_direccion'] ?? 'Destino' }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <div class="text-sm text-zinc-500">Inicio</div>
                            <div class="text-sm">{{ \Carbon\Carbon::parse($selected['fecha_inicio'])->format('d/m H:i') }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-zinc-500">Total</div>
                            <div class="text-sm">COP {{ number_format($selected['monto_final'] ?? $selected['monto'] ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-end gap-2">
                    @if(($selected['estado'] ?? null) === 'pendiente')
                        <flux:button variant="ghost" wire:click="closeModal">Cerrar</flux:button>
                        <flux:button variant="primary" wire:click="acceptReservation({{ $selected['id'] }})">Aceptar</flux:button>
                        <flux:button variant="danger" wire:click="rejectReservation({{ $selected['id'] }})">Rechazar</flux:button>
                    @elseif(($selected['estado'] ?? null) === 'activa')
                        <flux:button variant="ghost" wire:click="closeModal">Cerrar</flux:button>
                        <flux:button variant="primary" wire:click="completeReservation({{ $selected['id'] }})">Marcar completada</flux:button>
                        <flux:button variant="danger" wire:click="cancelActive({{ $selected['id'] }})">Cancelar</flux:button>
                    @else
                        <flux:button variant="ghost" wire:click="closeModal">Cerrar</flux:button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>