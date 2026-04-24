<div>
    <flux:main container>

        {{-- Header --}}
        <div class="mb-6">
            <flux:heading size="xl">Aduan ICT</flux:heading>
            <flux:text class="mt-1 text-zinc-500">
                {{ (auth()->user()->isPentadbir() || auth()->user()->isTeknician()) ? 'Unit: '.auth()->user()->unit_bpm : 'Semua Unit' }}
            </flux:text>
        </div>

        {{-- Stats Cards --}}
        <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-zinc-500">Aduan Hari Ini</flux:text>
                <div class="mt-1 text-3xl font-semibold tabular-nums">{{ $this->jumlahHariIni }}</div>
            </div>
            <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-zinc-500">Dalam Proses</flux:text>
                <div class="mt-1 text-3xl font-semibold tabular-nums text-yellow-600">{{ $this->jumlahDalamProses }}</div>
            </div>
            <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-zinc-500">Selesai Bulan Ini</flux:text>
                <div class="mt-1 text-3xl font-semibold tabular-nums text-green-600">{{ $this->jumlahSelesaiBulanIni }}</div>
            </div>
            <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-zinc-500">Purata Penyelesaian</flux:text>
                <div class="mt-1 text-3xl font-semibold tabular-nums">{{ $this->purataMasaPenyelesaian }}</div>
            </div>
        </div>

        {{-- Filters: Status (left) + Per-page + Search (right) --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <flux:select wire:model.live="filterStatus" class="w-44">
                    <flux:select.option value="">Semua Status</flux:select.option>
                    @foreach (\App\Enums\StatusAduan::cases() as $status)
                        <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <flux:select wire:model.live="perPage" class="w-20">
                        <flux:select.option value="10">10</flux:select.option>
                        <flux:select.option value="25">25</flux:select.option>
                        <flux:select.option value="50">50</flux:select.option>
                        <flux:select.option value="100">100</flux:select.option>
                    </flux:select>
                    <span class="text-sm text-zinc-500">setiap halaman</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-zinc-500">Cari:</span>
                    <div class="w-52">
                        <flux:input wire:model.live.debounce.300ms="search" placeholder="No. tiket / nama pemohon..." clearable size="sm" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <flux:table :paginate="$this->aduan">
            <flux:table.columns>
                <flux:table.column class="w-12">Bil</flux:table.column>
                <flux:table.column
                    sortable
                    :sorted="$sortBy === 'no_tiket'"
                    :direction="$sortDirection"
                    wire:click="sort('no_tiket')"
                >No. Tiket</flux:table.column>
                <flux:table.column>Nama Pemohon</flux:table.column>
                <flux:table.column>Bahagian</flux:table.column>
                <flux:table.column>Kategori</flux:table.column>
                <flux:table.column
                    sortable
                    :sorted="$sortBy === 'created_at'"
                    :direction="$sortDirection"
                    wire:click="sort('created_at')"
                >Tarikh Mohon</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->aduan as $loop_index => $aduan)
                    <flux:table.row :key="$aduan->id" wire:key="aduan-{{ $aduan->id }}">
                        <flux:table.cell class="text-sm tabular-nums text-zinc-400">
                            {{ $this->aduan->firstItem() + $loop_index }}
                        </flux:table.cell>
                        <flux:table.cell class="font-mono text-sm font-medium">
                            {{ $aduan->no_tiket }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $aduan->user?->name ?? '-' }}</flux:table.cell>
                        <flux:table.cell class="text-sm text-zinc-500">{{ $aduan->user?->bahagian ?? '-' }}</flux:table.cell>
                        <flux:table.cell class="text-sm">{{ $aduan->kategori?->nama ?? '-' }}</flux:table.cell>
                        <flux:table.cell class="text-sm text-zinc-500">
                            {{ $aduan->created_at->format('d/m/Y') }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ $aduan->status->color() }}" size="sm">
                                {{ $aduan->status->label() }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                :href="route('admin.aduan.show', $aduan->id)"
                                wire:navigate
                                variant="ghost"
                                size="sm"
                                icon="eye"
                            >
                                Semak
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="py-12 text-center text-zinc-500">
                            @if ($search || $filterStatus)
                                Tiada aduan sepadan dengan carian anda.
                            @else
                                Tiada aduan baru. Semua aduan telah ditindakan.
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

    </flux:main>
</div>
