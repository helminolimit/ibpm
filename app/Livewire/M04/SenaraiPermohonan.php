<?php

namespace App\Livewire\M04;

use App\Enums\StatusPermohonanPortal;
use App\Models\PermohonanPortal;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Permohonan Kemaskini Portal Saya')]
class SenaraiPermohonan extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filterStatus = '';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function records()
    {
        return PermohonanPortal::query()
            ->where('pemohon_id', Auth::id())
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('no_tiket', 'like', "%{$this->search}%")
                        ->orWhere('url_halaman', 'like', "%{$this->search}%")
                        ->orWhere('butiran_kemaskini', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function statusOptions(): array
    {
        return collect(StatusPermohonanPortal::cases())
            ->mapWithKeys(fn ($s) => [$s->value => $s->label()])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.m04.senarai-permohonan');
    }
}
