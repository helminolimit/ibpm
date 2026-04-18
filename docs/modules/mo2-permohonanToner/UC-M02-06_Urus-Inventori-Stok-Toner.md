# UC-M02-06 — Urus Inventori Stok Toner

| Perkara | Butiran |
|---|---|
| ID | UC-M02-06 |
| Modul | M02 — Permohonan Toner (Printer) |
| Pelakon Utama | Pentadbir BPM, Superadmin |
| Prasyarat | Log masuk sebagai `pentadbir` atau `superadmin` |
| Hasil Utama | Rekod stok toner ditambah atau dikemaskini dalam sistem |
| Keutamaan | Sederhana |

---

## Aliran Utama — Tambah Stok Baru

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 1 | Pentadbir | Navigasi ke **Inventori Stok Toner** dalam panel pentadbir |
| 2 | Sistem | Papar jadual stok toner semasa dengan penanda stok rendah |
| 3 | Pentadbir | Klik **+ Tambah Stok Baru** |
| 4 | Sistem | Papar modal borang tambah stok |
| 5 | Pentadbir | Isi maklumat: model toner, jenama, jenis, kuantiti, kuantiti minimum |
| 6 | Pentadbir | Klik **Simpan** |
| 7 | Sistem | Sahkan maklumat dan simpan rekod baru dalam jadual `stok_toner` |
| 8 | Sistem | Rekod log: `tindakan = stock_updated` |
| 9 | Sistem | Papar mesej berjaya, tutup modal, kemaskini jadual |

---

## Aliran Utama — Kemaskini Stok Sedia Ada

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 1 | Pentadbir | Navigasi ke **Inventori Stok Toner** |
| 2 | Pentadbir | Klik **Edit** pada rekod stok yang perlu dikemaskini |
| 3 | Sistem | Papar modal borang dengan nilai semasa |
| 4 | Pentadbir | Ubah kuantiti (cth: tambah stok baru diterima) atau tetapan kuantiti minimum |
| 5 | Pentadbir | Klik **Simpan** |
| 6 | Sistem | Kemaskini rekod, rekod log, papar mesej berjaya |

---

## Aliran Alternatif

### A1 — Amaran Stok Rendah

| Langkah | Tindakan |
|---|---|
| 2a | Sistem kesan `kuantiti_ada <= kuantiti_minimum` untuk mana-mana jenis toner |
| 2b | Sistem papar penanda amaran (badge merah/kuning) pada rekod berkenaan dalam jadual |
| 2c | Pentadbir ambil tindakan: kemaskini stok apabila bekalan baru diterima |

### A2 — Rekod Duplikasi

| Langkah | Tindakan |
|---|---|
| 7a | Sistem kesan kombinasi `model_toner + jenama + jenis` sudah wujud |
| 7b | Sistem papar ralat: *"Rekod stok toner ini sudah wujud. Sila gunakan fungsi Edit."* |

---

## Spesifikasi Borang Stok

| # | Medan | Jenis | Wajib | Peraturan |
|---|---|---|---|---|
| 1 | Model Toner | Text | Ya | Max 100 aksara. Cth: CF217A |
| 2 | Jenama | Text | Ya | Max 100 aksara. Cth: HP, Canon |
| 3 | Jenis | Dropdown | Ya | `hitam`, `cyan`, `magenta`, `kuning` |
| 4 | Warna | Text | Tidak | Keterangan tambahan |
| 5 | Kuantiti Ada | Number | Ya | Min 0 |
| 6 | Kuantiti Minimum | Number | Ya | Min 1 — trigger amaran stok rendah |

---

## Papar Jadual Inventori

| Medan Dipapar | Keterangan |
|---|---|
| Model Toner | Kod model toner |
| Jenama | HP, Canon, Epson dsb. |
| Jenis | Hitam / Cyan / Magenta / Kuning |
| Stok Ada | Kuantiti semasa |
| Stok Minimum | Paras minimum sebelum amaran |
| Status Stok | ✅ Mencukupi / ⚠️ Rendah / ❌ Habis |
| Tindakan | Edit |

---

## Logik Amaran Stok

```
jika kuantiti_ada = 0          → status: Habis   (merah)
jika kuantiti_ada <= kuantiti_minimum → status: Rendah  (kuning)
jika kuantiti_ada > kuantiti_minimum  → status: Mencukupi (hijau)
```

---

## Kesan pada Pangkalan Data

### `stok_toner` — cipta / kemaskini

| Operasi | Medan |
|---|---|
| Tambah baru | Semua medan `stok_toner` |
| Kemaskini | `kuantiti_ada`, `kuantiti_minimum`, `updated_at` |

### `log_toner` — rekod baru

| Medan | Nilai |
|---|---|
| `tindakan` | `stock_updated` |
| `keterangan` | `"Stok dikemaskini: {model} ({jenis}) — {kuantiti} unit."` |

---

## Kawalan Akses

- Hanya `pentadbir` dan `superadmin` boleh akses
- Middleware: `role:pentadbir,superadmin`

---

## Kod Berkaitan

| Fail | Keterangan |
|---|---|
| `app/Livewire/M02/Admin/InventoriStok.php` | Logik tambah & kemaskini stok |
| `app/Models/StokToner.php` | Model stok dengan method `stokRendah()`, `tambahStok()`, `kurangkanStok()` |
| `resources/views/livewire/m02/admin/inventori-stok.blade.php` | Paparan jadual & modal |
| `database/migrations/2026_04_02_000001_create_stok_toner_table.php` | Skema jadual |
| `database/seeders/M02TonerSeeder.php` | Data stok awal untuk ujian |
