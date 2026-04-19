<div class="mx-auto max-w-3xl space-y-6 px-4 py-6">
    <flux:heading size="xl">Permohonan Toner Baharu</flux:heading>

    {{-- Maklumat Pemohon --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Maklumat Pemohon</flux:heading>
        <flux:separator />

        <div class="grid gap-4 sm:grid-cols-2">
            <flux:field>
                <flux:label>Nama Penuh</flux:label>
                <flux:input value="{{ $nama }}" readonly />
            </flux:field>

            <flux:field>
                <flux:label>Jawatan & Gred</flux:label>
                <flux:input value="{{ $jawatan }}" readonly />
            </flux:field>

            <flux:field>
                <flux:label>Bahagian / Unit</flux:label>
                <flux:input value="{{ $bahagian }}" readonly />
            </flux:field>

            <flux:field>
                <flux:label>No. Telefon</flux:label>
                <flux:input value="{{ $no_telefon }}" readonly />
            </flux:field>
        </div>
    </flux:card>

    {{-- Maklumat Pencetak & Toner --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Maklumat Pencetak & Toner</flux:heading>
        <flux:separator />

        <div class="grid gap-4 sm:grid-cols-2">
            <flux:field>
                <flux:label>Model Pencetak <flux:badge color="red" size="sm" class="ml-1">Wajib</flux:badge></flux:label>
                <flux:input wire:model="model_pencetak" placeholder="Cth: HP LaserJet Pro M404n" maxlength="100" />
                <flux:error name="model_pencetak" />
            </flux:field>

            <flux:field>
                <flux:label>Jenama Toner <flux:badge color="red" size="sm" class="ml-1">Wajib</flux:badge></flux:label>
                <flux:input wire:model="jenama_toner" placeholder="Cth: HP, Canon, Brother" maxlength="100" />
                <flux:error name="jenama_toner" />
            </flux:field>

            <flux:field>
                <flux:label>Jenis / Warna Toner <flux:badge color="red" size="sm" class="ml-1">Wajib</flux:badge></flux:label>
                <flux:select wire:model="jenis_toner" placeholder="Pilih jenis toner...">
                    @foreach ($jenisToner as $jenis)
                        <flux:select.option value="{{ $jenis->value }}">{{ $jenis->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="jenis_toner" />
            </flux:field>

            <flux:field>
                <flux:label>No. Siri / Kod Toner</flux:label>
                <flux:input wire:model="no_siri_toner" placeholder="Pilihan" maxlength="100" />
                <flux:error name="no_siri_toner" />
            </flux:field>

            <flux:field>
                <flux:label>Kuantiti Diperlukan <flux:badge color="red" size="sm" class="ml-1">Wajib</flux:badge></flux:label>
                <flux:input type="number" wire:model="kuantiti" min="1" max="50" />
                <flux:error name="kuantiti" />
            </flux:field>

            <flux:field>
                <flux:label>Tarikh Diperlukan</flux:label>
                <flux:input type="date" wire:model="tarikh_diperlukan" min="{{ date('Y-m-d') }}" />
                <flux:error name="tarikh_diperlukan" />
            </flux:field>
        </div>

        <flux:field>
            <flux:label>Lokasi Pencetak <flux:badge color="red" size="sm" class="ml-1">Wajib</flux:badge></flux:label>
            <flux:input wire:model="lokasi_pencetak" placeholder="Cth: Tingkat 3, Bilik 304, Bangunan Utama" maxlength="150" />
            <flux:error name="lokasi_pencetak" />
        </flux:field>

        <flux:field>
            <flux:label>Tujuan Permohonan <flux:badge color="red" size="sm" class="ml-1">Wajib</flux:badge></flux:label>
            <flux:textarea wire:model="tujuan" placeholder="Terangkan tujuan permohonan toner ini..." rows="3" maxlength="500" />
            <flux:description>{{ strlen($tujuan) }} / 500 aksara (minimum 10)</flux:description>
            <flux:error name="tujuan" />
        </flux:field>
    </flux:card>

    {{-- Lampiran --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Lampiran</flux:heading>
        <flux:separator />

        <flux:field>
            <flux:label>Fail Lampiran</flux:label>
            <input
                type="file"
                wire:model="lampiranFiles"
                multiple
                accept=".jpg,.jpeg,.png,.pdf"
                class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-700 file:mr-3 file:rounded file:border-0 file:bg-zinc-100 file:px-3 file:py-1 file:text-sm file:font-medium hover:file:bg-zinc-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300"
            />
            <flux:description>Fail JPG, PNG atau PDF sahaja. Saiz maksimum 2MB setiap fail.</flux:description>
            <flux:error name="lampiranFiles.*" />
        </flux:field>

        @if (count($lampiranFiles) > 0)
            <div class="space-y-1">
                @foreach ($lampiranFiles as $fail)
                    <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                        <flux:icon name="paper-clip" class="size-4" />
                        <span>{{ $fail->getClientOriginalName() }}</span>
                        <span class="text-zinc-400">({{ number_format($fail->getSize() / 1024, 1) }} KB)</span>
                    </div>
                @endforeach
            </div>
        @endif
    </flux:card>

    {{-- Submit button with confirmation modal --}}
    <div class="flex justify-end">
        <flux:modal.trigger name="confirm-hantar">
            <flux:button variant="primary" icon="paper-airplane">
                Hantar Permohonan
            </flux:button>
        </flux:modal.trigger>
    </div>

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
                <flux:subheading>Semak semula maklumat sebelum menghantar. No. Tiket akan dijana selepas penghantaran.</flux:subheading>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="hantar" @click="loading = true" icon="paper-airplane">
                    Ya, Hantar
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
