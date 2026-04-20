# M05-B6 — Rekod Pengeluaran Peralatan
**M05 Pinjaman ICT | Borang C Bahagian 6**

> Fasa: 3 — Pengeluaran | Pelaku: Pentadbir BPM & Pemohon / Wakil Pengambilan | Diisi semasa: Peralatan diserahkan

---

## Tujuan

Merekodkan transaksi pengeluaran peralatan ICT secara rasmi — mencatatkan Pegawai Pengeluar (BPM) dan Pegawai Penerima (pemohon atau wakil pengambilan) lengkap dengan tarikh, masa dan pengesahan penerimaan.

---

## Prasyarat

Bahagian 6 hanya boleh diisi apabila:

| Syarat | Status |
|--------|--------|
| Permohonan telah disokong Ketua Bahagian (Bahagian 5) | `Disokong` |
| Pentadbir BPM telah mengisi Bahagian 8 (maklumat terperinci peralatan) | Lengkap |
| Status permohonan semasa | `Dalam Tindakan` |

---

## Bahagian 6 — Semasa Peminjaman

### Maklumat Pegawai Pengeluar (BPM)

Diisi secara automatik berdasarkan akaun pentadbir BPM yang log masuk dan menguruskan pengeluaran.

| Medan | Jenis | Sumber | Keterangan |
|-------|-------|--------|------------|
| Nama Penuh Pegawai Pengeluar | Teks (auto) | Akaun login pentadbir | Kakitangan BPM yang mengeluarkan peralatan |
| Jawatan & Gred | Teks (auto) | Akaun login pentadbir | |
| Bahagian / Unit | Teks (auto) | Akaun login pentadbir | Unit Operasi, Teknikal & Khidmat Pengguna |
| Tarikh & Masa Pengeluaran | DateTime (auto) | Sistem | Dicatat secara automatik semasa rekod disimpan |

### Maklumat Pegawai Penerima

Diisi oleh pentadbir BPM berdasarkan individu yang hadir mengambil peralatan.

| Medan | Jenis Input | Wajib | Keterangan |
|-------|------------|-------|------------|
| Nama Penuh Pegawai Penerima | Teks | Ya | Pemohon atau Wakil Pengambilan yang hadir |
| Bahagian / Unit | Teks | Ya | Bahagian penerima |
| Pengesahan Identiti | Checkbox | Ya | Pentadbir BPM mengesahkan identiti penerima |

---

## Cara Kerja Pengesahan Identiti Penerima

Pentadbir BPM akan menyemak maklumat berikut sebelum menyerahkan peralatan:

```
Jika penerima = PEMOHON
└── Semak berdasarkan: Kad pengenalan atau kad pekerja

Jika penerima = WAKIL PENGAMBILAN (Bahagian 4A)
└── Semak nama dengan rekod Bahagian 4A
└── Semak IC / No. Pekerja jika didaftarkan
└── Hubungi pemohon jika ada keraguan
```

| Situasi | Tindakan BPM |
|---------|-------------|
| Penerima sepadan dengan rekod pemohon | Teruskan pengeluaran |
| Penerima sepadan dengan rekod wakil (4A) | Teruskan pengeluaran |
| Penerima **tidak** sepadan dengan mana-mana rekod | Hubungi pemohon dahulu sebelum serahkan peralatan |

---

## Kemaskini Status

Apabila rekod Bahagian 6 disimpan:

| Tindakan | Nilai |
|----------|-------|
| Status permohonan | `Dipinjam` |
| `issued_by` | ID pentadbir BPM yang mengeluarkan |
| `issued_to_name` | Nama penerima sebenar (pemohon atau wakil) |
| `issued_at` | Masa semasa (auto) |

---

## Notifikasi Selepas Pengeluaran

| Penerima | Kandungan |
|----------|-----------|
| Pemohon Asal | Pengesahan bahawa peralatan telah dikeluarkan + tarikh pemulangan yang perlu dipatuhi |

### Contoh E-mel kepada Pemohon

```
Subjek: [ICTServe] Peralatan Pinjaman Telah Dikeluarkan — #ICT-2026-047

Assalamualaikum,

Peralatan pinjaman anda telah berjaya dikeluarkan.

  No. Tiket     : #ICT-2026-047
  Peralatan     : Laptop × 1, Projektor × 1
  Dikeluarkan   : 18 April 2026, 9:00 pagi
  Diterima oleh : Mohd Yusof bin Hamzah (Wakil)
  Tarikh Pulang : 20 April 2026 (SEBELUM 5:00 petang)

Sila pastikan peralatan dipulangkan dalam keadaan baik
sebelum atau pada tarikh yang ditetapkan.

⚠ Peringatan akan dihantar pada 19 April 2026.
```

---

## Pangkalan Data

### Kolum dalam `loan_requests`

```sql
issued_by           BIGINT UNSIGNED NULL    -- FK → users.id (pentadbir BPM)
issued_to_name      VARCHAR(255) NULL       -- Nama penerima sebenar
issued_to_unit      VARCHAR(255) NULL       -- Bahagian penerima
issued_at           TIMESTAMP NULL          -- Masa peralatan diserahkan
```

### Contoh Rekod

```json
{
  "issued_by"      : 15,
  "issued_to_name" : "Mohd Yusof bin Hamzah",
  "issued_to_unit" : "Unit Operasi, Teknikal & Khidmat Pengguna",
  "issued_at"      : "2026-04-18T09:00:00Z",
  "status"         : "dipinjam"
}
```

---

## Paparan dalam Dashboard

### Paparan Pemohon (selepas pengeluaran)

```
Status        : DIPINJAM
Dikeluarkan   : 18 April 2026, 9:00 pagi
Diterima oleh : Mohd Yusof bin Hamzah
Tarikh Pulang : 20 April 2026
```

### Paparan Pentadbir BPM

```
Status        : DIPINJAM
Pegawai Keluar: Nur Wahidah binti Mohamed (BPM)
Penerima      : Mohd Yusof bin Hamzah
Masa Keluar   : 18 Apr 2026, 09:00 pagi
Dijangka Pulang: 20 Apr 2026
```

---

## Kaitan dengan Bahagian Lain

| Bahagian | Kaitan |
|----------|--------|
| Bahagian 4A — Wakil Pengambilan | BPM rujuk rekod ini untuk mengesahkan identiti penerima |
| Bahagian 5 — Sokongan | Pengeluaran hanya boleh dilakukan selepas permohonan disokong |
| Bahagian 8 — Maklumat Terperinci | Bahagian 8 mesti lengkap sebelum pengeluaran boleh direkodkan |
| Bahagian 7 — Pemulangan | Rekod Bahagian 6 menjadi rujukan semasa pemulangan |

---

*ICTServe | M05-B6 — Rekod Pengeluaran Peralatan | Borang C Bahagian 6 | Versi 1.0 | 16 April 2026*
