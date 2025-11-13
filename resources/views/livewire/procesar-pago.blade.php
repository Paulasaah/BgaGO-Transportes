<div>
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="procesarPago" class="space-y-6">
        <div>
            <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-3">Selecciona tu método de pago</h3>
            <div class="space-y-3">
                @foreach($metodos as $m)
                    <label class="block">
                        <div class="flex items-center justify-between p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:border-blue-500 transition-colors {{ $metodo_seleccionado === $m['tipo'] ? 'border-blue-600 ring-2 ring-blue-200' : '' }}">
                            <div class="flex items-start gap-3">
                                <input type="radio" wire:model.live="metodo_seleccionado" value="{{ $m['tipo'] }}" class="mt-1 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <div class="font-semibold text-zinc-900 dark:text-white">{{ $m['nombre'] }}</div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">{{ $m['descripcion'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($m['tipo'] === 'tarjeta')
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="w-12 h-8">
                                        <g>
                                            <rect x="2" y="7" width="28" height="18" rx="3" ry="3" fill="#fff" />
                                            <path d="M27 7H5c-1.657 0-3 1.343-3 3v12c0 1.657 1.343 3 3 3h22c1.657 0 3-1.343 3-3V10c0-1.657-1.343-3-3-3Zm2 15c0 1.103-.897 2-2 2H5c-1.103 0-2-.897-2-2V10c0-1.103.897-2 2-2h22c1.103 0 2 .897 2 2v12Z" opacity=".15" fill="#000" />
                                            <path d="M27 8H5c-1.105 0-2 .895-2 2v1c0-1.105.895-2 2-2h22c1.105 0 2 .895 2 2v-1c0-1.105-.895-2-2-2Z" fill="#fff" opacity=".2" />
                                            <path d="M13.392 12.624l-2.838 6.77h-1.851l-1.397-5.403c-.085-.332-.158-.454-.416-.595-.421-.229-1.117-.443-1.728-.576l.041-.196h2.98c.38 0 .721.253.808.69l.738 3.918 1.822-4.608h1.84Z" fill="#1434cb" />
                                            <path d="M20.646 17.183c.008-1.787-2.47-1.886-2.453-2.684.005-.243.237-.501.743-.567.251-.032.943-.058 1.727.303l.307-1.436c-.421-.152-.964-.299-1.638-.299-1.732 0-2.95.92-2.959 2.238-.011.975.87 1.518 1.533 1.843.683.332.912.545.909.841-.005.454-.545.655-1.047.663-.881.014-1.392-.238-1.799-.428l-.318 1.484c.41.188 1.165.351 1.947.359 1.841 0 3.044-.909 3.05-2.317" fill="#1434cb" />
                                            <path d="M25.423 12.624h-1.494c-.337 0-.62.195-.746.496l-2.628 6.274h1.839l.365-1.011h2.247l.212 1.011h1.62l-1.415-6.77Zm-2.16 4.372.922-2.542.53 2.542h-1.452Z" fill="#1434cb" />
                                            <path d="M15.894 12.624 14.446 19.394 12.695 19.394 14.143 12.624 15.894 12.624z" fill="#1434cb" />
                                        </g>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="w-12 h-8">
                                        <g>
                                            <rect x="2" y="7" width="28" height="18" rx="3" ry="3" fill="#141413"></rect>
                                            <path d="m27,7H5c-1.657,0-3,1.343-3,3v12c0,1.657,1.343,3,3,3h22c1.657,0,3-1.343,3-3v-12c0-1.657-1.343-3-3-3Zm2,15c0,1.103-.897,2-2,2H5c-1.103,0-2-.897-2-2v-12c0-1.103.897-2,2-2h22c1.103,0,2,.897,2,2v12Z" opacity=".15"></path>
                                            <path d="m27,8H5c-1.105,0-2,.895-2,2v1c0-1.105.895-2,2-2h22c1.105,0,2,.895,2,2v-1c0-1.105-.895-2-2-2Z" fill="#fff" opacity=".2"></path>
                                            <path fill="#ff5f00" d="M13.597 11.677H18.407V20.32H13.597z"></path>
                                            <path d="m13.902,15.999c0-1.68.779-3.283,2.092-4.322-2.382-1.878-5.849-1.466-7.727.932-1.863,2.382-1.451,5.833.947,7.712,2,1.573,4.795,1.573,6.795,0-1.329-1.038-2.107-2.642-2.107-4.322Z" fill="#eb001b"></path>
                                            <path d="m24.897,15.999c0,3.039-2.459,5.497-5.497,5.497-1.237,0-2.428-.412-3.39-1.176,2.382-1.878,2.795-5.329.916-7.727-.275-.336-.58-.657-.916-.916,2.382-1.878,5.849-1.466,7.712.932.764.962,1.176,2.153,1.176,3.39Z" fill="#f79e1b"></path>
                                        </g>
                                    </svg>
                                @elseif($m['tipo'] === 'mercadopago')
                                    <span class="inline-flex items-center justify-center w-[26px] h-[18px] rounded-sm bg-blue-600 text-white text-[9px] font-bold">MP</span>
                                @elseif($m['tipo'] === 'transferencia')
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="w-12 h-8">
                                        <g>
                                            <rect x="2" y="7" width="28" height="18" rx="3" ry="3" fill="#e6e6e6"></rect>
                                            <path d="m27,7H5c-1.657,0-3,1.343-3,3v12c0,1.657,1.343,3,3,3h22c1.657,0,3-1.343,3-3v-12c0-1.657-1.343-3-3-3Zm2,15c0,1.103-.897,2-2,2H5c-1.103,0-2-.897-2-2v-12c0-1.103.897-2,2-2h22c1.103,0,2,.897,2,2v12Z" opacity=".15"></path>
                                            <path d="m27,8H5c-1.105,0-2,.895-2,2v1c0-1.105.895-2,2-2h22c1.105,0,2,.895,2,2v-1c0-1.105-.895-2-2-2Z" fill="#fff" opacity=".2"></path>
                                            <path d="m21.5,21h-11c-.2764,0-.5.2236-.5.5s.2236.5.5.5h11c.2764,0,.5-.2236.5-.5s-.2236-.5-.5-.5Z" fill="#1a1a1a" opacity=".5"></path>
                                            <path d="m21.6694,13.6787l-4.8486-3.2324c-.499-.332-1.1426-.332-1.6416,0l-4.8491,3.2324c-.2661.1777-.3823.5029-.2896.8096.0928.3062.3701.5117.6899.5117h1.0195v4h-.75c-.2764,0-.5.2236-.5.5s.2236.5.5.5h10c.2764,0,.5-.2236.5-.5s-.2236-.5-.5-.5h-.75v-4h1.0195c.3198,0,.5972-.2056.6899-.5117.0928-.3066-.0234-.6318-.29-.8096Zm-7.4194,5.3213h-1.5v-4h1.5v4Zm2.5,0h-1.5v-4h1.5v4Zm-.75-5.5c-.4142,0-.75-.3358-.75-.75s.3358-.75.75-.75.75.3358.75.75-.3358.75-.75.75Zm3.25,5.5h-1.5v-4h1.5v4Z" fill="#1a1a1a" opacity=".5"></path>
                                        </g>
                                    </svg>
                                @elseif($m['tipo'] === 'efectivo')
                                    <span class="inline-flex items-center justify-center w-[26px] h-[18px] rounded-sm bg-emerald-600 text-white text-[9px] font-bold">$</span>
                                @endif
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        @php($seleccion = collect($metodos)->firstWhere('tipo', $metodo_seleccionado))

        @if($metodo_seleccionado === 'tarjeta')
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Número de Tarjeta</label>
                    <input type="text" wire:model.live.debounce.300ms="numero_tarjeta" placeholder="1234 5678 9012 3456" class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" maxlength="19">
                    @error('numero_tarjeta')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Nombre del Titular</label>
                    <input type="text" wire:model.live="nombre_titular" placeholder="Como aparece en la tarjeta" class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    @error('nombre_titular')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="flex items-center h-8 text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Fecha de Expiración</label>
                    <input type="text" wire:model.live="fecha_expiracion" placeholder="MM/YY" class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" maxlength="5">
                    @error('fecha_expiracion')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="flex items-center h-8 text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2 gap-2">CVV
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="w-12 h-8">
                            <g>
                                <rect x="2" y="7" width="28" height="18" rx="3" ry="3" fill="#e6e6e6"></rect>
                                <path fill="#fff" d="M6 18H19V21H6z"></path>
                                <path d="m24.5,13.5c1.519,0,2.902.569,3.96,1.5h1.54v-4H2v4h18.541c1.057-.931,2.44-1.5,3.959-1.5Z" fill="#1a1a1a"></path>
                                <path d="m27,7H5c-1.657,0-3,1.343-3,3v12c0,1.657,1.343,3,3,3h22c1.657,0,3-1.343,3-3v-12c0-1.657-1.343-3-3-3Zm2,15c0,1.103-.897,2-2,2H5c-1.103,0-2-.897-2-2v-12c0-1.103.897-2,2-2h22c1.103,0,2,.897,2,2v12Z" opacity=".15"></path>
                                <path d="m27,8H5c-1.105,0-2,.895-2,2v1c0-1.105.895-2,2-2h22c1.105,0,2,.895,2,2v-1c0-1.105-.895-2-2-2Z" fill="#fff" opacity=".2"></path>
                                <circle cx="24.5" cy="19.5" r="6" fill="#e6e6e6"></circle>
                                <path fill="#fff" d="M19 17H29V22H19z"></path>
                                <circle cx="24.5" cy="19.5" r="6" fill="none" stroke="#ed1c24" stroke-miterlimit="10"></circle>
                                <circle cx="21.75" cy="19.5" r=".75" fill="#1a1a1a"></circle>
                                <circle cx="24.25" cy="19.5" r=".75" fill="#1a1a1a"></circle>
                                <circle cx="26.75" cy="19.5" r=".75" fill="#1a1a1a"></circle>
                            </g>
                        </svg>
                    </label>
                    <input type="password" wire:model.live="cvv" placeholder="***" class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" maxlength="4">
                    @error('cvv')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="flex items-center h-8 text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Tipo de Tarjeta</label>
                    <select wire:model.live="tipo_tarjeta" class="w-full px-4 py-3 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="credito">Crédito</option>
                        <option value="debito">Débito</option>
                    </select>
                    @error('tipo_tarjeta')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @else
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 bg-zinc-50 dark:bg-zinc-800/50">
                <p class="text-sm text-zinc-700 dark:text-zinc-300">
                    Método seleccionado: <span class="font-semibold">{{ $seleccion['nombre'] ?? ucfirst($metodo_seleccionado) }}</span>
                </p>
                <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-1">
                    {{ $seleccion['descripcion'] ?? '' }}
                </p>
            </div>
        @endif

        <div class="flex gap-4">
            <button type="submit" wire:loading.attr="disabled" wire:target="procesarPago" class="flex-1 px-6 py-4 bg-blue-600 hover:bg-blue-700 disabled:bg-zinc-400 text-white rounded-lg font-semibold text-lg transition-all hover:scale-[1.02] disabled:hover:scale-100 shadow-lg hover:shadow-xl disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="procesarPago">Pagar</span>
                <span wire:loading wire:target="procesarPago" class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Procesando...
                </span>
            </button>
        </div>
    </form>
</div>
