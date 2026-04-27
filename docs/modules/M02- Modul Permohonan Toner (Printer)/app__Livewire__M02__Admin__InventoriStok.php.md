# `app/Livewire/M02/Admin/InventoriStok.php`

```php
<?php

namespace App\Livewire\M02\Admin;

use App\Models\StokToner;
use App\Models\LogToner;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class InventoriStok extends Component
{
    // Form tambah / kemaskini stok
    public ?int   $stok_id         = null;
    public string $model_toner     = '';
    public string $jenama          = '';
    public string $jenis           = '';
    public string $warna           = '';
    public int    $kuantiti_ada    = 0;
    public int    $kuantiti_minimum = 5;

    public bool $showModal = false;

    public function bukaBaru(): void
    {
        $this->reset(['stok_id', 'model_toner', 'jenama', 'jenis', 'warna',
                      'kuantiti_ada', 'kuantiti_minimum']);
        $this->showModal = true;
    }

    public function bukaEdit(int $id): void
    {
        $stok = StokToner::findOrFail($id);
        $this->stok_id          = $stok->id;
        $this->model_toner      = $stok->model_toner;
        $this->jenama           = $stok->jenama;
        $this->jenis            = $stok->jenis;
        $this->warna            = $stok->warna ?? '';
        $this->kuantiti_ada     = $stok->kuantiti_ada;
        $this->kuantiti_minimum = $stok->kuantiti_minimum;
        $this->showModal        = true;
    }

    public function simpan(): void
    {
        $this->validate([
            'model_toner'      => 'required|string|max:100',
            'jenama'           => 'required|string|max:100',
            'jenis'            => 'required|in:hitam,cyan,magenta,kuning',
            'kuantiti_ada'     => 'required|integer|min:0',
            'kuantiti_minimum' => 'required|integer|min:1',
        ], [
            'model_toner.required'  => 'Sila isi model toner.',
            'jenama.required'       => 'Sila isi jenama toner.',
            'jenis.required'        => 'Sila pilih jenis toner.',
            'kuantiti_ada.min'      => 'Kuantiti tidak boleh negatif.',
            'kuantiti_minimum.min'  => 'Kuantiti minimum sekurang-kurangnya 1.',
        ]);

        $data = [
            'model_toner'      => $this->model_toner,
            'jenama'           => $this->jenama,
            'jenis'            => $this->jenis,
            'warna'            => $this->warna ?: null,
            'kuantiti_ada'     => $this->kuantiti_ada,
            'kuantiti_minimum' => $this->kuantiti_minimum,
        ];

        if ($this->stok_id) {
            StokToner::findOrFail($this->stok_id)->update($data);
        } else {
            StokToner::create($data);
        }

        LogToner::create([
            'permohonan_id' => 0,
            'pengguna_id'   => Auth::id(),
            'tindakan'      => 'stock_updated',
            'keterangan'    => "Stok dikemaskini: {$this->model_toner} ({$this->jenis}) — {$this->kuantiti_ada} unit.",
            'created_at'    => now(),
        ]);

        $this->showModal = false;
        $this->dispatch('berjaya', mesej: 'Stok toner berjaya dikemaskini.');
    }

    public function render()
    {
        $stok = StokToner::orderBy('model_toner')->get();

        return view('livewire.m02.admin.inventori-stok', compact('stok'))
            ->layout('layouts.admin', ['title' => 'Inventori Stok Toner']);
    }
}
```
