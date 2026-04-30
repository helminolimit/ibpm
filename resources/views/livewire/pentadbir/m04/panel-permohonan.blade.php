<div>
    <flux:main container>
        <x-breadcrumbs :items="[
            ['label' => 'iBPM', 'url' => route('dashboard')],
            ['label' => 'Kemaskini Portal'],
            ['label' => 'Panel Pentadbir'],
        ]" />

        <div class="mb-6">
            <flux:heading size="xl">Permohonan Kemaskini Portal</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Urus dan kemaskini status semua permohonan kemaskini portal</flux:text>
        </div>

        {{-- Status filter --}}
        <div class="mb-4 grid gap-3 sm:grid-cols-2">
            <flux:select wire:model.live="filterStatus" placeholder="Semua status...">
                @foreach ($this->statusOptions() as $value => $label)
                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        {{-- Per-page + Global search --}}
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:select wire:model.live="perPage" class="w-24">
                    <flux:select.option value="10">10</flux:select.option>
                    <flux:select.option value="20">20</flux:select.option>
                    <flux:select.option value="50">50</flux:select.option>
                    <flux:select.option value="100">100</flux:select.option>
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
                <flux:table.column>Pemohon</flux:table.column>
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
                        <flux:table.cell class="text-sm">
                            {{ $permohonan->pemohon->name }}
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
                            <div class="flex gap-1">
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    icon="pencil-square"
                                    wire:click="bukaPilihStatus('{{ $permohonan->id }}')"
                                    title="Kemaskini Status"
                                />
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    icon="user-plus"
                                    wire:click="bukaTugasan('{{ $permohonan->id }}')"
                                    title="Tugaskan Pembangun"
                                />
                            </div>
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
    </flux:main>

    {{-- Modal kemaskini status --}}
    <flux:modal
        name="kemaskini-status"
        class="min-w-[22rem]"
        :closable="false"
        x-data="{ loading: false }"
        x-on:cancel="loading && $event.preventDefault()"
        x-on:livewire:commit.window="loading = false"
    >
        <div class="relative space-y-6">
            <div
                wire:loading
                wire:target="kemaskiniStatus"
                class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/80 dark:bg-zinc-900/80"
            >
                <flux:icon name="arrow-path" class="size-6 animate-spin text-zinc-500" />
            </div>
            <div>
                <flux:heading size="lg">Kemaskini Status Permohonan</flux:heading>
                <flux:subheading>Perubahan status akan menghantar notifikasi emel kepada pemohon.</flux:subheading>
            </div>
            <flux:select wire:model="statusBaru" label="Status Baru">
                @foreach ($this->statusOptions() as $value => $label)
                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>
            @error('statusBaru')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="kemaskiniStatus" @click="loading = true">
                    Kemaskini
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal tugaskan pembangun --}}
    <flux:modal
        name="tugaskan-pembangun"
        class="min-w-[22rem]"
        :closable="false"
        x-data="{ loading: false }"
        x-on:cancel="loading && $event.preventDefault()"
        x-on:livewire:commit.window="loading = false"
    >
        <div class="relative space-y-6">
            <div
                wire:loading
                wire:target="tugaskanPembangun"
                class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/80 dark:bg-zinc-900/80"
            >
                <flux:icon name="arrow-path" class="size-6 animate-spin text-zinc-500" />
            </div>
            <div>
                <flux:heading size="lg">Tugaskan Pembangun Web</flux:heading>
                <flux:subheading>Pilih pembangun untuk menyelesaikan permohonan ini. Notifikasi emel akan dihantar.</flux:subheading>
            </div>
            <flux:select wire:model="teknisianId" label="Pembangun" placeholder="-- Pilih Pembangun --">
                @foreach ($this->senaraiTeknisian as $teknisian)
                    <flux:select.option value="{{ $teknisian->id }}">{{ $teknisian->name }} ({{ $teknisian->role->label() }})</flux:select.option>
                @endforeach
            </flux:select>
            @error('teknisianId')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            <flux:textarea wire:model="notaTugasan" label="Nota Tugasan (Pilihan)" placeholder="Arahan tambahan untuk pembangun..." rows="3" />
            @error('notaTugasan')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="tugaskanPembangun" @click="loading = true">
                    Tugaskan
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
