{{-- resources/views/components/landing/module-card.blade.php --}}
@props([
    'icon'  => 'ticket',
    'title' => '',
    'desc'  => '',
    'items' => [],
    'href'  => '#',
])

<a href="{{ $href }}" class="group block p-6 rounded-[14px] border border-zinc-200 bg-white hover:border-zinc-300 hover:shadow-[0_8px_24px_-12px_rgb(0_0_0_/_0.12)] transition-all">
    <div class="flex items-start justify-between mb-5">
        <div class="size-11 rounded-[10px] grid place-items-center bg-[rgb(var(--bpm))]/8 text-[rgb(var(--bpm))]">
            <x-landing.icon :name="$icon" size="22" />
        </div>
        <svg class="text-zinc-300 group-hover:text-[rgb(var(--bpm))] group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all"
             width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M7 17l10-10"/><path d="M7 7h10v10"/>
        </svg>
    </div>
    <div class="text-[16px] font-semibold text-zinc-900 mb-1.5">{{ __($title) }}</div>
    <div class="text-[13px] text-zinc-600 leading-relaxed mb-4">{{ __($desc) }}</div>
    <ul class="space-y-1.5">
        @foreach ($items as $it)
            <li class="text-[12.5px] text-zinc-500 flex items-start gap-2">
                <span class="text-[rgb(var(--bpm))] mt-1.5 inline-block size-1 rounded-full bg-current shrink-0"></span>
                {{ __($it) }}
            </li>
        @endforeach
    </ul>
</a>
