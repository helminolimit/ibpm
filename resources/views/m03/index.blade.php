<x-layouts::app :title="__('Permohonan Penamatan Akaun')">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Penamatan Akaun'],
    ]" />

    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Permohonan Penamatan Akaun Saya</flux:heading>
            <flux:button variant="primary" :href="route('penamatan-akaun.create')" wire:navigate icon="plus">
                Permohonan Baru
            </flux:button>
        </div>

        @if (session('berjaya'))
            <flux:callout variant="success" icon="check-circle">
                <flux:callout.text>{{ session('berjaya') }}</flux:callout.text>
            </flux:callout>
        @endif

        <flux:table :paginate="$senarai">
            <flux:table.columns>
                <flux:table.column class="w-12">Bil</flux:table.column>
                <flux:table.column>No. Tiket</flux:table.column>
                <flux:table.column>Pengguna Sasaran</flux:table.column>
                <flux:table.column>ID Login</flux:table.column>
                <flux:table.column>Jenis</flux:table.column>
                <flux:table.column>Tarikh Mohon</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($senarai as $i => $permohonan)
                    <flux:table.row :key="$permohonan->id">
                        <flux:table.cell class="tabular-nums text-sm text-zinc-400">
                            {{ $senarai->firstItem() + $i }}
                        </flux:table.cell>
                        <flux:table.cell class="font-mono font-semibold text-sm">
                            {{ $permohonan->no_tiket }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $permohonan->penggunaSasaran?->name ?? '-' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-sm">{{ $permohonan->id_login_komputer }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="{{ $permohonan->jenis_tindakan === 'TAMAT' ? 'red' : 'yellow' }}">
                                {{ $permohonan->jenis_tindakan }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-sm">{{ $permohonan->created_at->format('d/m/Y') }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="{{ $permohonan->status->color() }}">
                                {{ $permohonan->status->label() }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="eye"
                                :href="route('penamatan-akaun.show', $permohonan)"
                                wire:navigate
                            />
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="py-12 text-center text-zinc-500">
                            Tiada permohonan ditemui.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</x-layouts::app>
