<x-layouts::app :title="__('Permohonan Penamatan Akaun Baru')">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Penamatan Akaun', 'url' => route('penamatan-akaun.index')],
        ['label' => 'Permohonan Baru'],
    ]" />

    <livewire:m03.borang-permohonan />
</x-layouts::app>
