<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="mb-8">
        <flux:heading size="xl">Permohonan Pinjaman ICT</flux:heading>
        <flux:subheading>Borang C — Fasa 1: Permohonan | M05</flux:subheading>
    </div>

    <div class="space-y-10">

        {{-- Bahagian 1: Maklumat Pemohon Asal --}}
        <div>
            <div class="mb-1 flex items-center gap-2">
                <flux:badge color="blue" size="sm">Bahagian 1</flux:badge>
                <flux:heading size="lg">Maklumat Pemohon</flux:heading>
            </div>
            <flux:subheading class="mb-5">Maklumat ini diambil daripada akaun anda. No. telefon dan bahagian boleh dikemaskini.</flux:subheading>

            <div class="grid gap-5 sm:grid-cols-2">
                <flux:input
                    :value="auth()->user()->name"
                    label="Nama Penuh"
                    readonly
                    class="bg-zinc-50 dark:bg-zinc-800"
                />

                <flux:input
                    :value="auth()->user()->position ?? '—'"
                    label="Jawatan & Gred"
                    readonly
                    class="bg-zinc-50 dark:bg-zinc-800"
                />

                <flux:input
                    :value="auth()->user()->email"
                    label="E-mel"
                    type="email"
                    readonly
                    class="bg-zinc-50 dark:bg-zinc-800"
                />

                <flux:input
                    wire:model="phone"
                    label="No. Telefon"
                    type="tel"
                    placeholder="Contoh: 03-8888 8888"
                    required
                />

                <flux:select
                    wire:model="departmentId"
                    label="Bahagian / Unit"
                    placeholder="Pilih bahagian..."
                    required
                    class="sm:col-span-2"
                >
                    @foreach ($this->departments as $dept)
                        <flux:select.option :value="$dept->id">{{ $dept->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        <flux:separator />

        {{-- Bahagian 1A: Mohon Bagi Pihak Orang Lain --}}
        <div>
            <div class="mb-1 flex items-center gap-2">
                <flux:badge color="zinc" size="sm">Bahagian 1A</flux:badge>
                <flux:heading size="lg">Mohon Bagi Pihak Kakitangan Lain</flux:heading>
            </div>

            <div class="mt-4">
                <flux:field variant="inline">
                    <flux:label>Saya membuat permohonan bagi pihak kakitangan lain</flux:label>
                    <flux:switch wire:model.live="onBehalf" />
                </flux:field>
            </div>

            @if ($onBehalf)
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <flux:input
                        wire:model="onBehalfName"
                        label="Nama Penuh"
                        placeholder="Nama penuh kakitangan"
                        required
                    />

                    <flux:input
                        wire:model="onBehalfPosition"
                        label="Jawatan & Gred"
                        placeholder="Contoh: Pegawai Tadbir, N41"
                        required
                    />

                    <flux:input
                        wire:model="onBehalfPhone"
                        label="No. Telefon"
                        type="tel"
                        placeholder="Contoh: 03-8888 8888"
                        required
                    />

                    <flux:select
                        wire:model="onBehalfDepartmentId"
                        label="Bahagian / Unit"
                        placeholder="Pilih bahagian..."
                        required
                    >
                        @foreach ($this->departments as $dept)
                            <flux:select.option :value="$dept->id">{{ $dept->name }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select
                        wire:model="relationship"
                        label="Hubungan / Alasan Mewakili"
                        placeholder="Pilih hubungan..."
                        class="sm:col-span-2"
                    >
                        @foreach ($this->relationshipTypes as $type)
                            <flux:select.option :value="$type->value">{{ $type->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <flux:button :href="route('m05.loan.index')" wire:navigate variant="ghost">
                Batal
            </flux:button>

            <flux:modal.trigger name="confirm-submit">
                <flux:button variant="primary" icon="paper-airplane">
                    Hantar Permohonan
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    {{-- Confirmation modal --}}
    <flux:modal
        name="confirm-submit"
        class="min-w-[22rem]"
        :closable="false"
        x-data="{ loading: false }"
        x-on:cancel="loading && $event.preventDefault()"
        x-on:livewire:commit.window="loading = false"
    >
        <div class="relative space-y-6">
            <div
                wire:loading
                wire:target="submit"
                class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/80 dark:bg-zinc-900/80"
            >
                <flux:icon name="arrow-path" class="size-6 animate-spin text-zinc-500" />
            </div>

            <div>
                <flux:heading size="lg">Hantar permohonan?</flux:heading>
                <flux:subheading>Semak semula maklumat sebelum menghantar. Notifikasi akan dihantar kepada anda setelah permohonan diterima.</flux:subheading>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button
                    variant="primary"
                    wire:click="submit"
                    @click="loading = true"
                    icon="paper-airplane"
                >
                    Hantar
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
