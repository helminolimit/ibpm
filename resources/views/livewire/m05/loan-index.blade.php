<div class="px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Pinjaman ICT</flux:heading>
            <flux:subheading>Senarai permohonan pinjaman peralatan ICT anda.</flux:subheading>
        </div>
        <flux:button :href="route('m05.loan.create')" wire:navigate variant="primary" icon="plus">
            Permohonan Baharu
        </flux:button>
    </div>

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
            <div class="w-48">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari..." clearable size="sm" />
            </div>
        </div>
    </div>

    <flux:table :paginate="$this->records">
        <flux:table.columns>
            <flux:table.column class="w-12">Bil</flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'id'"
                :direction="$sortDirection"
                wire:click="sort('id')"
            >
                No. Permohonan
            </flux:table.column>
            <flux:table.column>Jenis</flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'status'"
                :direction="$sortDirection"
                wire:click="sort('status')"
            >
                Status
            </flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'created_at'"
                :direction="$sortDirection"
                wire:click="sort('created_at')"
            >
                Tarikh Mohon
            </flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->records as $loop_index => $record)
                <flux:table.row :key="$record->id" wire:key="record-{{ $record->id }}">
                    <flux:table.cell class="text-zinc-400 text-sm tabular-nums">
                        {{ $this->records->firstItem() + $loop_index }}
                    </flux:table.cell>
                    <flux:table.cell class="font-medium">
                        #{{ str_pad($record->id, 6, '0', STR_PAD_LEFT) }}
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($record->on_behalf_of)
                            <flux:badge color="purple" size="sm">Bagi Pihak Orang Lain</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">Diri Sendiri</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge :color="$record->status->color()" size="sm">
                            {{ $record->status->label() }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="text-sm text-zinc-500">
                        {{ $record->created_at->format('d/m/Y') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button size="sm" variant="ghost" icon="eye" :href="route('m05.loan.index')" wire:navigate>
                            Lihat
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="py-12 text-center text-zinc-500">
                        Tiada permohonan ditemui.
                        <a href="{{ route('m05.loan.create') }}" wire:navigate class="text-blue-600 hover:underline">Buat permohonan baharu.</a>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
