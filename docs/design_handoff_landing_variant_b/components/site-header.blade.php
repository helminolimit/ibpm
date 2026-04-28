{{-- resources/views/components/landing/site-header.blade.php --}}
<header class="border-b border-zinc-200 bg-white sticky top-0 z-30 backdrop-blur supports-[backdrop-filter]:bg-white/85">
    <div class="max-w-[1280px] mx-auto px-6 h-16 flex items-center gap-5">
        <a href="{{ url('/') }}" class="flex items-center gap-3 min-w-0">
            <img src="{{ asset('img/jata-negara.png') }}" alt="Jata Negara" class="h-9 w-auto object-contain" />
            <div class="hidden sm:block min-w-0 leading-tight">
                <div class="text-[10.5px] uppercase tracking-[0.08em] text-zinc-500 font-semibold">
                    {{ __('Kementerian Pelancongan, Seni dan Budaya') }}
                </div>
                <div class="text-[13px] font-semibold text-zinc-900">
                    {{ __('Bahagian Pengurusan Maklumat') }}
                </div>
            </div>
        </a>
        <div class="flex-1"></div>

        <nav class="hidden md:flex items-center gap-1 text-[13px]">
            <a href="#modul" class="px-3 py-2 rounded-md text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50">{{ __('Modul') }}</a>
            <a href="#panduan" class="px-3 py-2 rounded-md text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50">{{ __('Panduan') }}</a>
            <a href="#statistik" class="px-3 py-2 rounded-md text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50">{{ __('Statistik') }}</a>
            <a href="#hubungi" class="px-3 py-2 rounded-md text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50">{{ __('Hubungi BPM') }}</a>
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ route('login') }}" class="h-9 px-4 rounded-[8px] btn-ghost text-[13px] font-medium inline-flex items-center">
                {{ __('Log Masuk') }}
            </a>
            <a href="{{ route('register') }}" class="h-9 px-4 rounded-[8px] btn-primary text-[13px] font-semibold inline-flex items-center gap-1.5">
                {{ __('Daftar Akaun') }}
                <x-landing.icon name="arrow" />
            </a>
        </div>
    </div>
</header>
