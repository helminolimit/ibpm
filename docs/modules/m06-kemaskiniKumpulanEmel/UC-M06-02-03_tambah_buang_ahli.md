# UC-M06-02 — Tambah Ahli Kumpulan Emel
# UC-M06-03 — Buang Ahli Kumpulan Emel

## Actor
Pemohon

## Precondition
- Sedang dalam borang UC-M06-01
- `jenis_tindakan` dipilih

---

## UC-M06-02: Tambah Ahli

### Flow
1. Pemohon pilih `jenis_tindakan = tambah`
2. Sistem papar section **Senarai Ahli Untuk Ditambah**
3. Pemohon isi `nama_ahli` dan `emel_ahli` (boleh tambah baris)
4. Data simpan dalam `ahli_kumpulan` dengan `tindakan = tambah`

### Livewire — Dynamic Rows
```php
public array $ahli = [
    ['nama_ahli' => '', 'emel_ahli' => '', 'tindakan' => 'tambah']
];

public function addAhli(): void
{
    $this->ahli[] = ['nama_ahli' => '', 'emel_ahli' => '', 'tindakan' => 'tambah'];
}

public function removeAhli(int $index): void
{
    unset($this->ahli[$index]);
    $this->ahli = array_values($this->ahli);
}
```

---

## UC-M06-03: Buang Ahli

### Flow
1. Pemohon pilih `jenis_tindakan = buang`
2. Sistem papar section **Senarai Ahli Untuk Dibuang**
3. Pemohon isi `emel_ahli` ahli yang hendak dibuang
4. Data simpan dalam `ahli_kumpulan` dengan `tindakan = buang`

---

## Table: ahli_kumpulan
```php
Schema::create('ahli_kumpulan', function (Blueprint $table) {
    $table->id();
    $table->foreignId('permohonan_id')->constrained('permohonan_emel')->cascadeOnDelete();
    $table->string('nama_ahli');
    $table->string('emel_ahli');
    $table->enum('tindakan', ['tambah', 'buang']);
    $table->timestamps();
});
```

## DO NOT
- Izinkan `emel_ahli` duplikasi dalam satu permohonan
- Campurkan `tambah` dan `buang` dalam satu permohonan yang sama
