<div>
    @if ($langkah === 1)
        <flux:card class="max-w-2xl">
            <flux:heading size="lg">Permohonan Penamatan Akaun Login</flux:heading>
            <flux:subheading>Langkah 1 daripada 2 — Maklumat Permohonan</flux:subheading>

            <div class="mt-4 space-y-4">
                <flux:field>
                    <flux:label>Pengguna Sasaran <flux:badge size="sm" color="red">Wajib</flux:badge></flux:label>
                    <flux:select wire:model="pengguna_sasaran_id" placeholder="Pilih pengguna...">
                        @foreach ($senaraiPengguna as $id => $nama)
                            <flux:select.option value="{{ $id }}">{{ $nama }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="pengguna_sasaran_id" />
                </flux:field>

                <flux:field>
                    <flux:label>ID Login Komputer <flux:badge size="sm" color="red">Wajib</flux:badge></flux:label>
                    <flux:input wire:model="id_login_komputer" placeholder="Contoh: ahmad.hassan" />
                    <flux:error name="id_login_komputer" />
                </flux:field>

                <flux:field>
                    <flux:label>Jenis Tindakan <flux:badge size="sm" color="red">Wajib</flux:badge></flux:label>
                    <flux:select wire:model="jenis_tindakan">
                        <flux:select.option value="TAMAT">Tamat Akaun</flux:select.option>
                        <flux:select.option value="GANTUNG">Gantung Akaun</flux:select.option>
                    </flux:select>
                    <flux:error name="jenis_tindakan" />
                </flux:field>

                <flux:field>
                    <flux:label>Tarikh Berkuat Kuasa <flux:badge size="sm" color="red">Wajib</flux:badge></flux:label>
                    <flux:input type="date" wire:model="tarikh_berkuat_kuasa" min="{{ date('Y-m-d') }}" />
                    <flux:error name="tarikh_berkuat_kuasa" />
                </flux:field>

                <flux:field>
                    <flux:label>Sebab Penamatan <flux:badge size="sm" color="red">Wajib</flux:badge></flux:label>
                    <flux:textarea wire:model="sebab_penamatan" rows="4" placeholder="Nyatakan sebab penamatan akaun (min. 10 aksara)..." />
                    <flux:error name="sebab_penamatan" />
                </flux:field>
            </div>

            <div class="mt-6 flex justify-end">
                <flux:button variant="primary" wire:click="seterusnya">
                    Seterusnya →
                </flux:button>
            </div>
        </flux:card>

    @elseif ($langkah === 2)
        <flux:card class="max-w-2xl">
            <flux:heading size="lg">Semak Permohonan</flux:heading>
            <flux:subheading>Langkah 2 daripada 2 — Semak dan Sahkan</flux:subheading>

            <dl class="mt-4 divide-y divide-zinc-100 dark:divide-zinc-800">
                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-zinc-500">Pengguna Sasaran</dt>
                    <dd class="col-span-2 text-sm text-zinc-900 dark:text-zinc-100">
                        {{ $senaraiPengguna[$pengguna_sasaran_id] ?? '-' }}
                    </dd>
                </div>
                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-zinc-500">ID Login</dt>
                    <dd class="col-span-2 text-sm font-mono text-zinc-900 dark:text-zinc-100">{{ $id_login_komputer }}</dd>
                </div>
                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-zinc-500">Jenis Tindakan</dt>
                    <dd class="col-span-2 text-sm text-zinc-900 dark:text-zinc-100">{{ $jenis_tindakan === 'TAMAT' ? 'Tamat Akaun' : 'Gantung Akaun' }}</dd>
                </div>
                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-zinc-500">Tarikh Berkuat Kuasa</dt>
                    <dd class="col-span-2 text-sm text-zinc-900 dark:text-zinc-100">
                        {{ \Carbon\Carbon::parse($tarikh_berkuat_kuasa)->format('d/m/Y') }}
                    </dd>
                </div>
                <div class="py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm font-medium text-zinc-500">Sebab Penamatan</dt>
                    <dd class="col-span-2 text-sm text-zinc-900 dark:text-zinc-100">{{ $sebab_penamatan }}</dd>
                </div>
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
                    <flux:heading size="lg">Sahkan permohonan?</flux:heading>
                    <flux:subheading>Permohonan akan dihantar untuk kelulusan Gred 41+.</flux:subheading>
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
            <flux:card class="py-12 text-center space-y-4">
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
                    Permohonan anda sedang menunggu kelulusan Pegawai Gred 41+.
                    Anda akan menerima notifikasi emel apabila status berubah.
                </flux:text>
                <div class="pt-4 flex gap-3 justify-center">
                    <flux:button :href="route('penamatan-akaun.index')" wire:navigate>
                        Lihat Senarai Permohonan
                    </flux:button>
                    <flux:button variant="primary" wire:click="$set('langkah', 1); $set('noTiket', '')">
                        Hantar Permohonan Baru
                    </flux:button>
                </div>
            </flux:card>
        </div>
    @endif
</div>
