<div class="max-w-3xl space-y-6">
    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div>
            <flux:heading size="xl" class="font-mono">{{ $permohonan->no_tiket }}</flux:heading>
            <flux:text class="mt-1 text-zinc-500">
                Dikemukakan pada {{ $permohonan->created_at->format('d/m/Y H:i') }}
            </flux:text>
        </div>
        <flux:badge size="lg" color="{{ $permohonan->status->color() }}">
            {{ $permohonan->status->label() }}
        </flux:badge>
    </div>

    {{-- Maklumat permohonan --}}
    <flux:card>
        <flux:heading class="mb-4">Maklumat Permohonan</flux:heading>
        <dl class="divide-y divide-zinc-100 dark:divide-zinc-800">
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-zinc-500">Pemohon</dt>
                <dd class="col-span-2 text-sm">{{ $permohonan->pemohon?->name }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-zinc-500">Email Pemohon</dt>
                <dd class="col-span-2 text-sm">{{ $permohonan->pemohon?->email }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-zinc-500">URL Halaman</dt>
                <dd class="col-span-2 break-all text-sm">
                    <a href="{{ $permohonan->url_halaman }}" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400">
                        {{ $permohonan->url_halaman }}
                    </a>
                </dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-zinc-500">Jenis Perubahan</dt>
                <dd class="col-span-2 text-sm">
                    <flux:badge size="sm" color="zinc">
                        {{ match ($permohonan->jenis_perubahan) {
                            'kandungan' => 'Kandungan',
                            'konfigurasi' => 'Konfigurasi',
                            'lain_lain' => 'Lain-lain',
                            default => $permohonan->jenis_perubahan,
                        } }}
                    </flux:badge>
                </dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-zinc-500">Butiran Kemaskini</dt>
                <dd class="col-span-2 whitespace-pre-wrap text-sm">{{ $permohonan->butiran_kemaskini }}</dd>
            </div>
            @if ($permohonan->pentadbir)
                <div class="grid grid-cols-3 gap-4 py-3">
                    <dt class="text-sm font-medium text-zinc-500">Pentadbir</dt>
                    <dd class="col-span-2 text-sm">{{ $permohonan->pentadbir->name }}</dd>
                </div>
            @endif
            @if ($permohonan->tarikh_selesai)
                <div class="grid grid-cols-3 gap-4 py-3">
                    <dt class="text-sm font-medium text-zinc-500">Tarikh Selesai</dt>
                    <dd class="col-span-2 text-sm font-medium text-green-600">
                        {{ $permohonan->tarikh_selesai->format('d/m/Y H:i') }}
                    </dd>
                </div>
            @endif
        </dl>
    </flux:card>

    {{-- Lampiran --}}
    @if ($permohonan->lampirans->count() > 0)
        <flux:card>
            <flux:heading class="mb-4">Lampiran</flux:heading>
            <div class="space-y-2">
                @foreach ($permohonan->lampirans as $lampiran)
                    <div class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                        <div class="flex items-center gap-3">
                            <flux:icon name="paper-clip" class="size-5 text-zinc-400" />
                            <div>
                                <flux:text class="text-sm font-medium">{{ $lampiran->nama_fail }}</flux:text>
                                <flux:text class="text-xs text-zinc-500">
                                    {{ $lampiran->jenis_fail }}
                                </flux:text>
                            </div>
                        </div>
                        <flux:button
                            size="sm"
                            variant="ghost"
                            icon="arrow-down-tray"
                            wire:click="muatTurunLampiran({{ $lampiran->id }})"
                        >
                            Muat Turun
                        </flux:button>
                    </div>
                @endforeach
            </div>
        </flux:card>
    @endif

    {{-- Log Audit --}}
    @if ($permohonan->logAudits->count() > 0)
        <flux:card>
            <flux:heading class="mb-4">Log Aktiviti</flux:heading>
            <div class="space-y-3">
                @foreach ($permohonan->logAudits as $log)
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex-shrink-0">
                            <div class="flex size-8 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                                <flux:icon name="clock" class="size-4 text-zinc-500" />
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <flux:text class="text-sm font-medium">
                                {{ match ($log->tindakan) {
                                    'permohonan_dihantar' => 'Permohonan dihantar',
                                    'permohonan_diterima' => 'Permohonan diterima',
                                    'dalam_proses' => 'Dalam proses',
                                    'selesai' => 'Selesai',
                                    default => $log->tindakan,
                                } }}
                            </flux:text>
                            <flux:text class="text-xs text-zinc-500">
                                {{ $log->pengguna?->name }} • {{ $log->created_at->format('d/m/Y H:i') }}
                            </flux:text>
                        </div>
                    </div>
                @endforeach
            </div>
        </flux:card>
    @endif

    <div class="flex">
        <flux:button :href="route('kemaskini-portal.index')" wire:navigate icon="arrow-left">
            Kembali
        </flux:button>
    </div>
</div>
