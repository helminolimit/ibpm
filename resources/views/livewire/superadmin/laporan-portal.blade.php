<div>
    <flux:main container>
        <x-breadcrumbs :items="[
            ['label' => 'iBPM', 'url' => route('dashboard')],
            ['label' => 'Superadmin'],
            ['label' => 'Laporan Kemaskini Portal'],
        ]" />

        <div class="mb-6">
            <flux:heading size="xl">Laporan Kemaskini Portal</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Ringkasan dan analisis permohonan kemaskini portal</flux:text>
        </div>

        {{-- Statistik ringkasan --}}
        <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-sm text-zinc-500">Jumlah</p>
                <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->statistik['jumlah'] }}</p>
            </div>
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                <p class="text-sm text-blue-600 dark:text-blue-400">Diterima</p>
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $this->statistik['diterima'] }}</p>
            </div>
            <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-900/20">
                <p class="text-sm text-yellow-600 dark:text-yellow-400">Dalam Proses</p>
                <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ $this->statistik['dalam_proses'] }}</p>
            </div>
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
                <p class="text-sm text-green-600 dark:text-green-400">Selesai</p>
                <p class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $this->statistik['selesai'] }}</p>
            </div>
            <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-sm text-zinc-500">Masa Purata Selesai</p>
                <p class="text-2xl font-bold text-zinc-900 dark:text-white">
                    {{ $this->statistik['masa_purata'] ? $this->statistik['masa_purata'] . ' jam' : '—' }}
                </p>
            </div>
        </div>

        {{-- Penapis --}}
        <div class="mb-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <flux:input type="date" wire:model.live="dari" label="Dari" />
            <flux:input type="date" wire:model.live="hingga" label="Hingga" />
            <flux:select wire:model.live="filterStatus" label="Status" placeholder="Semua status...">
                @foreach ($this->statusOptions() as $value => $label)
                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="filterJenis" label="Jenis" placeholder="Semua jenis...">
                <flux:select.option value="kandungan">Kandungan</flux:select.option>
                <flux:select.option value="konfigurasi">Konfigurasi</flux:select.option>
                <flux:select.option value="lain_lain">Lain-lain</flux:select.option>
            </flux:select>
            <div class="flex items-end gap-2">
                <flux:button variant="ghost" wire:click="resetPenapis" icon="x-mark" size="sm">Reset</flux:button>
            </div>
        </div>

        {{-- Export buttons --}}
        <div class="mb-4 flex items-center gap-2">
            <flux:button variant="filled" color="green" wire:click="exportExcel" icon="arrow-down-tray" size="sm">
                Export Excel
            </flux:button>
            <flux:button variant="filled" color="red" wire:click="exportPdf" icon="document-arrow-down" size="sm">
                Export PDF
            </flux:button>
        </div>

        {{-- Jadual --}}
        <flux:table :paginate="$this->records">
            <flux:table.columns>
                <flux:table.column class="w-12">Bil</flux:table.column>
                <flux:table.column>No. Tiket</flux:table.column>
                <flux:table.column>Pemohon</flux:table.column>
                <flux:table.column>URL Halaman</flux:table.column>
                <flux:table.column>Jenis</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Tarikh Mohon</flux:table.column>
                <flux:table.column>Tarikh Selesai</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->records as $i => $item)
                    <flux:table.row :key="$item->id" wire:key="laporan-{{ $item->id }}">
                        <flux:table.cell class="tabular-nums text-sm text-zinc-400">
                            {{ $this->records->firstItem() + $i }}
                        </flux:table.cell>
                        <flux:table.cell class="font-mono text-sm font-semibold">
                            {{ $item->no_tiket }}
                        </flux:table.cell>
                        <flux:table.cell class="text-sm">
                            {{ $item->pemohon->name }}
                        </flux:table.cell>
                        <flux:table.cell class="max-w-xs truncate text-sm">
                            {{ $item->url_halaman }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="zinc">
                                {{ ucfirst($item->jenis_perubahan) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="{{ $item->status->color() }}">
                                {{ $item->status->label() }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-sm">
                            {{ $item->created_at->format('d/m/Y') }}
                        </flux:table.cell>
                        <flux:table.cell class="text-sm">
                            {{ $item->tarikh_selesai?->format('d/m/Y') ?? '—' }}
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="py-12 text-center text-zinc-500">
                            Tiada rekod ditemui.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:main>
</div>
