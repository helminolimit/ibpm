<?php

namespace App\Livewire\Pentadbir\M04;

use App\Enums\RolePengguna;
use App\Enums\StatusPermohonanPortal;
use App\Mail\TugasanPortalBaru;
use App\Models\LogAuditPortal;
use App\Models\NotifikasiPortal;
use App\Models\PermohonanPortal;
use App\Models\TugasanPortal;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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

    // Tugasan properties
    public ?string $permohonanIdTugasan = null;

    public string $teknisianId = '';

    public string $notaTugasan = '';

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

    public function bukaTugasan(string $id): void
    {
        $this->permohonanIdTugasan = $id;
        $this->teknisianId = '';
        $this->notaTugasan = '';
        $this->resetErrorBag();
        Flux::modal('tugaskan-pembangun')->show();
    }

    public function tugaskanPembangun(): void
    {
        $this->validate([
            'permohonanIdTugasan' => ['required', 'exists:permohonan_portals,id'],
            'teknisianId' => ['required', 'exists:users,id'],
            'notaTugasan' => ['nullable', 'string', 'max:500'],
        ]);

        $permohonan = PermohonanPortal::findOrFail($this->permohonanIdTugasan);
        $teknisian = User::findOrFail($this->teknisianId);

        $tugasan = TugasanPortal::create([
            'permohonan_portal_id' => $permohonan->id,
            'teknisian_id' => $teknisian->id,
            'ditugaskan_oleh' => Auth::id(),
            'nota_tugasan' => $this->notaTugasan ?: null,
            'status_tugasan' => 'baharu',
        ]);

        Mail::to($teknisian->email)->queue(new TugasanPortalBaru($tugasan));

        NotifikasiPortal::create([
            'pengguna_id' => $teknisian->id,
            'permohonan_portal_id' => $permohonan->id,
            'jenis' => 'tugasan_baru',
            'mesej' => "Anda ditugaskan untuk permohonan {$permohonan->no_tiket}.",
        ]);

        LogAuditPortal::create([
            'permohonan_portal_id' => $permohonan->id,
            'pengguna_id' => Auth::id(),
            'tindakan' => 'tugasan_dibuat',
            'butiran' => ['teknisian' => $teknisian->name, 'nota' => $this->notaTugasan],
            'modul' => 'M04',
            'ip_address' => request()->ip(),
        ]);

        $this->reset('permohonanIdTugasan', 'teknisianId', 'notaTugasan');
        Flux::modal('tugaskan-pembangun')->close();
        Flux::toast(text: 'Tugasan berjaya ditetapkan.', variant: 'success');
    }

    /**
     * @return Collection<int, User>
     */
    #[Computed]
    public function senaraiTeknisian(): Collection
    {
        return User::where('role', RolePengguna::Teknician)
            ->orWhere('role', RolePengguna::Pentadbir)
            ->orderBy('name')
            ->get(['id', 'name', 'role']);
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
