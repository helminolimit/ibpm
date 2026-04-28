{{--
  Variant B: Centered editorial hero with announcement marquee. No inline login form.
  Tone: editorial, confident, "ministerial gazette" feel.
  Place at: resources/views/landing.blade.php
--}}
<x-layouts.public title="iBPM · Sistem Sokongan ICT MOTAC">
    <x-landing.top-bar />
    <x-landing.site-header />
    <x-landing.announcement-bar :items="$announcements ?? []" />

    {{-- ============== HERO — editorial, centered ============== --}}
    <section class="relative overflow-hidden border-b border-zinc-200 bpm-pattern">
        <div class="relative max-w-[1100px] mx-auto px-6 pt-20 pb-16 text-center">
            <div class="inline-flex items-center gap-2 px-3 h-7 rounded-full bg-white border border-zinc-200 text-[11.5px] font-medium text-zinc-700 mb-7 fade-in">
                <span class="size-1.5 rounded-full bg-[rgb(var(--bpm))]"></span>
                {{ __('Bahagian Pengurusan Maklumat · MOTAC') }}
            </div>

            <h1 class="font-display text-[64px] sm:text-[84px] lg:text-[104px] leading-[0.95] tracking-[-0.03em] text-zinc-900 mb-6 fade-in">
                {{ __('Satu portal,') }}<br/>
                <span class="italic font-medium text-[rgb(var(--bpm))]">{{ __('semua sokongan') }}</span><br/>
                {{ __('ICT kementerian.') }}
            </h1>

            <p class="text-[17px] text-zinc-600 leading-relaxed max-w-2xl mx-auto mb-9 fade-in" style="animation-delay: 100ms">
                {{ __('iBPM mengumpulkan perkhidmatan Aduan ICT, Permohonan Toner dan Laporan di bawah satu sistem rasmi untuk seluruh kakitangan MOTAC dan agensi.') }}
            </p>

            <div class="flex flex-wrap items-center justify-center gap-3 mb-10 fade-in" style="animation-delay: 180ms">
                <a href="{{ route('login') }}" class="h-12 px-6 rounded-[10px] btn-primary text-[14.5px] font-semibold inline-flex items-center gap-2">
                    {{ __('Log Masuk Sistem') }} <x-landing.icon name="arrow" />
                </a>
                <a href="{{ route('register') }}" class="h-12 px-6 rounded-[10px] border border-zinc-300 hover:border-zinc-400 text-[14px] font-medium inline-flex items-center gap-2 text-zinc-800 bg-white">
                    {{ __('Daftar Akaun Baru') }}
                </a>
            </div>

            {{-- Inline service stats --}}
            <div class="grid grid-cols-3 gap-x-6 sm:gap-x-12 max-w-2xl mx-auto pt-8 border-t border-zinc-200 fade-in" style="animation-delay: 240ms">
                <div>
                    <div class="font-display text-[32px] sm:text-[40px] font-semibold text-zinc-900 leading-none tracking-tight">
                        {{ $stats['aduan_resolved'] ?? '1,247' }}
                    </div>
                    <div class="text-[11.5px] text-zinc-500 mt-1.5">{{ __('Aduan diselesaikan') }}</div>
                </div>
                <div>
                    <div class="font-display text-[32px] sm:text-[40px] font-semibold text-zinc-900 leading-none tracking-tight">
                        {{ $stats['sla_pct'] ?? '98' }}<span class="text-[rgb(var(--bpm))]">%</span>
                    </div>
                    <div class="text-[11.5px] text-zinc-500 mt-1.5">{{ __('SLA dipenuhi') }}</div>
                </div>
                <div>
                    <div class="font-display text-[32px] sm:text-[40px] font-semibold text-zinc-900 leading-none tracking-tight">
                        {{ $stats['active_users'] ?? '1.4k' }}
                    </div>
                    <div class="text-[11.5px] text-zinc-500 mt-1.5">{{ __('Pengguna aktif') }}</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============== MODULES ============== --}}
    <section id="modul" class="py-20 border-b border-zinc-200">
        <div class="max-w-[1280px] mx-auto px-6">
            <div class="text-center mb-12">
                <div class="text-[12px] font-semibold uppercase tracking-[0.12em] text-[rgb(var(--bpm))] mb-2">{{ __('Perkhidmatan') }}</div>
                <h2 class="font-display text-[40px] lg:text-[48px] leading-tight tracking-tight text-zinc-900 max-w-2xl mx-auto">
                    {{ __('Tiga modul utama,') }}<br/>{{ __('diuruskan oleh BPM.') }}
                </h2>
            </div>
            <div class="grid md:grid-cols-3 gap-5">
                <x-landing.module-card icon="ticket" title="Aduan ICT"
                    desc="Hantar aduan masalah perkakasan, perisian, rangkaian dan capaian sistem."
                    :items="['Tiket bernombor', 'Lampiran tangkap layar', 'Notifikasi automatik']"
                    href="{{ route('aduan-ict.create') }}" />
                <x-landing.module-card icon="toner" title="Permohonan Toner"
                    desc="Mohon toner pencetak untuk pejabat anda. Stok dipantau pentadbir BPM."
                    :items="['Pilih dari katalog', 'Pengesahan ketua', 'Rekod penghantaran']"
                    href="{{ route('toner.create') }}" />
                <x-landing.module-card icon="chart" title="Laporan & Analitik"
                    desc="Jana laporan suku tahunan, prestasi SLA dan eksport ke Excel/PDF."
                    :items="['Carta interaktif', 'Eksport CSV/PDF', 'Penapis bahagian']"
                    href="{{ route('admin.laporan.index') }}" />
            </div>
        </div>
    </section>

    {{-- ============== MISSION QUOTE BAND ============== --}}
    <section class="py-20 bg-zinc-900 text-white relative overflow-hidden">
        <div class="absolute inset-0 songket-stripe opacity-20 pointer-events-none"></div>
        <div class="relative max-w-[1100px] mx-auto px-6 text-center">
            <div class="text-[11.5px] font-semibold uppercase tracking-[0.16em] text-[rgb(var(--bpm))]/90 mb-5">{{ __('Misi BPM') }}</div>
            <p class="font-display text-[28px] lg:text-[36px] leading-[1.25] tracking-tight max-w-3xl mx-auto">
                &ldquo;{{ __('Memacu transformasi digital kementerian melalui perkhidmatan ICT yang') }}
                <span class="italic text-[rgb(254_211_77)]"> {{ __('cekap') }}</span>,
                <span class="italic text-[rgb(254_211_77)]"> {{ __('selamat') }}</span> {{ __('dan') }}
                <span class="italic text-[rgb(254_211_77)]"> {{ __('mesra pengguna') }}</span>.&rdquo;
            </p>
            <div class="mt-7 text-[12.5px] text-zinc-400 uppercase tracking-wider">
                {{ __('Bahagian Pengurusan Maklumat · MOTAC') }}
            </div>
        </div>
    </section>

    {{-- ============== HELP / FEATURE BAND ============== --}}
    <section id="panduan" class="py-16 bg-zinc-50 border-b border-zinc-200">
        <div class="max-w-[1280px] mx-auto px-6 grid md:grid-cols-3 gap-5">
            <div class="p-7 rounded-[14px] bg-white border border-zinc-200">
                <div class="size-10 rounded-[8px] bg-[rgb(var(--bpm))]/8 text-[rgb(var(--bpm))] grid place-items-center mb-4">
                    <x-landing.icon name="shield" size="22" />
                </div>
                <div class="text-[15px] font-semibold mb-1.5">{{ __('Akses selamat') }}</div>
                <p class="text-[13px] text-zinc-600 leading-relaxed">{{ __('Pengesahan dua faktor & log audit penuh untuk setiap tindakan.') }}</p>
            </div>
            <div class="p-7 rounded-[14px] bg-white border border-zinc-200">
                <div class="size-10 rounded-[8px] bg-[rgb(var(--bpm))]/8 text-[rgb(var(--bpm))] grid place-items-center mb-4">
                    <x-landing.icon name="clock" size="22" />
                </div>
                <div class="text-[15px] font-semibold mb-1.5">{{ __('SLA 24 jam') }}</div>
                <p class="text-[13px] text-zinc-600 leading-relaxed">{{ __('Aduan diiktiraf dalam 1 jam, diselesaikan mengikut keutamaan.') }}</p>
            </div>
            <div class="p-7 rounded-[14px] bg-white border border-zinc-200">
                <div class="size-10 rounded-[8px] bg-[rgb(var(--bpm))]/8 text-[rgb(var(--bpm))] grid place-items-center mb-4">
                    <x-landing.icon name="call" size="22" />
                </div>
                <div class="text-[15px] font-semibold mb-1.5">{{ __('Sokongan langsung') }}</div>
                <p class="text-[13px] text-zinc-600 leading-relaxed font-mono">03-8000 8000 ext. 1234</p>
            </div>
        </div>
    </section>

    <x-landing.site-footer />
</x-layouts.public>
