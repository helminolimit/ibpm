<?php

namespace App\Livewire\M06;

use App\Enums\StatusPermohonanEmel;
use App\Models\PermohonanEmel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Senarai Permohonan Kumpulan Emel')]
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

    /** @var string[] */
    private array $allowedSorts = ['no_tiket', 'created_at', 'status'];

    public function sort(string $column): void
    {
        if (! in_array($column, $this->allowedSorts)) {
            return;
        }

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
    public function permohonan(): LengthAwarePaginator
    {
        return PermohonanEmel::query()
            ->where('user_id', Auth::id())
            ->with(['kumpulanEmel', 'ahliKumpulan'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('no_tiket', 'like', "%{$this->search}%")
                    ->orWhereHas('kumpulanEmel', fn ($q) => $q->where('nama_kumpulan', 'like', "%{$this->search}%"));
            }))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    #[Computed]
    public function statuses(): array
    {
        return StatusPermohonanEmel::cases();
    }

    public function render()
    {
        return view('livewire.m06.senarai-permohonan');
    }
}
