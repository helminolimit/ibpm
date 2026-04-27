# `app/Livewire/M02/Admin/SenaraiAdmin.php`

```php
<?php

namespace App\Livewire\M02\Admin;

use App\Models\PermohonanToner;
use Livewire\Component;
use Livewire\WithPagination;

class SenaraiAdmin extends Component
{
    use WithPagination;

    public string $carian  = '';
    public string $status  = '';
    public string $isih    = 'terbaru';

    public function updatingCarian(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $permohonan = PermohonanToner::with('pemohon')
            ->when($this->carian, fn ($q) =>
                $q->where(function ($q) {
                    $q->where('no_tiket', 'like', "%{$this->carian}%")
                      ->orWhere('model_pencetak', 'like', "%{$this->carian}%")
                      ->orWhereHas('pemohon', fn ($q) =>
                          $q->where('name', 'like', "%{$this->carian}%")
                      );
                })
            )
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->isih === 'terbaru', fn ($q) => $q->latest('submitted_at'))
            ->when($this->isih === 'terlama', fn ($q) => $q->oldest('submitted_at'))
            ->paginate(15);

        return view('livewire.m02.admin.senarai-admin', compact('permohonan'))
            ->layout('layouts.admin', ['title' => 'Urus Permohonan Toner']);
    }
}
```
