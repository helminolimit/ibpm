<?php

namespace App\Livewire\Superadmin;

use App\Models\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Log Audit — Superadmin')]
class LogAudit extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filterModule = '';

    public int $perPage = 25;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterModule(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function logs(): LengthAwarePaginator
    {
        return AuditLog::query()
            ->with('user')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('action', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->filterModule, fn ($q) => $q->where('module', $this->filterModule))
            ->orderByDesc('created_at')
            ->paginate($this->perPage);
    }

    /** @return string[] */
    public function modules(): array
    {
        return AuditLog::distinct()->orderBy('module')->pluck('module')->all();
    }

    public function render()
    {
        return view('livewire.superadmin.log-audit');
    }
}
