<div>
    {{-- Filter row --}}
    <div class="mb-4 flex flex-wrap items-center gap-3">
        <flux:select wire:model.live="filterStatus" class="w-52" placeholder="Semua Status">
            <flux:select.option value="">Semua Status</flux:select.option>
            @foreach ($this->statusOptions() as $value => $label)
                <flux:select.option :value="$value">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:spacer />

        <div class="flex items-center gap-2">
            <flux:select wire:model.live="perPage" class="w-20">
                <flux:select.option value="10">10</flux:select.option>
                <flux:select.option value="20">20</flux:select.option>
                <flux:select.option value="50">50</flux:select.option>
            </flux:select>
            <span class="text-sm text-zinc-500">entries</span>
        </div>

        <flux:input
            wire:model.live.debounce.300ms="carian"
            placeholder="Cari tiket, nama, ID login..."
            clearable
            size="sm"
            class="w-56"
        />
    </div>

    <flux:table :paginate="$this->senarai">
        <flux:table.columns>
            <flux:table.column class="w-12">Bil</flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'no_tiket'"
                :direction="$sortDirection"
                wire:click="sort('no_tiket')"
            >No. Tiket</flux:table.column>
            <flux:table.column>Pemohon</flux:table.column>
            <flux:table.column>ID Login</flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'tarikh_berkuat_kuasa'"
                :direction="$sortDirection"
                wire:click="sort('tarikh_berkuat_kuasa')"
            >Tarikh BK</flux:table.column>
            <flux:table.column>Jenis</flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortBy === 'status'"
                :direction="$sortDirection"
                wire:click="sort('status')"
            >Status</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->senarai as $loop_index => $permohonan)
                <flux:table.row :key="$permohonan->id" wire:key="permohonan-{{ $permohonan->id }}">
                    <flux:table.cell class="text-sm tabular-nums text-zinc-400">
                        {{ $this->senarai->firstItem() + $loop_index }}
                    </flux:table.cell>
                    <flux:table.cell class="font-mono text-sm font-semibold">
                        {{ $permohonan->no_tiket }}
                    </flux:table.cell>
                    <flux:table.cell>{{ $permohonan->pemohon?->name ?? '-' }}</flux:table.cell>
                    <flux:table.cell class="font-mono text-sm">{{ $permohonan->id_login_komputer }}</flux:table.cell>
                    <flux:table.cell class="text-sm">
                        {{ $permohonan->tarikh_berkuat_kuasa->format('d/m/Y') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="{{ $permohonan->jenis_tindakan === 'TAMAT' ? 'red' : 'yellow' }}">
                            {{ $permohonan->jenis_tindakan }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="{{ $permohonan->status->color() }}">
                            {{ $permohonan->status->label() }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />
                            <flux:menu>
                                <flux:menu.item
                                    :href="route('admin.penamatan.audit', $permohonan)"
                                    wire:navigate
                                    icon="eye"
                                >Lihat / Audit</flux:menu.item>

                                @if ($permohonan->status->value === 'MENUNGGU_KEL_2')
                                    <flux:menu.separator />
                                    <form method="POST" action="{{ route('admin.penamatan.lulus', $permohonan) }}">
                                        @csrf @method('PATCH')
                                        <flux:menu.item as="button" type="submit" icon="check-circle">
                                            Lulus Peringkat 2
                                        </flux:menu.item>
                                    </form>
                                @endif

                                @if ($permohonan->status->value === 'DALAM_PROSES')
                                    <flux:menu.separator />
                                    <form method="POST" action="{{ route('admin.penamatan.selesai', $permohonan) }}">
                                        @csrf @method('PATCH')
                                        <flux:menu.item as="button" type="submit" icon="check-badge">
                                            Tandakan Selesai
                                        </flux:menu.item>
                                    </form>
                                @endif
                            </flux:menu>
                        </flux:dropdown>
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
</div>
