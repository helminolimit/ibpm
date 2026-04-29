<?php

namespace App\Livewire\M04;

use App\Models\PermohonanPortal;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Sejarah Permohonan')]
class SejarahPermohonan extends Component
{
    use WithPagination;

    #[Url]
    public string $carian = '';

    public function updatedCarian(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function senarai()
    {
        return PermohonanPortal::milikPemohon()
            ->when($this->carian, fn ($q) => $q->carian($this->carian))
            ->latest()
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.m04.sejarah-permohonan');
    }
}
