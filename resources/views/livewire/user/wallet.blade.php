<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.public')] class extends Component {
    public array $cards = [];
    public ?string $numero = null;
    public ?string $nombre = null;
    public ?string $exp = null;
    public string $tipo = 'credito';
    public ?string $brand = 'Visa';
    public ?int $preferred = null;

    public function mount(): void
    {
        $saved = session('wallet_cards_' . auth()->id());
        if (is_array($saved)) {
            $this->cards = $saved;
        }
        $pref = session('wallet_preferred_' . auth()->id());
        if (is_numeric($pref)) {
            $this->preferred = (int) $pref;
        }
    }

    public function add(): void
    {
        $this->validate([
            'numero' => ['required', 'regex:/^[0-9\s]{16,19}$/'],
            'nombre' => ['nullable', 'string', 'min:3'],
            'exp' => ['required', 'regex:/^(0[1-9]|1[0-2])\/[0-9]{2}$/'],
            'tipo' => ['required', 'in:credito,debito'],
        ]);

        $this->cards[] = [
            'numero' => $this->numero,
            'nombre' => $this->nombre,
            'exp' => $this->exp,
            'tipo' => $this->tipo,
            'brand' => $this->brand,
        ];

        session()->put('wallet_cards_' . auth()->id(), $this->cards);

        $this->numero = null;
        $this->nombre = null;
        $this->exp = null;
        $this->tipo = 'credito';
        $this->brand = 'Visa';
    }

    public function remove(int $index): void
    {
        if (!isset($this->cards[$index])) return;
        unset($this->cards[$index]);
        $this->cards = array_values($this->cards);
        session()->put('wallet_cards_' . auth()->id(), $this->cards);
        if ($this->preferred === $index) {
            $this->preferred = null;
            session()->forget('wallet_preferred_' . auth()->id());
        }
    }

    public function setPreferred(int $index): void
    {
        if (!isset($this->cards[$index])) return;
        $this->preferred = $index;
        session()->put('wallet_preferred_' . auth()->id(), $index);
    }
}; ?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">BgaGO Wallet</h1>
            <div class="flex items-center gap-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-sm font-semibold">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-gradient-to-r from-white to-zinc-50 dark:from-zinc-900 dark:to-zinc-800 p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">BgaGO Cash</div>
                    <div class="text-4xl font-bold text-zinc-900 dark:text-white">COP 0</div>
                </div>
                <button type="button" class="px-4 py-2 rounded-lg bg-zinc-900/5 dark:bg-white/10 hover:bg-zinc-900/10 dark:hover:bg-white/20 text-zinc-800 dark:text-zinc-200">+ Gift card</button>
            </div>
        </div>

        <div class="mb-4">
            <h2 class="text-lg font-bold text-zinc-900 dark:text-white">Payment Methods</h2>
        </div>

        <div class="grid md:grid-cols-2 gap-4 mb-8">
            @forelse($cards as $i => $card)
                <div class="p-5 rounded-2xl bg-[#0b1e6b] text-white">
                    <div class="flex items-center justify-between">
                        <span class="text-sm">{{ strtoupper($card['brand'] ?? 'Visa') }}</span>
                        <div class="flex items-center gap-2">
                            @if($preferred === $i)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-white/20 text-[10px]">
                                    Preferred
                                </span>
                            @endif
                            <span class="inline-flex items-center justify-center w-10 h-6 rounded bg-blue-500 text-white text-[10px]">{{ strtoupper(($card['brand'] ?? 'VISA')) }}</span>
                        </div>
                    </div>
                    <div class="mt-4 tracking-widest">**** {{ substr(preg_replace('/\D/', '', ($card['numero'] ?? '')), -4) }}</div>
                    <div class="mt-2 text-sm opacity-80">{{ $card['nombre'] ?? '' }}</div>
                    <div class="mt-4 flex items-center gap-2">
                        <button type="button" wire:click="setPreferred({{ $i }})" class="text-xs px-3 py-1 rounded bg-white/20 hover:bg-white/30">Set preferred</button>
                        <button type="button" wire:click="remove({{ $i }})" class="text-xs px-3 py-1 rounded bg-white/20 hover:bg-white/30">Remove</button>
                    </div>
                </div>
            @empty
                <div class="text-sm text-zinc-600 dark:text-zinc-400">No saved cards.</div>
            @endforelse

            <div class="p-5 rounded-2xl bg-green-700 text-white">
                <div class="flex items-center justify-between">
                    <span class="text-sm">Cash</span>
                    <span class="inline-flex items-center justify-center w-10 h-6 rounded bg-white/20 text-white text-[10px]">$</span>
                </div>
                <div class="mt-6">Disponible</div>
            </div>
        </div>

        <div x-data="{ open:false }" class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-zinc-900 dark:text-white">Add Payment Method</h3>
                <button type="button" @click="open = !open" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm">+ Add</button>
            </div>
            <div x-show="open" class="mt-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <input type="text" wire:model.live.debounce.300ms="numero" placeholder="1234 5678 9012 3456" class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" maxlength="19">
                    <input type="text" wire:model.live="nombre" placeholder="Cardholder name" class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <input type="text" wire:model.live="exp" placeholder="MM/YY" class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" maxlength="5">
                    <select wire:model.live="tipo" class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="credito">Credit</option>
                        <option value="debito">Debit</option>
                    </select>
                </div>
                <div class="mt-4">
                    <button type="button" wire:click="add" class="px-5 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold">Save card</button>
                </div>
            </div>
        </div>
    </div>
</div>