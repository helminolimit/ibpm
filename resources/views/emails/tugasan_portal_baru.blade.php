<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugasan Baru</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background: #1e40af; color: #fff; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; }
        .content { padding: 24px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; background: #dbeafe; color: #1e40af; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        table td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; }
        table td:first-child { font-weight: 600; width: 140px; color: #6b7280; }
        .btn { display: inline-block; padding: 12px 24px; background: #1e40af; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 600; margin-top: 16px; }
        .footer { padding: 16px 24px; background: #f9fafb; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tugasan Baru — Kemaskini Portal</h1>
        </div>
        <div class="content">
            <p>Tuan/Puan <strong>{{ $tugasan->teknisian->name }}</strong>,</p>
            <p>Anda telah ditugaskan untuk menyelesaikan permohonan kemaskini portal berikut:</p>

            <table>
                <tr>
                    <td>No. Tiket</td>
                    <td><span class="badge">{{ $tugasan->permohonan->no_tiket }}</span></td>
                </tr>
                <tr>
                    <td>URL Halaman</td>
                    <td>{{ $tugasan->permohonan->url_halaman }}</td>
                </tr>
                <tr>
                    <td>Jenis Perubahan</td>
                    <td>{{ ucfirst($tugasan->permohonan->jenis_perubahan) }}</td>
                </tr>
                <tr>
                    <td>Butiran</td>
                    <td>{{ $tugasan->permohonan->butiran_kemaskini }}</td>
                </tr>
                @if($tugasan->nota_tugasan)
                <tr>
                    <td>Nota Pentadbir</td>
                    <td>{{ $tugasan->nota_tugasan }}</td>
                </tr>
                @endif
                <tr>
                    <td>Ditugaskan Oleh</td>
                    <td>{{ $tugasan->ditugaskanOleh->name }}</td>
                </tr>
            </table>

            <p>Sila log masuk ke ICTServe untuk maklumat lanjut dan kemaskini status tugasan.</p>

            <a href="{{ url('/admin/kemaskini-portal') }}" class="btn">Lihat Panel Pentadbir</a>
        </div>
        <div class="footer">
            <p>ICTServe — Bahagian Pengurusan Maklumat, MOTAC</p>
        </div>
    </div>
</body>
</html>
