# 06 — Routes & Struktur Blade Views
## M03 Penamatan Akaun Login Komputer

---

## Routes

Tambah dalam `routes/web.php` dalam middleware group `auth`:

```php
use App\Http\Controllers\PenatamatanAkaunController;
use App\Http\Controllers\KelulusanPeringkat1Controller;
use App\Http\Controllers\Admin\PenatamatanAdminController;

Route::middleware(['auth'])->group(function () {

    // --- Pemohon: CRUD permohonan sendiri ---
    Route::prefix('penamatan-akaun')->name('penamatan-akaun.')->group(function () {
        Route::get('/',         [PenatamatanAkaunController::class, 'index'])->name('index');
        Route::get('/baru',     [PenatamatanAkaunController::class, 'create'])->name('create');
        Route::post('/',        [PenatamatanAkaunController::class, 'store'])->name('store');
        Route::get('/{id}',     [PenatamatanAkaunController::class, 'show'])->name('show');
    });

    // --- Pelulus Peringkat 1 (Gred 41+) ---
    Route::middleware(['role:pelulus_1,superadmin'])
        ->prefix('kelulusan/penamatan')
        ->name('kelulusan.penamatan.')
        ->group(function () {
            Route::get('/',              [KelulusanPeringkat1Controller::class, 'index'])->name('index');
            Route::patch('/{id}/lulus',  [KelulusanPeringkat1Controller::class, 'lulus'])->name('lulus');
            Route::patch('/{id}/tolak',  [KelulusanPeringkat1Controller::class, 'tolak'])->name('tolak');
        });

    // --- Pentadbir ICT ---
    Route::middleware(['role:pentadbir,superadmin'])
        ->prefix('admin/penamatan-akaun')
        ->name('admin.penamatan.')
        ->group(function () {
            Route::get('/',               [PenatamatanAdminController::class, 'index'])->name('index');
            Route::patch('/{id}/lulus',   [PenatamatanAdminController::class, 'lulus'])->name('lulus');
            Route::patch('/{id}/selesai', [PenatamatanAdminController::class, 'selesai'])->name('selesai');
            Route::get('/{id}/audit',     [PenatamatanAdminController::class, 'audit'])->name('audit');
        });
});
```

---

## Struktur Folder Blade Views

```
resources/views/
└── m03/
    ├── index.blade.php            ← Senarai permohonan (pemohon)
    ├── buat.blade.php             ← Borang permohonan baru (embed Livewire)
    ├── butiran.blade.php          ← Butiran 1 permohonan + timeline status
    ├── kelulusan1/
    │   └── index.blade.php        ← Senarai menunggu kelulusan Gred 41+
    └── admin/
        ├── index.blade.php        ← Senarai semua (admin, embed Livewire AdminSenarai)
        └── audit.blade.php        ← Log audit permohonan
```

---

## Panduan Setiap View

### `m03/index.blade.php` — Pemohon
- Extend layout utama
- Papar jadual: No. Tiket, Jenis, Tarikh Mohon, Status (badge warna), Tindakan (lihat)
- Butang "Permohonan Baru" di atas
- Status badge warna: `MENUNGGU_KEL_1` = kuning, `SELESAI` = hijau, `DITOLAK` = merah

### `m03/buat.blade.php` — Borang
- Embed Livewire component: `<livewire:m03.borang-permohonan />`
- Papar wizard 3 langkah dalam component

### `m03/butiran.blade.php` — Butiran
- Papar semua maklumat permohonan
- Timeline kelulusan (peringkat 1 → peringkat 2 → selesai)
- Jika status `DRAF`, papar butang edit

### `m03/kelulusan1/index.blade.php` — Gred 41+
- Papar senarai permohonan status `MENUNGGU_KEL_1`
- Setiap baris: No. Tiket, Pemohon, ID Login, Tarikh Mohon
- Butang "Lulus" dan "Tolak" dengan modal konfirmasi
- Modal tolak mesti ada textarea untuk catatan sebab penolakan

### `m03/admin/index.blade.php` — Pentadbir ICT
- Embed `<livewire:m03.admin-senarai />`
- Filter dropdown status di atas
- Butang "Lulus", "Selesai", "Audit" mengikut status semasa

### `m03/admin/audit.blade.php` — Log Audit
- Papar senarai semua rekod dalam `log_audits` untuk permohonan berkenaan
- Tunjukkan: tarikh/masa, nama pengguna, tindakan, IP address

---

## Komponen Blade Boleh Guna Semula

Cipta komponen kecil berikut untuk konsistensi:

```
resources/views/components/
├── status-badge.blade.php     ← Badge warna mengikut nilai status enum
└── tiket-id.blade.php         ← Paparan No. Tiket dengan gaya monospace
```

### `status-badge.blade.php`

```blade
@props(['status'])

@php
$kelas = match($status) {
    'DRAF'           => 'bg-gray-100 text-gray-700',
    'MENUNGGU_KEL_1' => 'bg-yellow-100 text-yellow-800',
    'MENUNGGU_KEL_2' => 'bg-purple-100 text-purple-800',
    'DALAM_PROSES'   => 'bg-blue-100 text-blue-800',
    'SELESAI'        => 'bg-green-100 text-green-800',
    'DITOLAK'        => 'bg-red-100 text-red-800',
    default          => 'bg-gray-100 text-gray-700',
};
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $kelas }}">
    {{ $status }}
</span>
```

Guna dalam mana-mana view:
```blade
<x-status-badge :status="$permohonan->status" />
```

---

## JANGAN

- Jangan buat route tanpa middleware `auth`
- Jangan papar data permohonan orang lain kepada pemohon biasa
- Jangan gunakan `Route::resource` — route M03 perlu kawalan eksplisit
- Jangan campurkan view pemohon dan admin dalam folder yang sama
