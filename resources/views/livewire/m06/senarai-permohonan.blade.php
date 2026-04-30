<div>
    <flux:main container>
        <x-breadcrumbs :items="[
            ['label' => 'iBPM', 'url' => route('dashboard')],
            ['label' => 'Kumpulan Emel'],
            ['label' => 'Senarai Permohonan'],
        ]" />

        <div class="mb-6 flex items-start justify-between">
            <div>
                <flux:heading size="xl">Senarai Permohonan Kumpulan Emel</flux:heading>
                <flux:text class="mt-1">Senarai permohonan kemaskini kumpulan emel yang anda telah hantar.</flux:text>
            </div>
            <flux:button :href="route('kumpulan-emel.create')" wire:navigate variant="primary" icon="plus">
                Permohonan Baharu
            </flux:button>
        </div>

        {{-- Filters --}}
        <div class="mb-4 flex flex-wrap items-center gap-3">
            <flux:select wire:model.live="filterStatus" class="w-52" placeholder="Semua Status">
                <flux:select.option value="">Semua Status</flux:select.option>
                @foreach ($this->statuses as $status)
                    <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        {{-- Per-page + Search --}}
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:select wire:model.live="perPage" class="w-24">
                    <flux:select.option value="10">10</flux:select.option>
                    <flux:select.option value="25">25</flux:select.option>
                    <flux:select.option value="50">50</flux:select.option>
                </flux:select>
                <span class="text-sm text-zinc-500">entries per page</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-zinc-500">Cari:</span>
                <div class="w-56">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="No. tiket atau kumpulan..." clearable size="sm" />
                </div>
            </div>
        </div>

        <flux:table :paginate="$this->permohonan">
            <flux:table.columns>
                <flux:table.column class="w-12">Bil</flux:table.column>
                <flux:table.column
                    sortable
                    :sorted="$sortBy === 'no_tiket'"
                    :direction="$sortDirection"
                    wire:click="sort('no_tiket')"
                >No. Tiket</flux:table.column>
                <flux:table.column>Kumpulan Emel</flux:table.column>
                <flux:table.column>Jenis Tindakan</flux:table.column>
                <flux:table.column>Bil. Ahli</flux:table.column>
                <flux:table.column
                    sortable
                    :sorted="$sortBy === 'status'"
                    :direction="$sortDirection"
                    wire:click="sort('status')"
                >Status</flux:table.column>
                <flux:table.column
                    sortable
                    :sorted="$sortBy === 'created_at'"
                    :direction="$sortDirection"
                    wire:click="sort('created_at')"
                >Tarikh Hantar</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->permohonan as $loop_index => $item)
                    <flux:table.row :key="$item->id" wire:key="permohonan-{{ $item->id }}">
                        <flux:table.cell class="text-sm tabular-nums text-zinc-400">
                            {{ $this->permohonan->firstItem() + $loop_index }}
                        </flux:table.cell>
                        <flux:table.cell class="font-mono font-medium">
                            {{ $item->no_tiket }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div>
                                <p class="font-medium">{{ $item->kumpulanEmel->nama_kumpulan }}</p>
                                <p class="text-xs text-zinc-400">{{ $item->kumpulanEmel->alamat_emel }}</p>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge
                                color="{{ $item->jenis_tindakan->value === 'tambah' ? 'green' : 'red' }}"
                                size="sm"
                            >
                                {{ $item->jenis_tindakan->label() }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="tabular-nums">
                            {{ $item->ahliKumpulan->count() }} orang
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ $item->status->color() }}" size="sm">
                                {{ $item->status->label() }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-sm text-zinc-500">
                            {{ $item->created_at->format('d/m/Y') }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                :href="route('kumpulan-emel.show', $item->id)"
                                wire:navigate
                                size="sm"
                                variant="ghost"
                                icon="eye"
                            >
                                Lihat
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <flux:icon name="inbox" class="size-10 text-zinc-300" />
                                <flux:text class="text-zinc-500">Tiada permohonan ditemui.</flux:text>
                                <flux:button :href="route('kumpulan-emel.create')" wire:navigate variant="primary" size="sm" icon="plus">
                                    Hantar Permohonan Pertama
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

    </flux:main>
</div>
