<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Permohonan Kemaskini Portal Diterima</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1e40af; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 24px; border: 1px solid #e5e7eb; }
        .info-box { background: white; padding: 16px; border-left: 4px solid #3b82f6; margin: 16px 0; }
        .footer { background: #f3f4f6; padding: 12px; text-align: center; font-size: 12px; color: #6b7280; border-radius: 0 0 8px 8px; }
        .badge { display: inline-block; padding: 4px 12px; background: #dbeafe; color: #1e40af; border-radius: 4px; font-size: 14px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>IBPM MOTAC — Permohonan Kemaskini Portal</h2>
        </div>
        <div class="content">
            <p>Salam Pentadbir Unit Aplikasi Teras dan Multimedia,</p>
            
            <p>Permohonan kemaskini portal baharu telah diterima dan memerlukan tindakan anda.</p>

            <div class="info-box">
                <p style="margin: 0 0 8px 0;"><strong>No. Tiket:</strong> <span class="badge">{{ $permohonan->no_tiket }}</span></p>
                <p style="margin: 0 0 8px 0;"><strong>Pemohon:</strong> {{ $permohonan->pemohon->name }}</p>
                <p style="margin: 0 0 8px 0;"><strong>Email Pemohon:</strong> {{ $permohonan->pemohon->email }}</p>
                <p style="margin: 0 0 8px 0;"><strong>Tarikh Mohon:</strong> {{ $permohonan->tarikh_mohon->format('d/m/Y H:i') }}</p>
            </div>

            <h3 style="color: #1e40af; margin-top: 24px;">Butiran Permohonan</h3>
            
            <p><strong>URL Halaman:</strong><br>
            <a href="{{ $permohonan->url_halaman }}" style="color: #3b82f6;">{{ $permohonan->url_halaman }}</a></p>

            <p><strong>Jenis Perubahan:</strong><br>
            {{ match($permohonan->jenis_perubahan) {
                'kandungan' => 'Kandungan',
                'konfigurasi' => 'Konfigurasi',
                'lain_lain' => 'Lain-lain',
                default => $permohonan->jenis_perubahan
            } }}</p>

            <p><strong>Butiran Kemaskini:</strong><br>
            {{ $permohonan->butiran_kemaskini }}</p>

            @if($permohonan->lampirans->count() > 0)
            <p><strong>Lampiran:</strong></p>
            <ul>
                @foreach($permohonan->lampirans as $lampiran)
                <li>{{ $lampiran->nama_fail }}</li>
                @endforeach
            </ul>
            @endif

            <p style="margin-top: 24px;">
                <a href="{{ url('/admin/kemaskini-portal/' . $permohonan->id) }}" 
                   style="display: inline-block; background: #1e40af; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600;">
                    Lihat Permohonan
                </a>
            </p>

            <p style="margin-top: 24px; font-size: 14px; color: #6b7280;">
                Sila log masuk ke sistem IBPM untuk memproses permohonan ini.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} IBPM MOTAC. Semua hak terpelihara.
        </div>
    </div>
</body>
</html>
