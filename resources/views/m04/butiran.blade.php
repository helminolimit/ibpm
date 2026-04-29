<x-layouts::app :title="__('Butiran Permohonan')">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Kemaskini Portal', 'url' => route('kemaskini-portal.index')],
        ['label' => $permohonan->no_tiket],
    ]" />

    <livewire:m04.butiran-permohonan :permohonan-id="$permohonan->id" />
</x-layouts::app>
