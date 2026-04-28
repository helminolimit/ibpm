{{-- resources/views/components/landing/site-footer.blade.php --}}
<footer id="hubungi" class="border-t border-zinc-200 bg-zinc-50">
    <div class="max-w-[1280px] mx-auto px-6 py-12">
        <div class="grid md:grid-cols-12 gap-8">
            <div class="md:col-span-5">
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('img/jata-negara.png') }}" alt="" class="h-12 w-auto" />
                    <div>
                        <div class="text-[10.5px] uppercase tracking-[0.08em] text-zinc-500 font-semibold">{{ __('Kerajaan Malaysia') }}</div>
                        <div class="text-[14px] font-semibold text-zinc-900">{{ __('Kementerian Pelancongan, Seni dan Budaya') }}</div>
                        <div class="text-[12px] text-zinc-600">{{ __('Bahagian Pengurusan Maklumat (BPM)') }}</div>
                    </div>
                </div>
                <p class="text-[13px] text-zinc-600 leading-relaxed max-w-md">
                    {{ __('iBPM adalah portal sokongan ICT bersepadu untuk seluruh kakitangan kementerian dan agensi di bawah MOTAC.') }}
                </p>
            </div>
            <div class="md:col-span-3">
                <div class="text-[12px] font-semibold uppercase tracking-wider text-zinc-500 mb-3">{{ __('Hubungi BPM') }}</div>
                <ul class="space-y-2.5 text-[13px] text-zinc-700">
                    <li class="flex items-start gap-2">
                        <span class="text-zinc-400 mt-0.5"><x-landing.icon name="pin" /></span>
                        <span>Aras 6, No. 2, Menara 1<br/>Jalan P5/6, Presint 5<br/>62200 Putrajaya</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-zinc-400"><x-landing.icon name="call" /></span>
                        <span class="font-mono">03-8000 8000 ext. 1234</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-zinc-400"><x-landing.icon name="mail" /></span>
                        <a class="link-underline" href="mailto:bpm@motac.gov.my">bpm@motac.gov.my</a>
                    </li>
                </ul>
            </div>
            <div class="md:col-span-2">
                <div class="text-[12px] font-semibold uppercase tracking-wider text-zinc-500 mb-3">{{ __('Pautan Pantas') }}</div>
                <ul class="space-y-2 text-[13px] text-zinc-700">
                    <li><a href="https://www.motac.gov.my" class="hover:text-[rgb(var(--bpm))]">MOTAC</a></li>
                    <li><a href="https://www.malaysia.gov.my" class="hover:text-[rgb(var(--bpm))]">MyGovernment</a></li>
                    <li><a href="https://epenyata.anm.gov.my" class="hover:text-[rgb(var(--bpm))]">e-Penyata Gaji</a></li>
                    <li><a href="#" class="hover:text-[rgb(var(--bpm))]">SPAB</a></li>
                </ul>
            </div>
            <div class="md:col-span-2">
                <div class="text-[12px] font-semibold uppercase tracking-wider text-zinc-500 mb-3">{{ __('Sokongan') }}</div>
                <ul class="space-y-2 text-[13px] text-zinc-700">
                    <li><a href="{{ route('panduan') }}" class="hover:text-[rgb(var(--bpm))]">{{ __('Panduan Pengguna') }}</a></li>
                    <li><a href="{{ route('faq') }}" class="hover:text-[rgb(var(--bpm))]">FAQ</a></li>
                    <li><a href="{{ route('privasi') }}" class="hover:text-[rgb(var(--bpm))]">{{ __('Dasar Privasi') }}</a></li>
                    <li><a href="{{ route('penafian') }}" class="hover:text-[rgb(var(--bpm))]">{{ __('Penafian') }}</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-10 pt-6 border-t border-zinc-200 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <div class="text-[12px] text-zinc-500">
                © {{ date('Y') }} {{ __('Kementerian Pelancongan, Seni dan Budaya') }} · iBPM v{{ config('app.version', '1.0') }}
            </div>
            <div class="text-[11.5px] text-zinc-500 flex items-center gap-3">
                <span>{{ __('Paparan terbaik: Chrome, Edge, Firefox versi terkini') }}</span>
                <span class="text-zinc-300">·</span>
                <span>{{ __('Resolusi 1280×720 ke atas') }}</span>
            </div>
        </div>
    </div>
</footer>
