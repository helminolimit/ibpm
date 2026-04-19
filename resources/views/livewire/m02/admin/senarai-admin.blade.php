<div class="mx-auto max-w-6xl space-y-6 px-4 py-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Urus Permohonan Toner</flux:heading>
    </div>

    {{-- Filter row --}}
    <div class="flex flex-wrap items-center gap-3">
        <flux:select wire:model.live="filterStatus" class="w-52" placeholder="Semua Status">
            <flux:select.option value="">Semua Status</flux:select.option>
            @foreach ($this->statuses as $status)
                <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
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
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="No. Tiket / Model Pencetak..."
                    clearable
                    size="sm"
                />
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
            >
                No. Tiket
            </flux:table.column>
            <flux:table.column>Pemohon</flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'model_pencetak'"
                :direction="$sortDirection"
                wire:click="sort('model_pencetak')"
            >
                Model Pencetak
            </flux:table.column>
            <flux:table.column>Kuantiti</flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'created_at'"
                :direction="$sortDirection"
                wire:click="sort('created_at')"
            >
                Tarikh Mohon
            </flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'status'"
                :direction="$sortDirection"
                wire:click="sort('status')"
            >
                Status
            </flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->permohonan as $i => $item)
                <flux:table.row :key="$item->id" wire:key="permohonan-{{ $item->id }}">
                    <flux:table.cell class="text-sm tabular-nums text-zinc-400">
                        {{ $this->permohonan->firstItem() + $i }}
                    </flux:table.cell>
                    <flux:table.cell class="font-mono text-sm font-medium">
                        {{ $item->no_tiket }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="text-sm font-medium">{{ $item->user->name }}</div>
                        <div class="text-xs text-zinc-400">{{ $item->user->bahagian ?? '—' }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $item->model_pencetak }}</flux:table.cell>
                    <flux:table.cell>
                        {{ $item->kuantiti }} unit
                        @if ($item->kuantiti_diluluskan && $item->kuantiti_diluluskan !== $item->kuantiti)
                            <span class="text-xs text-zinc-400">(diluluskan: {{ $item->kuantiti_diluluskan }})</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $item->created_at->format('d M Y') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="{{ $item->status->color() }}" size="sm">
                            {{ $item->status->label() }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($item->status === \App\Enums\StatusPermohonanToner::Submitted)
                            <flux:button
                                :href="route('m02.admin.proses', $item->id)"
                                wire:navigate
                                variant="primary"
                                size="sm"
                                icon="cog-6-tooth"
                            >
                                Proses
                            </flux:button>
                        @elseif ($item->status === \App\Enums\StatusPermohonanToner::Diluluskan)
                            <flux:button
                                :href="route('m02.admin.hantar', $item->id)"
                                wire:navigate
                                variant="outline"
                                size="sm"
                                icon="truck"
                            >
                                Hantar
                            </flux:button>
                        @else
                            <flux:button
                                :href="route('m02.butiran', $item->id)"
                                wire:navigate
                                variant="ghost"
                                size="sm"
                                icon="eye"
                            >
                                Lihat
                            </flux:button>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="py-12 text-center text-zinc-500">
                        Tiada permohonan dijumpai.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
