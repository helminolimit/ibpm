<?php

namespace App\Livewire\Pentadbir\M04;

use App\Enums\StatusPermohonanPortal;
use App\Models\PermohonanPortal;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Panel Pentadbir — Kemaskini Portal')]
class PanelPermohonan extends Component
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

    public int $perPage = 20;

    /** @var string[] */
    private array $allowedSorts = ['no_tiket', 'created_at', 'status'];

    public ?string $permohonanIdTerpilih = null;

    public string $statusBaru = '';

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

    public function bukaPilihStatus(string $id): void
    {
        $permohonan = PermohonanPortal::findOrFail($id);
        $this->permohonanIdTerpilih = $id;
        $this->statusBaru = $permohonan->status->value;
        $this->resetErrorBag();
        Flux::modal('kemaskini-status')->show();
    }

    public function kemaskiniStatus(): void
    {
        $this->validate([
            'permohonanIdTerpilih' => ['required', 'exists:permohonan_portals,id'],
            'statusBaru' => ['required', Rule::enum(StatusPermohonanPortal::class)],
        ]);

        $permohonan = PermohonanPortal::with('pemohon')->findOrFail($this->permohonanIdTerpilih);

        $statusOrder = [
            StatusPermohonanPortal::Diterima->value => 0,
            StatusPermohonanPortal::DalamProses->value => 1,
            StatusPermohonanPortal::Selesai->value => 2,
        ];

        if ($statusOrder[$this->statusBaru] < $statusOrder[$permohonan->status->value]) {
            $this->addError('statusBaru', 'Status tidak boleh dikemaskini ke belakang.');

            return;
        }

        if ($this->statusBaru === $permohonan->status->value) {
            Flux::modal('kemaskini-status')->close();

            return;
        }

        $permohonan->update([
            'status' => $this->statusBaru,
            'pentadbir_id' => Auth::id(),
            'tarikh_selesai' => $this->statusBaru === StatusPermohonanPortal::Selesai->value ? now() : null,
        ]);

        $this->reset('permohonanIdTerpilih', 'statusBaru');
        Flux::modal('kemaskini-status')->close();
        Flux::toast(text: 'Status berjaya dikemaskini.', variant: 'success');
    }

    public function statusOptions(): array
    {
        return collect(StatusPermohonanPortal::cases())
            ->mapWithKeys(fn ($s) => [$s->value => $s->label()])
            ->toArray();
    }

    #[Computed]
    public function records(): LengthAwarePaginator
    {
        return PermohonanPortal::query()
            ->with('pemohon')
            ->when($this->search, fn ($q) => $q->carian($this->search))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render(): View
    {
        return view('livewire.pentadbir.m04.panel-permohonan');
    }
}
