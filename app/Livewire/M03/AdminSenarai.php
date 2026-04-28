<?php

namespace App\Livewire\M03;

use App\Enums\StatusPermohonanPenamatan;
use App\Models\PermohonanPenamatan;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Senarai Penamatan Akaun — Pentadbir')]
class AdminSenarai extends Component
{
    use WithPagination;

    #[Url]
    public string $filterStatus = '';

    #[Url]
    public string $carian = '';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    public int $perPage = 20;

    private array $allowedSorts = ['no_tiket', 'created_at', 'tarikh_berkuat_kuasa', 'status'];

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

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedCarian(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    /** @return array<string, string> */
    public function statusOptions(): array
    {
        return collect(StatusPermohonanPenamatan::cases())
            ->mapWithKeys(fn ($s) => [$s->value => $s->label()])
            ->toArray();
    }

    #[Computed]
    public function senarai()
    {
        return PermohonanPenamatan::with(['pemohon', 'penggunaSasaran'])
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->carian, fn ($q) => $q
                ->where('no_tiket', 'like', '%'.$this->carian.'%')
                ->orWhereHas('pemohon', fn ($u) => $u->where('name', 'like', '%'.$this->carian.'%'))
                ->orWhere('id_login_komputer', 'like', '%'.$this->carian.'%')
            )
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.m03.admin-senarai');
    }
}
