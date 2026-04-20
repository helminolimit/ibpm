# M05-B7 — Rekod Pemulangan Peralatan
**M05 Pinjaman ICT | Borang C Bahagian 7**

> Fasa: 4 — Pemulangan | Pelaku: Pentadbir BPM & Pemohon / Wakil Pemulangan | Diisi semasa: Peralatan dikembalikan

---

## Tujuan

Merekodkan transaksi pemulangan peralatan ICT secara rasmi — mencatatkan Pegawai Yang Memulangkan dan Pegawai Terima Pulangan (BPM), semakan keadaan peralatan, serta catatan jika terdapat kerosakan atau kehilangan aksesori.

---

## Prasyarat

Bahagian 7 hanya boleh diisi apabila:

| Syarat | Status |
|--------|--------|
| Bahagian 6 telah lengkap diisi (peralatan telah dikeluarkan) | Lengkap |
| Status permohonan semasa | `Dipinjam` atau `Lewat Pulang` |

---

## Bahagian 7 — Semasa Pemulangan

### Maklumat Pegawai Terima Pulangan (BPM)

Diisi secara automatik berdasarkan akaun pentadbir BPM yang log masuk dan menguruskan pemulangan.

| Medan | Jenis | Sumber | Keterangan |
|-------|-------|--------|------------|
| Nama Penuh Pegawai Terima Pulangan | Teks (auto) | Akaun login pentadbir | Kakitangan BPM yang menerima peralatan |
| Jawatan & Gred | Teks (auto) | Akaun login pentadbir | |
| Tarikh & Masa Pemulangan | DateTime (auto) | Sistem | Dicatat secara automatik semasa rekod disimpan |

### Maklumat Pegawai Yang Memulangkan

Diisi oleh pentadbir BPM berdasarkan individu yang hadir memulangkan peralatan.

| Medan | Jenis Input | Wajib | Keterangan |
|-------|------------|-------|------------|
| Nama Penuh | Teks | Ya | Pemohon atau Wakil Pemulangan yang hadir |
| Bahagian / Unit | Teks | Ya | Bahagian individu yang memulangkan |
| Pengesahan Identiti | Checkbox | Ya | Pentadbir BPM mengesahkan identiti pemulang |

### Semakan Keadaan Peralatan

| Medan | Jenis Input | Wajib | Keterangan |
|-------|------------|-------|------------|
| Keadaan Peralatan | Dropdown | Ya | Lihat pilihan di bawah |
| Catatan Pemulangan | Textarea | Ya* | *Wajib jika keadaan bukan "Baik" |

#### Pilihan Keadaan Peralatan

| Nilai | Label | Tindakan Lanjut |
|-------|-------|-----------------|
| `baik` | Baik — tiada masalah | Tiada |
| `rosak_kecil` | Ada Kerosakan Kecil | Wajib isi catatan, notifikasi dihantar |
| `rosak_besar` | Ada Kerosakan Besar | Wajib isi catatan, tanda untuk siasatan |
| `hilang_aksesori` | Kehilangan Aksesori | Wajib isi catatan, nyatakan aksesori yang hilang |
| `hilang_peralatan` | Peralatan Hilang | Kes khas — eskalasi kepada penyelia BPM |

---

## Cara Kerja Pengesahan Identiti Pemulang

```
Jika pemulang = PEMOHON
└── Semak berdasarkan: Kad pengenalan atau kad pekerja

Jika pemulang = WAKIL PEMULANGAN (Bahagian 4B)
└── Semak nama dengan rekod Bahagian 4B
└── Semak IC / No. Pekerja jika didaftarkan
└── Hubungi pemohon jika ada keraguan

Jika pemulang BUKAN pemohon dan BUKAN wakil berdaftar
└── Hubungi pemohon untuk pengesahan sebelum terima pulangan
```

---

## Prosedur Semakan Peralatan oleh BPM

Sebelum menerima dan merekodkan pemulangan, pentadbir BPM wajib:

1. Semak bilangan peralatan — pastikan **sama** dengan rekod Bahagian 8
2. Semak aksesori — bandingkan dengan senarai aksesori dalam Bahagian 8
3. Semak keadaan fizikal peralatan — cari kerosakan, calar atau kerosakan lain
4. Uji fungsi asas jika perlu (contoh: pastikan laptop boleh dihidupkan)
5. Rekodkan keadaan sebenar dalam sistem

---

## Kemaskini Status

Apabila rekod Bahagian 7 disimpan:

| Tindakan | Nilai |
|----------|-------|
| Status permohonan | `Selesai` |
| `returned_by_name` | Nama pemulang sebenar |
| `returned_by_unit` | Bahagian pemulang |
| `received_by` | ID pentadbir BPM yang terima |
| `returned_at` | Masa semasa (auto) |
| `return_condition` | Keadaan peralatan |
| `return_notes` | Catatan jika ada |

---

## Notifikasi Selepas Pemulangan

### Jika Peralatan dalam Keadaan Baik

| Penerima | Kandungan |
|----------|-----------|
| Pemohon Asal | Pengesahan pemulangan berjaya — permohonan selesai |

### Jika Ada Kerosakan / Kehilangan

| Penerima | Kandungan |
|----------|-----------|
| Pemohon Asal | Notifikasi masalah yang dikesan — catatan dan tindakan lanjut |
| Penyelia BPM | Laporan kerosakan untuk tindakan selanjutnya |

### Contoh E-mel Pengesahan Selesai

```
Subjek: [ICTServe] Peralatan Pinjaman Telah Dipulangkan — #ICT-2026-047

Assalamualaikum,

Peralatan pinjaman bagi permohonan berikut telah berjaya dipulangkan.

  No. Tiket      : #ICT-2026-047
  Peralatan      : Laptop × 1, Projektor × 1
  Dipulangkan    : 20 April 2026, 4:30 petang
  Dipulangkan oleh: Ahmad Kamal bin Razali (Pemohon)
  Diterima oleh  : Nur Wahidah binti Mohamed (BPM)
  Keadaan        : Baik — tiada masalah

Terima kasih. Permohonan ini telah ditutup.
```

### Contoh E-mel Jika Ada Kerosakan

```
Subjek: [ICTServe] Makluman Kerosakan Peralatan — #ICT-2026-047

Assalamualaikum,

Peralatan yang dipulangkan bagi permohonan #ICT-2026-047 didapati
mempunyai masalah berikut:

  Keadaan  : Ada Kerosakan Kecil
  Catatan  : Skrin laptop terdapat calar pada bahagian kanan atas.
             Power adapter hilang.

Sila hubungi Unit Operasi, Teknikal & Khidmat Pengguna, BPM untuk
tindakan lanjut dalam tempoh 3 hari bekerja.
```

---

## Peringatan Automatik (Sebelum Tarikh Pulang)

Sistem menghantar peringatan automatik **1 hari sebelum** tarikh dijangka pulang:

```
Subjek: [ICTServe] Peringatan: Peralatan Pinjaman Perlu Dipulangkan Esok — #ICT-2026-047

Assalamualaikum,

Ini adalah peringatan bahawa peralatan pinjaman anda perlu
dipulangkan ESOK, 20 April 2026.

  No. Tiket   : #ICT-2026-047
  Peralatan   : Laptop × 1, Projektor × 1
  Tarikh Pulang: 20 April 2026

Sila pulangkan peralatan kepada:
  Unit Operasi, Teknikal & Khidmat Pengguna
  Bahagian Pengurusan Maklumat, MOTAC
```

---

## Pengurusan Kes Lewat Pulang

Jika peralatan tidak dipulangkan selepas tarikh dijangka pulang:

| Hari Selepas Tarikh Pulang | Tindakan Sistem |
|---------------------------|-----------------|
| Hari yang sama (tamat waktu pejabat) | Status ditukar kepada `Lewat Pulang` |
| Hari ke-1 | Notifikasi peringatan kedua kepada pemohon |
| Hari ke-3 | Notifikasi eskalasi kepada Ketua Bahagian pemohon |
| Hari ke-7 | Notifikasi kepada penyelia BPM untuk tindakan lanjut |

---

## Pangkalan Data

### Kolum dalam `loan_requests`

```sql
returned_by_name        VARCHAR(255) NULL    -- Nama pemulang sebenar
returned_by_unit        VARCHAR(255) NULL    -- Bahagian pemulang
received_by             BIGINT UNSIGNED NULL -- FK → users.id (pentadbir BPM)
returned_at             TIMESTAMP NULL       -- Masa peralatan dipulangkan
return_condition        ENUM(
                            'baik',
                            'rosak_kecil',
                            'rosak_besar',
                            'hilang_aksesori',
                            'hilang_peralatan'
                        ) NULL
return_notes            TEXT NULL            -- Wajib jika bukan 'baik'
```

### Contoh Rekod — Selesai Baik

```json
{
  "returned_by_name"   : "Ahmad Kamal bin Razali",
  "returned_by_unit"   : "Unit Promosi Digital",
  "received_by"        : 8,
  "returned_at"        : "2026-04-20T16:30:00Z",
  "return_condition"   : "baik",
  "return_notes"       : null,
  "status"             : "selesai"
}
```

### Contoh Rekod — Ada Kerosakan

```json
{
  "returned_by_name"   : "Ahmad Kamal bin Razali",
  "returned_by_unit"   : "Unit Promosi Digital",
  "received_by"        : 8,
  "returned_at"        : "2026-04-20T16:30:00Z",
  "return_condition"   : "hilang_aksesori",
  "return_notes"       : "Power adapter laptop tidak dikembalikan.",
  "status"             : "selesai"
}
```

---

## Kaitan dengan Bahagian Lain

| Bahagian | Kaitan |
|----------|--------|
| Bahagian 4B — Wakil Pemulangan | BPM rujuk rekod ini untuk mengesahkan identiti pemulang |
| Bahagian 6 — Pengeluaran | Pemulangan hanya boleh direkod selepas pengeluaran disahkan |
| Bahagian 8 — Maklumat Terperinci | Senarai aksesori dalam Bahagian 8 menjadi rujukan semakan semasa pemulangan |

---

*ICTServe | M05-B7 — Rekod Pemulangan Peralatan | Borang C Bahagian 7 | Versi 1.0 | 16 April 2026*
