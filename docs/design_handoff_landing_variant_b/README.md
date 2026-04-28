# Handoff: Landing Page · Variant B (Editorial Centered Hero)

## Overview

Production landing page for **iBPM** at the public root route (`/`). This variant leads with a centered, oversized serif-display headline ("editorial / ministerial gazette" feel), an announcement marquee under the header, and pushes login to the next page rather than embedding it in the hero.

Below the hero: 3-up modules grid, **dark mission-quote band** (signature moment of this variant), a 3-up feature/help band, full footer.

**Scope:** the public unauthenticated landing only. Authenticated dashboard and module pages are handled by separate handoffs.

## About this bundle

| Folder | Contents |
| --- | --- |
| `components/` | Blade view files + 1 CSS partial. Drop-in for Laravel 13 + Flux UI v2 + Tailwind v4. |
| `assets/` | Brand artwork. Place under `public/img/`. |

Source prototype: `iBPM Landing Page.html` (artboard B) in the design project.

## Fidelity

**High-fidelity** — exact Tailwind classes, copy, animation timing.

## Stack context (already verified)

- Laravel 13 + Livewire 4 + Flux UI v2 + Tailwind v4 + Fortify
- Public layout component expected at `resources/views/components/layouts/public.blade.php`
- Brand assets installed by `design_handoff_branding` should already be at `public/img/`

---

## File map

| Handoff file | Target path |
| --- | --- |
| `components/landing-page.blade.php` | `resources/views/landing.blade.php` |
| `components/top-bar.blade.php` | `resources/views/components/landing/top-bar.blade.php` |
| `components/site-header.blade.php` | `resources/views/components/landing/site-header.blade.php` |
| `components/announcement-bar.blade.php` | `resources/views/components/landing/announcement-bar.blade.php` |
| `components/module-card.blade.php` | `resources/views/components/landing/module-card.blade.php` |
| `components/icon.blade.php` | `resources/views/components/landing/icon.blade.php` |
| `components/site-footer.blade.php` | `resources/views/components/landing/site-footer.blade.php` |
| `components/landing.css` | `resources/css/landing.css` (then `@import` from `app.css`) |
| `assets/jata-negara.png` | `public/img/jata-negara.png` |
| `assets/motac-logo.png` | `public/img/motac-logo.png` |
| `assets/bpm-logo.jpg` | `public/img/bpm-logo.jpg` |

> Note: this variant has **no login Livewire component**. The hero CTAs route to `login` and `register` pages directly.

---

## Tasks for Claude Code

### Task 1 — Routes

Same routes as Variant A (`/`, `panduan`, `hubungi`, `faq`, `privasi`, `penafian`, `dasar-ict`, `locale.switch`). Module CTAs link to `aduan-ict.create`, `toner.create`, `admin.laporan.index` — those need to exist (or be stubbed) before the landing renders without `RouteNotFoundException`.

```php
Route::view('/', 'landing')->name('home');
Route::view('/panduan',  'pages.panduan')->name('panduan');
Route::view('/hubungi',  'pages.hubungi')->name('hubungi');
Route::view('/faq',      'pages.faq')->name('faq');
Route::view('/privasi',  'pages.privasi')->name('privasi');
Route::view('/penafian', 'pages.penafian')->name('penafian');
Route::view('/dasar-ict','pages.dasar-ict')->name('dasar-ict');

Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['ms', 'en'], true)) session(['locale' => $locale]);
    return back();
})->name('locale.switch');
```

### Task 2 — CSS

Append to `resources/css/app.css`:

```css
@import './landing.css';
```

Then `npm run build`.

### Task 3 — Stats + announcements (view composer)

Variant B reads `$stats` and `$announcements`. In `app/Providers/AppServiceProvider.php` `boot()`:

```php
\Illuminate\Support\Facades\View::composer('landing', function ($view) {
    $view->with([
        'stats' => \Illuminate\Support\Facades\Cache::remember(
            'landing.stats', now()->addMinutes(15), fn() => [
                'aduan_resolved' => number_format(\App\Models\Aduan::where('status','SELESAI')->count()),
                'sla_pct'        => '98',
                'active_users'   => '1.4k',
            ]
        ),
        'announcements' => \App\Models\Announcement::active()
            ->latest()->take(6)->pluck('label')->all(),
    ]);
});
```

Both have safe fallbacks in the Blade — the page renders without the composer.

### Task 4 — Public layout

Same as Variant A — create `resources/views/components/layouts/public.blade.php` if missing:

```blade
@props(['title' => config('app.name')])
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <title>{{ $title }}</title>
</head>
<body class="antialiased">
    {{ $slot }}
    @persist('toast') <flux:toast /> @endpersist
    @livewireScripts
</body>
</html>
```

---

## Design tokens

Identical to Variant A — same BPM palette, fonts, type scale, spacing rules. Two additions specific to Variant B:

| Element | Value |
| --- | --- |
| Hero pattern | `.bpm-pattern` — soft 32px grid + radial vignette in BPM red 6%/4% |
| Marquee timing | 38s linear infinite, paused under `prefers-reduced-motion` |
| Mission band bg | `bg-zinc-900` with `.songket-stripe` at `opacity-20` |
| Mission accent | `#FED34D` (yellow-300) for italic emphasis words |

### Type scale (Variant B specifics)

| Element | Class |
| --- | --- |
| H1 hero | `font-display text-[64px] sm:text-[84px] lg:text-[104px] leading-[0.95] tracking-[-0.03em]` |
| H2 modules | `font-display text-[40px] lg:text-[48px] leading-tight tracking-tight` |
| Quote band | `font-display text-[28px] lg:text-[36px] leading-[1.25]` |
| Inline stat | `font-display text-[32px] sm:text-[40px] font-semibold tracking-tight` |
| Lede | `text-[17px]` |

### Layout rules

- Hero container narrower than rest: `max-w-[1100px]` (vs `max-w-[1280px]` for sections)
- Hero is **center-aligned**: `text-center`, all CTAs and stats centered
- Mission band uses `max-w-[1100px]` to match the hero rhythm
- Modules and help bands use full `max-w-[1280px]`

---

## Animation contract

Three sequenced fade-ins on the hero:

| Element | `animation-delay` |
| --- | --- |
| Pill + H1 | `0ms` (default) |
| Lede paragraph | `100ms` |
| CTA pair | `180ms` |
| Stats row | `240ms` |

Marquee runs at 38s/cycle. The `<div class="marquee-track">` content is duplicated server-side (`array_merge($items, $items)`) so the loop seam is invisible — **don't try to do this with CSS clone tricks**.

---

## Verification checklist

```bash
npm run build
php artisan view:clear
php artisan route:cache
```

Then check:

- [ ] `/` renders without auth
- [ ] Top bar (govt-style strip), then sticky header, then announcement marquee, then hero
- [ ] Marquee scrolls smoothly left, no visible seam, pauses with reduced-motion
- [ ] Hero: pill → 3-line oversized serif headline (line 2 italic red) → lede → 2 CTAs centered → 3 stat numbers separated by a top border
- [ ] `bpm-pattern` very subtle grid visible behind hero text — should *not* dominate
- [ ] Modules section: centered heading, 3-up cards
- [ ] Mission band: dark `zinc-900` full-bleed with songket diagonals at 20% opacity, italic yellow-300 emphasis on three words ("cekap", "selamat", "mesra pengguna")
- [ ] Help band: 3 white cards on `bg-zinc-50` (Akses selamat / SLA 24 jam / Sokongan langsung)
- [ ] Footer renders (5/3/2/2 columns)
- [ ] No console warnings about missing routes
- [ ] Announcements override works: pass `$announcements = ['Hello']` from a controller → marquee shows just that one item, duplicated for seamless loop

---

## Notes & decisions

- **No inline login.** This variant assumes login is one click away on a dedicated page. If the user later wants Variant A's quick-login moved here, drop the `<livewire:auth.login-card />` from Variant A's bundle into the right side of the hero — but you lose the editorial centered effect, so confirm before doing it.
- **Yellow accent** in the mission quote (`#FED34D`) is the only non-red brand colour on the page. Keep it as italic-only emphasis; don't extend it to buttons or links.
- **Marquee a11y:** the strip has `aria-label="Pengumuman"`. Screen readers will read the duplicated track twice — acceptable for a low-priority announcement region. If accessibility audit flags it, wrap the second copy in `aria-hidden="true"`.
- **Reduced motion:** the CSS pauses the marquee but the fade-ins still play. To kill those too, add `@media (prefers-reduced-motion: reduce) { .fade-in { animation: none; } }` to `landing.css`.
- The Misi BPM quote text is provisional — confirm the exact wording with BPM's pejabat before launch.
