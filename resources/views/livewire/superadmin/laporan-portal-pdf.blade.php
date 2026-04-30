<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kemaskini Portal</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; margin: 20px; }
        h1 { font-size: 16px; text-align: center; margin-bottom: 4px; }
        .subtitle { text-align: center; font-size: 11px; color: #666; margin-bottom: 20px; }
        .stats { display: table; width: 100%; margin-bottom: 20px; }
        .stat-item { display: table-cell; text-align: center; padding: 8px; border: 1px solid #e5e7eb; }
        .stat-label { font-size: 9px; color: #6b7280; text-transform: uppercase; }
        .stat-value { font-size: 14px; font-weight: bold; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #1e40af; color: #fff; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; }
        td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
        tr:nth-child(even) { background: #f9fafb; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: 600; }
        .badge-diterima { background: #dbeafe; color: #1e40af; }
        .badge-dalam_proses { background: #fef3c7; color: #92400e; }
        .badge-selesai { background: #d1fae5; color: #065f46; }
        .footer { margin-top: 20px; text-align: center; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        .filter-info { font-size: 9px; color: #6b7280; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Laporan Kemaskini Portal</h1>
    <p class="subtitle">ICTServe - Bahagian Pengurusan Maklumat, MOTAC</p>

    @if($dari || $hingga)
        <p class="filter-info">
            Tempoh: {{ $dari ? \Carbon\Carbon::parse($dari)->format('d/m/Y') : 'Awal' }}
            - {{ $hingga ? \Carbon\Carbon::parse($hingga)->format('d/m/Y') : 'Kini' }}
        </p>
    @endif

    <div class="stats">
        <div class="stat-item">
            <div class="stat-label">Jumlah</div>
            <div class="stat-value">{{ $statistik['jumlah'] }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Diterima</div>
            <div class="stat-value">{{ $statistik['diterima'] }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Dalam Proses</div>
            <div class="stat-value">{{ $statistik['dalam_proses'] }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Selesai</div>
            <div class="stat-value">{{ $statistik['selesai'] }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Masa Purata</div>
            <div class="stat-value">{{ $statistik['masa_purata'] ? $statistik['masa_purata'] . ' jam' : '-' }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Bil</th>
                <th>No. Tiket</th>
                <th>Pemohon</th>
                <th>URL Halaman</th>
                <th>Jenis</th>
                <th>Status</th>
                <th>Tarikh Mohon</th>
                <th>Tarikh Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->no_tiket }}</td>
                    <td>{{ $item->pemohon->name }}</td>
                    <td>{{ Str::limit($item->url_halaman, 30) }}</td>
                    <td>{{ ucfirst($item->jenis_perubahan) }}</td>
                    <td>
                        <span class="badge badge-{{ $item->status->value }}">
                            {{ $item->status->label() }}
                        </span>
                    </td>
                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                    <td>{{ $item->tarikh_selesai?->format('d/m/Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tiada rekod ditemui.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dijana pada: {{ now()->format('d/m/Y H:i') }} | ICTServe - MOTAC</p>
    </div>
</body>
</html>
