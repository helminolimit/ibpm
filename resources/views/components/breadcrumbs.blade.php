@props(['items' => []])

@if (count($items) > 0)
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="flex items-center gap-1.5 text-[11.5px] text-zinc-600 dark:text-zinc-400">
            @foreach ($items as $i => $item)
                @if ($i > 0)
                    <li aria-hidden="true" class="text-zinc-400 dark:text-zinc-500">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </li>
                @endif
                <li @class(['text-zinc-900 dark:text-zinc-100' => $loop->last])>
                    @if (! $loop->last && ! empty($item['url']))
                        <a href="{{ $item['url'] }}" class="hover:text-zinc-900 dark:hover:text-zinc-100" wire:navigate>
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span @if ($loop->last) aria-current="page" @endif>{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
