# 01 — Migration & Skema Pangkalan Data
## M03 Penamatan Akaun Login Komputer

Cipta 4 migration berikut **mengikut urutan**. Jalankan `php artisan migrate` selepas semua siap.

---

## Migration 1: `permohonan_penamatan`

**Fail:** `database/migrations/YYYY_MM_DD_000001_create_permohonan_penamatan_table.php`

```php
Schema::create('permohonan_penamatan', function (Blueprint $table) {
    $table->id();
    $table->string('no_tiket', 20)->unique(); // Format: PAK-YYYY-NNN
    $table->foreignId('pemohon_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('pengguna_sasaran_id')->constrained('users');
    $table->string('id_login_komputer', 100);  // domain\username atau UPN
    $table->date('tarikh_berkuat_kuasa');
    $table->enum('jenis_tindakan', ['TAMAT', 'GANTUNG']);
    $table->text('sebab_penamatan');
    $table->enum('status', [
        'DRAF',
        'MENUNGGU_KEL_1',
        'MENUNGGU_KEL_2',
        'DALAM_PROSES',
        'SELESAI',
        'DITOLAK',
    ])->default('DRAF');
    $table->text('catatan_pentadbir')->nullable();
    $table->timestamp('tarikh_selesai')->nullable(); // diisi bila status = SELESAI
    $table->timestamps();
});
```

---

## Migration 2: `kelulusan`

**Fail:** `database/migrations/YYYY_MM_DD_000002_create_kelulusan_table.php`

```php
Schema::create('kelulusan', function (Blueprint $table) {
    $table->id();
    $table->foreignId('permohonan_id')
          ->constrained('permohonan_penamatan')
          ->cascadeOnDelete();
    $table->foreignId('pelulus_id')->constrained('users');
    $table->enum('peringkat', ['PERINGKAT_1', 'PERINGKAT_2']);
    $table->enum('keputusan', ['LULUS', 'TOLAK']);
    $table->text('catatan')->nullable(); // sebab tolak atau nota tambahan
    $table->timestamp('tarikh_tindakan')->useCurrent();
    $table->timestamps();
});
```

---

## Migration 3: `log_audits`

**Fail:** `database/migrations/YYYY_MM_DD_000003_create_log_audits_table.php`

```php
Schema::create('log_audits', function (Blueprint $table) {
    $table->id();
    $table->foreignId('permohonan_id')
          ->constrained('permohonan_penamatan')
          ->cascadeOnDelete();
    $table->foreignId('pengguna_id')->constrained('users');
    $table->string('tindakan', 100); // contoh: permohonan_dihantar, akaun_ditamatkan
    $table->json('butiran')->nullable(); // data sebelum/selepas perubahan
    $table->string('modul', 20)->default('M03');
    $table->string('ip_address', 45)->nullable();
    $table->timestamp('created_at')->useCurrent();
});
```

---

## Migration 4: `notifikasi`

**Fail:** `database/migrations/YYYY_MM_DD_000004_create_notifikasi_table.php`

```php
Schema::create('notifikasi', function (Blueprint $table) {
    $table->id();
    $table->foreignId('permohonan_id')
          ->constrained('permohonan_penamatan')
          ->cascadeOnDelete();
    $table->foreignId('penerima_id')->constrained('users');
    $table->enum('jenis', ['HANTAR', 'KELULUSAN', 'TOLAK', 'SELESAI']);
    $table->string('tajuk', 255);
    $table->text('mesej');
    $table->boolean('dibaca')->default(false);
    $table->timestamp('dihantar_pada')->nullable(); // null = belum dihantar
    $table->timestamps();
});
```

---

## Semakan Selepas Migrate

```bash
php artisan migrate --path=database/migrations/YYYY_MM_DD_*_penamatan*
php artisan migrate --path=database/migrations/YYYY_MM_DD_*_kelulusan*
php artisan migrate --path=database/migrations/YYYY_MM_DD_*_log_audits*
php artisan migrate --path=database/migrations/YYYY_MM_DD_*_notifikasi*
```

### JANGAN
- Jangan edit migration yang sudah di-`migrate` — buat migration baru untuk alter
- Jangan kongsi jadual `log_audits` atau `notifikasi` dengan modul lain tanpa kolum `modul`
