# 07 — UI Design Handoff
## M03 Penamatan Akaun Login Komputer

> **Rujukan visual:** `iBPM M03 Penamatan Akaun.html` (fail HTML prototype yang telah dijana)  
> **Sistem padanan:** mestilah konsisten dengan modul M01 (Aduan ICT) & M02 (Toner) sedia ada.

Fail ini WAJIB dibaca selepas `06-routes-views.md` dan SEBELUM tulis Blade views. Tujuan: pastikan UI Blade mengikuti bahasa visual iBPM dan tidak menggunakan komponen Tailwind generik.

---

## 1. Token Warna (CSS Variables)

Tambah dalam `resources/css/app.css` (atau extend `tailwind.config.js`). Sistem iBPM guna RGB triplet supaya boleh `rgb(var(--xx) / opacity)`.

```css
:root{
  --bg: 255 255 255;
  --surface: 255 255 255;
  --surface-muted: 250 250 250;       /* zinc-50 */
  --border: 228 228 231;              /* zinc-200 */
  --border-strong: 212 212 216;       /* zinc-300 */
  --fg: 24 24 27;                     /* zinc-900 */
  --fg-muted: 82 82 91;               /* zinc-600 */
  --fg-faint: 161 161 170;            /* zinc-400 */
  --primary: 38 38 38;                /* neutral-800 — butang utama, nav aktif */
  --primary-fg: 255 255 255;
  --primary-50: 245 245 245;
  --accent: 124 58 237;               /* violet-600 — MENUNGGU_KEL_2 */
  --accent-50: 245 243 255;
  --ok: 21 128 61;        --ok-50: 236 253 245;
  --warn: 180 83 9;       --warn-50: 254 249 231;
  --danger: 185 28 28;    --danger-50: 254 242 242;
  --info: 29 78 216;      --info-50: 239 246 255;
}
html.dark{ /* flip ke zinc-800 surface, white primary */ }
```

**Tipografi:** Instrument Sans (UI) + JetBrains Mono (no. tiket, ID login, kod modul, masa).

---

## 2. Status Badge — WAJIB ikut palette ini

| Status | Tone | Background | Text |
|---|---|---|---|
| `DRAF` | zinc | `--surface-muted` | `--fg-muted` |
| `MENUNGGU_KEL_1` | warn | `--warn-50` | `--warn` |
| `MENUNGGU_KEL_2` | accent | `--accent-50` | `--accent` (violet) |
| `DALAM_PROSES` | info | `--info-50` | `--info` |
| `SELESAI` | ok | `--ok-50` | `--ok` |
| `DITOLAK` | danger | `--danger-50` | `--danger` |

Semua badge ada **dot** kecil (size 1.5) di kiri label. Bentuk pill (`rounded-full`), border 1px tone-coloured 25% opacity, font-semibold, text 11px.

**Jenis tindakan badge:** `TAMAT` = danger, `GANTUNG` = warn.

---

## 3. Komponen Wajib Dibina

### 3.1 Wizard Stepper (Borang Permohonan)
3 langkah: **Borang → Semakan → Selesai**. Bulatan size-7, border-2, animated transition. Langkah selesai → fill primary + ikon ✓; langkah semasa → outline primary; akan datang → outline border-strong.

### 3.2 Aliran Kelulusan (Butiran Permohonan)
4-step horizontal flowchart pada page butiran:
`Dihantar → Kelulusan Pelulus 1 → Kelulusan Pentadbir ICT → Tindakan Penamatan`

Setiap step: bulatan size-9 dengan nombor/tick/X, label di bawah, tarikh kecil, garis penyambung yang fill primary apabila step seterusnya selesai. Step ditolak → fill danger-50 + X icon merah.

### 3.3 Timeline Audit (Sidebar butiran)
Vertical timeline:
- Dot 2.5 dalam bulatan size-8 berwarna ikut tone status
- Connector vertical 1px dari dot ke dot
- Setiap entry: badge status + masa + catatan + "oleh {nama}"

### 3.4 Senarai Semak Tindakan ICT (Modal "Tandakan Selesai")
Checkbox list yang Pentadbir ICT tick semasa nak tutup permohonan:
- Active Directory account — disabled
- Mailbox Exchange — hide from GAL
- Lesen Microsoft 365 — dilepaskan
- Akses VPN & Wi-Fi — dicabut
- Sesi aktif — log keluar paksa
- Dokumen OneDrive — dipindahkan kepada penyelia

Setiap row: checkbox + label dalam bekas border rounded-8.

### 3.5 KPI Cards (Dashboard Pentadbir ICT)
4 cards atas senarai:
- Menunggu Kel. ICT (tone accent)
- Dalam Proses (tone info)
- Selesai bulan ini (tone ok)
- Ditolak (tone danger)

Card style: padding 5, rounded-12, shadow-sm, label kecil + value 28px bold + delta text muted.

---

## 4. Layout Halaman

### Page wrapper
```html
<div class="max-w-[1200px] mx-auto px-6 py-6">…</div>
```

### Header pattern (semua page senarai)
```
[ M03 · UPPERCASE FONT-MONO 12px text-muted ]
[ Tajuk H1 22px font-semibold tracking-tight ]
[ Subtajuk 13px text-muted ]                      [ Butang Primer ]
```

### Card style
- `rounded-[12px]`, `border border-default`, `bg-surface`, padding 5 (20px)
- `shadow-sm` lembut
- SectionTitle: 11px UPPERCASE tracking-wider text-faint

### Table style
- Header: 11px UPPERCASE font-semibold tracking-wider text-faint
- Cells: text 13px, padding 12px
- `tbody tr:hover { background: surface-muted }`
- Divide-y dengan border colour
- Tabs di atas table dengan tab counts (`<Tabs>`)

### Tabs
Border-bottom 2px style. Tab aktif → border primary + text primary. Count chip sebelah label dengan bg primary-50.

---

## 5. Padanan dengan Modul Sedia Ada

UI M03 mesti **identik** dengan M01 Aduan ICT dari segi:
- Padding/spacing
- Card radius (12px)
- Button radius (8px)
- Header + subtajuk pattern
- Stepper wizard borang
- Timeline audit
- Toast notification

Rujuk: `resources/views/m01/**` & `app/Livewire/M01/**` (jika modul M01 sudah wujud).

---

## 6. Komponen Blade Boleh Guna Semula (Wajib Cipta)

```
resources/views/components/
├── status-badge.blade.php       # Badge dengan tone matching enum
├── tiket-id.blade.php           # No. tiket monospace
├── stepper.blade.php            # Wizard 3-langkah (borang)
├── aliran-kelulusan.blade.php   # Flowchart 4-step (butiran)
├── timeline-audit.blade.php     # Sejarah status vertical
├── kpi-card.blade.php           # KPI dengan label/value/delta/tone
├── empty-state.blade.php        # Empty state ikon + title + subtitle
└── page-header.blade.php        # Eyebrow + h1 + subtitle + slot action
```

### Contoh `status-badge.blade.php` (ikut palette)
```blade
@props(['status'])
@php
$map = [
    'DRAF'           => ['bg-zinc-100','text-zinc-600','bg-zinc-400'],
    'MENUNGGU_KEL_1' => ['bg-amber-50','text-amber-700','bg-amber-600'],
    'MENUNGGU_KEL_2' => ['bg-violet-50','text-violet-700','bg-violet-600'],
    'DALAM_PROSES'   => ['bg-blue-50','text-blue-700','bg-blue-600'],
    'SELESAI'        => ['bg-emerald-50','text-emerald-700','bg-emerald-600'],
    'DITOLAK'        => ['bg-red-50','text-red-700','bg-red-600'],
];
[$bg,$txt,$dot] = $map[$status] ?? $map['DRAF'];
$labels = [
    'DRAF'=>'Draf','MENUNGGU_KEL_1'=>'Menunggu Kelulusan 1',
    'MENUNGGU_KEL_2'=>'Menunggu Kelulusan 2','DALAM_PROSES'=>'Dalam Proses',
    'SELESAI'=>'Selesai','DITOLAK'=>'Ditolak',
];
@endphp
<span class="inline-flex items-center gap-1.5 px-2 h-[22px] rounded-full text-[11px] font-semibold border border-current/20 {{ $bg }} {{ $txt }}">
    <span class="size-1.5 rounded-full {{ $dot }}"></span>
    {{ $labels[$status] ?? $status }}
</span>
```

---

## 7. Halaman Yang Perlu Dibina (Senarai Lengkap)

| Route | View Blade | Komponen Khas |
|---|---|---|
| `penamatan-akaun.index` | `m03/index.blade.php` | page-header, table tabs, status-badge |
| `penamatan-akaun.create` | `m03/buat.blade.php` | embed `<livewire:m03.borang-permohonan />` + stepper |
| `penamatan-akaun.show` | `m03/butiran.blade.php` | aliran-kelulusan, timeline-audit, sidebar info |
| `kelulusan.penamatan.index` | `m03/kelulusan1/index.blade.php` | table + modal Lulus + modal Tolak |
| `admin.penamatan.index` | `m03/admin/index.blade.php` | 4 KPI cards + tabs + senarai-semak modal |
| `admin.penamatan.audit` | `m03/admin/audit.blade.php` | table audit log monospace |

---

## 8. Peraturan UI

- **JANGAN** guna alert Bootstrap. Semua notifikasi → Toast komponen (atas-kanan, slide-in 240ms).
- **JANGAN** guna modal default `<dialog>`. Cipta `<x-modal>` dengan backdrop blur + animation.
- **JANGAN** rounded-2xl atau lebih besar. Standard ialah 8px (button/input), 10–12px (card/modal).
- **JANGAN** guna emoji.
- **JANGAN** font lain selain Instrument Sans + JetBrains Mono.
- **WAJIB** semua tarikh format DD.MM.YYYY (contoh: `30.04.2026`).
- **WAJIB** no. tiket dan ID login dalam font-mono.
- **WAJIB** mod gelap — guna `html.dark` token flipping, bukan dual-class Tailwind.

---

## 9. Rujukan Visual

Buka `iBPM M03 Penamatan Akaun.html` semasa coding. Setiap skrin Blade mestilah identik secara visual dengan skrin React di sini. Jika ragu — toggle peranan di topbar prototype untuk lihat semua 3 perspektif (Pemohon / Pelulus 1 / Pentadbir ICT).
