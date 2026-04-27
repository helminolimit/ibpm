# `app/Livewire/M02/Admin/RekodHantar.php`

```php
<?php

namespace App\Livewire\M02\Admin;

use App\Models\PermohonanToner;
use App\Models\PenghantaranToner;
use App\Models\LogToner;
use App\Notifications\TonerDihantar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RekodHantar extends Component
{
    public PermohonanToner $permohonan;

    public int    $kuantiti_dihantar = 0;
    public string $catatan           = '';

    public function mount(int $id): void
    {
        $this->permohonan = PermohonanToner::with([
            'pemohon', 'stokToner',
        ])->findOrFail($id);

        abort_unless(
            $this->permohonan->status === 'approved',
            403,
            'Permohonan ini tidak layak untuk direkodkan penghantaran.'
        );

        $this->kuantiti_dihantar = $this->permohonan->kuantiti_diluluskan ?? 1;
    }

    public function simpan(): void
    {
        $this->validate([
            'kuantiti_dihantar' => 'required|integer|min:1',
            'catatan'           => 'nullable|string|max:300',
        ], [
            'kuantiti_dihantar.required' => 'Sila isi kuantiti toner yang dihantar.',
            'kuantiti_dihantar.min'      => 'Kuantiti minimum ialah 1.',
        ]);

        DB::transaction(function () {
            // Rekod penghantaran
            PenghantaranToner::create([
                'permohonan_id'    => $this->permohonan->id,
                'dihantar_oleh'    => Auth::id(),
                'kuantiti_dihantar'=> $this->kuantiti_dihantar,
                'catatan'          => $this->catatan ?: null,
                'tarikh_hantar'    => now(),
            ]);

            // Kurangkan stok toner
            if ($this->permohonan->stokToner) {
                $this->permohonan->stokToner->kurangkanStok($this->kuantiti_dihantar);
            }

            // Kemaskini status permohonan
            $this->permohonan->update(['status' => 'delivered']);

            // Log
            LogToner::create([
                'permohonan_id' => $this->permohonan->id,
                'pengguna_id'   => Auth::id(),
                'tindakan'      => 'delivered',
                'keterangan'    => "Toner dihantar: {$this->kuantiti_dihantar} unit.",
                'created_at'    => now(),
            ]);
        });

        // Notifikasi kepada pemohon
        $this->permohonan->pemohon->notify(new TonerDihantar($this->permohonan));

        session()->flash('berjaya', 'Rekod penghantaran berjaya disimpan.');
        $this->redirect(route('admin.m02.senarai'));
    }

    public function render()
    {
        return view('livewire.m02.admin.rekod-hantar')
            ->layout('layouts.admin', ['title' => "Rekod Hantar — {$this->permohonan->no_tiket}"]);
    }
}
```
