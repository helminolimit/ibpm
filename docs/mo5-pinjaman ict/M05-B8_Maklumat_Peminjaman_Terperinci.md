# M05-B8 — Maklumat Peminjaman Terperinci
**M05 Pinjaman ICT | Borang C Bahagian 8**

> Fasa: 3 — Pengeluaran | Pelaku: Pentadbir BPM | Diisi semasa: Menyediakan peralatan untuk dikeluarkan

---

## Tujuan

Merekodkan maklumat teknikal terperinci setiap unit peralatan yang akan dikeluarkan — termasuk jenama, model, nombor siri, nombor tag aset dan senarai aksesori yang disertakan. Rekod ini menjadi **dokumen audit rasmi** untuk semakan semasa pemulangan (Bahagian 7).

---

## Konteks Pengisian

Bahagian 8 diisi oleh **Pentadbir BPM** selepas permohonan disokong dan sebelum peralatan dikeluarkan. Maklumat ini **tidak diisi oleh pemohon**.

| Bila diisi | Oleh siapa | Tujuan |
|-----------|------------|--------|
| Selepas permohonan disokong Ketua Bahagian | Pentadbir Unit Operasi, Teknikal & Khidmat Pengguna | Rekod teknikal peralatan sebenar yang dikeluarkan |

---

## Bahagian 8 — Maklumat Peminjaman (Kegunaan BPM Sahaja)

Setiap baris mewakili **satu unit peralatan**. Jika pemohon memohon Laptop × 2, maka terdapat 2 baris berasingan untuk setiap unit laptop.

### Medan untuk Setiap Unit Peralatan

| Medan | Jenis Input | Wajib | Keterangan |
|-------|------------|-------|------------|
| Bil. | Auto (nombor urutan) | — | Jana automatik: 1, 2, 3... |
| Jenis Peralatan | Teks (auto dari B3) | Ya | Diisi automatik daripada Bahagian 3 |
| Jenama dan Model | Teks | Ya | Contoh: Dell Latitude 5540, Epson EB-X51 |
| No. Siri / Tag ID | Teks | Ya | No. siri pengilang atau tag ID aset MOTAC |
| Aksesori Disertakan | Checkbox (berbilang) | Ya | Senarai aksesori yang disertakan bersama peralatan |

### Senarai Aksesori Standard

| Aksesori | Kod |
|----------|-----|
| Power Adapter (Penyesuai Kuasa) | `power_adapter` |
| Beg / Kes Peralatan | `beg` |
| Mouse (Tetikus) | `mouse` |
| Kabel USB | `kabel_usb` |
| Kabel HDMI / VGA | `kabel_hdmi_vga` |
| Remote / Alat Kawalan Jauh | `remote` |
| Lain-lain (nyatakan) | `lain` |

Jika aksesori `lain` dipilih, medan teks tambahan dipaparkan untuk nyatakan aksesori berkenaan.

---

## Contoh Pengisian

### Contoh: Permohonan Laptop × 1 + Projektor × 1

**Baris 1 — Laptop**

| Medan | Nilai |
|-------|-------|
| Bil. | 1 |
| Jenis Peralatan | Laptop |
| Jenama dan Model | Dell Latitude 5540 |
| No. Siri / Tag ID | DL5540-MOTAC-042 |
| Aksesori | ☑ Power Adapter ☑ Beg ☑ Mouse ☐ Kabel USB ☐ Kabel HDMI/VGA ☐ Remote |

**Baris 2 — Projektor**

| Medan | Nilai |
|-------|-------|
| Bil. | 2 |
| Jenis Peralatan | Projektor |
| Jenama dan Model | Epson EB-X51 |
| No. Siri / Tag ID | EPX51-MOTAC-007 |
| Aksesori | ☐ Power Adapter ☐ Beg ☐ Mouse ☐ Kabel USB ☑ Kabel HDMI/VGA ☑ Remote |

---

## Kepentingan Rekod Ini

Bahagian 8 menjadi rujukan penting semasa **Bahagian 7 (Pemulangan)**:

| Semasa Pemulangan | Tindakan BPM |
|-------------------|-------------|
| Semak bilangan unit | Pastikan semua unit dalam Bahagian 8 dipulangkan |
| Semak aksesori | Bandingkan aksesori yang dipulangkan dengan rekod Bahagian 8 |
| Semak no. siri | Pastikan peralatan yang dipulangkan adalah unit yang sama |
| Rekod perbezaan | Jika ada aksesori hilang atau peralatan rosak, rekodkan dalam Bahagian 7 |

---

## Pangkalan Data

### Kolum dalam `loan_requests`

```sql
equipment_details   JSON NULL
-- Diisi oleh pentadbir BPM sebelum pengeluaran
-- Struktur JSON:
-- [
--   {
--     "bil"        : 1,
--     "type"       : "Laptop",
--     "brand_model": "Dell Latitude 5540",
--     "serial"     : "DL5540-MOTAC-042",
--     "accessories": ["power_adapter", "beg", "mouse"],
--     "notes"      : ""
--   },
--   { ... }
-- ]
```

### Contoh Data JSON Lengkap

```json
{
  "equipment_details": [
    {
      "bil"         : 1,
      "type"        : "Laptop",
      "brand_model" : "Dell Latitude 5540",
      "serial"      : "DL5540-MOTAC-042",
      "accessories" : ["power_adapter", "beg", "mouse"],
      "notes"       : ""
    },
    {
      "bil"         : 2,
      "type"        : "Projektor",
      "brand_model" : "Epson EB-X51",
      "serial"      : "EPX51-MOTAC-007",
      "accessories" : ["kabel_hdmi_vga", "remote"],
      "notes"       : "Tiada beg asal. Disimpan dalam kotak."
    }
  ]
}
```

---

## Paparan dalam Sistem

### Paparan Pentadbir BPM (semasa isi Bahagian 8)

```
┌─────────────────────────────────────────────────────────────────┐
│ BAHAGIAN 8 — MAKLUMAT PEMINJAMAN           [Kegunaan BPM Sahaja] │
├────┬────────────┬──────────────────────┬──────────────────────────┤
│ Bil│ Jenis      │ Jenama & Model       │ No. Siri / Tag ID        │
├────┼────────────┼──────────────────────┼──────────────────────────┤
│  1 │ Laptop     │ Dell Latitude 5540   │ DL5540-MOTAC-042         │
│    │ Aksesori   │ ☑ Power Adapter  ☑ Beg  ☑ Mouse              │
│    │            │ ☐ Kabel USB  ☐ HDMI/VGA  ☐ Remote           │
├────┼────────────┼──────────────────────┼──────────────────────────┤
│  2 │ Projektor  │ Epson EB-X51         │ EPX51-MOTAC-007          │
│    │ Aksesori   │ ☐ Power Adapter  ☐ Beg  ☐ Mouse              │
│    │            │ ☐ Kabel USB  ☑ HDMI/VGA  ☑ Remote           │
└────┴────────────┴──────────────────────┴──────────────────────────┘
```

---

## Peraturan & Validasi

| Peraturan | Keterangan |
|-----------|------------|
| Bilangan baris | Mesti sama atau lebih dengan bilangan unit dalam Bahagian 3 |
| No. Siri / Tag ID | Tidak boleh kosong — rekod wajib untuk audit aset |
| Aksesori | Sekurang-kurangnya satu aksesori mesti ditanda, atau nyatakan "Tiada aksesori" |
| Bahagian 8 wajib lengkap | Pengeluaran (Bahagian 6) tidak boleh direkod jika Bahagian 8 belum diisi |

---

## Kaitan dengan Bahagian Lain

| Bahagian | Kaitan |
|----------|--------|
| Bahagian 3 — Maklumat Peralatan | Jenis peralatan dalam B3 menjadi asas baris dalam B8 |
| Bahagian 6 — Pengeluaran | B8 mesti lengkap sebelum B6 boleh direkodkan |
| Bahagian 7 — Pemulangan | Senarai aksesori B8 menjadi senarai semak semasa pemulangan |

---

*ICTServe | M05-B8 — Maklumat Peminjaman Terperinci | Borang C Bahagian 8 | Versi 1.0 | 16 April 2026*
