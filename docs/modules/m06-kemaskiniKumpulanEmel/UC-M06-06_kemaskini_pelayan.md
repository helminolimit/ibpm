# UC-M06-06 — Kemaskini Kumpulan Emel Pelayan

## Actor
Pentadbir / Sistem (auto setelah lulus)

## Precondition
- Permohonan berstatus `dalam_tindakan`
- Konfigurasi SMTP/Exchange tersedia dalam `.env`

## Flow
1. Sistem/Pentadbir trigger kemaskini setelah lulus (UC-M06-05)
2. Sistem ambil senarai `ahli_kumpulan` berdasarkan `permohonan_id`
3. Untuk setiap ahli:
   - Jika `tindakan = tambah` → tambah ke kumpulan pelayan
   - Jika `tindakan = buang` → buang dari kumpulan pelayan
4. Kemaskini `jumlah_ahli` dalam `kumpulan_emel`
5. Kemaskini `status = selesai` dalam `permohonan_emel`
6. Catat dalam `log_audit`

## Service Class
```php
// App\Services\KumpulanEmelService.php
class KumpulanEmelService
{
    public function proses(PermohonanEmel $permohonan): bool
    {
        foreach ($permohonan->ahliKumpulan as $ahli) {
            if ($ahli->tindakan === 'tambah') {
                $this->tambahAhli($permohonan->kumpulanEmel, $ahli);
            } else {
                $this->buangAhli($permohonan->kumpulanEmel, $ahli);
            }
        }

        $permohonan->update([
            'status'     => 'selesai',
            'selesai_at' => now(),
        ]);

        return true;
    }
}
```

## .env Keys
```
MAIL_EXCHANGE_HOST=
MAIL_EXCHANGE_PORT=
MAIL_EXCHANGE_USERNAME=
MAIL_EXCHANGE_PASSWORD=
```

## Integration Note
Jika integrasi Exchange/O365 API belum siap, pentadbir lakukan kemaskini manual dan tandakan selesai dalam sistem.

## DO NOT
- Hard-code credentials Exchange dalam kod
- Tandakan `selesai` sebelum kemaskini pelayan berjaya
- Skip kemaskini `jumlah_ahli` dalam `kumpulan_emel`
