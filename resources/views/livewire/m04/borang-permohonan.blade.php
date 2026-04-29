<div>
    @if ($langkah === 1)
        <flux:card class="max-w-2xl">
            <flux:heading size="lg">Permohonan Kemaskini Portal</flux:heading>
            <flux:subheading>Langkah 1 daripada 2 — Maklumat Permohonan</flux:subheading>

            <div class="mt-4 space-y-4">
                <flux:field>
                    <flux:label>URL Halaman <flux:badge size="sm" color="red">Wajib</flux:badge></flux:label>
                    <flux:input wire:model="url_halaman" type="url" placeholder="https://portal.motac.gov.my/..." />
                    <flux:error name="url_halaman" />
                </flux:field>

                <flux:field>
                    <flux:label>Jenis Perubahan <flux:badge size="sm" color="red">Wajib</flux:badge></flux:label>
                    <flux:select wire:model="jenis_perubahan" placeholder="Pilih jenis perubahan...">
                        <flux:select.option value="kandungan">Kandungan</flux:select.option>
                        <flux:select.option value="konfigurasi">Konfigurasi</flux:select.option>
                        <flux:select.option value="lain_lain">Lain-lain</flux:select.option>
                    </flux:select>
                    <flux:error name="jenis_perubahan" />
                </flux:field>

                <flux:field>
                    <flux:label>Butiran Kemaskini <flux:badge size="sm" color="red">Wajib</flux:badge></flux:label>
                    <flux:textarea wire:model="butiran_kemaskini" rows="5" placeholder="Huraikan perubahan yang diperlukan (min. 10 aksara)..." />
                    <flux:error name="butiran_kemaskini" />
                </flux:field>

                <flux:field>
                    <flux:label>Lampiran <flux:badge size="sm" color="zinc">Pilihan</flux:badge></flux:label>
                    <flux:description>PDF, JPG atau PNG sahaja. Saiz maksimum 5MB setiap fail.</flux:description>
                    <input
                        type="file"
                        wire:model="lampiran"
                        multiple
                        accept=".pdf,.jpg,.png"
                        class="block w-full text-sm text-zinc-500 file:mr-4 file:rounded-md file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-medium hover:file:bg-zinc-200 dark:file:bg-zinc-800 dark:file:text-zinc-300"
                    />
                    <flux:error name="lampiran.*" />
                    <div wire:loading wire:target="lampiran" class="text-sm text-zinc-500">
                        Memuat naik lampiran...
                    </div>
                </flux:field>
            </div>

            <div class="mt-6 flex justify-end">
                <flux:button variant="primary" wire:click="seterusnya" wire:loading.attr="disabled">
                    Seterusnya →
                </flux:button>
            </div>
        </flux:card>

    @elseif ($langkah === 2)
        <flux:card class="max-w-2xl">
            <flux:heading size="lg">Semak Permohonan</flux:heading>
            <flux:subheading>Langkah 2 daripada 2 — Semak dan Sahkan</flux:subheading>

            <dl class="mt-4 divide-y divide-zinc-100 dark:divide-zinc-800">
                <div class="grid grid-cols-3 gap-4 py-3">
                    <dt class="text-sm font-medium text-zinc-500">URL Halaman</dt>
                    <dd class="col-span-2 break-all text-sm text-zinc-900 dark:text-zinc-100">{{ $url_halaman }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-4 py-3">
                    <dt class="text-sm font-medium text-zinc-500">Jenis Perubahan</dt>
                    <dd class="col-span-2 text-sm text-zinc-900 dark:text-zinc-100">
                        {{ match ($jenis_perubahan) {
                            'kandungan' => 'Kandungan',
                            'konfigurasi' => 'Konfigurasi',
                            'lain_lain' => 'Lain-lain',
                            default => $jenis_perubahan,
                        } }}
                    </dd>
                </div>
                <div class="grid grid-cols-3 gap-4 py-3">
                    <dt class="text-sm font-medium text-zinc-500">Butiran Kemaskini</dt>
                    <dd class="col-span-2 text-sm whitespace-pre-wrap text-zinc-900 dark:text-zinc-100">{{ $butiran_kemaskini }}</dd>
                </div>
                @if (count($lampiran) > 0)
                    <div class="grid grid-cols-3 gap-4 py-3">
                        <dt class="text-sm font-medium text-zinc-500">Lampiran</dt>
                        <dd class="col-span-2 text-sm text-zinc-900 dark:text-zinc-100">
                            <ul class="space-y-1">
                                @foreach ($lampiran as $fail)
                                    <li class="flex items-center gap-1">
                                        <flux:icon name="paper-clip" class="size-4 text-zinc-400" />
                                        {{ $fail->getClientOriginalName() }}
                                    </li>
                                @endforeach
                            </ul>
                        </dd>
                    </div>
                @endif
            </dl>

            <div class="mt-6 flex justify-between">
                <flux:button variant="ghost" wire:click="kembali">← Kembali</flux:button>

                <flux:modal.trigger name="confirm-hantar">
                    <flux:button variant="primary">Hantar Permohonan</flux:button>
                </flux:modal.trigger>
            </div>
        </flux:card>

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
                    <flux:heading size="lg">Hantar permohonan?</flux:heading>
                    <flux:subheading>Permohonan akan dihantar kepada Pentadbir Unit Aplikasi untuk diproses.</flux:subheading>
                </div>
                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">Batal</flux:button>
                    </flux:modal.close>
                    <flux:button variant="primary" wire:click="hantar" @click="loading = true">
                        Ya, Hantar
                    </flux:button>
                </div>
            </div>
        </flux:modal>

    @elseif ($langkah === 3)
        <div class="max-w-2xl">
            <flux:card class="space-y-4 py-12 text-center">
                <div class="flex justify-center">
                    <div class="rounded-full bg-green-100 p-4">
                        <flux:icon name="check-circle" class="size-10 text-green-600" />
                    </div>
                </div>
                <flux:heading size="xl">Permohonan Berjaya Dihantar!</flux:heading>
                <flux:text>
                    No. Tiket: <span class="font-mono font-semibold text-zinc-900 dark:text-zinc-100">{{ $noTiket }}</span>
                </flux:text>
                <flux:text class="text-zinc-500">
                    Permohonan anda telah dihantar kepada Pentadbir Unit Aplikasi Teras dan Multimedia.
                    Anda akan dimaklumkan melalui emel apabila status berubah.
                </flux:text>
                <div class="flex justify-center gap-3 pt-4">
                    <flux:button :href="route('kemaskini-portal.index')" wire:navigate>
                        Lihat Senarai Permohonan
                    </flux:button>
                    <flux:button variant="primary" wire:click="$set('langkah', 1)">
                        Hantar Permohonan Baru
                    </flux:button>
                </div>
            </flux:card>
        </div>
    @endif
</div>
