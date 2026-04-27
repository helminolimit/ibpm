<div>
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Lengkapkan Profil Anda')"
            :description="__('Maklumat berikut diperlukan sebelum anda boleh menggunakan sistem.')"
        />

        <form wire:submit.prevent class="flex flex-col gap-5">
            <flux:input
                wire:model="bahagian"
                :label="__('Bahagian')"
                type="text"
                required
                autofocus
                autocomplete="organization"
            />

            <flux:input
                wire:model="unit_bpm"
                :label="__('Unit BPM')"
                type="text"
                autocomplete="off"
            />

            <flux:input
                wire:model="jawatan"
                :label="__('Jawatan')"
                type="text"
                required
                autocomplete="organization-title"
            />

            <flux:input
                wire:model="no_telefon"
                :label="__('Nombor Telefon')"
                type="tel"
                required
                autocomplete="tel"
            />

            <flux:modal.trigger name="confirm-kemaskini-profil">
                <flux:button variant="primary" class="w-full" type="button">
                    {{ __('Simpan & Teruskan') }}
                </flux:button>
            </flux:modal.trigger>
        </form>

        <div class="text-center text-sm">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <flux:button variant="ghost" type="submit" size="sm">
                    {{ __('Log Keluar') }}
                </flux:button>
            </form>
        </div>
    </div>

    <flux:modal
        name="confirm-kemaskini-profil"
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
                <flux:heading size="lg">Sahkan maklumat profil?</flux:heading>
                <flux:subheading>Semak semula maklumat anda sebelum meneruskan.</flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Semak Semula</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="simpan" @click="loading = true">
                    Simpan
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
