# Handoff: Footer & Breadcrumb (iBPM)

## Overview

Add two new UI elements to the **iBPM** Laravel app, matching the prototype design:

1. **MOTAC Footer** — official ministry footer at the bottom of every authenticated page (Jata Negara + ministry name + links + copyright).
2. **Breadcrumb component** — small breadcrumb trail at the top of each page header (e.g. *iBPM › Permohonan › Aduan ICT*).

Both are reusable Blade components dropped into the existing Flux UI / Livewire 4 / Tailwind v4 layout.

---

## About this bundle

- `components/` — three ready-to-paste Blade component files
- `assets/jata-negara.png` — Jata Negara artwork (place at `public/img/jata-negara.png`)
- This README — exact paste locations and edit instructions

The HTML prototype lives at `iBPM Prototype v1.html` in the design project — the spec below is what we extracted from it.

## Fidelity

**High-fidelity** — exact Tailwind classes, colours, spacing. Match pixel-for-pixel.

---

## Stack context (already verified)

- Laravel 13 + Livewire 4 + Flux UI v2 + Tailwind v4 + Fortify
- Authenticated layout: `resources/views/layouts/app/sidebar.blade.php`
- Pages render via `{{ $slot }}` inside the layout
- Tailwind theme tokens defined in `resources/css/app.css` `@theme` block (uses `--color-zinc-*` and `--color-accent-*`)

---

## 1. MOTAC Footer

### 1a. Create `resources/views/components/motac-footer.blade.php`

```blade
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
```

### 1b. Place artwork

Copy `assets/jata-negara.png` from this bundle to `public/img/jata-negara.png`. Create the `img/` folder if it doesn't exist.

### 1c. Wire it into the layout

Edit `resources/views/layouts/app/sidebar.blade.php`. Find:

```blade
        {{ $slot }}

        @persist('toast')
```

Replace with:

```blade
        <div class="flex min-h-svh flex-col">
            <div class="flex-1">
                {{ $slot }}
            </div>
            <x-motac-footer />
        </div>

        @persist('toast')
```

The flex column ensures the footer sticks to the bottom even on short pages. The Flux sidebar already handles the left rail; this wraps the right-hand content area.

### 1d. Localisation strings (optional but recommended)

If you use `lang/ms.json`, add:

```json
{
    "Kerajaan Malaysia": "Kerajaan Malaysia",
    "Kementerian Pelancongan, Seni dan Budaya": "Kementerian Pelancongan, Seni dan Budaya",
    "Bahagian Pengurusan Maklumat (BPM)": "Bahagian Pengurusan Maklumat (BPM)",
    "Dasar Privasi": "Dasar Privasi",
    "Penafian": "Penafian",
    "Hubungi BPM": "Hubungi BPM"
}
```

(They're already in Bahasa, so this is a no-op for `ms` but useful if you ever add `en`.)

---

## 2. Breadcrumb component

### 2a. Create `resources/views/components/breadcrumbs.blade.php`

```blade
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
```

### 2b. Use it in pages

At the top of any Livewire view (e.g. `resources/views/livewire/permohonan/aduan-ict-form.blade.php`):

```blade
<div>
    <x-breadcrumbs :items="[
        ['label' => 'iBPM', 'url' => route('dashboard')],
        ['label' => 'Permohonan'],
        ['label' => 'Aduan ICT'],
    ]" />

    <flux:heading size="xl">{{ __('Hantar Aduan ICT') }}</flux:heading>

    {{-- ... rest of page ... --}}
</div>
```

### 2c. Suggested breadcrumb map

Use these for consistency across the app (matches the prototype's `headerMap`):

| Route                          | Crumbs                                                |
| ------------------------------ | ----------------------------------------------------- |
| `dashboard`                    | iBPM › Dashboard                                      |
| `aduan-ict.create`             | iBPM › Permohonan › Aduan ICT                         |
| `senarai-saya`                 | iBPM › Permohonan › Aduan Saya                        |
| `aduan-ict.show`               | iBPM › Aduan Saya › Butiran                           |
| `admin.aduan.index`            | iBPM › Aduan ICT › Kotak Masuk                        |
| `admin.aduan.show`             | iBPM › Aduan ICT › Butiran                            |
| `admin.laporan.index`          | iBPM › Laporan                                        |
| `profile.edit`                 | iBPM › Tetapan › Profil                               |

### 2d. Optional: centralised breadcrumb helper

Instead of repeating arrays in every view, you can put the map in a Blade view composer. Skip this unless asked — repetition is fine and keeps each page self-documenting.

---

## Design tokens reference

Both components use existing Tailwind v4 zinc palette tokens already defined in `resources/css/app.css`. No new theme additions needed.

| Element             | Tailwind class                                      |
| ------------------- | --------------------------------------------------- |
| Footer surface      | `bg-zinc-50 dark:bg-zinc-900`                       |
| Footer border       | `border-zinc-200 dark:border-zinc-700`              |
| Heading text        | `text-zinc-900 dark:text-zinc-100`                  |
| Body text           | `text-zinc-600 dark:text-zinc-400`                  |
| Faint / meta text   | `text-zinc-400 dark:text-zinc-500`                  |
| Crumb separator     | `text-zinc-400 dark:text-zinc-500` + chevron 12px   |
| Crumb current page  | `text-zinc-900 dark:text-zinc-100`                  |

Typography scale (matches prototype):

- Footer ministry name: `text-[12.5px] font-semibold`
- Footer kicker ("Kerajaan Malaysia"): `text-[11px] font-semibold uppercase tracking-wider`
- Footer body: `text-[11px]`
- Footer links: `text-[11.5px]`
- Breadcrumbs: `text-[11.5px]`

Spacing:

- Footer container: `max-w-[1400px] px-6 py-5`
- Footer flex gap: `gap-4 sm:gap-6`
- Footer divider pad: `sm:pl-5`
- Breadcrumb gap between items: `gap-1.5`
- Breadcrumb bottom margin: `mb-3`

---

## Verification checklist

After applying:

```bash
npm run build
php artisan view:clear
```

Then check:

- [ ] Footer appears at bottom of dashboard, full width of content column
- [ ] Footer sticks to viewport bottom on short pages (no floating mid-page)
- [ ] Footer is responsive: stacks vertically on mobile, horizontal on `sm:` and up
- [ ] Jata Negara image loads (no broken icon)
- [ ] Dark mode: footer switches to `zinc-900` background cleanly
- [ ] Breadcrumb appears above page heading on any page where you've added it
- [ ] Last crumb is darker (current page indicator)
- [ ] Non-last crumbs with `url` are clickable and use `wire:navigate`
- [ ] Chevron separators are 12px, faint colour

---

## Notes

- The footer references `config('app.version')`. Either add `'version' => '1.0'` to `config/app.php` or replace with a hard-coded `"1.0"`.
- The breadcrumb on the prototype is rendered inside the top header bar; we've moved it into page bodies here because that's where Flux UI's `flux:sidebar` layout naturally exposes the slot. If you'd rather have it in the header, it would need a `flux:header` slot above `{{ $slot }}` — let the user decide.
- Both components are pure Blade (no Livewire state). Safe to use anywhere.
