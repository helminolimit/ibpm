<?php

use App\Livewire\M02\Admin\InventoriStok;
use App\Livewire\M02\Admin\LaporanToner;
use App\Livewire\M02\Admin\ProsesPermohonan;
use App\Livewire\M02\Admin\RekodHantar;
use App\Livewire\M02\Admin\SenaraiAdmin;
use App\Livewire\M02\BorangPermohonan;
use App\Livewire\M02\ButiranPermohonan;
use App\Livewire\M02\SenaraiPermohonan;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('m02/permohonan-baru', BorangPermohonan::class)->name('m02.permohonan-baru');
    Route::livewire('m02/permohonan', SenaraiPermohonan::class)->name('m02.senarai');
    Route::livewire('m02/permohonan/{id}', ButiranPermohonan::class)->name('m02.butiran');

    Route::livewire('m02/admin/permohonan', SenaraiAdmin::class)->name('m02.admin.senarai');
    Route::livewire('m02/admin/permohonan/{id}', ProsesPermohonan::class)->name('m02.admin.proses');
    Route::livewire('m02/admin/hantar/{id}', RekodHantar::class)->name('m02.admin.hantar');
    Route::livewire('m02/admin/inventori-stok', InventoriStok::class)->name('m02.admin.inventori-stok');
    Route::livewire('m02/admin/laporan', LaporanToner::class)->name('m02.admin.laporan');
});
