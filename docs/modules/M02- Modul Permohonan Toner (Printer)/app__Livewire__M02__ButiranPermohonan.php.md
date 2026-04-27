# `app/Livewire/M02/ButiranPermohonan.php`

```php
<?php

namespace App\Livewire\M02;

use App\Models\PermohonanToner;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ButiranPermohonan extends Component
{
    public PermohonanToner $permohonan;

    public function mount(string $noTiket): void
    {
        $this->permohonan = PermohonanToner::where('no_tiket', $noTiket)
            ->with(['pemohon', 'penghantaran', 'log.pengguna'])
            ->firstOrFail();

        // Pemohon hanya boleh lihat permohonan sendiri
        abort_unless(
            Auth::id() === $this->permohonan->pemohon_id
            || Auth::user()->hasRole(['pentadbir', 'superadmin']),
            403,
            'Anda tidak mempunyai kebenaran untuk melihat permohonan ini.'
        );
    }

    public function render()
    {
        return view('livewire.m02.butiran-permohonan')
            ->layout('layouts.app', ['title' => "Butiran {$this->permohonan->no_tiket}"]);
    }
}
```
