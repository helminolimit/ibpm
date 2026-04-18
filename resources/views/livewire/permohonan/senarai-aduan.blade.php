<div>
    <flux:main container>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <flux:heading size="xl">Senarai Aduan Saya</flux:heading>
                <flux:text class="mt-1">Semak status dan butiran semua aduan ICT yang telah anda hantar.</flux:text>
            </div>
            <flux:button :href="route('aduan-ict.create')" wire:navigate variant="primary" icon="plus">
                Aduan Baharu
            </flux:button>
        </div>

        {{-- Status filter --}}
        <div class="mb-4 grid gap-3 sm:grid-cols-3 lg:grid-cols-4">
            <flux:select wire:model.live="filterStatus" placeholder="Semua Status">
                @foreach (\App\Enums\StatusAduan::cases() as $status)
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
                    <flux:select.option value="100">100</flux:select.option>
                </flux:select>
                <span class="text-sm text-zinc-500">entries per page</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-zinc-500">Cari:</span>
                <div class="w-56">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="No. tiket atau tajuk..." clearable size="sm" />
                </div>
            </div>
        </div>

        <flux:table :paginate="$this->aduan">
            <flux:table.columns>
                <flux:table.column class="w-12">Bil</flux:table.column>
                <flux:table.column
                    sortable
                    :sorted="$sortBy === 'no_tiket'"
                    :direction="$sortDirection"
                    wire:click="sort('no_tiket')"
                >No. Tiket</flux:table.column>
                <flux:table.column>Kategori</flux:table.column>
                <flux:table.column
                    sortable
                    :sorted="$sortBy === 'created_at'"
                    :direction="$sortDirection"
                    wire:click="sort('created_at')"
                >Tarikh Mohon</flux:table.column>
                <flux:table.column
                    sortable
                    :sorted="$sortBy === 'updated_at'"
                    :direction="$sortDirection"
                    wire:click="sort('updated_at')"
                >Tarikh Kemaskini</flux:table.column>
                <flux:table.column
                    sortable
                    :sorted="$sortBy === 'status'"
                    :direction="$sortDirection"
                    wire:click="sort('status')"
                >Status</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->aduan as $loop_index => $aduan)
                    <flux:table.row :key="$aduan->id" wire:key="aduan-{{ $aduan->id }}">
                        <flux:table.cell class="text-zinc-400 text-sm tabular-nums">
                            {{ $this->aduan->firstItem() + $loop_index }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="font-mono text-sm font-medium">{{ $aduan->no_tiket }}</span>
                        </flux:table.cell>
                        <flux:table.cell>{{ $aduan->kategori->nama }}</flux:table.cell>
                        <flux:table.cell>
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $aduan->created_at->format('d/m/Y') }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $aduan->updated_at->format('d/m/Y') }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ $aduan->status->color() }}" size="sm">
                                {{ $aduan->status->label() }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                :href="route('aduan-ict.show', $aduan->id)"
                                wire:navigate
                                variant="ghost"
                                size="sm"
                                icon="eye"
                                inset="top bottom"
                            >Lihat</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="flex size-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                                    <flux:icon name="inbox" class="size-6 text-zinc-400" />
                                </div>
                                @if ($search || $filterStatus)
                                    <flux:text class="text-zinc-500">Tiada aduan ditemui berdasarkan carian semasa.</flux:text>
                                @else
                                    <flux:text class="text-zinc-500">Anda belum mempunyai sebarang aduan.</flux:text>
                                    <flux:button :href="route('aduan-ict.create')" wire:navigate variant="primary" size="sm" icon="plus">
                                        Klik di sini untuk membuat aduan baru
                                    </flux:button>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

    </flux:main>
</div>
