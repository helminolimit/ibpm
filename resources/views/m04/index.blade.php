<x-layouts::app :title="__('Senarai Permohonan Kemaskini Portal')">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Kemaskini Portal'],
    ]" />

    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Permohonan Kemaskini Portal Saya</flux:heading>
            <flux:button variant="primary" :href="route('kemaskini-portal.create')" wire:navigate icon="plus">
                Permohonan Baru
            </flux:button>
        </div>

        @if (session('berjaya'))
            <flux:callout variant="success" icon="check-circle">
                <flux:callout.text>{{ session('berjaya') }}</flux:callout.text>
            </flux:callout>
        @endif

        <livewire:m04.senarai-permohonan />
    </div>
</x-layouts::app>
