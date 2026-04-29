<div class="space-y-4">
    {{-- Filters --}}
    <div class="grid gap-3 sm:grid-cols-2">
        <flux:select wire:model.live="filterStatus" placeholder="Semua status...">
            @foreach ($this->statusOptions() as $value => $label)
                <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    {{-- Per-page + Global search --}}
    <div class="flex items-center justify-between">
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
                <flux:input wire:model.live.debounce.300ms="search" placeholder="No. tiket, URL..." clearable size="sm" />
            </div>
        </div>
    </div>

    <flux:table :paginate="$this->records">
        <flux:table.columns>
            <flux:table.column class="w-12">Bil</flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'no_tiket'"
                :direction="$sortDirection"
                wire:click="sort('no_tiket')"
            >No. Tiket</flux:table.column>
            <flux:table.column>URL Halaman</flux:table.column>
            <flux:table.column>Jenis</flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'created_at'"
                :direction="$sortDirection"
                wire:click="sort('created_at')"
            >Tarikh Mohon</flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'status'"
                :direction="$sortDirection"
                wire:click="sort('status')"
            >Status</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->records as $i => $permohonan)
                <flux:table.row :key="$permohonan->id" wire:key="permohonan-{{ $permohonan->id }}">
                    <flux:table.cell class="tabular-nums text-sm text-zinc-400">
                        {{ $this->records->firstItem() + $i }}
                    </flux:table.cell>
                    <flux:table.cell class="font-mono text-sm font-semibold">
                        {{ $permohonan->no_tiket }}
                    </flux:table.cell>
                    <flux:table.cell class="max-w-xs truncate text-sm">
                        {{ $permohonan->url_halaman }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="zinc">
                            {{ match ($permohonan->jenis_perubahan) {
                                'kandungan' => 'Kandungan',
                                'konfigurasi' => 'Konfigurasi',
                                default => 'Lain-lain',
                            } }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="text-sm">
                        {{ $permohonan->created_at->format('d/m/Y') }}
                    </flux:table.cell>
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
                            :href="route('kemaskini-portal.show', $permohonan)"
                            wire:navigate
                        />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="py-12 text-center text-zinc-500">
                        Tiada permohonan ditemui.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
