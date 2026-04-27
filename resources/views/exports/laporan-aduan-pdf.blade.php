<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 15mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        h1 { font-size: 14px; margin: 0 0 4px 0; }
        h2 { font-size: 11px; margin: 14px 0 6px 0; color: #374151; }
        .meta { font-size: 9px; color: #6b7280; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th { background: #f3f4f6; text-align: left; padding: 5px 7px; border: 1px solid #d1d5db; font-size: 9px; }
        td { padding: 4px 7px; border: 1px solid #e5e7eb; vertical-align: top; }
        tr { page-break-inside: avoid; }
        .even td { background: #f9fafb; }
        .stat-label { font-size: 9px; color: #6b7280; }
        .stat-value { font-size: 12px; font-weight: bold; }
        .stat-table td { border: 1px solid #e5e7eb; padding: 6px 10px; }
        .right { text-align: right; }
        .center { text-align: center; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 4px; font-size: 9px; }
        .badge-baru { background: #dbeafe; color: #1e40af; }
        .badge-dalam_proses { background: #fef9c3; color: #854d0e; }
        .badge-selesai { background: #dcfce7; color: #166534; }
        .badge-ditolak { background: #fee2e2; color: #991b1b; }
        .badge-dibatalkan { background: #f4f4f5; color: #52525b; }
        .limit-notice { background: #fef3c7; border: 1px solid #f59e0b; padding: 5px 8px; margin-bottom: 10px; font-size: 9px; color: #78350f; }
    </style>
</head>
<body>

    <h1>LAPORAN ADUAN ICT</h1>
    <p class="meta">
        Unit: {{ $unitLabel }} &nbsp;|&nbsp;
        Tempoh: {{ \Carbon\Carbon::parse($tarikhDari)->format('d/m/Y') }} hingga {{ \Carbon\Carbon::parse($tarikhHingga)->format('d/m/Y') }} &nbsp;|&nbsp;
        Dicetak: {{ now()->format('d/m/Y H:i') }}
        {{ ($limited ?? false) ? ' | Dihadkan kepada 500 daripada '.$total.' rekod' : '' }}
    </p>

    @if ($limited ?? false)
        <div class="limit-notice">
            Amaran: PDF ini dihadkan kepada 500 rekod daripada {{ $total }} rekod. Sila eksport ke Excel untuk mendapatkan semua rekod.
        </div>
    @endif

    {{-- Section A: Ringkasan Statistik --}}
    <h2>Bahagian A — Ringkasan Statistik</h2>
    <table class="stat-table" style="width: 60%;">
        <tr>
            <td><span class="stat-label">Jumlah Aduan</span><br><span class="stat-value">{{ $stats['jumlahAduan'] }}</span></td>
            <td><span class="stat-label">Aduan Selesai</span><br><span class="stat-value">{{ $stats['jumlahSelesai'] }}</span></td>
            <td><span class="stat-label">Kadar Penyelesaian</span><br><span class="stat-value">{{ $stats['kadarPenyelesaian'] }}</span></td>
        </tr>
        <tr>
            <td><span class="stat-label">Dalam Tindakan</span><br><span class="stat-value">{{ $stats['jumlahDalamProses'] }}</span></td>
            <td><span class="stat-label">Aduan Baru</span><br><span class="stat-value">{{ $stats['jumlahBaru'] }}</span></td>
            <td><span class="stat-label">Purata Masa Penyelesaian</span><br><span class="stat-value">{{ $stats['purataMasaPenyelesaian'] }}</span></td>
        </tr>
    </table>

    {{-- Section B: Pecahan Kategori --}}
    @if ($pecahanKategori->isNotEmpty())
        <h2>Bahagian B — Pecahan mengikut Kategori</h2>
        <table style="width: 50%;">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th style="text-align: right;">Bilangan</th>
                    <th style="text-align: right;">%</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pecahanKategori as $i => $item)
                    <tr class="{{ $i % 2 === 1 ? 'even' : '' }}">
                        <td>{{ $item->kategori?->nama ?? '(Tanpa Kategori)' }}</td>
                        <td style="text-align: right;">{{ $item->jumlah }}</td>
                        <td style="text-align: right;">{{ $stats['jumlahAduan'] > 0 ? round(($item->jumlah / $stats['jumlahAduan']) * 100, 1).'%' : '0%' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Section C: Senarai Terperinci --}}
    <h2>Bahagian C — Senarai Aduan Terperinci</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th style="width: 90px;">No. Tiket</th>
                <th style="width: 100px;">Pemohon</th>
                <th style="width: 80px;">Bahagian</th>
                <th style="width: 90px;">Kategori</th>
                <th style="width: 100px;">Lokasi</th>
                <th style="width: 65px;">Tgk Mohon</th>
                <th style="width: 65px;">Tgk Selesai</th>
                <th style="width: 45px;">Status</th>
                <th>P. Jawab</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $i => $aduan)
                <tr class="{{ $i % 2 === 1 ? 'even' : '' }}">
                    <td class="right">{{ $i + 1 }}</td>
                    <td style="font-family: monospace;">{{ $aduan->no_tiket }}</td>
                    <td>{{ $aduan->user?->name ?? '-' }}</td>
                    <td>{{ $aduan->user?->bahagian ?? '-' }}</td>
                    <td>{{ $aduan->kategori?->nama ?? '-' }}</td>
                    <td>{{ $aduan->lokasi }}</td>
                    <td class="center">{{ $aduan->created_at->format('d/m/Y') }}</td>
                    <td class="center">{{ $aduan->tarikh_selesai?->format('d/m/Y') ?? '-' }}</td>
                    <td><span class="badge badge-{{ $aduan->status->value }}">{{ $aduan->status->label() }}</span></td>
                    <td>{{ $aduan->pentadbir?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="center" style="padding: 12px; color: #6b7280;">Tiada rekod.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
