<?php

namespace App\Livewire\Permohonan;

use App\Models\AduanIct;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Senarai Aduan Saya')]
class SenaraiAduan extends Component
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
    private array $allowedSorts = ['no_tiket', 'created_at', 'updated_at', 'status'];

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
    public function aduan(): LengthAwarePaginator
    {
        return AduanIct::query()
            ->where('user_id', Auth::id())
            ->with('kategori')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('no_tiket', 'like', "%{$this->search}%")
                    ->orWhere('tajuk', 'like', "%{$this->search}%");
            }))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.permohonan.senarai-aduan');
    }
}
