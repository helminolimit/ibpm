# UC-M06-08 — Urus Pengguna dan Kumpulan
# UC-M06-09 — Jana Laporan Log Perubahan

## Actor
Superadmin

---

## UC-M06-08: Urus Pengguna dan Kumpulan

### Fungsi
- Tambah / edit / padam rekod `kumpulan_emel`
- Tetapkan pentadbir kepada unit berkaitan
- Semak semua permohonan M06 tanpa had unit

### Route
```php
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin/m06')->group(function () {
    Route::resource('kumpulan-emel', SuperadminKumpulanEmelController::class);
    Route::resource('permohonan', SuperadminPermohonanEmelController::class)->only(['index','show']);
});
```

### Table: kumpulan_emel
```php
Schema::create('kumpulan_emel', function (Blueprint $table) {
    $table->id();
    $table->string('nama_kumpulan');
    $table->string('alamat_emel')->unique();
    $table->string('pemilik_unit')->nullable();
    $table->integer('jumlah_ahli')->default(0);
    $table->timestamps();
});
```

---

## UC-M06-09: Jana Laporan Log Perubahan

### Fungsi
- Tapis log mengikut tarikh, unit, jenis tindakan
- Export ke PDF / Excel

### Query
```php
LogAudit::where('modul', 'm06')
    ->whereBetween('created_at', [$dari, $hingga])
    ->with('user')
    ->orderBy('created_at', 'desc')
    ->get();
```

### Export
```php
// Guna maatwebsite/excel untuk Excel
// Guna barryvdh/laravel-dompdf untuk PDF
// Kedua-dua shared dengan M01-M05
```

### Modul Tag dalam log_audit
```php
LogAudit::create([
    'permohonan_id' => $permohonan->id,
    'user_id'       => auth()->id(),
    'modul'         => 'm06',   // WAJIB isi untuk filtering laporan
    'tindakan'      => 'status_dikemaskini',
    'butiran'       => json_encode($changes),
    'ip_address'    => request()->ip(),
]);
```

## DO NOT
- Benarkan superadmin padam permohonan — hanya semak sahaja
- Export data tanpa date range filter — boleh timeout
- Lupa isi field `modul` dalam `log_audit` — laporan tidak dapat filter
