<?php

use App\Livewire\Admin;
use App\Livewire\Permohonan\AduanIctForm;
use App\Livewire\Permohonan\ButiranAduan;
use App\Livewire\Permohonan\SenaraiAduan;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('permohonan/aduan-ict', AduanIctForm::class)->name('aduan-ict.create');
    Route::livewire('senarai-saya', SenaraiAduan::class)->name('senarai-saya');
    Route::livewire('permohonan/aduan-ict/{id}', ButiranAduan::class)->name('aduan-ict.show');
});

Route::middleware(['auth', 'verified', 'role:pentadbir,superadmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::livewire('aduan', Admin\SenaraiAduan::class)->name('aduan.index');
        Route::livewire('aduan/{id}', Admin\ButiranAduan::class)->name('aduan.show');
        Route::livewire('laporan', Admin\LaporanAduan::class)->name('laporan.index');
    });

require __DIR__.'/settings.php';
