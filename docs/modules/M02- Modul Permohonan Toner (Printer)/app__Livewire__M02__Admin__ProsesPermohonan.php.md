# `app/Livewire/M02/Admin/ProsesPermohonan.php`

```php
<?php

namespace App\Livewire\M02\Admin;

use App\Models\PermohonanToner;
use App\Models\LogToner;
use App\Notifications\TonerDiluluskan;
use App\Notifications\TonerDitolak;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProsesPermohonan extends Component
{
    public PermohonanToner $permohonan;

    public int    $kuantiti_diluluskan = 0;
    public string $catatan_pentadbir   = '';
    public string $sebab_tolak         = '';

    public bool $showModalLulus  = false;
    public bool $showModalTolak  = false;

    public function mount(int $id): void
    {
        $this->permohonan = PermohonanToner::with([
            'pemohon', 'stokToner', 'log.pengguna',
        ])->findOrFail($id);

        $this->kuantiti_diluluskan = $this->permohonan->kuantiti_diminta;
    }

    public function luluskan(): void
    {
        $this->validate([
            'kuantiti_diluluskan' => 'required|integer|min:1',
            'catatan_pentadbir'   => 'nullable|string|max:500',
        ], [
            'kuantiti_diluluskan.required' => 'Sila isi kuantiti yang diluluskan.',
            'kuantiti_diluluskan.min'      => 'Kuantiti minimum ialah 1.',
        ]);

        // Semak sama ada stok mencukupi
        $stokStatus = 'approved';
        if ($this->permohonan->stokToner &&
            !$this->permohonan->stokToner->stokMencukupi($this->kuantiti_diluluskan)) {
            $stokStatus = 'pending_stock';
        }

        $this->permohonan->update([
            'status'              => $stokStatus,
            'diproses_oleh'       => Auth::id(),
            'kuantiti_diluluskan' => $this->kuantiti_diluluskan,
            'catatan_pentadbir'   => $this->catatan_pentadbir ?: null,
        ]);

        LogToner::create([
            'permohonan_id' => $this->permohonan->id,
            'pengguna_id'   => Auth::id(),
            'tindakan'      => 'approved',
            'keterangan'    => "Diluluskan: {$this->kuantiti_diluluskan} unit. {$this->catatan_pentadbir}",
            'created_at'    => now(),
        ]);

        $this->permohonan->pemohon->notify(new TonerDiluluskan($this->permohonan));

        $this->showModalLulus = false;
        $this->dispatch('berjaya', mesej: 'Permohonan berjaya diluluskan.');
    }

    public function tolak(): void
    {
        $this->validate([
            'sebab_tolak' => 'required|string|min:10|max:500',
        ], [
            'sebab_tolak.required' => 'Sila nyatakan sebab penolakan.',
            'sebab_tolak.min'      => 'Sebab penolakan terlalu pendek.',
        ]);

        $this->permohonan->update([
            'status'            => 'rejected',
            'diproses_oleh'     => Auth::id(),
            'catatan_pentadbir' => $this->sebab_tolak,
        ]);

        LogToner::create([
            'permohonan_id' => $this->permohonan->id,
            'pengguna_id'   => Auth::id(),
            'tindakan'      => 'rejected',
            'keterangan'    => "Ditolak: {$this->sebab_tolak}",
            'created_at'    => now(),
        ]);

        $this->permohonan->pemohon->notify(new TonerDitolak($this->permohonan));

        $this->showModalTolak = false;
        $this->dispatch('berjaya', mesej: 'Permohonan telah ditolak.');
    }

    public function render()
    {
        return view('livewire.m02.admin.proses-permohonan')
            ->layout('layouts.admin', ['title' => "Proses {$this->permohonan->no_tiket}"]);
    }
}
```
