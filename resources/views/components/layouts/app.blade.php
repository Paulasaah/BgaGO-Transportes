<x-layouts.app.sidebar>
    <flux:main>
        {{ $slot }}

        <livewire:alerts.alert-manager />
        <livewire:alerts.confirm-dialog />

        @if(session('success'))
            <script>
                window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'success', message: @json(session('success')) } }));
            </script>
        @endif
        @if(session('error'))
            <script>
                window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'error', message: @json(session('error')) } }));
            </script>
        @endif
    </flux:main>
</x-layouts.app.sidebar>
