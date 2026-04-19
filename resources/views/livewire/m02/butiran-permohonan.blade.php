<div class="mx-auto max-w-6xl space-y-6 px-4 py-6">
    {{-- Header --}}
    <div class="flex flex-wrap items-start gap-4">
        <flux:button :href="route('m02.senarai')" wire:navigate variant="ghost" icon="arrow-left" size="sm">
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
                Permohonan dibuat pada {{ $this->permohonan->created_at->format('d M Y, H:i') }}
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
        </div>

        {{-- Sidebar (1/3) --}}
        <div class="space-y-6">
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

            {{-- Admin Actions --}}
            @if ($isAdmin)
                <flux:card class="space-y-4">
                    <flux:heading size="lg">Tindakan Pentadbir</flux:heading>
                    <flux:separator />

                    @php $status = $this->permohonan->status; @endphp

                    @if (in_array($status, [\App\Enums\StatusPermohonanToner::Ditolak, \App\Enums\StatusPermohonanToner::Dihantar]))
                        <p class="text-sm text-zinc-500">Tiada tindakan tersedia.</p>
                    @else
                        <div class="flex flex-col gap-2">
                            {{-- Semak --}}
                            @if ($status === \App\Enums\StatusPermohonanToner::Submitted)
                                <flux:modal.trigger name="confirm-semak">
                                    <flux:button variant="outline" icon="magnifying-glass" class="w-full justify-start">
                                        Tandakan Dalam Semakan
                                    </flux:button>
                                </flux:modal.trigger>
                            @endif

                            {{-- Luluskan --}}
                            @if (in_array($status, [\App\Enums\StatusPermohonanToner::Submitted, \App\Enums\StatusPermohonanToner::Disemak]))
                                <flux:modal.trigger name="confirm-luluskan">
                                    <flux:button variant="primary" icon="check" class="w-full justify-start">
                                        Luluskan Permohonan
                                    </flux:button>
                                </flux:modal.trigger>
                            @endif

                            {{-- Tolak --}}
                            @if (in_array($status, [\App\Enums\StatusPermohonanToner::Submitted, \App\Enums\StatusPermohonanToner::Disemak]))
                                <flux:modal.trigger name="confirm-tolak">
                                    <flux:button variant="danger" icon="x-mark" class="w-full justify-start">
                                        Tolak Permohonan
                                    </flux:button>
                                </flux:modal.trigger>
                            @endif

                            {{-- Hantar Toner --}}
                            @if ($status === \App\Enums\StatusPermohonanToner::Diluluskan)
                                <flux:modal.trigger name="confirm-hantar">
                                    <flux:button variant="primary" icon="truck" class="w-full justify-start">
                                        Hantar Toner
                                    </flux:button>
                                </flux:modal.trigger>
                            @endif
                        </div>
                    @endif
                </flux:card>
            @endif
        </div>
    </div>

    {{-- Admin Confirmation Modals --}}
    @if ($isAdmin)
        {{-- Modal: Semak --}}
        <flux:modal
            name="confirm-semak"
            class="min-w-[22rem]"
            :closable="false"
            x-data="{ loading: false }"
            x-on:cancel="loading && $event.preventDefault()"
            x-on:livewire:commit.window="loading = false"
        >
            <div class="relative space-y-6">
                <div
                    wire:loading
                    wire:target="semak"
                    class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/80 dark:bg-zinc-900/80"
                >
                    <flux:icon name="arrow-path" class="size-6 animate-spin text-zinc-500" />
                </div>
                <div>
                    <flux:heading size="lg">Tandakan dalam semakan?</flux:heading>
                    <flux:subheading>Status akan ditukar kepada Dalam Semakan.</flux:subheading>
                </div>
                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">Batal</flux:button>
                    </flux:modal.close>
                    <flux:button variant="primary" wire:click="semak" @click="loading = true">Sahkan</flux:button>
                </div>
            </div>
        </flux:modal>

        {{-- Modal: Luluskan --}}
        <flux:modal
            name="confirm-luluskan"
            class="min-w-[22rem]"
            :closable="false"
            x-data="{ loading: false }"
            x-on:cancel="loading && $event.preventDefault()"
            x-on:livewire:commit.window="loading = false"
        >
            <div class="relative space-y-6">
                <div
                    wire:loading
                    wire:target="luluskan"
                    class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/80 dark:bg-zinc-900/80"
                >
                    <flux:icon name="arrow-path" class="size-6 animate-spin text-zinc-500" />
                </div>
                <div>
                    <flux:heading size="lg">Luluskan permohonan?</flux:heading>
                    <flux:subheading>Notifikasi akan dihantar kepada pemohon.</flux:subheading>
                </div>
                <flux:field>
                    <flux:label>Catatan (pilihan)</flux:label>
                    <flux:textarea wire:model="catatanLuluskan" placeholder="Catatan tambahan untuk pemohon..." rows="3" />
                </flux:field>
                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">Batal</flux:button>
                    </flux:modal.close>
                    <flux:button variant="primary" wire:click="luluskan" @click="loading = true">Luluskan</flux:button>
                </div>
            </div>
        </flux:modal>

        {{-- Modal: Tolak --}}
        <flux:modal
            name="confirm-tolak"
            class="min-w-[22rem]"
            :closable="false"
            x-data="{ loading: false }"
            x-on:cancel="loading && $event.preventDefault()"
            x-on:livewire:commit.window="loading = false"
        >
            <div class="relative space-y-6">
                <div
                    wire:loading
                    wire:target="tolak"
                    class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/80 dark:bg-zinc-900/80"
                >
                    <flux:icon name="arrow-path" class="size-6 animate-spin text-zinc-500" />
                </div>
                <div>
                    <flux:heading size="lg">Tolak permohonan?</flux:heading>
                    <flux:subheading>Tindakan ini tidak boleh dibatalkan. Notifikasi akan dihantar kepada pemohon.</flux:subheading>
                </div>
                <flux:field>
                    <flux:label>Sebab Penolakan <flux:badge size="sm" color="red">Wajib</flux:badge></flux:label>
                    <flux:textarea wire:model="sebabPenolakan" placeholder="Nyatakan sebab penolakan..." rows="3" required />
                    <flux:error name="sebabPenolakan" />
                </flux:field>
                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">Batal</flux:button>
                    </flux:modal.close>
                    <flux:button variant="danger" wire:click="tolak" @click="loading = true">Tolak</flux:button>
                </div>
            </div>
        </flux:modal>

        {{-- Modal: Hantar Toner --}}
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
                    wire:target="hantarToner"
                    class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/80 dark:bg-zinc-900/80"
                >
                    <flux:icon name="arrow-path" class="size-6 animate-spin text-zinc-500" />
                </div>
                <div>
                    <flux:heading size="lg">Sahkan penghantaran toner?</flux:heading>
                    <flux:subheading>
                        Toner untuk <span class="font-semibold">{{ $this->permohonan->no_tiket }}</span>
                        ({{ $this->permohonan->kuantiti }} unit) akan ditandakan sebagai dihantar.
                        Notifikasi akan dihantar kepada pemohon.
                    </flux:subheading>
                </div>
                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">Batal</flux:button>
                    </flux:modal.close>
                    <flux:button variant="primary" wire:click="hantarToner" @click="loading = true">Sahkan Penghantaran</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
