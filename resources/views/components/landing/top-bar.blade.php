{{-- resources/views/components/landing/top-bar.blade.php --}}
<div class="border-b border-zinc-200 bg-white">
    <div class="max-w-[1280px] mx-auto px-6 h-9 flex items-center justify-between text-[12px] text-zinc-600">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 font-medium text-zinc-900">
                <span class="inline-block size-1.5 rounded-full bg-[rgb(var(--bpm))]"></span>
                {{ __('Portal Rasmi Kerajaan Malaysia') }}
            </span>
        </div>
        <div class="hidden sm:flex items-center gap-4">
            <a href="{{ route('hubungi') }}" class="hover:text-zinc-900">{{ __('Hubungi Kami') }}</a>
            <span class="text-zinc-300">|</span>
            {{-- Font-size controls (wire to a small Alpine helper if you want it functional) --}}
            <button type="button" class="hover:text-zinc-900" data-text-size="lg">A<span class="text-[10px]">+</span></button>
            <button type="button" class="hover:text-zinc-900 font-medium" data-text-size="md">A</button>
            <button type="button" class="hover:text-zinc-900" data-text-size="sm">A<span class="text-[10px]">−</span></button>
            <span class="text-zinc-300">|</span>
            <a href="{{ route('locale.switch', 'ms') }}" class="hover:text-zinc-900 {{ app()->getLocale()==='ms' ? 'font-medium' : '' }}">BM</a>
            <span class="text-zinc-300">·</span>
            <a href="{{ route('locale.switch', 'en') }}" class="hover:text-zinc-900 {{ app()->getLocale()==='en' ? 'font-medium' : '' }}">EN</a>
        </div>
    </div>
</div>
