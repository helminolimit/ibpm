<?php

use App\Livewire\Admin;
use App\Livewire\KemaskiniProfil;
use App\Livewire\Permohonan\AduanIctForm;
use App\Livewire\Permohonan\ButiranAduan;
use App\Livewire\Permohonan\SenaraiAduan;
use App\Livewire\Superadmin;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::livewire('kemaskini-profil', KemaskiniProfil::class)->name('profile.complete');
});

Route::middleware(['auth', 'verified', 'profile.complete'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('permohonan/aduan-ict', AduanIctForm::class)->name('aduan-ict.create');
    Route::livewire('senarai-saya', SenaraiAduan::class)->name('senarai-saya');
    Route::livewire('permohonan/aduan-ict/{id}', ButiranAduan::class)->name('aduan-ict.show');
});

Route::middleware(['auth', 'verified', 'profile.complete', 'role:pentadbir,superadmin,teknician'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::livewire('aduan', Admin\SenaraiAduan::class)->name('aduan.index');
        Route::livewire('aduan/{id}', Admin\ButiranAduan::class)->name('aduan.show');
    });

Route::middleware(['auth', 'verified', 'profile.complete', 'role:pentadbir,superadmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::livewire('laporan', Admin\LaporanAduan::class)->name('laporan.index');
    });

Route::middleware(['auth', 'verified', 'profile.complete', 'role:superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::livewire('pengguna', Superadmin\SenaraiPengguna::class)->name('pengguna.index');
        Route::livewire('peranan-akses', Superadmin\PerananAkses::class)->name('peranan-akses.index');
        Route::livewire('log-audit', Superadmin\LogAudit::class)->name('log-audit.index');
    });

require __DIR__.'/settings.php';
