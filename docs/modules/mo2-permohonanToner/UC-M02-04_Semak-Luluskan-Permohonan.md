# UC-M02-04 — Semak & Luluskan / Tolak Permohonan

| Perkara | Butiran |
|---|---|
| ID | UC-M02-04 |
| Modul | M02 — Permohonan Toner (Printer) |
| Pelakon Utama | Pentadbir BPM (Unit Operasi, Teknikal & Khidmat Pengguna) |
| Prasyarat | Terdapat permohonan dengan status `submitted` |
| Hasil Utama | Permohonan diluluskan atau ditolak, pemohon dimaklumkan |
| Keutamaan | Tinggi |

---

## Aliran Utama — Luluskan

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 1 | Pentadbir | Log masuk ke sistem ICTServe sebagai Pentadbir BPM |
| 2 | Sistem | Papar panel pentadbir dengan notifikasi permohonan baharu |
| 3 | Pentadbir | Navigasi ke **Urus Permohonan Toner** |
| 4 | Sistem | Papar senarai semua permohonan, tapis mengikut status |
| 5 | Pentadbir | Klik **Proses** pada permohonan status `submitted` |
| 6 | Sistem | Papar butiran penuh permohonan termasuk maklumat stok toner berkaitan |
| 7 | Pentadbir | Semak stok toner (model & jenis yang diminta) |
| 8 | Pentadbir | Tetapkan kuantiti diluluskan (boleh berbeza dari kuantiti diminta) |
| 9 | Pentadbir | Isi catatan pentadbir (pilihan) |
| 10 | Pentadbir | Klik **Luluskan** → sahkan dalam modal pengesahan |
| 11 | Sistem | Kemaskini status: `submitted` → `approved` |
| 12 | Sistem | Rekod log: `tindakan = approved` |
| 13 | Sistem | Hantar notifikasi emel `TonerDiluluskan` kepada Pemohon |

---

## Aliran Alternatif — Tolak

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 10a | Pentadbir | Klik **Tolak** → modal pengesahan papar |
| 10b | Pentadbir | Isi sebab penolakan (wajib, min 10 aksara) |
| 10c | Pentadbir | Klik **Sahkan Tolak** |
| 11a | Sistem | Kemaskini status: `submitted` → `rejected` |
| 12a | Sistem | Rekod log: `tindakan = rejected` dengan sebab penolakan |
| 13a | Sistem | Hantar notifikasi emel `TonerDitolak` kepada Pemohon |

---

## Aliran Alternatif — Stok Tidak Mencukupi

| Langkah | Tindakan |
|---|---|
| 7a | Pentadbir semak stok — didapati stok tidak mencukupi |
| 8a | Pentadbir boleh luluskan dengan kuantiti yang ada sahaja |
| 11a | Sistem kemaskini status: `submitted` → `pending_stock` |
| 11b | Pemohon dimaklumkan bahawa permohonan diluluskan tetapi menunggu stok |
| 11c | Apabila stok ditambah, pentadbir kemaskini status ke `approved` secara manual |

---

## Peraturan Validasi (Semasa Luluskan)

| Medan | Peraturan |
|---|---|
| Kuantiti diluluskan | Wajib, integer, minimum 1 |
| Catatan pentadbir | Pilihan, maksimum 500 aksara |

## Peraturan Validasi (Semasa Tolak)

| Medan | Peraturan |
|---|---|
| Sebab penolakan | Wajib, minimum 10 aksara, maksimum 500 aksara |

---

## Perubahan Status

```
submitted  ──[Luluskan]──►  approved
           ──[Stok kurang]► pending_stock
           ──[Tolak]──────►  rejected
```

---

## Kawalan Akses

- Hanya pengguna dengan peranan `pentadbir` atau `superadmin` boleh akses fungsi ini
- Middleware: `role:pentadbir,superadmin`
- Policy: `PermohonanTonerPolicy::proses()`

---

## Kod Berkaitan

| Fail | Keterangan |
|---|---|
| `app/Livewire/M02/Admin/ProsesPermohonan.php` | Logik luluskan & tolak |
| `resources/views/livewire/m02/admin/proses-permohonan.blade.php` | Paparan proses |
| `app/Livewire/M02/Admin/SenaraiAdmin.php` | Senarai permohonan pentadbir |
| `app/Notifications/TonerDiluluskan.php` | Emel diluluskan |
| `app/Notifications/TonerDitolak.php` | Emel ditolak |
| `app/Models/LogToner.php` | Rekod audit trail |
