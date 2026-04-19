<?php

namespace App\Exports;

use App\Enums\JenisToner;
use App\Enums\StatusPermohonanToner;
use App\Models\PermohonanToner;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class M02TonerExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(
        public readonly string $tarikhDari = '',
        public readonly string $tarikhHingga = '',
        public readonly string $filterBahagian = '',
        public readonly string $filterJenis = '',
        public readonly string $filterStatus = '',
        public readonly string $search = '',
    ) {}

    public function query()
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
            ))
            ->orderBy('created_at');
    }

    /** @return array<int, string> */
    public function headings(): array
    {
        return [
            'Bil',
            'No. Tiket',
            'Tarikh Mohon',
            'Pemohon',
            'Bahagian',
            'Model Pencetak',
            'Jenama / Jenis',
            'Kuantiti Diminta',
            'Kuantiti Dihantar',
            'Status',
            'Tarikh Dihantar',
        ];
    }

    /** @return array<int, mixed> */
    public function map(mixed $record): array
    {
        static $bil = 0;
        $bil++;

        $jenisToner = $record->jenis_toner instanceof JenisToner
            ? $record->jenis_toner->label()
            : (string) $record->jenis_toner;

        $status = $record->status instanceof StatusPermohonanToner
            ? $record->status->label()
            : (string) $record->status;

        return [
            $bil,
            $record->no_tiket,
            $record->created_at->format('d/m/Y'),
            $record->user->name ?? '—',
            $record->user->bahagian ?? '—',
            $record->model_pencetak,
            "{$record->jenama_toner} / {$jenisToner}",
            $record->kuantiti,
            $record->kuantiti_diluluskan ?? '—',
            $status,
            $record->penghantaran?->tarikh_hantar?->format('d/m/Y') ?? '—',
        ];
    }
}
