<div>
    <flux:main container>

        {{-- Step 1: Borang Aduan --}}
        @if ($step === 1)
            <div class="mx-auto max-w-2xl">
                <div class="mb-6">
                    <flux:heading size="xl">Hantar Aduan ICT</flux:heading>
                    <flux:text class="mt-1">Isi borang di bawah untuk menghantar aduan ICT kepada unit BPM berkenaan.</flux:text>
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

                    {{-- Kategori Aduan --}}
                    <flux:field>
                        <flux:label>Kategori Aduan <flux:badge size="sm" color="red" class="ml-1">Wajib</flux:badge></flux:label>
                        <flux:select wire:model.live="kategoriId" placeholder="Pilih kategori aduan...">
                            @foreach ($this->kategoris as $kategori)
                                <flux:select.option value="{{ $kategori->id }}">{{ $kategori->nama }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="kategoriId" />
                    </flux:field>

                    {{-- Unit BPM Penerima --}}
                    @if ($this->selectedKategori)
                        <div class="flex items-center gap-2 rounded-md border border-blue-200 bg-blue-50 px-4 py-3 dark:border-blue-800 dark:bg-blue-950">
                            <flux:icon name="building-office" class="size-4 shrink-0 text-blue-600 dark:text-blue-400" />
                            <flux:text size="sm" class="text-blue-700 dark:text-blue-300">
                                Unit BPM Penerima: <span class="font-semibold">{{ $this->selectedKategori->unit_bpm }}</span>
                            </flux:text>
                        </div>
                    @endif

                    {{-- Lokasi --}}
                    <flux:field>
                        <flux:label>Lokasi / Bilik <flux:badge size="sm" color="red" class="ml-1">Wajib</flux:badge></flux:label>
                        <flux:input wire:model="lokasi" placeholder="Contoh: Bilik 302, Aras 3" />
                        <flux:error name="lokasi" />
                    </flux:field>

                    {{-- Tajuk --}}
                    <flux:field>
                        <flux:label>Tajuk Aduan <flux:badge size="sm" color="red" class="ml-1">Wajib</flux:badge></flux:label>
                        <flux:input wire:model="tajuk" placeholder="Ringkasan masalah yang dihadapi" maxlength="255" />
                        <flux:error name="tajuk" />
                    </flux:field>

                    {{-- Keterangan --}}
                    <flux:field>
                        <flux:label>Keterangan Masalah <flux:badge size="sm" color="red" class="ml-1">Wajib</flux:badge></flux:label>
                        <flux:textarea wire:model="keterangan" placeholder="Huraikan masalah secara terperinci..." rows="5" />
                        <flux:error name="keterangan" />
                    </flux:field>

                    {{-- No. Telefon --}}
                    <flux:field>
                        <flux:label>No. Telefon <flux:badge size="sm" color="red" class="ml-1">Wajib</flux:badge></flux:label>
                        <flux:input wire:model="noTelefon" placeholder="Contoh: 03-88891234" type="tel" />
                        <flux:description>Untuk dihubungi oleh pegawai BPM jika perlu.</flux:description>
                        <flux:error name="noTelefon" />
                    </flux:field>

                    {{-- Lampiran --}}
                    <flux:field>
                        <flux:label>Lampiran</flux:label>
                        <flux:input wire:model="lampiran" type="file" accept=".jpg,.jpeg,.png,.pdf" />
                        <flux:description>JPG, PNG, PDF sahaja. Had saiz: 5MB.</flux:description>
                        <flux:error name="lampiran" />
                        @if ($lampiran)
                            <flux:text size="sm" class="mt-1 text-green-600 dark:text-green-400">
                                <flux:icon name="check-circle" class="inline size-4" />
                                {{ $lampiran->getClientOriginalName() }}
                            </flux:text>
                        @endif
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
                    <flux:heading size="xl">Semak Maklumat Aduan</flux:heading>
                    <flux:text class="mt-1">Sila semak maklumat aduan anda sebelum menghantar.</flux:text>
                </div>

                <div class="space-y-4 rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">

                    {{-- Maklumat Pemohon --}}
                    <div>
                        <flux:heading size="sm" class="mb-3 text-zinc-500 uppercase tracking-wide">Maklumat Pemohon</flux:heading>
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

                    <flux:separator />

                    {{-- Maklumat Aduan --}}
                    <div>
                        <flux:heading size="sm" class="mb-3 text-zinc-500 uppercase tracking-wide">Maklumat Aduan</flux:heading>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <flux:text size="sm" class="text-zinc-500">Kategori</flux:text>
                                <flux:text class="font-medium">{{ $this->selectedKategori?->nama }}</flux:text>
                            </div>
                            <div>
                                <flux:text size="sm" class="text-zinc-500">Unit BPM Penerima</flux:text>
                                <flux:text class="font-medium">{{ $this->selectedKategori?->unit_bpm }}</flux:text>
                            </div>
                            <div>
                                <flux:text size="sm" class="text-zinc-500">Lokasi / Bilik</flux:text>
                                <flux:text class="font-medium">{{ $lokasi }}</flux:text>
                            </div>
                            <div>
                                <flux:text size="sm" class="text-zinc-500">No. Telefon</flux:text>
                                <flux:text class="font-medium">{{ $noTelefon }}</flux:text>
                            </div>
                            <div class="sm:col-span-2">
                                <flux:text size="sm" class="text-zinc-500">Tajuk Aduan</flux:text>
                                <flux:text class="font-medium">{{ $tajuk }}</flux:text>
                            </div>
                            <div class="sm:col-span-2">
                                <flux:text size="sm" class="text-zinc-500">Keterangan Masalah</flux:text>
                                <flux:text class="whitespace-pre-wrap">{{ $keterangan }}</flux:text>
                            </div>
                            @if ($lampiran)
                                <div class="sm:col-span-2">
                                    <flux:text size="sm" class="text-zinc-500">Lampiran</flux:text>
                                    <flux:text class="font-medium">{{ $lampiran->getClientOriginalName() }}</flux:text>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                <div class="mt-6 flex items-center justify-between">
                    <flux:button wire:click="balik" variant="ghost" icon="arrow-left">
                        Kembali
                    </flux:button>

                    {{-- Confirmation modal trigger --}}
                    <flux:modal.trigger name="confirm-hantar">
                        <flux:button variant="primary" icon-trailing="paper-airplane">
                            Hantar Aduan
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

                <flux:heading size="xl">Aduan Berjaya Dihantar!</flux:heading>
                <flux:text class="mt-2">Aduan ICT anda telah berjaya dihantar dan sedang diproses oleh unit BPM berkenaan.</flux:text>

                <div class="my-8 rounded-lg border border-zinc-200 bg-zinc-50 px-6 py-5 dark:border-zinc-700 dark:bg-zinc-900">
                    <flux:text size="sm" class="text-zinc-500">Nombor Tiket Anda</flux:text>
                    <p class="mt-1 text-3xl font-bold tracking-widest text-zinc-900 dark:text-white">{{ $noTiket }}</p>
                </div>

                <flux:text size="sm" class="text-zinc-500">Emel pengesahan telah dihantar ke <span class="font-medium">{{ auth()->user()->email }}</span>. Simpan nombor tiket di atas untuk memantau status aduan anda.</flux:text>

                <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                    <flux:button :href="route('dashboard')" wire:navigate icon="home">
                        Kembali ke Dashboard
                    </flux:button>
                    <flux:button :href="route('aduan-ict.create')" wire:navigate variant="primary" icon="plus">
                        Hantar Aduan Baharu
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
                <flux:heading size="lg">Sahkan Penghantaran Aduan?</flux:heading>
                <flux:subheading>Aduan yang telah dihantar tidak boleh dipadam. Pastikan semua maklumat adalah betul.</flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="hantar" @click="loading = true" icon-trailing="paper-airplane">
                    Hantar Aduan
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
