<div class="space-y-4">
    {{-- Search Input --}}
    <div class="flex items-center justify-end">
        <div class="flex items-center gap-2">
            <span class="text-sm text-zinc-500">Cari:</span>
            <div class="w-64">
                <flux:input 
                    wire:model.live.debounce.400ms="carian" 
                    placeholder="Cari no. tiket atau URL..." 
                    clearable 
                    size="sm" 
                />
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <flux:table :paginate="$this->senarai">
        <flux:table.columns>
            <flux:table.column>No. Tiket</flux:table.column>
            <flux:table.column>URL Halaman</flux:table.column>
            <flux:table.column>Jenis</flux:table.column>
            <flux:table.column>Tarikh Mohon</flux:table.column>
            <flux:table.column>Tarikh Selesai</flux:table.column>
            <flux:table.column>Status</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->senarai as $permohonan)
                <flux:table.row :key="$permohonan->id" wire:key="permohonan-{{ $permohonan->id }}">
                    <flux:table.cell class="font-mono text-sm font-semibold">
                        <a 
                            href="{{ route('kemaskini-portal.show', $permohonan) }}" 
                            wire:navigate
                            class="text-blue-600 hover:text-blue-800 hover:underline"
                        >
                            {{ $permohonan->no_tiket }}
                        </a>
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
                        {{ $permohonan->created_at->format('d M Y') }}
                    </flux:table.cell>
                    <flux:table.cell class="text-sm">
                        {{ $permohonan->tarikh_selesai ? $permohonan->tarikh_selesai->format('d M Y') : '—' }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="{{ $permohonan->status->color() }}">
                            {{ $permohonan->status->label() }}
                        </flux:badge>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="py-12 text-center text-zinc-500">
                        Tiada rekod ditemui.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
