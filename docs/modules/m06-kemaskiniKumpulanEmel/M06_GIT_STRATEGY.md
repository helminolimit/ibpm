# M06 — Git Branch Strategy

## Branch Name
`feature/m06-kumpulan-emel`

## Base Branch
Fork dari `main` — bukan dari branch modul lain

## File Structure (M06 sahaja)
```
app/
  Livewire/M06/
    HantarPermohonan.php
    SenaraiPermohonan.php
    ButiranPermohonan.php
    PentadbirSenarai.php
    PentadbirButiran.php
  Models/
    PermohonanEmel.php
    AhliKumpulan.php
    KumpulanEmel.php
  Services/
    KumpulanEmelService.php   ← baru M06
    NotificationService.php   ← shared, JANGAN edit
  Http/Controllers/M06/
    PermohonanEmelController.php
    PentadbirM06Controller.php
    SuperadminM06Controller.php

database/migrations/
  2024_01_01_000010_create_kumpulan_emel_table.php
  2024_01_01_000011_create_permohonan_emel_table.php
  2024_01_01_000012_create_ahli_kumpulan_table.php

resources/views/
  m06/
    pemohon/
      borang.blade.php
      senarai.blade.php
      butiran.blade.php
    pentadbir/
      senarai.blade.php
      butiran.blade.php
    superadmin/
      kumpulan-emel/
        index.blade.php
        create.blade.php
        edit.blade.php
      laporan.blade.php
  emails/m06/
    permohonan_diterima.blade.php
    permohonan_dilulus.blade.php
    permohonan_ditolak.blade.php
    permohonan_selesai.blade.php

routes/
  m06.php   ← include dalam web.php

tests/Feature/M06/
  HantarPermohonanTest.php
  PentadbirLulusTest.php
```

## Prevent Merge Conflict
- JANGAN edit `routes/web.php` terus — buat `routes/m06.php` dan include
- JANGAN edit `NotificationService.php` — extend atau wrap sahaja
- JANGAN edit `LogAudit` model — guna terus
- Setiap modul guna namespace `M06` dalam Livewire dan Controller

## Merge Process
1. PR dari `feature/m06-kumpulan-emel` ke `main`
2. Semak tiada migration conflict (check timestamp prefix)
3. Run `php artisan migrate` setelah merge
4. Tag release: `v1.6.0`

## DO NOT
- Push terus ke `main`
- Edit file dari modul lain dalam branch M06
- Guna `array_merge` pada shared config — extend class sahaja
