<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Akaun Baharu</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1e40af; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 24px; border: 1px solid #e5e7eb; }
        .footer { background: #f3f4f6; padding: 12px; text-align: center; font-size: 12px; color: #6b7280; border-radius: 0 0 8px 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>IBPM MOTAC — Akaun Baharu</h2>
        </div>
        <div class="content">
            <p>Salam {{ $user->name }},</p>
            <p>Akaun anda dalam sistem IBPM MOTAC telah berjaya didaftarkan.</p>
            <p><strong>Email:</strong> {{ $user->email }}<br>
            <strong>Peranan:</strong> {{ $user->role->label() }}<br>
            <strong>Status:</strong> {{ $user->status->label() }}</p>
            <p>Anda akan menerima notifikasi apabila akaun diaktifkan oleh Superadmin.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} IBPM MOTAC. Semua hak terpelihara.
        </div>
    </div>
</body>
</html>
