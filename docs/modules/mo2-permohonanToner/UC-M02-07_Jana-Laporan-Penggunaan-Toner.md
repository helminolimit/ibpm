# UC-M02-07 — Jana Laporan Penggunaan Toner

| Perkara | Butiran |
|---|---|
| ID | UC-M02-07 |
| Modul | M02 — Permohonan Toner (Printer) |
| Pelakon Utama | Pentadbir BPM, Superadmin |
| Prasyarat | Terdapat rekod permohonan dalam sistem |
| Hasil Utama | Laporan berjaya dijana dan boleh dieksport dalam format PDF atau Excel |
| Keutamaan | Sederhana |

---

## Aliran Utama

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 1 | Pentadbir | Navigasi ke **Laporan Toner** dalam panel pentadbir |
| 2 | Sistem | Papar borang parameter laporan |
| 3 | Pentadbir | Tetapkan parameter: tarikh dari, tarikh hingga, jabatan, jenis toner, status |
| 4 | Pentadbir | Klik **Jana Laporan** |
| 5 | Sistem | Tapis rekod `permohonan_toner` mengikut parameter yang dipilih |
| 6 | Sistem | Papar laporan dalam bentuk jadual dengan ringkasan statistik |
| 7 | Pentadbir | Semak laporan yang dipapar |
| 8 | Pentadbir | Klik **Eksport PDF** atau **Eksport Excel** |
| 9 | Sistem | Jana fail dan muat turun secara automatik |

---

## Aliran Alternatif

### A1 — Tiada Data dalam Tempoh Dipilih

| Langkah | Tindakan |
|---|---|
| 6a | Sistem papar mesej: *"Tiada rekod permohonan dalam tempoh yang dipilih."* |
| 6b | Pentadbir ubah parameter dan jana semula |

### A2 — Eksport PDF

| Langkah | Tindakan |
|---|---|
| 8a | Pentadbir klik **Eksport PDF** |
| 8b | Sistem jana fail PDF menggunakan pustaka `barryvdh/laravel-dompdf` |
| 8c | Pelayar muat turun fail `Laporan-Toner-{tarikh}.pdf` |

### A3 — Eksport Excel

| Langkah | Tindakan |
|---|---|
| 8a | Pentadbir klik **Eksport Excel** |
| 8b | Sistem jana fail Excel menggunakan pustaka `maatwebsite/excel` |
| 8c | Pelayar muat turun fail `Laporan-Toner-{tarikh}.xlsx` |

---

## Parameter Laporan

| # | Parameter | Jenis | Wajib | Keterangan |
|---|---|---|---|---|
| 1 | Tarikh Dari | Date | Ya | Tarikh mula tempoh laporan |
| 2 | Tarikh Hingga | Date | Ya | Tarikh akhir tempoh laporan |
| 3 | Bahagian / Unit | Dropdown | Tidak | Tapis mengikut bahagian pemohon |
| 4 | Jenis Toner | Dropdown | Tidak | `hitam`, `cyan`, `magenta`, `kuning`, atau semua |
| 5 | Status | Dropdown | Tidak | `submitted`, `delivered`, `rejected`, atau semua |

---

## Kandungan Laporan

### Ringkasan Statistik (Header Laporan)

| Metrik | Keterangan |
|---|---|
| Jumlah Permohonan | Bilangan keseluruhan permohonan dalam tempoh |
| Jumlah Diluluskan | Bilangan permohonan status `approved` + `delivered` |
| Jumlah Dihantar | Bilangan permohonan status `delivered` |
| Jumlah Ditolak | Bilangan permohonan status `rejected` |
| Jumlah Unit Toner | Jumlah unit toner yang telah dihantar |

### Jadual Perincian

| Lajur | Sumber Data |
|---|---|
| No. Tiket | `permohonan_toner.no_tiket` |
| Tarikh Mohon | `permohonan_toner.submitted_at` |
| Pemohon | `users.name` |
| Bahagian | `permohonan_toner.bahagian_pemohon` |
| Model Pencetak | `permohonan_toner.model_pencetak` |
| Jenama / Jenis | `permohonan_toner.jenama_toner`, `jenis_toner` |
| Kuantiti Diminta | `permohonan_toner.kuantiti_diminta` |
| Kuantiti Dihantar | `permohonan_toner.kuantiti_diluluskan` |
| Status | `permohonan_toner.status` |
| Tarikh Dihantar | `penghantaran_toner.tarikh_hantar` |

---

## Query Asas Laporan

```php
PermohonanToner::with(['pemohon', 'penghantaran'])
    ->whereBetween('submitted_at', [$tarikhDari, $tarikhHingga])
    ->when($bahagian, fn ($q) => $q->where('bahagian_pemohon', $bahagian))
    ->when($jenisToner, fn ($q) => $q->where('jenis_toner', $jenisToner))
    ->when($status, fn ($q) => $q->where('status', $status))
    ->orderBy('submitted_at')
    ->get();
```

---

## Kawalan Akses

- Hanya `pentadbir` dan `superadmin` boleh jana laporan
- Middleware: `role:pentadbir,superadmin`

---

## Kod Berkaitan

| Fail | Keterangan |
|---|---|
| `app/Livewire/M02/Admin/LaporanToner.php` | Logik jana laporan & eksport |
| `resources/views/livewire/m02/admin/laporan-toner.blade.php` | Paparan borang & jadual laporan |
| `resources/views/exports/m02-laporan-toner.blade.php` | Templat PDF eksport |
| `app/Exports/M02TonerExport.php` | Kelas Excel eksport (Maatwebsite) |
