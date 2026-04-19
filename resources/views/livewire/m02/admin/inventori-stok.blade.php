<div class="mx-auto max-w-6xl space-y-6 px-4 py-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Inventori Stok Toner</flux:heading>
        <flux:button wire:click="bukaTambah" icon="plus" variant="primary">
            Tambah Stok Baru
        </flux:button>
    </div>

    {{-- Filter jenis --}}
    <div class="flex flex-wrap items-center gap-3">
        <flux:select wire:model.live="filterJenis" class="w-52" placeholder="Semua Jenis">
            <flux:select.option value="">Semua Jenis</flux:select.option>
            @foreach ($this->getJenisList() as $jenis)
                <flux:select.option value="{{ $jenis->value }}">{{ $jenis->label() }}</flux:select.option>
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
                    placeholder="Model toner / Jenama..."
                    clearable
                    size="sm"
                />
            </div>
        </div>
    </div>

    <flux:table :paginate="$this->stok">
        <flux:table.columns>
            <flux:table.column class="w-12">Bil</flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'model_toner'"
                :direction="$sortDirection"
                wire:click="sort('model_toner')"
            >
                Model Toner
            </flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'jenama'"
                :direction="$sortDirection"
                wire:click="sort('jenama')"
            >
                Jenama
            </flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'jenis_toner'"
                :direction="$sortDirection"
                wire:click="sort('jenis_toner')"
            >
                Jenis
            </flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'kuantiti_ada'"
                :direction="$sortDirection"
                wire:click="sort('kuantiti_ada')"
            >
                Stok Ada
            </flux:table.column>
            <flux:table.column>Stok Min</flux:table.column>
            <flux:table.column>Status Stok</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->stok as $i => $item)
                <flux:table.row :key="$item->id" wire:key="stok-{{ $item->id }}">
                    <flux:table.cell class="text-sm tabular-nums text-zinc-400">
                        {{ $this->stok->firstItem() + $i }}
                    </flux:table.cell>
                    <flux:table.cell class="font-mono text-sm font-medium">
                        {{ $item->model_toner ?? '—' }}
                    </flux:table.cell>
                    <flux:table.cell>{{ $item->jenama ?? '—' }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($item->jenis_toner instanceof \App\Enums\JenisToner)
                            <flux:badge color="{{ $item->jenis_toner->color() }}" size="sm">
                                {{ $item->jenis_toner->label() }}
                            </flux:badge>
                        @else
                            {{ $item->jenis_toner }}
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="tabular-nums">{{ $item->kuantiti_ada }}</flux:table.cell>
                    <flux:table.cell class="tabular-nums text-zinc-400">{{ $item->kuantiti_minimum }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($item->kuantiti_ada === 0)
                            <flux:badge color="red" size="sm">Habis</flux:badge>
                        @elseif ($item->kuantiti_ada <= $item->kuantiti_minimum)
                            <flux:badge color="yellow" size="sm">Rendah</flux:badge>
                        @else
                            <flux:badge color="green" size="sm">Mencukupi</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button
                            wire:click="bukaEdit({{ $item->id }})"
                            variant="ghost"
                            size="sm"
                            icon="pencil"
                        >
                            Edit
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="py-12 text-center text-zinc-500">
                        Tiada rekod stok toner dijumpai.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- Modal: Tambah / Kemaskini Stok --}}
    <flux:modal
        name="tambah-stok"
        class="min-w-[28rem]"
        :closable="false"
        x-data="{ loading: false }"
        x-on:cancel="loading && $event.preventDefault()"
        x-on:livewire:commit.window="loading = false"
    >
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editId ? 'Kemaskini Stok Toner' : 'Tambah Stok Toner Baru' }}
                </flux:heading>
                <flux:subheading>
                    {{ $editId ? 'Ubah maklumat atau kuantiti stok toner.' : 'Isi maklumat stok toner baharu.' }}
                </flux:subheading>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>
                        Model Toner
                        <flux:badge color="red" size="sm" class="ml-1">Wajib</flux:badge>
                    </flux:label>
                    <flux:input
                        wire:model="modelToner"
                        placeholder="Cth: CF217A"
                        maxlength="100"
                    />
                    <flux:error name="modelToner" />
                </flux:field>

                <flux:field>
                    <flux:label>
                        Jenama
                        <flux:badge color="red" size="sm" class="ml-1">Wajib</flux:badge>
                    </flux:label>
                    <flux:input
                        wire:model="jenama"
                        placeholder="Cth: HP, Canon"
                        maxlength="100"
                    />
                    <flux:error name="jenama" />
                </flux:field>

                <flux:field>
                    <flux:label>
                        Jenis
                        <flux:badge color="red" size="sm" class="ml-1">Wajib</flux:badge>
                    </flux:label>
                    <flux:select wire:model="jenisToner" placeholder="Pilih jenis toner...">
                        @foreach ($this->getJenisList() as $jenis)
                            <flux:select.option value="{{ $jenis->value }}">
                                {{ $jenis->label() }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="jenisToner" />
                </flux:field>

                <flux:field>
                    <flux:label>Warna</flux:label>
                    <flux:input
                        wire:model="warna"
                        placeholder="Keterangan tambahan..."
                        maxlength="100"
                    />
                    <flux:error name="warna" />
                </flux:field>

                <flux:field>
                    <flux:label>
                        Kuantiti Ada
                        <flux:badge color="red" size="sm" class="ml-1">Wajib</flux:badge>
                    </flux:label>
                    <flux:input
                        type="number"
                        wire:model="kuantitiAda"
                        min="0"
                    />
                    <flux:error name="kuantitiAda" />
                </flux:field>

                <flux:field>
                    <flux:label>
                        Kuantiti Minimum
                        <flux:badge color="red" size="sm" class="ml-1">Wajib</flux:badge>
                    </flux:label>
                    <flux:input
                        type="number"
                        wire:model="kuantitiMinimum"
                        min="1"
                    />
                    <flux:description>Paras minimum sebelum amaran stok rendah.</flux:description>
                    <flux:error name="kuantitiMinimum" />
                </flux:field>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" wire:click="resetForm">Batal</flux:button>
                </flux:modal.close>
                <flux:modal.trigger name="confirm-stok">
                    <flux:button variant="primary">Simpan</flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    </flux:modal>

    {{-- Confirmation modal --}}
    <flux:modal
        name="confirm-stok"
        class="min-w-[22rem]"
        :closable="false"
        x-data="{ loading: false }"
        x-on:cancel="loading && $event.preventDefault()"
        x-on:livewire:commit.window="loading = false"
    >
        <div class="relative space-y-6">
            <div
                wire:loading
                wire:target="simpan"
                class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/80 dark:bg-zinc-900/80"
            >
                <flux:icon name="arrow-path" class="size-6 animate-spin text-zinc-500" />
            </div>

            <div>
                <flux:heading size="lg">Sahkan simpan?</flux:heading>
                <flux:subheading>Semak semula maklumat stok sebelum menyimpan.</flux:subheading>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="simpan" @click="loading = true">
                    Ya, Simpan
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
