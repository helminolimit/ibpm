# M06 — Database Migrations

## Branch
`feature/m06-kumpulan-emel`

## New Tables (M06 sahaja)

### 1. kumpulan_emel
```php
// database/migrations/2024_01_01_000010_create_kumpulan_emel_table.php
Schema::create('kumpulan_emel', function (Blueprint $table) {
    $table->id();
    $table->string('nama_kumpulan');
    $table->string('alamat_emel')->unique();
    $table->string('pemilik_unit')->nullable();
    $table->integer('jumlah_ahli')->default(0);
    $table->timestamps();
});
```

### 2. permohonan_emel
```php
// database/migrations/2024_01_01_000011_create_permohonan_emel_table.php
Schema::create('permohonan_emel', function (Blueprint $table) {
    $table->id();
    $table->string('no_tiket')->unique();
    $table->foreignId('user_id')->constrained('users');
    $table->foreignId('pentadbir_id')->nullable()->constrained('users');
    $table->foreignId('kumpulan_emel_id')->constrained('kumpulan_emel');
    $table->enum('jenis_tindakan', ['tambah', 'buang']);
    $table->enum('status', ['baru', 'dalam_tindakan', 'selesai', 'ditolak'])->default('baru');
    $table->text('catatan_pemohon')->nullable();
    $table->text('catatan_pentadbir')->nullable();
    $table->timestamp('selesai_at')->nullable();
    $table->timestamps();
});
```

### 3. ahli_kumpulan
```php
// database/migrations/2024_01_01_000012_create_ahli_kumpulan_table.php
Schema::create('ahli_kumpulan', function (Blueprint $table) {
    $table->id();
    $table->foreignId('permohonan_id')->constrained('permohonan_emel')->cascadeOnDelete();
    $table->string('nama_ahli');
    $table->string('emel_ahli');
    $table->enum('tindakan', ['tambah', 'buang']);
    $table->timestamps();

    $table->unique(['permohonan_id', 'emel_ahli']);
});
```

## Shared Tables (sudah ada dari M01 — JANGAN buat migration baru)
- `notifikasi` — dari M01
- `log_audit` — dari M01
- `users` — dari Laravel Breeze
- `pentadbir_roles` — dari M01

## Naming Convention
- Timestamp prefix format: `2024_01_01_0000XX_` — increment XX dari modul sebelum
- M01: 000001–000003
- M02: 000004–000005
- M03: 000006–000007
- M04: 000008–000009
- **M05: 000009**
- **M06: 000010–000012** ← nombor ini

## DO NOT
- Run `php artisan migrate:fresh` dalam production
- Buat column baru dalam migration lama — buat migration baru
- Skip `cascadeOnDelete` pada foreign key `permohonan_id`
