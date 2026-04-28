{{-- resources/views/components/landing/announcement-bar.blade.php
     Marquee strip below the header, used in Variant B. --}}
@props(['items' => []])

@php
    // Sensible defaults; controller / view composer can override $items.
    $items = ! empty($items) ? $items : [
        '📢 ' . __('Penyelenggaraan sistem dijadualkan: Sabtu 2 Mei 2026, 22:00–02:00'),
        '🆕 ' . __('Modul Permohonan Toner kini menyokong eksport laporan Excel'),
        '📞 ' . __('Talian sokongan BPM: 03-8000 8000 ext. 1234 (Isnin–Jumaat, 8:30 pagi–5:30 petang)'),
        '✅ ' . __('1,247 aduan ICT diselesaikan pada suku pertama 2026'),
    ];
    // Duplicate the track so the seamless marquee doesn't show a gap.
    $track = array_merge($items, $items);
@endphp

<div class="border-b border-zinc-200 bg-[rgb(var(--bpm-50))]/60 overflow-hidden" aria-label="{{ __('Pengumuman') }}">
    <div class="max-w-[1280px] mx-auto px-6 h-10 flex items-center gap-4">
        <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 h-6 rounded-full bg-[rgb(var(--bpm))] text-white text-[10.5px] font-semibold uppercase tracking-wider">
            <span class="size-1.5 rounded-full bg-white animate-pulse"></span>
            {{ __('Pengumuman') }}
        </span>
        <div class="flex-1 overflow-hidden">
            <div class="marquee-track flex gap-12 whitespace-nowrap text-[12.5px] text-zinc-700">
                @foreach ($track as $i => $t)
                    <span class="inline-flex items-center gap-2">
                        <span class="text-[rgb(var(--bpm))]">◆</span>
                        {{ $t }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</div>
