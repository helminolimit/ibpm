<?php

use App\Livewire\M02\BorangPermohonan;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('m02/permohonan-baru', BorangPermohonan::class)->name('m02.permohonan-baru');
});
