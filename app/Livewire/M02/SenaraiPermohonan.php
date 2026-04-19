<?php

namespace App\Livewire\M02;

use App\Enums\StatusPermohonanToner;
use App\Models\PermohonanToner;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Senarai Permohonan Toner')]
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

    private const ALLOWED_SORTS = ['no_tiket', 'model_pencetak', 'status', 'created_at'];

    public function sort(string $column): void
    {
        if (! in_array($column, self::ALLOWED_SORTS, true)) {
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
        $user = Auth::user();
        $isAdmin = in_array($user->role, ['admin', 'superadmin'], true);

        return PermohonanToner::query()
            ->when(! $isAdmin, fn ($q) => $q->where('user_id', $user->id))
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('no_tiket', 'like', "%{$this->search}%")
                    ->orWhere('model_pencetak', 'like', "%{$this->search}%");
            }))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    #[Computed]
    public function statuses(): array
    {
        return StatusPermohonanToner::cases();
    }

    public function render(): View
    {
        return view('livewire.m02.senarai-permohonan');
    }
}
