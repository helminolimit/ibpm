<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Permohonan')" class="grid">
                    <flux:sidebar.item icon="exclamation-circle" :href="route('aduan-ict.create')" :current="request()->routeIs('aduan-ict.create')" wire:navigate>
                        {{ __('Aduan ICT') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="queue-list" :href="route('senarai-saya')" :current="request()->routeIs('senarai-saya') || request()->routeIs('aduan-ict.show')" wire:navigate>
                        {{ __('Senarai Saya') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @if (auth()->user()?->isAdmin())
                    @php
                        $jumlahAduanBaru = \App\Models\AduanIct::query()
                            ->when(auth()->user()->isPentadbir(), fn ($q) =>
                                $q->whereHas('kategori', fn ($k) => $k->where('unit_bpm', auth()->user()->bahagian))
                            )
                            ->where('status', \App\Enums\StatusAduan::Baru)
                            ->count();
                    @endphp
                    <flux:sidebar.group :heading="__('Pentadbiran')" class="grid">
                        <flux:sidebar.item
                            icon="shield-check"
                            :href="route('admin.aduan.index')"
                            :current="request()->routeIs('admin.aduan.*')"
                            wire:navigate
                        >
                            <div class="flex w-full items-center justify-between">
                                <span>{{ __('Aduan ICT') }}</span>
                                @if ($jumlahAduanBaru > 0)
                                    <flux:badge color="red" size="sm">{{ $jumlahAduanBaru }}</flux:badge>
                                @endif
                            </div>
                        </flux:sidebar.item>
                        <flux:sidebar.item
                            icon="chart-bar"
                            :href="route('admin.laporan.index')"
                            :current="request()->routeIs('admin.laporan.*')"
                            wire:navigate
                        >
                            {{ __('Jana Laporan') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
