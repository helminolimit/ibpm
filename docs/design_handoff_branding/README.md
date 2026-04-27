# Handoff: Brand Identity (Favicon + Logo)

## Overview

Replace the default Laravel starter-kit branding (Laravel "L" mark) in the **iBPM** application with the official **Bahagian Pengurusan Maklumat (BPM)** and **Jata Negara / Kementerian Pelancongan, Seni dan Budaya (MOTAC)** identity.

This handoff covers two concerns:

1. **Browser favicon / app icon** — what shows up in the browser tab, bookmark, and home-screen shortcut.
2. **In-app logo mark** — what shows up on the login/register screens and in the sidebar brand area.

---

## About this bundle

The files here are **production-ready static assets** plus a checklist of code edits. There is no HTML prototype to "translate" — these go straight into the existing Laravel codebase at the paths shown.

- `public/` → drop these into `public/` of the Laravel repo (overwrites the Laravel-default favicons).
- `assets/` → reference copies of the source brand artwork. Use `bpm-logo.jpg` and `jata-negara.png` in app views (move them into `public/img/` or `resources/images/` per existing convention — check the repo before deciding).

## Fidelity

**High-fidelity** — exact files, exact paths, exact tasks. No interpretation needed.

---

## Stack context (already verified)

- Laravel 13 + Livewire 4 + Flux UI v2 + Tailwind v4 + Fortify
- Favicon links live in `resources/views/partials/head.blade.php`
- Logo component lives in `resources/views/components/app-logo-icon.blade.php` (the SVG that renders the Laravel mark) and `resources/views/components/app-logo.blade.php` (wraps it for sidebar/auth)

---

## Tasks for Claude Code

### Task 1 — Replace favicon files in `public/`

Copy these files from this handoff bundle into the Laravel repo, overwriting the originals:

| From handoff                                  | To repo path                              |
| --------------------------------------------- | ----------------------------------------- |
| `public/favicon.svg`                          | `public/favicon.svg`                      |
| `public/favicon-16x16.png`                    | `public/favicon-16x16.png` *(new)*        |
| `public/favicon-32x32.png`                    | `public/favicon-32x32.png` *(new)*        |
| `public/apple-touch-icon.png`                 | `public/apple-touch-icon.png`             |
| `public/android-chrome-192x192.png`           | `public/android-chrome-192x192.png` *(new)* |
| `public/android-chrome-512x512.png`           | `public/android-chrome-512x512.png` *(new)* |

**`favicon.ico` is not in this bundle.** Generate it from `favicon-32x32.png` using ImageMagick or an online tool, OR remove the `.ico` reference from `head.blade.php` and rely on `favicon.svg` + the PNGs (modern browsers handle this fine).

```bash
# If you have ImageMagick:
convert public/favicon-32x32.png public/favicon.ico
```

### Task 2 — Update `resources/views/partials/head.blade.php`

Current state (lines 8–10):

```blade
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
```

Replace with:

```blade
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="192x192" href="/android-chrome-192x192.png">
<link rel="icon" type="image/png" sizes="512x512" href="/android-chrome-512x512.png">
<meta name="theme-color" content="#E30613">
```

### Task 3 — Replace the in-app logo mark

The Laravel "L" SVG lives in `resources/views/components/app-logo-icon.blade.php` and is rendered:

- On **login** / **register** / **forgot-password** screens via `resources/views/layouts/auth/simple.blade.php`
- In the **sidebar brand** area via `resources/views/components/app-logo.blade.php`

#### 3a. Add brand images to public assets

Copy from this handoff:

| From handoff                       | To repo path                         |
| ---------------------------------- | ------------------------------------ |
| `assets/bpm-logo.jpg`              | `public/img/bpm-logo.jpg`            |
| `assets/jata-negara.png`           | `public/img/jata-negara.png`         |
| `assets/motac-logo.png`            | `public/img/motac-logo.png`          |

(Create `public/img/` if it doesn't exist. If the repo already has another convention like `public/images/` or `public/assets/`, use that instead.)

#### 3b. Rewrite `resources/views/components/app-logo-icon.blade.php`

Current: hand-rolled Laravel SVG. Replace the whole file with a simple `<img>` tag pointing to the BPM mark — keep the `{{ $attributes }}` passthrough so callers can still pass `class="size-9 ..."`:

```blade
<img
    src="{{ asset('img/bpm-logo.jpg') }}"
    alt="iBPM"
    {{ $attributes->merge(['class' => 'rounded-md object-cover']) }}
/>
```

Note: drop the `fill-current text-white` classes from caller sites where they no longer apply — see Task 3d.

#### 3c. Update `resources/views/components/app-logo.blade.php` for the sidebar

The sidebar brand currently renders the icon inside a dark accent square. With the BPM logo (already a red square), drop the wrapper background. Replace:

```blade
<x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
    <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
</x-slot>
```

with:

```blade
<x-slot name="logo" class="flex aspect-square size-8 items-center justify-center">
    <x-app-logo-icon class="size-8" />
</x-slot>
```

Do this in **both** branches of the `@if($sidebar)` … `@else` conditional in `app-logo.blade.php`.

Also change `name="Laravel Starter Kit"` to `name="iBPM"` in both `<flux:sidebar.brand>` and `<flux:brand>` calls. (Or pull from `config('app.name')` if the project sets that.)

#### 3d. Update `resources/views/layouts/auth/simple.blade.php`

Currently:

```blade
<span class="flex h-9 w-9 mb-1 items-center justify-center rounded-md">
    <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
</span>
```

Replace with:

```blade
<span class="flex h-12 w-12 mb-1 items-center justify-center">
    <x-app-logo-icon class="size-12" />
</span>
```

Also check `resources/views/layouts/auth/card.blade.php` and `split.blade.php` — apply the same fix anywhere `<x-app-logo-icon>` appears with `fill-current` classes.

### Task 4 — Update `config/app.php` name

Change `'name' => env('APP_NAME', 'Laravel')` default if the `.env` file still says `APP_NAME=Laravel`. The recommended value for `APP_NAME` in `.env`:

```
APP_NAME="iBPM"
```

This flows into the `<title>` tag via `head.blade.php` and into the sidebar brand if you choose to bind it.

### Task 5 — Optional: add MOTAC ministry chip to login footer

For a small "official" touch on the auth pages, add a footer below the login form showing the MOTAC ministry mark — entirely optional, ask the user before doing this. If yes, add to `resources/views/layouts/auth/simple.blade.php` just before `</body>`:

```blade
<footer class="flex flex-col items-center gap-2 pb-6 text-xs text-zinc-500">
    <img src="{{ asset('img/motac-logo.png') }}" alt="Kementerian Pelancongan, Seni dan Budaya" class="h-12 w-auto opacity-90">
    <span>Kementerian Pelancongan, Seni dan Budaya</span>
</footer>
```

---

## Design tokens (BPM brand)

| Token            | Value      | Notes                              |
| ---------------- | ---------- | ---------------------------------- |
| BPM red          | `#E30613`  | Primary brand colour from BPM logo |
| BPM red on white | foreground | Used for accents, primary buttons  |
| White            | `#FFFFFF`  | Logo wordmark colour               |

If you later want to swap the app's accent colour from neutral-800 to BPM red (in `resources/css/app.css` `@theme` block), change:

```css
--color-accent: var(--color-neutral-800);
--color-accent-content: var(--color-neutral-800);
```

to:

```css
--color-accent: #E30613;
--color-accent-content: #E30613;
```

But **ask the user first** — that's a broader theme change, not part of the favicon task.

---

## Verification checklist

After applying changes, run:

```bash
npm run build         # rebuild Vite assets
php artisan view:clear
php artisan config:clear
```

Then verify:

- [ ] Browser tab shows red BPM favicon (hard-refresh to bypass cache: Ctrl+Shift+R)
- [ ] Login page shows BPM logo above the form
- [ ] Sidebar brand shows BPM mark, not Laravel "L"
- [ ] Page title reads "iBPM" (or "<page> - iBPM")
- [ ] No 404s for icon files in browser DevTools → Network tab
- [ ] Apple touch icon visible when "Add to home screen" on mobile

---

## Source materials

The brand artwork in `assets/` was provided by the user:

- `bpm-logo.jpg` — BPM unit logomark (red square, white "bpm" italic wordmark)
- `jata-negara.png` — Malaysian federal coat of arms (Jata Negara)
- `motac-logo.png` — Kementerian Pelancongan, Seni dan Budaya logo + ministry name

The user's email domain is `motac.gov.my` — they have authority to use these government identity assets.
