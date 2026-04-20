# M05-B4 — Wakil Pengambilan & Pemulangan
**M05 Pinjaman ICT | Borang C Bahagian 4 (Baharu)**

> Fasa: 1 — Permohonan | Pelaku: Pemohon | Diisi semasa: Menghantar permohonan

---

## Tujuan

Merekodkan maklumat wakil yang diberi kuasa untuk mengambil dan/atau memulangkan peralatan ICT bagi pihak pemohon. Wakil pengambilan dan wakil pemulangan **boleh merupakan individu yang berbeza**.

Bahagian ini adalah **tambahan baharu** kepada Borang C asal untuk menyokong fleksibiliti operasi peminjaman.

---

## Konsep Wakil

| Jenis Wakil | Takrifan |
|-------------|----------|
| **Wakil Pengambilan** | Kakitangan yang hadir secara fizikal di BPM untuk **mengambil** peralatan bagi pihak pemohon |
| **Wakil Pemulangan** | Kakitangan yang hadir secara fizikal di BPM untuk **memulangkan** peralatan bagi pihak pemohon |

> Wakil **tidak menanggung tanggungjawab** ke atas peralatan. Tanggungjawab penuh kekal pada Pegawai Bertanggungjawab (Bahagian 2).

---

## Bahagian 4A — Wakil Pengambilan

### Toggle Pengaktifan

| Elemen | Jenis | Lalai | Keterangan |
|--------|-------|-------|------------|
| Peralatan akan diambil oleh wakil (bukan pemohon) | Toggle (Boolean) | Mati (Off) | Aktifkan untuk paparkan medan di bawah |

### Medan Maklumat Wakil Pengambilan

*(Dipaparkan hanya apabila toggle aktif)*

| Medan | Jenis Input | Wajib | Keterangan |
|-------|------------|-------|------------|
| Nama Penuh | Teks | Ya* | Nama penuh wakil yang akan hadir |
| No. Kad Pengenalan / No. Pekerja | Teks | Tidak | Untuk pengesahan identiti semasa pengambilan di kaunter BPM |
| No. Telefon | Input telefon | Ya* | Untuk dihubungi jika perlu |
| Bahagian / Unit | Teks | Tidak | Bahagian / unit wakil |

> *Wajib diisi apabila toggle aktif

---

## Bahagian 4B — Wakil Pemulangan

### Toggle Pengaktifan

| Elemen | Jenis | Lalai | Keterangan |
|--------|-------|-------|------------|
| Peralatan akan dipulangkan oleh wakil (bukan pemohon) | Toggle (Boolean) | Mati (Off) | Aktifkan untuk paparkan medan di bawah |

### Medan Maklumat Wakil Pemulangan

*(Dipaparkan hanya apabila toggle aktif)*

| Medan | Jenis Input | Wajib | Keterangan |
|-------|------------|-------|------------|
| Nama Penuh | Teks | Ya* | Nama penuh wakil yang akan hadir memulangkan |
| No. Kad Pengenalan / No. Pekerja | Teks | Tidak | Untuk pengesahan identiti semasa pemulangan di kaunter BPM |
| No. Telefon | Input telefon | Ya* | Untuk dihubungi jika perlu |
| Bahagian / Unit | Teks | Tidak | Bahagian / unit wakil |

> *Wajib diisi apabila toggle aktif

---

## Senario Penggunaan

| Senario | 4A Aktif | 4B Aktif | Keterangan |
|---------|----------|----------|------------|
| **S1** — Standard | Tidak | Tidak | Pemohon sendiri ambil dan pulang |
| **S2** — Wakil ambil sahaja | Ya | Tidak | Pemohon tidak dapat hadir semasa pengambilan |
| **S3** — Wakil pulang sahaja | Tidak | Ya | Pemohon ambil sendiri, wakil pulangkan |
| **S4** — Dua wakil berbeza | Ya | Ya | Wakil ambil dan wakil pulang adalah individu berbeza |
| **S5** — Wakil sama untuk ambil & pulang | Ya | Ya | Isi nama yang sama pada 4A dan 4B |

---

## Peraturan Logik & Validasi

| Peraturan | Keterangan |
|-----------|------------|
| Toggle bebas antara satu sama lain | Mengaktifkan 4A tidak memaksa 4B diisi, dan sebaliknya |
| Toggle dimatikan | Medan dikosongkan dan disembunyikan secara automatik |
| Wakil boleh sama dengan pemohon | Tiada sekatan teknikal, tetapi tidak digalakkan (gunakan toggle mati sahaja) |
| Rekod disimpan walaupun tiada wakil | `proxy_collector` dan `proxy_returner` disimpan sebagai `null` |

---

## Kegunaan Rekod Wakil oleh BPM

Semasa proses pengeluaran (Bahagian 6) dan pemulangan (Bahagian 7), pentadbir BPM akan:

1. **Menyemak** nama wakil yang didaftarkan dalam Bahagian 4
2. **Mengesahkan identiti** individu yang hadir berdasarkan nama dan IC/No. Pekerja
3. **Merekodkan** nama penerima atau pemulang sebenar dalam Bahagian 6 dan 7
4. Jika individu yang hadir **tidak sepadan** dengan rekod wakil, BPM boleh menghubungi pemohon untuk pengesahan

---

## Pangkalan Data

### Kolum dalam `loan_requests`

```sql
-- Bahagian 4A: Wakil pengambilan (nullable)
proxy_collector     JSON NULL
-- Struktur JSON:
-- {
--   "name"         : "Nama Penuh",
--   "ic_or_staff"  : "No. IC / No. Pekerja",
--   "phone"        : "No. Telefon",
--   "unit"         : "Bahagian / Unit"
-- }

-- Bahagian 4B: Wakil pemulangan (nullable)
proxy_returner      JSON NULL
-- Struktur JSON: (sama dengan proxy_collector)
```

### Contoh Data — Senario 4 (Dua Wakil Berbeza)

```json
{
  "proxy_collector": {
    "name"        : "Mohd Yusof bin Hamzah",
    "ic_or_staff" : "JTK001",
    "phone"       : "012-345 6789",
    "unit"        : "Unit Operasi, Teknikal & Khidmat Pengguna"
  },
  "proxy_returner": {
    "name"        : "Ashraf bin Mohd Hanafiah",
    "ic_or_staff" : "JTK002",
    "phone"       : "011-234 5678",
    "unit"        : "Unit Operasi, Teknikal & Khidmat Pengguna"
  }
}
```

### Contoh Data — Senario 1 (Tiada Wakil)

```json
{
  "proxy_collector": null,
  "proxy_returner":  null
}
```

---

## Paparan dalam Halaman Kelulusan Ketua Bahagian

Maklumat wakil dipaparkan dalam halaman kelulusan (Bahagian 5) supaya Ketua Bahagian sedar siapa yang akan mengambil dan memulangkan peralatan:

```
Wakil Pengambilan : Mohd Yusof bin Hamzah (JTK001) — 012-345 6789
Wakil Pemulangan  : Ashraf bin Mohd Hanafiah (JTK002) — 011-234 5678
```

Jika tiada wakil didaftarkan:
```
Wakil Pengambilan : Tiada (pemohon sendiri akan hadir)
Wakil Pemulangan  : Tiada (pemohon sendiri akan hadir)
```

---

## Kaitan dengan Bahagian Lain

| Bahagian | Kaitan |
|----------|--------|
| Bahagian 1 — Pemohon | Pemohon dan wakil adalah individu berbeza |
| Bahagian 2 — Pegawai Bertanggungjawab | Tanggungjawab peralatan kekal pada Pegawai Bertanggungjawab walaupun wakil yang hadir |
| Bahagian 5 — Sokongan Ketua | Maklumat wakil dipaparkan dalam halaman kelulusan untuk makluman Ketua Bahagian |
| Bahagian 6 — Pengeluaran | BPM rujuk 4A untuk semak siapa yang berhak mengambil peralatan |
| Bahagian 7 — Pemulangan | BPM rujuk 4B untuk semak siapa yang berhak memulangkan peralatan |

---

*ICTServe | M05-B4 — Wakil Pengambilan & Pemulangan | Borang C Bahagian 4 (Baharu) | Versi 1.0 | 16 April 2026*
