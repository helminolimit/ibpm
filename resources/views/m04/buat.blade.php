<x-layouts::app :title="__('Permohonan Kemaskini Portal Baru')">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Kemaskini Portal', 'url' => route('kemaskini-portal.index')],
        ['label' => 'Permohonan Baru'],
    ]" />

    <livewire:m04.borang-permohonan />
</x-layouts::app>
