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
            {{-- Tindakan Pentadbir --}}
            <flux:card class="space-y-4">
                <flux:heading size="lg">Tindakan Pentadbir</flux:heading>
                <flux:separator />

                @php $status = $this->permohonan->status; @endphp

                @if ($status === \App\Enums\StatusPermohonanToner::PendingStock)
                    {{-- Pending stock: only allow approve when stock arrives --}}
                    <div class="rounded-md bg-orange-50 p-3 dark:bg-orange-950/30">
                        <p class="text-sm text-orange-700 dark:text-orange-300">
                            Permohonan ini sedang menunggu stok tambahan.
                            Kuantiti diluluskan: <strong>{{ $this->permohonan->kuantiti_diluluskan }} unit</strong>.
                        </p>
                    </div>
                    <flux:modal.trigger name="confirm-luluskan-stok-tiba">
                        <flux:button variant="primary" icon="check" class="w-full justify-start">
                            Luluskan (Stok Tiba)
                        </flux:button>
                    </flux:modal.trigger>

                @elseif (in_array($status, [\App\Enums\StatusPermohonanToner::Ditolak, \App\Enums\StatusPermohonanToner::Dihantar, \App\Enums\StatusPermohonanToner::Diluluskan]))
                    <p class="text-sm text-zinc-500">Tiada tindakan tersedia untuk status ini.</p>

                @else
                    {{-- Submitted / Disemak: show approval fields + action buttons --}}
                    <flux:field>
                        <flux:label>
                            Kuantiti Diluluskan
                            <flux:badge size="sm" color="red" class="ml-1">Wajib</flux:badge>
                        </flux:label>
                        <flux:input
                            type="number"
                            wire:model="kuantitiDiluluskan"
                            min="1"
                            max="99"
                        />
                        <flux:error name="kuantitiDiluluskan" />
                        <flux:description>Kuantiti diminta: {{ $this->permohonan->kuantiti }} unit</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>Catatan Pentadbir (pilihan)</flux:label>
                        <flux:textarea
                            wire:model="catatanLuluskan"
                            placeholder="Catatan tambahan untuk pemohon..."
                            rows="3"
                        />
                        <flux:error name="catatanLuluskan" />
                    </flux:field>

                    <div class="flex flex-col gap-2">
                        <flux:modal.trigger name="confirm-luluskan">
                            <flux:button variant="primary" icon="check" class="w-full justify-start">
                                Luluskan Permohonan
                            </flux:button>
                        </flux:modal.trigger>

                        <flux:modal.trigger name="confirm-pending-stock">
                            <flux:button variant="outline" icon="clock" class="w-full justify-start">
                                Luluskan — Stok Terhad
                            </flux:button>
                        </flux:modal.trigger>

                        <flux:modal.trigger name="confirm-tolak">
                            <flux:button variant="danger" icon="x-mark" class="w-full justify-start">
                                Tolak Permohonan
                            </flux:button>
                        </flux:modal.trigger>
                    </div>
                @endif
            </flux:card>
        </div>
    </div>

    {{-- Confirmation Modals --}}

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
                <flux:subheading>
                    Kuantiti diluluskan: <strong>{{ $kuantitiDiluluskan }} unit</strong>.
                    Notifikasi akan dihantar kepada pemohon.
                </flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="luluskan" @click="loading = true">
                    Luluskan
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal: Luluskan — Stok Terhad (pending_stock) --}}
    <flux:modal
        name="confirm-pending-stock"
        class="min-w-[22rem]"
        :closable="false"
        x-data="{ loading: false }"
        x-on:cancel="loading && $event.preventDefault()"
        x-on:livewire:commit.window="loading = false"
    >
        <div class="relative space-y-6">
            <div
                wire:loading
                wire:target="tandaPendingStock"
                class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/80 dark:bg-zinc-900/80"
            >
                <flux:icon name="arrow-path" class="size-6 animate-spin text-zinc-500" />
            </div>
            <div>
                <flux:heading size="lg">Luluskan dengan stok terhad?</flux:heading>
                <flux:subheading>
                    Permohonan akan ditandakan <strong>Menunggu Stok</strong> dengan kuantiti
                    <strong>{{ $kuantitiDiluluskan }} unit</strong>.
                    Pemohon akan dimaklumkan apabila stok penuh tiba.
                </flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="outline" wire:click="tandaPendingStock" @click="loading = true">
                    Sahkan
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal: Luluskan dari Pending Stock --}}
    <flux:modal
        name="confirm-luluskan-stok-tiba"
        class="min-w-[22rem]"
        :closable="false"
        x-data="{ loading: false }"
        x-on:cancel="loading && $event.preventDefault()"
        x-on:livewire:commit.window="loading = false"
    >
        <div class="relative space-y-6">
            <div
                wire:loading
                wire:target="luluskanDariPendingStock"
                class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/80 dark:bg-zinc-900/80"
            >
                <flux:icon name="arrow-path" class="size-6 animate-spin text-zinc-500" />
            </div>
            <div>
                <flux:heading size="lg">Sahkan penerimaan stok?</flux:heading>
                <flux:subheading>
                    Stok telah tiba. Permohonan ini akan diluluskan dan notifikasi dihantar kepada pemohon.
                </flux:subheading>
            </div>
            <flux:field>
                <flux:label>Catatan (pilihan)</flux:label>
                <flux:textarea
                    wire:model="catatanLuluskan"
                    placeholder="Catatan tambahan untuk pemohon..."
                    rows="3"
                />
            </flux:field>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="luluskanDariPendingStock" @click="loading = true">
                    Luluskan
                </flux:button>
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
                <flux:label>
                    Sebab Penolakan
                    <flux:badge size="sm" color="red" class="ml-1">Wajib</flux:badge>
                </flux:label>
                <flux:textarea
                    wire:model="sebabPenolakan"
                    placeholder="Nyatakan sebab penolakan (min. 10 aksara)..."
                    rows="3"
                    required
                />
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
</div>
