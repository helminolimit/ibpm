# UC08 — Jana Laporan Kemaskini Portal

Modul: M04 Kemaskini Portal  
Pelakon: Superadmin

---

## Keperluan

- Tapis laporan mengikut: tarikh, status, jenis perubahan, pemohon
- Papar ringkasan statistik (jumlah, status, masa purata selesai)
- Export ke PDF dan Excel
- Guna `maatwebsite/excel` untuk Excel dan `barryvdh/laravel-dompdf` untuk PDF

---

## Package

```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

---

## Route

```php
Route::middleware(['auth', 'peranan:superadmin'])->prefix('superadmin')->group(function () {
    Route::get('/m04/laporan', [LaporanPortalController::class, 'index'])->name('superadmin.m04.laporan');
    Route::get('/m04/laporan/export-excel', [LaporanPortalController::class, 'exportExcel'])->name('superadmin.m04.laporan.excel');
    Route::get('/m04/laporan/export-pdf', [LaporanPortalController::class, 'exportPdf'])->name('superadmin.m04.laporan.pdf');
});
```

---

## Controller

```php
// app/Http/Controllers/LaporanPortalController.php

public function index(Request $request)
{
    $query = PermohonanPortal::with('pemohon')
        ->when($request->dari, fn($q) => $q->whereDate('tarikh_mohon', '>=', $request->dari))
        ->when($request->hingga, fn($q) => $q->whereDate('tarikh_mohon', '<=', $request->hingga))
        ->when($request->status, fn($q, $s) => $q->where('status', $s))
        ->when($request->jenis, fn($q, $j) => $q->where('jenis_perubahan', $j));

    $senarai = $query->latest()->paginate(20);

    $statistik = [
        'jumlah'         => $query->count(),
        'diterima'       => $query->where('status', 'diterima')->count(),
        'dalam_proses'   => $query->where('status', 'dalam_proses')->count(),
        'selesai'        => $query->where('status', 'selesai')->count(),
        'masa_purata'    => $query->whereNotNull('tarikh_selesai')
                                  ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, tarikh_mohon, tarikh_selesai)) as purata')
                                  ->value('purata'),
    ];

    return view('superadmin.m04.laporan', compact('senarai', 'statistik'));
}

public function exportExcel(Request $request)
{
    return Excel::download(
        new LaporanPortalExport($request->all()),
        'laporan-kemaskini-portal-' . now()->format('Ymd') . '.xlsx'
    );
}

public function exportPdf(Request $request)
{
    $data = PermohonanPortal::with('pemohon')
        ->when($request->dari, fn($q) => $q->whereDate('tarikh_mohon', '>=', $request->dari))
        ->when($request->hingga, fn($q) => $q->whereDate('tarikh_mohon', '<=', $request->hingga))
        ->latest()->get();

    $pdf = Pdf::loadView('superadmin.m04.laporan-pdf', compact('data'));
    return $pdf->download('laporan-kemaskini-portal-' . now()->format('Ymd') . '.pdf');
}
```

---

## Export Class (Excel)

```php
// app/Exports/LaporanPortalExport.php

class LaporanPortalExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private array $penapis) {}

    public function query(): Builder
    {
        return PermohonanPortal::with('pemohon')
            ->when($this->penapis['dari'] ?? null, fn($q) => $q->whereDate('tarikh_mohon', '>=', $this->penapis['dari']))
            ->when($this->penapis['hingga'] ?? null, fn($q) => $q->whereDate('tarikh_mohon', '<=', $this->penapis['hingga']))
            ->latest();
    }

    public function headings(): array
    {
        return ['No. Tiket', 'Pemohon', 'URL Halaman', 'Jenis', 'Status', 'Tarikh Mohon', 'Tarikh Selesai'];
    }

    public function map($row): array
    {
        return [
            $row->no_tiket,
            $row->pemohon->nama,
            $row->url_halaman,
            ucfirst($row->jenis_perubahan),
            ucfirst(str_replace('_', ' ', $row->status)),
            $row->tarikh_mohon->format('d/m/Y'),
            $row->tarikh_selesai?->format('d/m/Y') ?? '—',
        ];
    }
}
```

---

## Blade View (laporan)

```blade
{{-- resources/views/superadmin/m04/laporan.blade.php --}}
<div>
    {{-- Penapis --}}
    <form method="GET">
        <input type="date" name="dari" value="{{ request('dari') }}" />
        <input type="date" name="hingga" value="{{ request('hingga') }}" />
        <select name="status">
            <option value="">Semua Status</option>
            <option value="diterima">Diterima</option>
            <option value="dalam_proses">Dalam Proses</option>
            <option value="selesai">Selesai</option>
        </select>
        <button type="submit">Tapis</button>
    </form>

    {{-- Statistik --}}
    <div>
        <span>Jumlah: {{ $statistik['jumlah'] }}</span>
        <span>Diterima: {{ $statistik['diterima'] }}</span>
        <span>Dalam Proses: {{ $statistik['dalam_proses'] }}</span>
        <span>Selesai: {{ $statistik['selesai'] }}</span>
        <span>Masa Purata Selesai: {{ round($statistik['masa_purata'] ?? 0) }} jam</span>
    </div>

    {{-- Export --}}
    <a href="{{ route('superadmin.m04.laporan.excel', request()->all()) }}">Export Excel</a>
    <a href="{{ route('superadmin.m04.laporan.pdf', request()->all()) }}">Export PDF</a>

    {{-- Jadual --}}
    <table>
        <thead>
            <tr>
                <th>No. Tiket</th>
                <th>Pemohon</th>
                <th>URL Halaman</th>
                <th>Jenis</th>
                <th>Status</th>
                <th>Tarikh Mohon</th>
            </tr>
        </thead>
        <tbody>
            @forelse($senarai as $item)
            <tr>
                <td>{{ $item->no_tiket }}</td>
                <td>{{ $item->pemohon->nama }}</td>
                <td>{{ $item->url_halaman }}</td>
                <td>{{ ucfirst($item->jenis_perubahan) }}</td>
                <td>@include('m04._badge', ['permohonan' => $item])</td>
                <td>{{ $item->tarikh_mohon->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="6">Tiada rekod.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $senarai->links() }}
</div>
```

---

## Larangan

- Jangan biar pentadbir biasa akses route laporan superadmin
- Jangan export tanpa penapis tarikh jika data melebihi 1000 rekod
- Jangan hardcode nama fail export

---

## Kriteria Penerimaan

- [ ] Laporan boleh ditapis mengikut tarikh, status, jenis
- [ ] Statistik ringkasan dipapar dengan betul
- [ ] Export Excel mengandungi semua kolum yang ditentukan
- [ ] Export PDF berjaya dimuat turun
- [ ] Akses terhad kepada Superadmin sahaja

---

*ICTServe M04 | UC08 | April 2026*
