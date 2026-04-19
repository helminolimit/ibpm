<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 15mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        h2 { font-size: 14px; margin: 0 0 4px 0; }
        .meta { font-size: 9px; color: #6b7280; margin-bottom: 10px; }
        .stats { border-collapse: collapse; width: 100%; margin-bottom: 12px; }
        .stats td { padding: 4px 8px; border: 1px solid #d1d5db; font-size: 10px; }
        .stats .label { background: #f3f4f6; font-weight: bold; width: 20%; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1f2937; color: #fff; text-align: left; padding: 5px 6px; border: 1px solid #374151; font-size: 9px; }
        td { padding: 4px 6px; border: 1px solid #e5e7eb; font-size: 9px; }
        .even td { background-color: #f9fafb; }
        tr { page-break-inside: avoid; }
        .badge { display: inline-block; padding: 1px 5px; border-radius: 4px; font-size: 8px; font-weight: bold; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-teal { background: #ccfbf1; color: #134e4a; }
        .badge-orange { background: #ffedd5; color: #9a3412; }
        .badge-zinc { background: #f4f4f5; color: #3f3f46; }
    </style>
</head>
<body>
    <h2>Laporan Penggunaan Toner</h2>
    <p class="meta">
        Tempoh: {{ $tarikhDari ? \Carbon\Carbon::parse($tarikhDari)->format('d/m/Y') : '—' }}
        hingga {{ $tarikhHingga ? \Carbon\Carbon::parse($tarikhHingga)->format('d/m/Y') : '—' }}
        &nbsp;|&nbsp; Dicetak pada: {{ now()->format('d/m/Y H:i') }}
        {{ ($limited ?? false) ? ' (dihadkan daripada '.$total.' rekod)' : '' }}
    </p>

    {{-- Summary statistics --}}
    <table class="stats">
        <tr>
            <td class="label">Jumlah Permohonan</td>
            <td>{{ $statistik['jumlah'] }}</td>
            <td class="label">Diluluskan</td>
            <td>{{ $statistik['diluluskan'] }}</td>
            <td class="label">Dihantar</td>
            <td>{{ $statistik['dihantar'] }}</td>
            <td class="label">Ditolak</td>
            <td>{{ $statistik['ditolak'] }}</td>
            <td class="label">Jumlah Unit</td>
            <td>{{ $statistik['jumlahUnit'] }}</td>
        </tr>
    </table>

    {{-- Detail table --}}
    <table>
        <thead>
            <tr>
                <th style="width:3%">#</th>
                <th style="width:10%">No. Tiket</th>
                <th style="width:8%">Tarikh Mohon</th>
                <th style="width:13%">Pemohon</th>
                <th style="width:10%">Bahagian</th>
                <th style="width:12%">Model Pencetak</th>
                <th style="width:12%">Jenama / Jenis</th>
                <th style="width:7%">Diminta</th>
                <th style="width:7%">Dihantar</th>
                <th style="width:9%">Status</th>
                <th style="width:9%">Tarikh Dihantar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $i => $record)
                @php
                    $jenisToner = $record->jenis_toner instanceof \App\Enums\JenisToner
                        ? $record->jenis_toner->label()
                        : (string) $record->jenis_toner;

                    $statusLabel = $record->status instanceof \App\Enums\StatusPermohonanToner
                        ? $record->status->label()
                        : (string) $record->status;

                    $badgeMap = [
                        'submitted' => 'blue',
                        'disemak' => 'yellow',
                        'diluluskan' => 'green',
                        'ditolak' => 'red',
                        'dihantar' => 'teal',
                        'pending_stock' => 'orange',
                    ];
                    $statusValue = $record->status instanceof \App\Enums\StatusPermohonanToner
                        ? $record->status->value
                        : (string) $record->status;
                    $badgeColor = $badgeMap[$statusValue] ?? 'zinc';
                @endphp
                <tr class="{{ $i % 2 === 1 ? 'even' : '' }}">
                    <td>{{ $i + 1 }}</td>
                    <td style="font-family: monospace;">{{ $record->no_tiket }}</td>
                    <td>{{ $record->created_at->format('d/m/Y') }}</td>
                    <td>{{ $record->user->name ?? '—' }}</td>
                    <td>{{ $record->user->bahagian ?? '—' }}</td>
                    <td>{{ $record->model_pencetak }}</td>
                    <td>{{ $record->jenama_toner }} / {{ $jenisToner }}</td>
                    <td style="text-align:center">{{ $record->kuantiti }}</td>
                    <td style="text-align:center">{{ $record->kuantiti_diluluskan ?? '—' }}</td>
                    <td><span class="badge badge-{{ $badgeColor }}">{{ $statusLabel }}</span></td>
                    <td>{{ $record->penghantaran?->tarikh_hantar?->format('d/m/Y') ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
