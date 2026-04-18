<?php

namespace App\Livewire\Admin;

use App\Enums\StatusAduan;
use App\Models\AduanIct;
use App\Models\KategoriAduan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator as ConcretePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Title('Jana Laporan Aduan')]
class LaporanAduan extends Component
{
    use WithPagination;

    #[Url]
    public string $tarikhDari = '';

    #[Url]
    public string $tarikhHingga = '';

    #[Url]
    public string $filterKategori = '';

    #[Url]
    public string $filterStatus = '';

    #[Url]
    public string $filterUnit = '';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    public int $perPage = 25;

    public bool $hasGenerated = false;

    public bool $periodoLuasWarning = false;

    /** @var string[] */
    private array $allowedSorts = ['no_tiket', 'created_at', 'tarikh_selesai', 'status'];

    public function mount(): void
    {
        if (empty($this->tarikhDari)) {
            $this->tarikhDari = now()->startOfMonth()->toDateString();
        }

        if (empty($this->tarikhHingga)) {
            $this->tarikhHingga = now()->toDateString();
        }
    }

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

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function janaLaporan(): void
    {
        $this->validate([
            'tarikhDari' => ['required', 'date'],
            'tarikhHingga' => ['required', 'date', 'after_or_equal:tarikhDari'],
        ], [
            'tarikhDari.required' => 'Tarikh dari diperlukan.',
            'tarikhDari.date' => 'Format tarikh tidak sah.',
            'tarikhHingga.required' => 'Tarikh hingga diperlukan.',
            'tarikhHingga.date' => 'Format tarikh tidak sah.',
            'tarikhHingga.after_or_equal' => 'Tarikh hingga mesti selepas atau sama dengan tarikh dari.',
        ]);

        $dari = Carbon::parse($this->tarikhDari);
        $hingga = Carbon::parse($this->tarikhHingga);

        if ($dari->diffInMonths($hingga) > 12) {
            $this->periodoLuasWarning = true;

            return;
        }

        $this->periodoLuasWarning = false;
        $this->doGenerate();
    }

    public function confirmJanaLaporan(): void
    {
        $this->validate([
            'tarikhDari' => ['required', 'date'],
            'tarikhHingga' => ['required', 'date', 'after_or_equal:tarikhDari'],
        ]);

        $this->periodoLuasWarning = false;
        $this->doGenerate();
    }

    public function batalPeriodoLuas(): void
    {
        $this->periodoLuasWarning = false;
    }

    private function doGenerate(): void
    {
        $this->hasGenerated = true;
        $this->resetPage();
    }

    private function filterQuery(): Builder
    {
        $user = Auth::user();

        return AduanIct::query()
            ->when(
                $user->isPentadbir(),
                fn ($q) => $q->whereHas('kategori', fn ($k) => $k->where('unit_bpm', $user->bahagian))
            )
            ->when(
                $user->isSuperadmin() && $this->filterUnit,
                fn ($q) => $q->whereHas('kategori', fn ($k) => $k->where('unit_bpm', $this->filterUnit))
            )
            ->when($this->tarikhDari, fn ($q) => $q->whereDate('created_at', '>=', $this->tarikhDari))
            ->when($this->tarikhHingga, fn ($q) => $q->whereDate('created_at', '<=', $this->tarikhHingga))
            ->when($this->filterKategori, fn ($q) => $q->where('kategori_aduan_id', $this->filterKategori))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus));
    }

    private function baseQuery(): Builder
    {
        return $this->filterQuery()->with(['kategori', 'user', 'pentadbir']);
    }

    #[Computed]
    public function aduan(): LengthAwarePaginator
    {
        if (! $this->hasGenerated) {
            return new ConcretePaginator([], 0, $this->perPage, 1);
        }

        return $this->baseQuery()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    #[Computed]
    public function jumlahAduan(): int
    {
        return $this->hasGenerated ? $this->filterQuery()->count() : 0;
    }

    #[Computed]
    public function jumlahSelesai(): int
    {
        return $this->hasGenerated
            ? $this->filterQuery()->where('status', StatusAduan::Selesai)->count()
            : 0;
    }

    #[Computed]
    public function jumlahDalamProses(): int
    {
        return $this->hasGenerated
            ? $this->filterQuery()->where('status', StatusAduan::DalamProses)->count()
            : 0;
    }

    #[Computed]
    public function jumlahBaru(): int
    {
        return $this->hasGenerated
            ? $this->filterQuery()->where('status', StatusAduan::Baru)->count()
            : 0;
    }

    #[Computed]
    public function purataMasaPenyelesaian(): string
    {
        if (! $this->hasGenerated) {
            return '-';
        }

        $records = $this->filterQuery()
            ->where('status', StatusAduan::Selesai)
            ->whereNotNull('tarikh_selesai')
            ->get(['created_at', 'tarikh_selesai']);

        if ($records->isEmpty()) {
            return '-';
        }

        $totalHari = $records->sum(fn ($a) => $a->created_at->diffInDays($a->tarikh_selesai));
        $avg = $totalHari / $records->count();

        return round($avg, 1).' hari';
    }

    #[Computed]
    public function kadarPenyelesaian(): string
    {
        if (! $this->hasGenerated || $this->jumlahAduan === 0) {
            return '0%';
        }

        return round(($this->jumlahSelesai / $this->jumlahAduan) * 100, 1).'%';
    }

    #[Computed]
    public function pecahanKategori(): Collection
    {
        if (! $this->hasGenerated) {
            return collect();
        }

        return $this->filterQuery()
            ->select('kategori_aduan_id', DB::raw('count(*) as jumlah'))
            ->with('kategori:id,nama')
            ->groupBy('kategori_aduan_id')
            ->orderByDesc('jumlah')
            ->get();
    }

    #[Computed]
    public function kategoriList(): Collection
    {
        $user = Auth::user();

        return KategoriAduan::aktif()
            ->when($user->isPentadbir(), fn ($q) => $q->where('unit_bpm', $user->bahagian))
            ->orderBy('nama')
            ->get();
    }

    #[Computed]
    public function unitList(): Collection
    {
        return KategoriAduan::aktif()
            ->select('unit_bpm')
            ->distinct()
            ->orderBy('unit_bpm')
            ->pluck('unit_bpm');
    }

    public function exportExcel(): BinaryFileResponse
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'laporan_');
        $tmpFileXlsx = $tmpFile.'.xlsx';

        $writer = new Writer;
        $writer->openToFile($tmpFileXlsx);

        $writer->addRow(Row::fromValues([
            'Bil', 'No. Tiket', 'Pemohon', 'Bahagian', 'Kategori',
            'Lokasi', 'Tarikh Mohon', 'Tarikh Selesai', 'Masa (hari)', 'Status', 'Penanggung Jawab',
        ]));

        $i = 1;
        foreach ($this->baseQuery()->cursor() as $aduan) {
            $writer->addRow(Row::fromValues([
                $i++,
                $aduan->no_tiket,
                $aduan->user?->name ?? '-',
                $aduan->user?->bahagian ?? '-',
                $aduan->kategori?->nama ?? '-',
                $aduan->lokasi,
                $aduan->created_at->format('d/m/Y'),
                $aduan->tarikh_selesai?->format('d/m/Y') ?? '-',
                $aduan->tarikh_selesai ? $aduan->created_at->diffInDays($aduan->tarikh_selesai) : '-',
                $aduan->status->label(),
                $aduan->pentadbir?->name ?? '-',
            ]));
        }

        $writer->close();
        @unlink($tmpFile);

        return response()->download(
            $tmpFileXlsx,
            'laporan-aduan-'.now()->format('Ymd-His').'.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend();
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
            Flux::toast(variant: 'warning', text: "PDF dihadkan kepada 500 rekod daripada {$total}. Guna Excel untuk semua rekod.");
        }

        $user = Auth::user();
        $unitLabel = $user->isPentadbir()
            ? $user->bahagian
            : ($this->filterUnit ?: 'Semua Unit');

        $content = Pdf::loadView('exports.laporan-aduan-pdf', [
            'records' => $records,
            'stats' => [
                'jumlahAduan' => $this->jumlahAduan,
                'jumlahSelesai' => $this->jumlahSelesai,
                'jumlahDalamProses' => $this->jumlahDalamProses,
                'jumlahBaru' => $this->jumlahBaru,
                'purataMasaPenyelesaian' => $this->purataMasaPenyelesaian,
                'kadarPenyelesaian' => $this->kadarPenyelesaian,
            ],
            'pecahanKategori' => $this->pecahanKategori,
            'tarikhDari' => $this->tarikhDari,
            'tarikhHingga' => $this->tarikhHingga,
            'unitLabel' => $unitLabel,
            'limited' => $limited,
            'total' => $total,
        ])
            ->setPaper('a4', 'landscape')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false)
            ->output();

        return response()->streamDownload(
            fn () => print ($content),
            'laporan-aduan-'.now()->format('Ymd-His').'.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    public function render()
    {
        return view('livewire.admin.laporan-aduan');
    }
}
