<div>
    <flux:main container>

        <div class="mb-6">
            <flux:heading size="xl">Log Audit</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Rekod semua tindakan kritikal dalam sistem</flux:text>
        </div>

        {{-- Filters --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <flux:select wire:model.live="filterModule" class="w-40">
                    <flux:select.option value="">Semua Modul</flux:select.option>
                    @foreach ($this->modules() as $module)
                        <flux:select.option value="{{ $module }}">{{ $module }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <flux:select wire:model.live="perPage" class="w-20">
                        <flux:select.option value="25">25</flux:select.option>
                        <flux:select.option value="50">50</flux:select.option>
                        <flux:select.option value="100">100</flux:select.option>
                    </flux:select>
                    <span class="text-sm text-zinc-500">setiap halaman</span>
                </div>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari tindakan / keterangan..." clearable size="sm" class="w-64" />
            </div>
        </div>

        {{-- Table --}}
        <flux:table :paginate="$this->logs">
            <flux:table.columns>
                <flux:table.column>Tarikh</flux:table.column>
                <flux:table.column>Pengguna</flux:table.column>
                <flux:table.column>Tindakan</flux:table.column>
                <flux:table.column>Modul</flux:table.column>
                <flux:table.column>Keterangan</flux:table.column>
                <flux:table.column>IP</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->logs as $log)
                    <flux:table.row :key="$log->id" wire:key="log-{{ $log->id }}">
                        <flux:table.cell class="text-sm text-zinc-500 whitespace-nowrap">
                            {{ $log->created_at->format('d/m/Y H:i') }}
                        </flux:table.cell>
                        <flux:table.cell class="text-sm">
                            {{ $log->user?->name ?? '—' }}
                        </flux:table.cell>
                        <flux:table.cell class="text-sm font-medium">{{ $log->action }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="zinc">{{ $log->module }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-sm text-zinc-500">{{ $log->description }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-400">{{ $log->ip_address ?? '—' }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="py-12 text-center text-zinc-500">
                            Tiada rekod log audit.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

    </flux:main>
</div>
