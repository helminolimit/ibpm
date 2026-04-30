# UC-M06-05 — Semak dan Lulus Permohonan

## Actor
Pentadbir (Unit Infrastruktur & Keselamatan ICT)

## Precondition
- Pentadbir log masuk dengan role `unit_infrastruktur_keselamatan_ict`
- Permohonan wujud dengan `status = baru`

## Flow
1. Pentadbir masuk **Dashboard Pentadbir**
2. Sistem papar senarai permohonan M06 yang belum diproses
3. Pentadbir klik permohonan untuk lihat butiran
4. Pentadbir semak senarai ahli dan kumpulan emel
5. Pentadbir pilih tindakan:
   - **Lulus** → status jadi `dalam_tindakan`
   - **Tolak** → status jadi `ditolak`, wajib isi `catatan_pentadbir`
6. Jika lulus → trigger UC-M06-06 (kemaskini pelayan)
7. Sistem trigger UC-M06-07 (notifikasi kepada pemohon)

## Middleware
```php
Route::middleware(['auth', 'role:pentadbir,superadmin'])->group(function () {
    Route::get('/pentadbir/m06', [M06PentadbirController::class, 'index']);
});
```

## Role Check
```php
// Pastikan hanya pentadbir unit berkaitan boleh akses
if (!auth()->user()->hasUnit('unit_infrastruktur_keselamatan_ict')) {
    abort(403);
}
```

## DO NOT
- Benarkan pentadbir unit lain akses permohonan M06
- Lulus permohonan tanpa semak senarai ahli
- Skip log audit semasa kemaskini status
