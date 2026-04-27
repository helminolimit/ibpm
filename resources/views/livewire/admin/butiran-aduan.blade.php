<div>
    <flux:main container>
        <x-breadcrumbs :items="[
            ['label' => 'iBPM', 'url' => route('dashboard')],
            ['label' => 'Aduan ICT', 'url' => route('admin.aduan.index')],
            ['label' => 'Butiran'],
        ]" />

        {{-- Back link --}}
        <div class="mb-4">
            <flux:button :href="route('admin.aduan.index')" wire:navigate variant="ghost" icon="arrow-left" size="sm">
                Kembali ke Senarai
            </flux:button>
        </div>

        {{-- Header --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <flux:heading size="xl" class="font-mono">{{ $this->aduan->no_tiket }}</flux:heading>
                    <flux:badge color="{{ $this->aduan->status->color() }}" size="lg">
                        {{ $this->aduan->status->label() }}
                    </flux:badge>
                </div>
                <flux:text class="mt-1 text-zinc-500">
                    Dihantar pada {{ $this->aduan->created_at->format('d/m/Y, H:i') }}
                </flux:text>
            </div>

            {{-- Header actions --}}
            <div>
                @if ($this->aduan->status === \App\Enums\StatusAduan::Selesai)
                    <flux:modal.trigger name="confirm-buka-semula">
                        <flux:button variant="outline" icon="arrow-path">Buka Semula</flux:button>
                    </flux:modal.trigger>
                @endif
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Maklumat Aduan + Sejarah Status (2/3 width) --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- Maklumat Aduan --}}
                <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <flux:heading size="sm" class="mb-4 uppercase tracking-wide text-zinc-500">Maklumat Aduan</flux:heading>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <flux:text size="sm" class="text-zinc-500">Kategori</flux:text>
                            <flux:text class="font-medium">{{ $this->aduan->kategori->nama }}</flux:text>
                        </div>
                        <div>
                            <flux:text size="sm" class="text-zinc-500">Unit BPM Penerima</flux:text>
                            <flux:text class="font-medium">{{ $this->aduan->kategori->unit_bpm }}</flux:text>
                        </div>
                        <div>
                            <flux:text size="sm" class="text-zinc-500">Lokasi / Bilik</flux:text>
                            <flux:text class="font-medium">{{ $this->aduan->lokasi }}</flux:text>
                        </div>
                        <div>
                            <flux:text size="sm" class="text-zinc-500">No. Telefon</flux:text>
                            <flux:text class="font-medium">{{ $this->aduan->no_telefon }}</flux:text>
                        </div>
                        <div class="sm:col-span-2">
                            <flux:text size="sm" class="text-zinc-500">Tajuk Aduan</flux:text>
                            <flux:text class="font-medium">{{ $this->aduan->tajuk }}</flux:text>
                        </div>
                        <div class="sm:col-span-2">
                            <flux:text size="sm" class="text-zinc-500">Keterangan Masalah</flux:text>
                            <flux:text class="whitespace-pre-wrap">{{ $this->aduan->keterangan }}</flux:text>
                        </div>
                    </div>
                </div>

                {{-- Sejarah Status --}}
                <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <flux:heading size="sm" class="mb-4 uppercase tracking-wide text-zinc-500">Sejarah Status</flux:heading>
                    <div class="space-y-0">
                        @forelse ($this->aduan->statusLogs as $log)
                            <div class="relative flex gap-4">
                                @if (! $loop->last)
                                    <div class="absolute bottom-0 left-4 top-8 w-px bg-zinc-200 dark:bg-zinc-700"></div>
                                @endif
                                <div class="relative flex size-8 shrink-0 items-center justify-center rounded-full border-2 border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                                    <div class="size-2.5 rounded-full
                                        @if ($log->status->color() === 'blue') bg-blue-500
                                        @elseif ($log->status->color() === 'yellow') bg-yellow-500
                                        @elseif ($log->status->color() === 'green') bg-green-500
                                        @elseif ($log->status->color() === 'red') bg-red-500
                                        @else bg-zinc-400
                                        @endif
                                    "></div>
                                </div>
                                <div class="min-w-0 flex-1 pb-6">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <flux:badge color="{{ $log->status->color() }}" size="sm">
                                            {{ $log->status->label() }}
                                        </flux:badge>
                                        <flux:text size="sm" class="text-zinc-400">
                                            {{ $log->created_at->format('d/m/Y, H:i') }}
                                        </flux:text>
                                    </div>
                                    @if ($log->catatan)
                                        <flux:text size="sm" class="mt-1 text-zinc-600 dark:text-zinc-400">
                                            {{ $log->catatan }}
                                        </flux:text>
                                    @endif
                                    @if ($log->user)
                                        <flux:text size="sm" class="mt-0.5 text-zinc-400">
                                            oleh {{ $log->user->name }}
                                        </flux:text>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <flux:text size="sm" class="text-zinc-400">Tiada sejarah status.</flux:text>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- Sidebar (1/3 width) --}}
            <div class="space-y-6">

                {{-- Kemaskini Status --}}
                @if (count($this->availableStatuses()) > 0)
                    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                        <flux:heading size="sm" class="mb-4 uppercase tracking-wide text-zinc-500">Kemaskini Status</flux:heading>
                        <div class="space-y-4">
                            <div>
                                <flux:select wire:model="statusBaru" label="Status Baru">
                                    <flux:select.option value="">— Pilih Status —</flux:select.option>
                                    @foreach ($this->availableStatuses() as $status)
                                        <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @error('statusBaru')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <flux:textarea
                                    wire:model="catatanTindakan"
                                    label="Catatan Tindakan"
                                    placeholder="Jelaskan tindakan yang diambil..."
                                    rows="4"
                                />
                                @error('catatanTindakan')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <flux:modal.trigger name="confirm-kemaskini">
                                <flux:button variant="primary" class="w-full">Simpan</flux:button>
                            </flux:modal.trigger>
                        </div>
                    </div>
                @endif

                {{-- Penugasan Teknician --}}
                @unless (auth()->user()->isTeknician())
                @if (in_array($this->aduan->status, [\App\Enums\StatusAduan::Baru, \App\Enums\StatusAduan::DalamProses]))
                    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                        <flux:heading size="sm" class="mb-4 uppercase tracking-wide text-zinc-500">Penugasan Teknician</flux:heading>
                        <div class="space-y-4">

                            @if ($this->aduan->pentadbir)
                                <div class="flex items-center gap-2 rounded-md bg-zinc-50 px-3 py-2 dark:bg-zinc-800">
                                    <flux:icon name="user-circle" class="size-4 shrink-0 text-zinc-400" />
                                    <flux:text size="sm">
                                        <span class="font-medium">{{ $this->aduan->pentadbir->name }}</span>
                                        <span class="ml-1 text-zinc-400">(semasa)</span>
                                    </flux:text>
                                </div>
                            @endif

                            @if ($this->availableTeknicians->isEmpty())
                                <flux:text size="sm" class="text-zinc-400">
                                    Tiada teknician tersedia dalam unit ini. Sila hubungi Superadmin.
                                </flux:text>
                            @else
                                <div>
                                    <flux:select wire:model="teknicianId" label="Pilih Teknician">
                                        <flux:select.option value="">— Pilih Teknician —</flux:select.option>
                                        @foreach ($this->availableTeknicians as $tek)
                                            <flux:select.option value="{{ $tek->id }}">{{ $tek->name }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    @error('teknicianId')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <flux:textarea
                                        wire:model="catatanArahan"
                                        label="Arahan Pentadbir (pilihan)"
                                        placeholder="Arahan atau maklumat tambahan untuk teknician..."
                                        rows="3"
                                    />
                                    @error('catatanArahan')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <flux:modal.trigger name="confirm-tugaskan">
                                    <flux:button variant="primary" icon="user-plus" class="w-full">
                                        {{ $this->aduan->pentadbir ? 'Tukar Teknician' : 'Tugaskan Teknician' }}
                                    </flux:button>
                                </flux:modal.trigger>
                            @endif
                        </div>
                    </div>
                @endif
                @endunless

                {{-- Maklumat Pemohon --}}
                <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <flux:heading size="sm" class="mb-4 uppercase tracking-wide text-zinc-500">Maklumat Pemohon</flux:heading>
                    <div class="space-y-3">
                        <div>
                            <flux:text size="sm" class="text-zinc-500">Nama</flux:text>
                            <flux:text class="font-medium">{{ $this->aduan->user?->name ?? '-' }}</flux:text>
                        </div>
                        <div>
                            <flux:text size="sm" class="text-zinc-500">E-mel</flux:text>
                            <flux:text class="font-medium">{{ $this->aduan->user?->email ?? '-' }}</flux:text>
                        </div>
                        @if ($this->aduan->user?->bahagian)
                            <div>
                                <flux:text size="sm" class="text-zinc-500">Bahagian</flux:text>
                                <flux:text class="font-medium">{{ $this->aduan->user->bahagian }}</flux:text>
                            </div>
                        @endif
                        @if ($this->aduan->user?->jawatan)
                            <div>
                                <flux:text size="sm" class="text-zinc-500">Jawatan</flux:text>
                                <flux:text class="font-medium">{{ $this->aduan->user->jawatan }}</flux:text>
                            </div>
                        @endif
                        @if ($this->aduan->user?->no_telefon)
                            <div>
                                <flux:text size="sm" class="text-zinc-500">No. Telefon</flux:text>
                                <flux:text class="font-medium">{{ $this->aduan->user->no_telefon }}</flux:text>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Lampiran --}}
                @if ($this->aduan->lampiran->isNotEmpty())
                    <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                        <flux:heading size="sm" class="mb-4 uppercase tracking-wide text-zinc-500">Lampiran</flux:heading>
                        <div class="space-y-2">
                            @foreach ($this->aduan->lampiran as $lampiran)
                                <a
                                    href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($lampiran->path) }}"
                                    target="_blank"
                                    class="flex items-center gap-3 rounded-md border border-zinc-200 p-3 text-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800"
                                >
                                    <flux:icon
                                        name="{{ str_starts_with($lampiran->jenis_fail, 'image/') ? 'photo' : 'document' }}"
                                        class="size-5 shrink-0 text-zinc-400"
                                    />
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate font-medium text-zinc-700 dark:text-zinc-300">{{ $lampiran->nama_fail }}</p>
                                        <p class="text-zinc-400">
                                            {{ $lampiran->saiz >= 1048576 ? number_format($lampiran->saiz / 1048576, 1).' MB' : number_format($lampiran->saiz / 1024, 1).' KB' }}
                                        </p>
                                    </div>
                                    <flux:icon name="arrow-down-tray" class="size-4 shrink-0 text-zinc-400" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </flux:main>

    {{-- Confirmation modal: Kemaskini Status --}}
    <flux:modal
        name="confirm-kemaskini"
        class="min-w-[22rem]"
        :closable="false"
        x-data="{ loading: false }"
        x-on:cancel="loading && $event.preventDefault()"
        x-on:livewire:commit.window="loading = false"
    >
        <div class="relative space-y-6">
            <div
                wire:loading
                wire:target="kemaskiniStatus"
                class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/80 dark:bg-zinc-900/80"
            >
                <flux:icon name="arrow-path" class="size-6 animate-spin text-zinc-500" />
            </div>
            <div>
                <flux:heading size="lg">Sahkan perubahan status?</flux:heading>
                <flux:subheading>Emel notifikasi akan dihantar kepada pemohon selepas status dikemaskini.</flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="kemaskiniStatus" @click="loading = true">Simpan</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Confirmation modal: Tugaskan Teknician --}}
    <flux:modal
        name="confirm-tugaskan"
        class="min-w-[22rem]"
        :closable="false"
        x-data="{ loading: false }"
        x-on:cancel="loading && $event.preventDefault()"
        x-on:livewire:commit.window="loading = false"
    >
        <div class="relative space-y-6">
            <div
                wire:loading
                wire:target="tugaskanTeknician"
                class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/80 dark:bg-zinc-900/80"
            >
                <flux:icon name="arrow-path" class="size-6 animate-spin text-zinc-500" />
            </div>
            <div>
                <flux:heading size="lg">Tugaskan teknician?</flux:heading>
                <flux:subheading>Teknician yang dipilih akan menerima emel notifikasi sebagai arahan kerja.</flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="tugaskanTeknician" @click="loading = true">Tugaskan</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Confirmation modal: Buka Semula --}}
    <flux:modal
        name="confirm-buka-semula"
        class="min-w-[22rem]"
        :closable="false"
        x-data="{ loading: false }"
        x-on:cancel="loading && $event.preventDefault()"
        x-on:livewire:commit.window="loading = false"
    >
        <div class="relative space-y-6">
            <div
                wire:loading
                wire:target="bukaSemulaAduan"
                class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/80 dark:bg-zinc-900/80"
            >
                <flux:icon name="arrow-path" class="size-6 animate-spin text-zinc-500" />
            </div>
            <div>
                <flux:heading size="lg">Buka semula aduan?</flux:heading>
                <flux:subheading>Status akan ditukar kembali kepada "Dalam Proses" dan pemohon akan dimaklumkan.</flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="bukaSemulaAduan" @click="loading = true">Buka Semula</flux:button>
            </div>
        </div>
    </flux:modal>

</div>
