<?php

use App\Livewire\M05\LoanCreate;
use App\Livewire\M05\LoanIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('pinjaman-ict')->name('m05.loan.')->group(function () {
    Route::livewire('/', LoanIndex::class)->name('index');
    Route::livewire('/baru', LoanCreate::class)->name('create');
});
