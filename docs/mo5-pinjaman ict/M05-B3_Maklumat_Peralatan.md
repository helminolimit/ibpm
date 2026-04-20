# M05-B3 — Maklumat Peralatan & Tempoh Pinjaman
**M05 Pinjaman ICT | Borang C Bahagian 3**

> Fasa: 1 — Permohonan | Pelaku: Pemohon | Diisi semasa: Menghantar permohonan

---

## Tujuan

Merekodkan maklumat peralatan ICT yang dipohon, tujuan penggunaan, lokasi, dan tempoh pinjaman. Bahagian ini menjadi asas kepada semakan ketersediaan peralatan oleh pentadbir BPM.

---

## Bahagian 3 — Maklumat Peralatan

### 3A — Senarai Peralatan Dipohon

Pemohon boleh memohon **lebih daripada satu jenis peralatan** dalam satu permohonan. Setiap baris mewakili satu jenis peralatan.

| Medan | Jenis Input | Wajib | Keterangan |
|-------|------------|-------|------------|
| Jenis Peralatan | Dropdown | Ya | Lihat senarai jenis peralatan di bawah |
| Kuantiti | Nombor (min: 1) | Ya | Bilangan unit yang diperlukan |
| Catatan | Teks | Tidak | Spesifikasi khusus jika ada (contoh: perlu HDMI port) |

**Butang:** `+ Tambah Peralatan Lain` — menambah baris baharu untuk peralatan tambahan

#### Senarai Jenis Peralatan

| Kod | Jenis Peralatan |
|-----|-----------------|
| `laptop` | Laptop |
| `projektor` | Projektor |
| `tablet` | Tablet |
| `kamera` | Kamera Digital |
| `mikrofon` | Mikrofon |
| `pembesar_suara` | Pembesar Suara (Speaker) |
| `skrin_paparan` | Skrin Paparan Mudah Alih |
| `pencetak_mudah_alih` | Pencetak Mudah Alih |
| `lain` | Lain-lain (nyatakan dalam catatan) |

---

### 3B — Tujuan & Lokasi

| Medan | Jenis Input | Wajib | Keterangan |
|-------|------------|-------|------------|
| Tujuan Permohonan | Textarea | Ya | Huraian ringkas tujuan penggunaan peralatan |
| Lokasi Penggunaan | Teks | Ya | Tempat peralatan akan digunakan (Bilik / Dewan / Lokasi luar) |

**Contoh Tujuan:** Pembentangan kepada delegasi luar, Program latihan dalaman, Majlis rasmi kementerian, Lawatan kerja luar pejabat

---

### 3C — Tempoh Pinjaman

| Medan | Jenis Input | Wajib | Keterangan |
|-------|------------|-------|------------|
| Tarikh Pinjaman | Date picker | Ya | Tarikh peralatan perlu diambil |
| Tarikh Dijangka Pulang | Date picker | Ya | Tarikh peralatan akan dikembalikan |
| Jumlah Hari | Auto-kira | — | Dikira automatik oleh sistem |

#### Peraturan Validasi Tarikh

- Tarikh Pinjaman **tidak boleh** sebelum tarikh hari ini
- Tarikh Dijangka Pulang **mesti sama atau selepas** Tarikh Pinjaman
- Permohonan hendaklah dikemukakan **sekurang-kurangnya 3 hari bekerja** sebelum Tarikh Pinjaman
- Tiada had maksimum tempoh pinjaman, tetapi pentadbir BPM boleh menentukan had mengikut dasar semasa

---

## Peraturan Logik & Validasi

| Peraturan | Keterangan |
|-----------|------------|
| Minimum 1 baris peralatan | Borang tidak boleh dihantar tanpa sekurang-kurangnya satu peralatan dipilih |
| Kuantiti minimum | Setiap peralatan mesti mempunyai kuantiti sekurang-kurangnya 1 |
| Jenis `lain-lain` | Jika dipilih, medan Catatan menjadi wajib |
| Tarikh pinjaman | Mesti sekurang-kurangnya hari semasa atau masa hadapan |
| Tarikh pulang | Tidak boleh lebih awal daripada tarikh pinjaman |

---

## Semakan Ketersediaan Peralatan

Selepas permohonan disokong Ketua Bahagian, pentadbir BPM akan menyemak ketersediaan berdasarkan maklumat ini:

| Semakan | Keterangan |
|---------|------------|
| Stok peralatan mencukupi | Jumlah unit yang tersedia ≥ kuantiti yang dipohon |
| Tiada konflik tarikh | Peralatan tidak dalam pinjaman lain pada tarikh yang sama |
| Keadaan peralatan baik | Peralatan tidak dalam proses penyelenggaraan |

Jika peralatan **tidak tersedia**, pentadbir akan maklumkan pemohon dan menawarkan tarikh alternatif atau pilihan peralatan lain.

---

## Pangkalan Data

### Kolum dalam `loan_requests`

```sql
purpose                 TEXT NOT NULL
location                VARCHAR(255) NOT NULL
loan_date               DATE NOT NULL
expected_return_date    DATE NOT NULL
equipment_list          JSON NOT NULL
```

### Struktur JSON `equipment_list`

```json
[
  {
    "type"     : "laptop",
    "quantity" : 1,
    "notes"    : "Perlu port HDMI"
  },
  {
    "type"     : "projektor",
    "quantity" : 1,
    "notes"    : ""
  }
]
```

### Contoh Rekod Lengkap

```json
{
  "purpose"              : "Pembentangan Program Lancongan 2026 kepada delegasi luar",
  "location"             : "Bilik Mesyuarat Utama, Aras 4, Blok D, MOTAC",
  "loan_date"            : "2026-04-18",
  "expected_return_date" : "2026-04-20",
  "equipment_list"       : [
    { "type": "laptop",    "quantity": 1, "notes": "Perlu port HDMI" },
    { "type": "projektor", "quantity": 1, "notes": "" }
  ]
}
```

---

## Kaitan dengan Bahagian Lain

| Bahagian | Kaitan |
|----------|--------|
| Bahagian 5 — Sokongan Ketua | Ringkasan peralatan & tempoh dipaparkan dalam e-mel dan halaman kelulusan |
| Bahagian 8 — Maklumat Terperinci | Pentadbir BPM akan isi jenama, model & siri peralatan sebenar yang dikeluarkan |
| Bahagian 6 — Pengeluaran | Tarikh pinjaman menjadi tarikh pengeluaran peralatan |
| Bahagian 7 — Pemulangan | Tarikh dijangka pulang menjadi tarikh pemulangan sasaran |

---

*ICTServe | M05-B3 — Maklumat Peralatan & Tempoh | Borang C Bahagian 3 | Versi 1.0 | 16 April 2026*
