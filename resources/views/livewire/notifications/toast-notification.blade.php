<div>
    <div 
        x-data="{ 
            show: @entangle('show'),
            type: @entangle('type'),
            message: @entangle('message')
        }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
        x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:translate-x-0"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
        @auto-hide-notification.window="setTimeout(() => { show = false }, $event.detail.duration)"
        class="fixed top-4 right-4 z-50 max-w-sm w-full pointer-events-auto"
        style="display: none;"
        role="alert"
        aria-live="assertive"
    >
    <div class="rounded-lg shadow-lg overflow-hidden border"
        :class="{
            'bg-white dark:bg-zinc-900 border-green-200 dark:border-green-800': type === 'success',
            'bg-white dark:bg-zinc-900 border-red-200 dark:border-red-800': type === 'error',
            'bg-white dark:bg-zinc-900 border-yellow-200 dark:border-yellow-800': type === 'warning',
            'bg-white dark:bg-zinc-900 border-blue-200 dark:border-blue-800': type === 'info'
        }"
    >
        <div class="p-4">
            <div class="flex items-start gap-3">
                <!-- Icono -->
                <div class="flex-shrink-0">
                    <template x-if="type === 'success'">
                        <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                    
                    <template x-if="type === 'error'">
                        <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                    
                    <template x-if="type === 'warning'">
                        <svg class="w-6 h-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </template>
                    
                    <template x-if="type === 'info'">
                        <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                </div>

                <!-- Mensaje -->
                <div class="flex-1 pt-0.5">
                    <p class="text-sm font-medium"
                        :class="{
                            'text-green-800 dark:text-green-200': type === 'success',
                            'text-red-800 dark:text-red-200': type === 'error',
                            'text-yellow-800 dark:text-yellow-200': type === 'warning',
                            'text-blue-800 dark:text-blue-200': type === 'info'
                        }"
                        x-text="message"
                    ></p>
                </div>

                <!-- Botón cerrar -->
                <div class="flex-shrink-0">
                    <button 
                        type="button"
                        @click="show = false"
                        class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors"
                        :class="{
                            'text-green-500 hover:bg-green-100 dark:hover:bg-green-900/30 focus:ring-green-500': type === 'success',
                            'text-red-500 hover:bg-red-100 dark:hover:bg-red-900/30 focus:ring-red-500': type === 'error',
                            'text-yellow-500 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 focus:ring-yellow-500': type === 'warning',
                            'text-blue-500 hover:bg-blue-100 dark:hover:bg-blue-900/30 focus:ring-blue-500': type === 'info'
                        }"
                    >
                        <span class="sr-only">Cerrar</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Barra de progreso -->
        <div class="h-1 bg-zinc-100 dark:bg-zinc-800">
            <div 
                x-show="show"
                class="h-full transition-all ease-linear"
                :class="{
                    'bg-green-500': type === 'success',
                    'bg-red-500': type === 'error',
                    'bg-yellow-500': type === 'warning',
                    'bg-blue-500': type === 'info'
                }"
                :style="'animation: progress ' + {{ $duration }}ms + 'ms linear forwards;'"
            ></div>
        </div>
    </div>
    </div>

    <style>
        @keyframes progress {
            from {
                width: 100%;
            }
            to {
                width: 0%;
            }
        }
    </style>
</div>
