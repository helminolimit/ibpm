<x-layouts::app :title="'Butiran — ' . $permohonan->no_tiket">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Penamatan Akaun', 'url' => route('penamatan-akaun.index')],
        ['label' => $permohonan->no_tiket],
    ]" />

    <div class="space-y-6 max-w-3xl">
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
                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-zinc-500">Pemohon</dt>
                    <dd class="col-span-2 text-sm">{{ $permohonan->pemohon?->name }}</dd>
                </div>
                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-zinc-500">Pengguna Sasaran</dt>
                    <dd class="col-span-2 text-sm">{{ $permohonan->penggunaSasaran?->name }}</dd>
                </div>
                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-zinc-500">ID Login Komputer</dt>
                    <dd class="col-span-2 text-sm font-mono font-semibold">{{ $permohonan->id_login_komputer }}</dd>
                </div>
                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-zinc-500">Jenis Tindakan</dt>
                    <dd class="col-span-2 text-sm">
                        <flux:badge size="sm" color="{{ $permohonan->jenis_tindakan === 'TAMAT' ? 'red' : 'yellow' }}">
                            {{ $permohonan->jenis_tindakan === 'TAMAT' ? 'Tamat Akaun' : 'Gantung Akaun' }}
                        </flux:badge>
                    </dd>
                </div>
                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-zinc-500">Tarikh Berkuat Kuasa</dt>
                    <dd class="col-span-2 text-sm">{{ $permohonan->tarikh_berkuat_kuasa->format('d/m/Y') }}</dd>
                </div>
                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-zinc-500">Sebab Penamatan</dt>
                    <dd class="col-span-2 text-sm">{{ $permohonan->sebab_penamatan }}</dd>
                </div>
                @if ($permohonan->tarikh_selesai)
                    <div class="py-3 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-zinc-500">Tarikh Selesai</dt>
                        <dd class="col-span-2 text-sm text-green-600 font-medium">
                            {{ $permohonan->tarikh_selesai->format('d/m/Y H:i') }}
                        </dd>
                    </div>
                @endif
            </dl>
        </flux:card>

        {{-- Timeline kelulusan --}}
        <flux:card>
            <flux:heading class="mb-4">Timeline Kelulusan</flux:heading>
            @if ($permohonan->kelulusan->isEmpty())
                <flux:text class="text-zinc-500">Belum ada tindakan kelulusan.</flux:text>
            @else
                <div class="space-y-4">
                    @foreach ($permohonan->kelulusan as $kel)
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex-shrink-0">
                                <flux:badge
                                    size="sm"
                                    color="{{ $kel->keputusan === 'LULUS' ? 'green' : 'red' }}"
                                    icon="{{ $kel->keputusan === 'LULUS' ? 'check-circle' : 'x-circle' }}"
                                >
                                    {{ $kel->peringkat === 'PERINGKAT_1' ? 'Peringkat 1' : 'Peringkat 2' }}
                                </flux:badge>
                            </div>
                            <div class="flex-1 min-w-0">
                                <flux:text class="font-medium text-sm">{{ $kel->pelulus?->name }}</flux:text>
                                <flux:text class="text-xs text-zinc-500">{{ $kel->diluluskan_pada?->format('d/m/Y H:i') }}</flux:text>
                                @if ($kel->catatan)
                                    <flux:text class="mt-1 text-sm text-zinc-600 dark:text-zinc-400 italic">
                                        "{{ $kel->catatan }}"
                                    </flux:text>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </flux:card>

        <div class="flex">
            <flux:button :href="route('penamatan-akaun.index')" wire:navigate icon="arrow-left">
                Kembali
            </flux:button>
        </div>
    </div>
</x-layouts::app>
