<?php

namespace App\Livewire\Superadmin;

use App\Enums\StatusPermohonanPortal;
use App\Models\PermohonanPortal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Title('Laporan Kemaskini Portal')]
class LaporanPortal extends Component
{
    use WithPagination;

    #[Url]
    public string $dari = '';

    #[Url]
    public string $hingga = '';

    #[Url]
    public string $filterStatus = '';

    #[Url]
    public string $filterJenis = '';

    public int $perPage = 20;

    public function updatedDari(): void
    {
        $this->resetPage();
    }

    public function updatedHingga(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterJenis(): void
    {
        $this->resetPage();
    }

    public function resetPenapis(): void
    {
        $this->reset('dari', 'hingga', 'filterStatus', 'filterJenis');
        $this->resetPage();
    }

    private function baseQuery(): Builder
    {
        return PermohonanPortal::query()
            ->with('pemohon')
            ->when($this->dari, fn ($q) => $q->whereDate('created_at', '>=', $this->dari))
            ->when($this->hingga, fn ($q) => $q->whereDate('created_at', '<=', $this->hingga))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterJenis, fn ($q) => $q->where('jenis_perubahan', $this->filterJenis))
            ->latest();
    }

    /**
     * @return array{jumlah: int, diterima: int, dalam_proses: int, selesai: int, masa_purata: float|null}
     */
    #[Computed]
    public function statistik(): array
    {
        $baseQuery = PermohonanPortal::query()
            ->when($this->dari, fn ($q) => $q->whereDate('created_at', '>=', $this->dari))
            ->when($this->hingga, fn ($q) => $q->whereDate('created_at', '<=', $this->hingga))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterJenis, fn ($q) => $q->where('jenis_perubahan', $this->filterJenis));

        $jumlah = $baseQuery->count();

        $statusCounts = PermohonanPortal::query()
            ->when($this->dari, fn ($q) => $q->whereDate('created_at', '>=', $this->dari))
            ->when($this->hingga, fn ($q) => $q->whereDate('created_at', '<=', $this->hingga))
            ->when($this->filterJenis, fn ($q) => $q->where('jenis_perubahan', $this->filterJenis))
            ->selectRaw("
                COUNT(CASE WHEN status = 'diterima' THEN 1 END) as diterima,
                COUNT(CASE WHEN status = 'dalam_proses' THEN 1 END) as dalam_proses,
                COUNT(CASE WHEN status = 'selesai' THEN 1 END) as selesai
            ")
            ->first();

        // Calculate average completion time using PHP for database portability
        $masaPurata = null;
        $selesaiRecords = PermohonanPortal::query()
            ->when($this->dari, fn ($q) => $q->whereDate('created_at', '>=', $this->dari))
            ->when($this->hingga, fn ($q) => $q->whereDate('created_at', '<=', $this->hingga))
            ->when($this->filterJenis, fn ($q) => $q->where('jenis_perubahan', $this->filterJenis))
            ->whereNotNull('tarikh_selesai')
            ->get(['created_at', 'tarikh_selesai']);

        if ($selesaiRecords->isNotEmpty()) {
            $totalHours = $selesaiRecords->sum(fn ($r) => $r->created_at->diffInHours($r->tarikh_selesai));
            $masaPurata = round($totalHours / $selesaiRecords->count(), 1);
        }

        return [
            'jumlah' => $jumlah,
            'diterima' => (int) $statusCounts->diterima,
            'dalam_proses' => (int) $statusCounts->dalam_proses,
            'selesai' => (int) $statusCounts->selesai,
            'masa_purata' => $masaPurata,
        ];
    }

    #[Computed]
    public function records(): LengthAwarePaginator
    {
        return $this->baseQuery()->paginate($this->perPage);
    }

    public function exportExcel(): StreamedResponse
    {
        $data = $this->baseQuery()->get();

        return response()->streamDownload(function () use ($data) {
            $writer = new Writer;
            $writer->openToBrowser('laporan.xlsx');

            $writer->addRow(Row::fromValues([
                'No. Tiket',
                'Pemohon',
                'URL Halaman',
                'Jenis Perubahan',
                'Status',
                'Tarikh Mohon',
                'Tarikh Selesai',
            ]));

            foreach ($data as $item) {
                $writer->addRow(Row::fromValues([
                    $item->no_tiket,
                    $item->pemohon->name,
                    $item->url_halaman,
                    ucfirst($item->jenis_perubahan),
                    $item->status->label(),
                    $item->created_at->format('d/m/Y'),
                    $item->tarikh_selesai?->format('d/m/Y') ?? '-',
                ]));
            }

            $writer->close();
        }, 'laporan-kemaskini-portal-'.now()->format('Ymd').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportPdf(): Response
    {
        $data = $this->baseQuery()->get();
        $statistik = $this->statistik;

        $pdf = Pdf::loadView('livewire.superadmin.laporan-portal-pdf', [
            'data' => $data,
            'statistik' => $statistik,
            'dari' => $this->dari,
            'hingga' => $this->hingga,
        ]);

        return $pdf->download('laporan-kemaskini-portal-'.now()->format('Ymd').'.pdf');
    }

    public function statusOptions(): array
    {
        return collect(StatusPermohonanPortal::cases())
            ->mapWithKeys(fn ($s) => [$s->value => $s->label()])
            ->toArray();
    }

    public function render(): View
    {
        return view('livewire.superadmin.laporan-portal');
    }
}
