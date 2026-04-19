<?php

namespace App\Livewire\M02\Admin;

use App\Enums\JenisToner;
use App\Enums\StatusPermohonanToner;
use App\Exports\M02TonerExport;
use App\Models\PermohonanToner;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
#[Title('Laporan Penggunaan Toner')]
class LaporanToner extends Component
{
    use WithPagination;

    #[Url]
    public string $tarikhDari = '';

    #[Url]
    public string $tarikhHingga = '';

    #[Url]
    public string $filterBahagian = '';

    #[Url]
    public string $filterJenis = '';

    #[Url]
    public string $filterStatus = '';

    #[Url]
    public string $search = '';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless(in_array($user->role, ['admin', 'superadmin'], true), 403);

        if ($this->tarikhDari === '') {
            $this->tarikhDari = now()->startOfMonth()->toDateString();
        }

        if ($this->tarikhHingga === '') {
            $this->tarikhHingga = now()->toDateString();
        }
    }

    #[Computed]
    public function permohonan(): LengthAwarePaginator
    {
        return $this->baseQuery()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    /** @return array<string, int> */
    #[Computed]
    public function statistik(): array
    {
        $query = $this->baseQuery();

        return [
            'jumlah' => (clone $query)->count(),
            'diluluskan' => (clone $query)->whereIn('status', [
                StatusPermohonanToner::Diluluskan->value,
                StatusPermohonanToner::Dihantar->value,
            ])->count(),
            'dihantar' => (clone $query)->where('status', StatusPermohonanToner::Dihantar->value)->count(),
            'ditolak' => (clone $query)->where('status', StatusPermohonanToner::Ditolak->value)->count(),
            'jumlahUnit' => (int) (clone $query)->where('status', StatusPermohonanToner::Dihantar->value)->sum('kuantiti_diluluskan'),
        ];
    }

    private function baseQuery(): Builder
    {
        return PermohonanToner::with(['user', 'penghantaran'])
            ->when($this->tarikhDari && $this->tarikhHingga, fn ($q) => $q->whereBetween('created_at', [
                $this->tarikhDari.' 00:00:00',
                $this->tarikhHingga.' 23:59:59',
            ]))
            ->when($this->filterBahagian, fn ($q) => $q->whereHas('user', fn ($uq) => $uq->where('bahagian', $this->filterBahagian)))
            ->when($this->filterJenis, fn ($q) => $q->where('jenis_toner', $this->filterJenis))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->search, fn ($q) => $q->where(fn ($sq) => $sq
                ->where('no_tiket', 'like', "%{$this->search}%")
                ->orWhere('model_pencetak', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$this->search}%"))
            ));
    }

    public function sort(string $column): void
    {
        $allowed = ['no_tiket', 'created_at', 'model_pencetak', 'status'];

        if (! in_array($column, $allowed, true)) {
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

    public function updatedFilterBahagian(): void
    {
        $this->resetPage();
    }

    public function updatedFilterJenis(): void
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

    public function updatedTarikhDari(): void
    {
        $this->resetPage();
    }

    public function updatedTarikhHingga(): void
    {
        $this->resetPage();
    }

    /** @return list<string> */
    public function getBahagianList(): array
    {
        return User::select('bahagian')
            ->whereNotNull('bahagian')
            ->distinct()
            ->orderBy('bahagian')
            ->pluck('bahagian')
            ->toArray();
    }

    /** @return list<JenisToner> */
    public function getJenisList(): array
    {
        return JenisToner::cases();
    }

    /** @return list<StatusPermohonanToner> */
    public function getStatusList(): array
    {
        return StatusPermohonanToner::cases();
    }

    public function exportExcel(): BinaryFileResponse
    {
        return Excel::download(
            new M02TonerExport(
                tarikhDari: $this->tarikhDari,
                tarikhHingga: $this->tarikhHingga,
                filterBahagian: $this->filterBahagian,
                filterJenis: $this->filterJenis,
                filterStatus: $this->filterStatus,
                search: $this->search,
            ),
            'laporan-toner-'.now()->format('Ymd-His').'.xlsx'
        );
    }

    public function exportPdf(): StreamedResponse
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '120');

        $query = $this->baseQuery()->orderBy($this->sortBy, $this->sortDirection);

        $total = $query->count();
        $limited = $total > 500;
        $records = $query->limit(500)->get();

        if ($limited) {
            Flux::toast(
                variant: 'warning',
                text: "PDF dihadkan kepada 500 rekod daripada {$total}. Guna Excel untuk semua rekod."
            );
        }

        $statistik = $this->statistik;

        $content = Pdf::loadView('exports.m02-laporan-toner', [
            'records' => $records,
            'statistik' => $statistik,
            'tarikhDari' => $this->tarikhDari,
            'tarikhHingga' => $this->tarikhHingga,
            'limited' => $limited,
            'total' => $total,
        ])
            ->setPaper('a4', 'landscape')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false)
            ->output();

        return response()->streamDownload(
            fn () => print ($content),
            'laporan-toner-'.now()->format('Ymd-His').'.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    public function render(): View
    {
        return view('livewire.m02.admin.laporan-toner');
    }
}
