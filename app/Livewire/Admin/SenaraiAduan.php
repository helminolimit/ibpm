<?php

namespace App\Livewire\Admin;

use App\Enums\StatusAduan;
use App\Models\AduanIct;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Senarai Aduan — Pentadbir')]
class SenaraiAduan extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filterStatus = '';

    #[Url]
    public string $sortBy = 'status_order';

    #[Url]
    public string $sortDirection = 'asc';

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

    private function baseQuery(): Builder
    {
        $user = Auth::user();

        return AduanIct::query()
            ->with(['kategori', 'user'])
            ->when($user->isPentadbir(), fn ($q) => $q->whereHas(
                'kategori',
                fn ($k) => $k->where('unit_bpm', $user->bahagian)
            ));
    }

    #[Computed]
    public function aduan(): LengthAwarePaginator
    {
        return $this->baseQuery()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('no_tiket', 'like', "%{$this->search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when(
                $this->sortBy === 'status_order',
                fn ($q) => $q->orderByRaw("CASE status WHEN 'baru' THEN 1 WHEN 'dalam_proses' THEN 2 WHEN 'selesai' THEN 3 WHEN 'ditolak' THEN 4 ELSE 5 END"),
                fn ($q) => $q->orderBy($this->sortBy, $this->sortDirection)
            )
            ->paginate($this->perPage);
    }

    #[Computed]
    public function jumlahHariIni(): int
    {
        return $this->baseQuery()->whereDate('created_at', today())->count();
    }

    #[Computed]
    public function jumlahDalamProses(): int
    {
        return $this->baseQuery()->where('status', StatusAduan::DalamProses)->count();
    }

    #[Computed]
    public function jumlahSelesaiBulanIni(): int
    {
        return $this->baseQuery()
            ->where('status', StatusAduan::Selesai)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    #[Computed]
    public function purataMasaPenyelesaian(): string
    {
        $aduanSelesai = $this->baseQuery()
            ->where('status', StatusAduan::Selesai)
            ->with(['statusLogs' => fn ($q) => $q
                ->where('status', StatusAduan::Selesai->value)
                ->orderBy('created_at')
                ->limit(1),
            ])
            ->get(['id', 'created_at']);

        if ($aduanSelesai->isEmpty()) {
            return '-';
        }

        $total = $aduanSelesai->sum(fn ($a) => optional($a->statusLogs->first())?->created_at?->diffInMinutes($a->created_at) ?? 0);
        $avgMinutes = $total / $aduanSelesai->count();

        $hours = (int) floor($avgMinutes / 60);
        $minutes = (int) round($avgMinutes % 60);

        return $hours > 0 ? "{$hours}j {$minutes}m" : "{$minutes}m";
    }

    public function render()
    {
        return view('livewire.admin.senarai-aduan');
    }
}
