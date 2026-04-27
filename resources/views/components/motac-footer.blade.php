<footer class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <div class="mx-auto max-w-[1400px] px-6 py-5 flex flex-col sm:flex-row items-center sm:items-stretch gap-4 sm:gap-6">
        {{-- Jata Negara + Ministry --}}
        <div class="flex items-center gap-3 min-w-0">
            <img
                src="{{ asset('img/jata-negara.png') }}"
                alt="Jata Negara Malaysia"
                class="h-12 w-auto shrink-0 object-contain"
            />
            <div class="min-w-0">
                <div class="text-[11px] font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                    {{ __('Kerajaan Malaysia') }}
                </div>
                <div class="text-[12.5px] font-semibold leading-tight text-zinc-900 dark:text-zinc-100">
                    {{ __('Kementerian Pelancongan, Seni dan Budaya') }}
                </div>
                <div class="text-[11px] leading-tight text-zinc-600 dark:text-zinc-400">
                    {{ __('Bahagian Pengurusan Maklumat (BPM)') }}
                </div>
            </div>
        </div>

        {{-- Vertical divider (desktop only) --}}
        <div class="hidden sm:block w-px bg-zinc-200 dark:bg-zinc-700 mx-1"></div>

        {{-- Links + copyright --}}
        <div class="flex-1 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 sm:gap-5 text-[11.5px]">
            <nav class="flex items-center gap-4 text-zinc-600 dark:text-zinc-400">
                <a href="#" class="hover:text-zinc-900 dark:hover:text-zinc-100">{{ __('Dasar Privasi') }}</a>
                <a href="#" class="hover:text-zinc-900 dark:hover:text-zinc-100">{{ __('Penafian') }}</a>
                <a href="#" class="hover:text-zinc-900 dark:hover:text-zinc-100">{{ __('Hubungi BPM') }}</a>
            </nav>
            <div class="text-zinc-400 dark:text-zinc-500 sm:border-l sm:border-zinc-200 sm:dark:border-zinc-700 sm:pl-5">
                © {{ date('Y') }} MOTAC · iBPM v{{ config('app.version', '1.0') }}
            </div>
        </div>
    </div>
</footer>
