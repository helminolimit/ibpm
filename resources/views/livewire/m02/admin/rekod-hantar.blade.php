<div class="mx-auto max-w-6xl space-y-6 px-4 py-6">
    {{-- Header --}}
    <div class="flex flex-wrap items-start gap-4">
        <flux:button :href="route('m02.admin.senarai')" wire:navigate variant="ghost" icon="arrow-left" size="sm">
            Kembali
        </flux:button>
        <div class="flex-1">
            <div class="flex flex-wrap items-center gap-3">
                <flux:heading size="xl">{{ $this->permohonan->no_tiket }}</flux:heading>
                <flux:badge color="{{ $this->permohonan->status->color() }}">
                    {{ $this->permohonan->status->label() }}
                </flux:badge>
            </div>
            <flux:subheading>
                Rekod penghantaran toner untuk permohonan ini
            </flux:subheading>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Main content (2/3) --}}
        <div class="space-y-6 lg:col-span-2">
            {{-- Maklumat Permohonan --}}
            <flux:card class="space-y-4">
                <flux:heading size="lg">Maklumat Permohonan</flux:heading>
                <flux:separator />

                <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No. Tiket</dt>
                        <dd class="mt-1 font-mono text-sm font-semibold">{{ $this->permohonan->no_tiket }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Status</dt>
                        <dd class="mt-1">
                            <flux:badge color="{{ $this->permohonan->status->color() }}" size="sm">
                                {{ $this->permohonan->status->label() }}
                            </flux:badge>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Model Pencetak</dt>
                        <dd class="mt-1 text-sm">{{ $this->permohonan->model_pencetak }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Jenama Toner</dt>
                        <dd class="mt-1 text-sm">{{ $this->permohonan->jenama_toner }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Jenis / Warna</dt>
                        <dd class="mt-1">
                            <flux:badge color="{{ $this->permohonan->jenis_toner->color() }}" size="sm">
                                {{ $this->permohonan->jenis_toner->label() }}
                            </flux:badge>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No. Siri Toner</dt>
                        <dd class="mt-1 text-sm">{{ $this->permohonan->no_siri_toner ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Kuantiti Diminta</dt>
                        <dd class="mt-1 text-sm">{{ $this->permohonan->kuantiti }} unit</dd>
                    </div>
                    @if ($this->permohonan->kuantiti_diluluskan)
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Kuantiti Diluluskan</dt>
                            <dd class="mt-1 text-sm font-semibold text-green-600 dark:text-green-400">
                                {{ $this->permohonan->kuantiti_diluluskan }} unit
                            </dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Tarikh Diperlukan</dt>
                        <dd class="mt-1 text-sm">
                            {{ $this->permohonan->tarikh_diperlukan?->format('d M Y') ?? '—' }}
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Lokasi Pencetak</dt>
                        <dd class="mt-1 text-sm">{{ $this->permohonan->lokasi_pencetak }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Tujuan Permohonan</dt>
                        <dd class="mt-1 text-sm leading-relaxed">{{ $this->permohonan->tujuan }}</dd>
                    </div>
                </dl>
            </flux:card>

            {{-- Maklumat Pemohon --}}
            <flux:card class="space-y-4">
                <flux:heading size="lg">Maklumat Pemohon</flux:heading>
                <flux:separator />

                <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Nama</dt>
                        <dd class="mt-1 text-sm">{{ $this->permohonan->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Jawatan</dt>
                        <dd class="mt-1 text-sm">{{ $this->permohonan->user->jawatan ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Bahagian / Unit</dt>
                        <dd class="mt-1 text-sm">{{ $this->permohonan->user->bahagian ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No. Telefon</dt>
                        <dd class="mt-1 text-sm">{{ $this->permohonan->user->no_telefon ?? '—' }}</dd>
                    </div>
                </dl>
            </flux:card>

            {{-- Sejarah Log --}}
            <flux:card class="space-y-4">
                <flux:heading size="lg">Sejarah Log</flux:heading>
                <flux:separator />

                @if ($this->permohonan->logs->isEmpty())
                    <p class="text-sm text-zinc-500">Tiada sejarah log.</p>
                @else
                    <ol class="relative space-y-5 border-s border-zinc-200 ps-5 dark:border-zinc-700">
                        @foreach ($this->permohonan->logs as $log)
                            <li wire:key="log-{{ $log->id }}">
                                <div class="absolute -start-1.5 mt-1 size-3 rounded-full border border-white bg-zinc-300 dark:border-zinc-900 dark:bg-zinc-600"></div>
                                <p class="text-xs text-zinc-400">
                                    {{ $log->created_at->format('d M Y, H:i') }}
                                </p>
                                <p class="mt-0.5 text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                    {{ $log->tindakan }}
                                </p>
                                @if ($log->catatan)
                                    <p class="mt-0.5 text-xs text-zinc-500">{{ $log->catatan }}</p>
                                @endif
                                @if ($log->user)
                                    <p class="mt-0.5 text-xs text-zinc-400">
                                        oleh {{ $log->user->name }}
                                    </p>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                @endif
            </flux:card>
        </div>

        {{-- Sidebar (1/3) --}}
        <div class="space-y-6">
            <flux:card class="space-y-4">
                <flux:heading size="lg">Rekod Penghantaran</flux:heading>
                <flux:separator />

                <flux:field>
                    <flux:label>
                        Kuantiti Dihantar
                        <flux:badge size="sm" color="red" class="ml-1">Wajib</flux:badge>
                    </flux:label>
                    <flux:input
                        type="number"
                        wire:model="kuantitiDihantar"
                        min="1"
                    />
                    <flux:error name="kuantitiDihantar" />
                    <flux:description>
                        Kuantiti diluluskan: {{ $this->permohonan->kuantiti_diluluskan ?? $this->permohonan->kuantiti }} unit
                    </flux:description>
                </flux:field>

                <flux:field x-data="{ count: $wire.entangle('catatan') }">
                    <flux:label>Catatan (pilihan)</flux:label>
                    <flux:textarea
                        wire:model="catatan"
                        placeholder="Catatan pentadbir..."
                        rows="3"
                        maxlength="300"
                        x-on:input="count = $el.value"
                    />
                    <flux:error name="catatan" />
                    <flux:description class="text-right">
                        <span x-text="(count || '').length"></span>/300
                    </flux:description>
                </flux:field>

                <flux:modal.trigger name="confirm-hantar">
                    <flux:button variant="primary" icon="truck" class="w-full justify-start">
                        Simpan Rekod Penghantaran
                    </flux:button>
                </flux:modal.trigger>
            </flux:card>
        </div>
    </div>

    {{-- Modal: Simpan Rekod Penghantaran --}}
    <flux:modal
        name="confirm-hantar"
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
                <flux:heading size="lg">Sahkan rekod penghantaran?</flux:heading>
                <flux:subheading>
                    No. Tiket: <strong>{{ $this->permohonan->no_tiket }}</strong><br>
                    Kuantiti dihantar: <strong>{{ $kuantitiDihantar }} unit</strong><br>
                    Status akan dikemaskini kepada <strong>Toner Dihantar</strong> dan notifikasi dihantar kepada pemohon.
                </flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="simpan" @click="loading = true">
                    Simpan
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
