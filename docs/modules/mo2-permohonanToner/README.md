# M02 — Indeks Use Case: Permohonan Toner (Printer)
**ICTServe — Sistem Pengurusan Perkhidmatan ICT, BPM MOTAC**

---

## Senarai Use Case

| ID | Nama | Pelakon | Fail |
|---|---|---|---|
| UC-M02-01 | Isi Borang Permohonan Toner | Pemohon | `UC-M02-01_Isi-Borang-Permohonan.md` |
| UC-M02-02 | Semak Status Permohonan | Pemohon | `UC-M02-02_Semak-Status-Permohonan.md` |
| UC-M02-03 | Terima Notifikasi Emel | Pemohon, Pentadbir BPM | `UC-M02-03_Terima-Notifikasi-Emel.md` |
| UC-M02-04 | Semak & Luluskan / Tolak Permohonan | Pentadbir BPM | `UC-M02-04_Semak-Luluskan-Permohonan.md` |
| UC-M02-05 | Rekod Penghantaran Toner | Pentadbir BPM | `UC-M02-05_Rekod-Penghantaran-Toner.md` |
| UC-M02-06 | Urus Inventori Stok Toner | Pentadbir BPM, Superadmin | `UC-M02-06_Urus-Inventori-Stok-Toner.md` |
| UC-M02-07 | Jana Laporan Penggunaan Toner | Pentadbir BPM, Superadmin | `UC-M02-07_Jana-Laporan-Penggunaan-Toner.md` |

---

## Pelakon & Use Case

```
Pemohon
  ├── UC-M02-01  Isi Borang Permohonan Toner
  ├── UC-M02-02  Semak Status Permohonan
  └── UC-M02-03  Terima Notifikasi Emel

Pentadbir BPM
  ├── UC-M02-03  Terima Notifikasi Emel
  ├── UC-M02-04  Semak & Luluskan / Tolak Permohonan
  ├── UC-M02-05  Rekod Penghantaran Toner
  └── UC-M02-06  Urus Inventori Stok Toner

Superadmin
  ├── UC-M02-06  Urus Inventori Stok Toner
  └── UC-M02-07  Jana Laporan Penggunaan Toner
```

---

## Aliran Status Permohonan

```
submitted
  ├──[Luluskan]──────────► approved
  │                             └──[Rekod Hantar]──► delivered
  ├──[Stok Kurang]────────► pending_stock
  │                             └──[Stok Ada + Hantar]► delivered
  └──[Tolak]──────────────► rejected
```

---

*ICTServe | M02 Use Cases | Versi 1.0 | 18 April 2026*
