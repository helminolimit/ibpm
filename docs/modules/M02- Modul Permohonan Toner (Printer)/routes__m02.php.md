# `routes/m02.php`

```php
<?php

use App\Livewire\M02\BorangPermohonan;
use App\Livewire\M02\SenaraiPermohonan;
use App\Livewire\M02\ButiranPermohonan;
use App\Livewire\M02\Admin\SenaraiAdmin;
use App\Livewire\M02\Admin\ProsesPermohonan;
use App\Livewire\M02\Admin\RekodHantar;
use App\Livewire\M02\Admin\InventoriStok;
use Illuminate\Support\Facades\Route;

// ─── Pemohon ──────────────────────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('m02')->name('m02.')->group(function () {

    Route::get('/permohonan', SenaraiPermohonan::class)
        ->name('senarai');

    Route::get('/permohonan/baru', BorangPermohonan::class)
        ->name('borang');

    Route::get('/permohonan/{noTiket}', ButiranPermohonan::class)
        ->name('butiran');
});

// ─── Pentadbir BPM ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:pentadbir,superadmin'])
    ->prefix('admin/m02')
    ->name('admin.m02.')
    ->group(function () {

        Route::get('/permohonan', SenaraiAdmin::class)
            ->name('senarai');

        Route::get('/permohonan/{id}', ProsesPermohonan::class)
            ->name('proses');

        Route::get('/hantar/{id}', RekodHantar::class)
            ->name('hantar');

        Route::get('/stok', InventoriStok::class)
            ->name('stok');
    });
```
