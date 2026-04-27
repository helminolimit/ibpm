<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-2">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="flex h-12 w-12 mb-1 items-center justify-center">
                        <x-app-logo-icon class="size-12" />
                    </span>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts

        <footer class="flex flex-col items-center gap-2 pb-6 text-xs text-zinc-500">
            <img src="{{ asset('img/motac-logo.png') }}" alt="Kementerian Pelancongan, Seni dan Budaya" class="h-12 w-auto opacity-90">
            <span>Kementerian Pelancongan, Seni dan Budaya</span>
        </footer>
    </body>
</html>
