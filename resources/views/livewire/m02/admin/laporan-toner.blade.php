<div class="mx-auto max-w-7xl space-y-6 px-4 py-6">
    <flux:heading size="xl">Laporan Penggunaan Toner</flux:heading>

    {{-- Filter row --}}
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
        <flux:field>
            <flux:label>Tarikh Dari</flux:label>
            <flux:input type="date" wire:model.live="tarikhDari" />
        </flux:field>

        <flux:field>
            <flux:label>Tarikh Hingga</flux:label>
            <flux:input type="date" wire:model.live="tarikhHingga" />
        </flux:field>

        <flux:field>
            <flux:label>Bahagian / Unit</flux:label>
            <flux:select wire:model.live="filterBahagian" placeholder="Semua Bahagian">
                <flux:select.option value="">Semua Bahagian</flux:select.option>
                @foreach ($this->getBahagianList() as $bahagian)
                    <flux:select.option value="{{ $bahagian }}">{{ $bahagian }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>

        <flux:field>
            <flux:label>Jenis Toner</flux:label>
            <flux:select wire:model.live="filterJenis" placeholder="Semua Jenis">
                <flux:select.option value="">Semua Jenis</flux:select.option>
                @foreach ($this->getJenisList() as $jenis)
                    <flux:select.option value="{{ $jenis->value }}">{{ $jenis->label() }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>

        <flux:field>
            <flux:label>Status</flux:label>
            <flux:select wire:model.live="filterStatus" placeholder="Semua Status">
                <flux:select.option value="">Semua Status</flux:select.option>
                @foreach ($this->getStatusList() as $status)
                    <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>
    </div>

    {{-- Summary statistics --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="text-sm text-zinc-500">Jumlah Permohonan</div>
            <div class="mt-1 text-2xl font-semibold tabular-nums">{{ $this->statistik['jumlah'] }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="text-sm text-zinc-500">Diluluskan</div>
            <div class="mt-1 text-2xl font-semibold tabular-nums text-green-600">{{ $this->statistik['diluluskan'] }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="text-sm text-zinc-500">Dihantar</div>
            <div class="mt-1 text-2xl font-semibold tabular-nums text-teal-600">{{ $this->statistik['dihantar'] }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="text-sm text-zinc-500">Ditolak</div>
            <div class="mt-1 text-2xl font-semibold tabular-nums text-red-600">{{ $this->statistik['ditolak'] }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="text-sm text-zinc-500">Jumlah Unit Toner</div>
            <div class="mt-1 text-2xl font-semibold tabular-nums text-blue-600">{{ $this->statistik['jumlahUnit'] }}</div>
        </div>
    </div>

    {{-- Export buttons --}}
    <div class="flex items-center gap-2">
        <flux:button wire:click="exportExcel" icon="arrow-down-tray" size="sm" variant="outline">
            Excel
        </flux:button>
        <flux:button wire:click="exportPdf" icon="arrow-down-tray" size="sm" variant="outline">
            PDF
        </flux:button>
    </div>

    {{-- Per-page + Global search --}}
    <div class="flex items-center justify-between">
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
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="No. Tiket / Pemohon / Model..."
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
            <flux:table.column
                sortable
                :sorted="$sortBy === 'created_at'"
                :direction="$sortDirection"
                wire:click="sort('created_at')"
            >
                Tarikh Mohon
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
            <flux:table.column>Jenama / Jenis</flux:table.column>
            <flux:table.column>Kuantiti</flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'status'"
                :direction="$sortDirection"
                wire:click="sort('status')"
            >
                Status
            </flux:table.column>
            <flux:table.column>Tarikh Dihantar</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->permohonan as $i => $item)
                <flux:table.row :key="$item->id" wire:key="laporan-{{ $item->id }}">
                    <flux:table.cell class="text-sm tabular-nums text-zinc-400">
                        {{ $this->permohonan->firstItem() + $i }}
                    </flux:table.cell>
                    <flux:table.cell class="font-mono text-sm font-medium">
                        {{ $item->no_tiket }}
                    </flux:table.cell>
                    <flux:table.cell>{{ $item->created_at->format('d M Y') }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="text-sm font-medium">{{ $item->user->name }}</div>
                        <div class="text-xs text-zinc-400">{{ $item->user->bahagian ?? '—' }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $item->model_pencetak }}</flux:table.cell>
                    <flux:table.cell>
                        <div>{{ $item->jenama_toner }}</div>
                        @if ($item->jenis_toner instanceof \App\Enums\JenisToner)
                            <flux:badge color="{{ $item->jenis_toner->color() }}" size="sm">
                                {{ $item->jenis_toner->label() }}
                            </flux:badge>
                        @else
                            <div class="text-xs text-zinc-400">{{ $item->jenis_toner }}</div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="tabular-nums">
                        {{ $item->kuantiti }} diminta
                        @if ($item->kuantiti_diluluskan !== null)
                            <div class="text-xs text-zinc-400">{{ $item->kuantiti_diluluskan }} diluluskan</div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="{{ $item->status->color() }}" size="sm">
                            {{ $item->status->label() }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="text-sm text-zinc-500">
                        {{ $item->penghantaran?->tarikh_hantar?->format('d M Y') ?? '—' }}
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="9" class="py-12 text-center text-zinc-500">
                        Tiada rekod permohonan dalam tempoh yang dipilih.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
