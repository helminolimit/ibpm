<x-layouts::app :title="__('Penamatan Akaun — Pentadbir ICT')">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Penamatan Akaun'],
    ]" />

    <div class="space-y-4">
        <flux:heading size="xl">Pengurusan Penamatan Akaun</flux:heading>

        @if (session('berjaya'))
            <flux:callout variant="success" icon="check-circle">
                <flux:callout.text>{{ session('berjaya') }}</flux:callout.text>
            </flux:callout>
        @endif

        <livewire:m03.admin-senarai />
    </div>
</x-layouts::app>
