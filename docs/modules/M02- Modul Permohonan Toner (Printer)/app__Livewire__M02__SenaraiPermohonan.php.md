# `app/Livewire/M02/SenaraiPermohonan.php`

```php
<?php

namespace App\Livewire\M02;

use App\Models\PermohonanToner;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SenaraiPermohonan extends Component
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
        $permohonan = PermohonanToner::where('pemohon_id', Auth::id())
            ->when($this->carian, fn ($q) =>
                $q->where(function ($q) {
                    $q->where('no_tiket', 'like', "%{$this->carian}%")
                      ->orWhere('model_pencetak', 'like', "%{$this->carian}%");
                })
            )
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->isih === 'terbaru', fn ($q) => $q->latest('submitted_at'))
            ->when($this->isih === 'terlama', fn ($q) => $q->oldest('submitted_at'))
            ->paginate(10);

        return view('livewire.m02.senarai-permohonan', compact('permohonan'))
            ->layout('layouts.app', ['title' => 'Senarai Permohonan Toner']);
    }
}
```
