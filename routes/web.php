<?php

use App\Http\Controllers\Admin\PenatamatanAdminController;
use App\Http\Controllers\KelulusanPeringkat1Controller;
use App\Http\Controllers\PenatamatanAkaunController;
use App\Http\Controllers\PermohonanPortalController;
use App\Livewire\Admin;
use App\Livewire\KemaskiniProfil;
use App\Livewire\M04\SejarahPermohonan;
use App\Livewire\Pentadbir\M04\PanelPermohonan;
use App\Livewire\Permohonan\AduanIctForm;
use App\Livewire\Permohonan\ButiranAduan;
use App\Livewire\Permohonan\SenaraiAduan;
use App\Livewire\Superadmin;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('home');

Route::view('/panduan', 'pages.panduan')->name('panduan');
Route::view('/hubungi', 'pages.hubungi')->name('hubungi');
Route::view('/faq', 'pages.faq')->name('faq');
Route::view('/privasi', 'pages.privasi')->name('privasi');
Route::view('/penafian', 'pages.penafian')->name('penafian');
Route::view('/dasar-ict', 'pages.dasar-ict')->name('dasar-ict');

Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['ms', 'en'], true)) {
        session(['locale' => $locale]);
    }

    return back();
})->name('locale.switch');

Route::middleware(['auth'])->group(function () {
    Route::livewire('kemaskini-profil', KemaskiniProfil::class)->name('profile.complete');
});

Route::middleware(['auth', 'verified', 'profile.complete'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('permohonan/aduan-ict', AduanIctForm::class)->name('aduan-ict.create');
    Route::livewire('senarai-saya', SenaraiAduan::class)->name('senarai-saya');
    Route::livewire('permohonan/aduan-ict/{id}', ButiranAduan::class)->name('aduan-ict.show');
    Route::view('permohonan/toner', 'pages.coming-soon')->name('toner.create');

    // M04 — Kemaskini Portal (Pengguna)
    Route::prefix('kemaskini-portal')->name('kemaskini-portal.')->group(function () {
        Route::get('/', [PermohonanPortalController::class, 'index'])->name('index');
        Route::get('/baru', [PermohonanPortalController::class, 'create'])->name('create');
        Route::get('/sejarah', SejarahPermohonan::class)->name('sejarah')->middleware('role:pengguna');
        Route::get('/{id}', [PermohonanPortalController::class, 'show'])->name('show');
    });

    // M03 — Penamatan Akaun (Pemohon)
    Route::prefix('penamatan-akaun')->name('penamatan-akaun.')->group(function () {
        Route::get('/', [PenatamatanAkaunController::class, 'index'])->name('index');
        Route::get('/baru', [PenatamatanAkaunController::class, 'create'])->name('create');
        Route::post('/', [PenatamatanAkaunController::class, 'store'])->name('store');
        Route::get('/{id}', [PenatamatanAkaunController::class, 'show'])->name('show');
    });
});

// M03 — Kelulusan Peringkat 1 (Gred 41+)
Route::middleware(['auth', 'verified', 'profile.complete', 'role:pelulus_1,superadmin'])
    ->prefix('kelulusan/penamatan')
    ->name('kelulusan.penamatan.')
    ->group(function () {
        Route::get('/', [KelulusanPeringkat1Controller::class, 'index'])->name('index');
        Route::patch('/{id}/lulus', [KelulusanPeringkat1Controller::class, 'lulus'])->name('lulus');
        Route::patch('/{id}/tolak', [KelulusanPeringkat1Controller::class, 'tolak'])->name('tolak');
    });

Route::middleware(['auth', 'verified', 'profile.complete', 'role:pentadbir,superadmin,teknician'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::livewire('aduan', Admin\SenaraiAduan::class)->name('aduan.index');
        Route::livewire('aduan/{id}', Admin\ButiranAduan::class)->name('aduan.show');
    });

// M03 — Pentadbir ICT
Route::middleware(['auth', 'verified', 'profile.complete', 'role:pentadbir,superadmin'])
    ->prefix('admin/penamatan-akaun')
    ->name('admin.penamatan.')
    ->group(function () {
        Route::get('/', [PenatamatanAdminController::class, 'index'])->name('index');
        Route::patch('/{id}/lulus', [PenatamatanAdminController::class, 'lulus'])->name('lulus');
        Route::patch('/{id}/selesai', [PenatamatanAdminController::class, 'selesai'])->name('selesai');
        Route::get('/{id}/audit', [PenatamatanAdminController::class, 'audit'])->name('audit');
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
        Route::livewire('laporan-portal', Superadmin\LaporanPortal::class)->name('laporan-portal.index');
    });

// M04 — Panel Pentadbir Kemaskini Portal
Route::middleware(['auth', 'verified', 'profile.complete', 'role:pentadbir,superadmin'])
    ->prefix('admin/kemaskini-portal')
    ->name('admin.kemaskini-portal.')
    ->group(function () {
        Route::livewire('/', PanelPermohonan::class)->name('index');
    });

require __DIR__.'/settings.php';
