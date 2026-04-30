<div>
    <flux:main container>
        <x-breadcrumbs :items="[
            ['label' => 'iBPM', 'url' => route('dashboard')],
            ['label' => 'Permohonan'],
            ['label' => 'Kemaskini Kumpulan Emel'],
        ]" />

        {{-- Step 1: Borang --}}
        @if ($step === 1)
            <div class="mx-auto max-w-2xl">
                <div class="mb-6">
                    <flux:heading size="xl">Permohonan Kemaskini Kumpulan Emel</flux:heading>
                    <flux:text class="mt-1">Isi borang di bawah untuk memohon tambah atau buang ahli kumpulan emel.</flux:text>
                </div>

                {{-- Maklumat Pemohon (read-only) --}}
                <div class="mb-6 rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900">
                    <flux:heading size="sm" class="mb-3">Maklumat Pemohon</flux:heading>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <flux:text size="sm" class="text-zinc-500">Nama</flux:text>
                            <flux:text class="font-medium">{{ auth()->user()->name }}</flux:text>
                        </div>
                        <div>
                            <flux:text size="sm" class="text-zinc-500">E-mel</flux:text>
                            <flux:text class="font-medium">{{ auth()->user()->email }}</flux:text>
                        </div>
                        @if (auth()->user()->bahagian)
                            <div>
                                <flux:text size="sm" class="text-zinc-500">Bahagian</flux:text>
                                <flux:text class="font-medium">{{ auth()->user()->bahagian }}</flux:text>
                            </div>
                        @endif
                        @if (auth()->user()->jawatan)
                            <div>
                                <flux:text size="sm" class="text-zinc-500">Jawatan</flux:text>
                                <flux:text class="font-medium">{{ auth()->user()->jawatan }}</flux:text>
                            </div>
                        @endif
                    </div>
                </div>

                <form wire:submit="teruskan" class="space-y-5">

                    {{-- Kumpulan Emel --}}
                    <flux:field>
                        <flux:label>Kumpulan Emel <flux:badge size="sm" color="red" class="ml-1">Wajib</flux:badge></flux:label>
                        <flux:select wire:model.live="kumpulanEmelId" placeholder="Pilih kumpulan emel...">
                            @foreach ($this->kumpulanEmels as $kumpulan)
                                <flux:select.option value="{{ $kumpulan->id }}">{{ $kumpulan->nama_kumpulan }} ({{ $kumpulan->alamat_emel }})</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="kumpulanEmelId" />
                    </flux:field>

                    {{-- Jenis Tindakan --}}
                    <flux:field>
                        <flux:label>Jenis Tindakan <flux:badge size="sm" color="red" class="ml-1">Wajib</flux:badge></flux:label>
                        <flux:select wire:model.live="jenisTindakan" placeholder="Pilih jenis tindakan...">
                            <flux:select.option value="tambah">Tambah Ahli</flux:select.option>
                            <flux:select.option value="buang">Buang Ahli</flux:select.option>
                        </flux:select>
                        <flux:error name="jenisTindakan" />
                    </flux:field>

                    {{-- Senarai Ahli --}}
                    <flux:field>
                        <flux:label>
                            Senarai Ahli
                            <flux:badge size="sm" color="red" class="ml-1">Wajib</flux:badge>
                        </flux:label>
                        <flux:description class="mb-3">Tambah semua ahli yang ingin ditambah atau dibuang daripada kumpulan.</flux:description>
                        <flux:error name="ahli" />

                        <div class="space-y-3">
                            @foreach ($ahli as $i => $row)
                                <div wire:key="ahli-{{ $i }}" class="flex items-start gap-3 rounded-lg border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-900">
                                    <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row">
                                        <div class="flex-1">
                                            <flux:input
                                                wire:model="ahli.{{ $i }}.nama_ahli"
                                                placeholder="Nama ahli"
                                                size="sm"
                                            />
                                            <flux:error name="ahli.{{ $i }}.nama_ahli" />
                                        </div>
                                        <div class="flex-1">
                                            <flux:input
                                                wire:model="ahli.{{ $i }}.emel_ahli"
                                                placeholder="emel@example.com"
                                                type="email"
                                                size="sm"
                                            />
                                            <flux:error name="ahli.{{ $i }}.emel_ahli" />
                                        </div>
                                    </div>
                                    @if (count($ahli) > 1)
                                        <flux:button
                                            wire:click="buangAhli({{ $i }})"
                                            size="sm"
                                            variant="ghost"
                                            icon="x-mark"
                                            inset="top bottom"
                                            class="mt-0.5 shrink-0 text-zinc-400 hover:text-red-500"
                                        />
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            <flux:button wire:click="tambahAhli" size="sm" variant="outline" icon="plus">
                                Tambah Baris
                            </flux:button>
                        </div>
                    </flux:field>

                    {{-- Catatan Pemohon --}}
                    <flux:field>
                        <flux:label>Catatan <flux:badge size="sm" color="zinc" class="ml-1">Pilihan</flux:badge></flux:label>
                        <flux:textarea wire:model="catatanPemohon" placeholder="Catatan tambahan (jika ada)..." rows="3" maxlength="500" />
                        <flux:error name="catatanPemohon" />
                    </flux:field>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <flux:button type="submit" variant="primary" icon-trailing="arrow-right">
                            Seterusnya
                        </flux:button>
                    </div>

                </form>
            </div>
        @endif

        {{-- Step 2: Semakan --}}
        @if ($step === 2)
            <div class="mx-auto max-w-2xl">
                <div class="mb-6">
                    <flux:heading size="xl">Semak Maklumat Permohonan</flux:heading>
                    <flux:text class="mt-1">Sila semak maklumat permohonan anda sebelum menghantar.</flux:text>
                </div>

                <div class="space-y-4 rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">

                    {{-- Maklumat Pemohon --}}
                    <div>
                        <flux:heading size="sm" class="mb-3 uppercase tracking-wide text-zinc-500">Maklumat Pemohon</flux:heading>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <flux:text size="sm" class="text-zinc-500">Nama</flux:text>
                                <flux:text class="font-medium">{{ auth()->user()->name }}</flux:text>
                            </div>
                            <div>
                                <flux:text size="sm" class="text-zinc-500">E-mel</flux:text>
                                <flux:text class="font-medium">{{ auth()->user()->email }}</flux:text>
                            </div>
                            @if (auth()->user()->bahagian)
                                <div>
                                    <flux:text size="sm" class="text-zinc-500">Bahagian</flux:text>
                                    <flux:text class="font-medium">{{ auth()->user()->bahagian }}</flux:text>
                                </div>
                            @endif
                        </div>
                    </div>

                    <flux:separator />

                    {{-- Maklumat Permohonan --}}
                    <div>
                        <flux:heading size="sm" class="mb-3 uppercase tracking-wide text-zinc-500">Maklumat Permohonan</flux:heading>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <flux:text size="sm" class="text-zinc-500">Kumpulan Emel</flux:text>
                                <flux:text class="font-medium">{{ $this->selectedKumpulan?->nama_kumpulan }}</flux:text>
                                <flux:text size="sm" class="text-zinc-400">{{ $this->selectedKumpulan?->alamat_emel }}</flux:text>
                            </div>
                            <div>
                                <flux:text size="sm" class="text-zinc-500">Jenis Tindakan</flux:text>
                                <flux:text class="font-medium">{{ $this->jenisTindakanLabel }}</flux:text>
                            </div>
                            @if ($catatanPemohon)
                                <div class="sm:col-span-2">
                                    <flux:text size="sm" class="text-zinc-500">Catatan</flux:text>
                                    <flux:text class="whitespace-pre-wrap">{{ $catatanPemohon }}</flux:text>
                                </div>
                            @endif
                        </div>
                    </div>

                    <flux:separator />

                    {{-- Senarai Ahli --}}
                    <div>
                        <flux:heading size="sm" class="mb-3 uppercase tracking-wide text-zinc-500">Senarai Ahli ({{ count($ahli) }} orang)</flux:heading>
                        <div class="space-y-2">
                            @foreach ($ahli as $i => $row)
                                <div class="flex items-center gap-3 rounded-md border border-zinc-100 bg-zinc-50 px-4 py-2 dark:border-zinc-700 dark:bg-zinc-800">
                                    <span class="w-6 text-center text-sm tabular-nums text-zinc-400">{{ $i + 1 }}</span>
                                    <div class="flex-1">
                                        <flux:text class="font-medium">{{ $row['nama_ahli'] }}</flux:text>
                                        <flux:text size="sm" class="text-zinc-400">{{ $row['emel_ahli'] }}</flux:text>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="mt-6 flex items-center justify-between">
                    <flux:button wire:click="balik" variant="ghost" icon="arrow-left">
                        Kembali
                    </flux:button>

                    <flux:modal.trigger name="confirm-hantar">
                        <flux:button variant="primary" icon-trailing="paper-airplane">
                            Hantar Permohonan
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>
        @endif

        {{-- Step 3: Berjaya --}}
        @if ($step === 3)
            <div class="mx-auto max-w-lg text-center">
                <div class="mb-6 flex justify-center">
                    <div class="flex size-20 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                        <flux:icon name="check-circle" class="size-10 text-green-600 dark:text-green-400" />
                    </div>
                </div>

                <flux:heading size="xl">Permohonan Berjaya Dihantar!</flux:heading>
                <flux:text class="mt-2">Permohonan anda telah berjaya dihantar dan akan disemak oleh Unit Infrastruktur & Keselamatan ICT.</flux:text>

                <div class="my-8 rounded-lg border border-zinc-200 bg-zinc-50 px-6 py-5 dark:border-zinc-700 dark:bg-zinc-900">
                    <flux:text size="sm" class="text-zinc-500">Nombor Tiket Anda</flux:text>
                    <p class="mt-1 text-3xl font-bold tracking-widest text-zinc-900 dark:text-white">{{ $noTiket }}</p>
                </div>

                <flux:text size="sm" class="text-zinc-500">Simpan nombor tiket di atas untuk memantau status permohonan anda.</flux:text>

                <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                    <flux:button :href="route('dashboard')" wire:navigate icon="home">
                        Kembali ke Dashboard
                    </flux:button>
                    <flux:button :href="route('kumpulan-emel.create')" wire:navigate variant="primary" icon="plus">
                        Permohonan Baharu
                    </flux:button>
                </div>
            </div>
        @endif

    </flux:main>

    {{-- Confirmation Modal --}}
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
                wire:target="hantar"
                class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/80 dark:bg-zinc-900/80"
            >
                <flux:icon name="arrow-path" class="size-6 animate-spin text-zinc-500" />
            </div>
            <div>
                <flux:heading size="lg">Sahkan Penghantaran?</flux:heading>
                <flux:subheading>Permohonan yang dihantar akan diproses oleh pentadbir. Pastikan semua maklumat adalah betul.</flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="hantar" @click="loading = true" icon-trailing="paper-airplane">
                    Hantar
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
