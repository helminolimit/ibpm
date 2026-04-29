<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Status Permohonan Dikemaskini</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1e40af; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 24px; border: 1px solid #e5e7eb; }
        .info-box { background: white; padding: 16px; border-left: 4px solid #3b82f6; margin: 16px 0; }
        .status-badge { display: inline-block; padding: 6px 16px; border-radius: 6px; font-size: 14px; font-weight: 600; margin: 8px 0; }
        .status-diterima { background: #dbeafe; color: #1e40af; }
        .status-dalam-proses { background: #fef3c7; color: #92400e; }
        .status-selesai { background: #d1fae5; color: #065f46; }
        .footer { background: #f3f4f6; padding: 12px; text-align: center; font-size: 12px; color: #6b7280; border-radius: 0 0 8px 8px; }
        .btn { display: inline-block; background: #1e40af; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>IBPM MOTAC — Status Permohonan Dikemaskini</h2>
        </div>
        <div class="content">
            <p>Salam {{ $permohonan->pemohon->name }},</p>
            
            <p>Status permohonan kemaskini portal anda telah dikemaskini.</p>

            <div class="info-box">
                <p style="margin: 0 0 8px 0;"><strong>No. Tiket:</strong> <span style="font-family: monospace; font-weight: 600;">{{ $permohonan->no_tiket }}</span></p>
                <p style="margin: 0 0 8px 0;"><strong>URL Halaman:</strong><br>
                <a href="{{ $permohonan->url_halaman }}" style="color: #3b82f6; word-break: break-all;">{{ $permohonan->url_halaman }}</a></p>
                <p style="margin: 0;"><strong>Status Terkini:</strong><br>
                <span class="status-badge status-{{ str_replace('_', '-', $permohonan->status->value) }}">
                    {{ $permohonan->status->label() }}
                </span>
                </p>
            </div>

            @if($permohonan->status->value === 'dalam_proses')
            <p style="color: #92400e; background: #fef3c7; padding: 12px; border-radius: 6px; font-size: 14px;">
                <strong>ℹ️ Permohonan anda sedang diproses.</strong><br>
                Pentadbir sedang bekerja untuk melaksanakan kemaskini yang diminta.
            </p>
            @elseif($permohonan->status->value === 'selesai')
            <p style="color: #065f46; background: #d1fae5; padding: 12px; border-radius: 6px; font-size: 14px;">
                <strong>✅ Permohonan anda telah selesai!</strong><br>
                Kemaskini telah dilaksanakan pada {{ $permohonan->tarikh_selesai?->format('d/m/Y H:i') }}.
            </p>
            @endif

            <p style="margin-top: 24px;">
                <a href="{{ url('/kemaskini-portal/' . $permohonan->id) }}" class="btn">
                    Lihat Butiran Permohonan
                </a>
            </p>

            <p style="margin-top: 24px; font-size: 14px; color: #6b7280;">
                Sila log masuk ke sistem IBPM untuk maklumat lanjut mengenai permohonan anda.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} IBPM MOTAC. Semua hak terpelihara.
        </div>
    </div>
</body>
</html>
