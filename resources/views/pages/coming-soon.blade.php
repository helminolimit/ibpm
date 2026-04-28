<x-layouts.public title="{{ __('Akan Datang') }} · iBPM">
    <x-landing.top-bar />
    <x-landing.site-header />
    <main class="max-w-[1100px] mx-auto px-6 py-20 text-center">
        <h1 class="font-display text-[48px] text-zinc-900 mb-4">{{ __('Akan Datang') }}</h1>
        <p class="text-zinc-500">{{ __('Modul ini sedang dalam pembangunan. Sila semak semula kemudian.') }}</p>
        <a href="{{ route('home') }}" class="mt-8 inline-flex btn-primary h-10 px-5 rounded-[8px] text-[13px] font-semibold items-center gap-2">
            ← {{ __('Kembali ke laman utama') }}
        </a>
    </main>
    <x-landing.site-footer />
</x-layouts.public>
