<x-layouts::app :title="'Log Audit — ' . $permohonan->no_tiket">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Penamatan Akaun', 'url' => route('admin.penamatan.index')],
        ['label' => $permohonan->no_tiket . ' — Audit'],
    ]" />

    <div class="space-y-6 max-w-4xl">
        <div class="flex items-start justify-between">
            <div>
                <flux:heading size="xl">Log Audit</flux:heading>
                <flux:text class="text-zinc-500 font-mono">{{ $permohonan->no_tiket }}</flux:text>
            </div>
            <flux:badge size="lg" color="{{ $permohonan->status->color() }}">
                {{ $permohonan->status->label() }}
            </flux:badge>
        </div>

        {{-- Maklumat ringkas --}}
        <flux:card>
            <dl class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div>
                    <dt class="text-xs font-medium text-zinc-500">Pemohon</dt>
                    <dd class="mt-1 text-sm font-medium">{{ $permohonan->pemohon?->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-zinc-500">Pengguna Sasaran</dt>
                    <dd class="mt-1 text-sm font-medium">{{ $permohonan->penggunaSasaran?->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-zinc-500">ID Login</dt>
                    <dd class="mt-1 text-sm font-mono font-semibold">{{ $permohonan->id_login_komputer }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-zinc-500">Tarikh Berkuat Kuasa</dt>
                    <dd class="mt-1 text-sm">{{ $permohonan->tarikh_berkuat_kuasa->format('d/m/Y') }}</dd>
                </div>
            </dl>
        </flux:card>

        {{-- Log audit --}}
        <flux:card>
            <flux:heading class="mb-4">Rekod Audit ({{ $permohonan->logAudit->count() }})</flux:heading>
            @if ($permohonan->logAudit->isEmpty())
                <div class="py-12 text-center text-zinc-500">Tiada rekod audit.</div>
            @else
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column class="w-12">Bil</flux:table.column>
                        <flux:table.column>Tarikh / Masa</flux:table.column>
                        <flux:table.column>Pengguna</flux:table.column>
                        <flux:table.column>Tindakan</flux:table.column>
                        <flux:table.column>IP Address</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($permohonan->logAudit as $i => $log)
                            <flux:table.row :key="$log->id">
                                <flux:table.cell class="tabular-nums text-sm text-zinc-400">{{ $i + 1 }}</flux:table.cell>
                                <flux:table.cell class="text-sm tabular-nums">
                                    {{ $log->created_at?->format('d/m/Y H:i:s') }}
                                </flux:table.cell>
                                <flux:table.cell class="text-sm">{{ $log->pengguna?->name ?? '-' }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge size="sm" color="zinc">
                                        {{ str_replace('_', ' ', $log->tindakan) }}
                                    </flux:badge>
                                </flux:table.cell>
                                <flux:table.cell class="font-mono text-xs text-zinc-500">
                                    {{ $log->ip_address ?? '-' }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif
        </flux:card>

        <flux:button :href="route('admin.penamatan.index')" wire:navigate icon="arrow-left">
            Kembali
        </flux:button>
    </div>
</x-layouts::app>
