<?php

use App\Livewire\Permohonan\AduanIctForm;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('permohonan/aduan-ict', AduanIctForm::class)->name('aduan-ict.create');
});

require __DIR__.'/settings.php';
