<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.public')] class extends Component {
    //
}; ?>

<div class="flex flex-col items-start px-4 sm:px-6 lg:px-8 mb-16">
    @include('partials.settings-heading')

    <x-settings.layout heading="Apariencia" subheading="Configura la apariencia de tu cuenta">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">Light</flux:radio>
            <flux:radio value="dark" icon="moon">Dark</flux:radio>
            <flux:radio value="system" icon="computer-desktop">System</flux:radio>
        </flux:radio.group>
    </x-settings.layout>
</div>
