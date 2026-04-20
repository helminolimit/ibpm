# M05-B1 — Maklumat Pemohon
**M05 Pinjaman ICT | Borang C Bahagian 1 & 1A**

> Fasa: 1 — Permohonan | Pelaku: Pemohon | Diisi semasa: Menghantar permohonan

---

## Tujuan

Merekodkan maklumat pemohon yang menghantar permohonan pinjaman peralatan ICT. Pemohon boleh membuat permohonan untuk keperluan diri sendiri **atau** bagi pihak kakitangan lain menggunakan fungsi wakil (Bahagian 1A).

---

## Bahagian 1 — Maklumat Pemohon Asal

> Pemohon asal ialah kakitangan yang **log masuk sistem** dan menghantar borang. Rekod ini kekal sebagai rujukan utama dan penerima semua notifikasi walaupun permohonan dibuat bagi pihak orang lain.

| Medan | Jenis Input | Wajib | Sumber / Keterangan |
|-------|------------|-------|---------------------|
| Nama Penuh | Teks (readonly) | Ya | Auto-isi daripada akaun login |
| Jawatan & Gred | Teks (readonly) | Ya | Auto-isi daripada akaun login |
| No. Telefon | Input telefon | Ya | Boleh dikemaskini jika berbeza |
| Bahagian / Unit | Dropdown | Ya | Auto-isi, boleh tukar jika perlu |
| E-mel | Input e-mel (readonly) | Ya | Auto-isi daripada akaun login |

### Peraturan Validasi

- Semua medan wajib mesti diisi sebelum borang boleh diteruskan
- Format e-mel mesti sah (`@motac.gov.my` disyorkan)
- No. telefon mesti dalam format Malaysia (contoh: 03-XXXX XXXX atau 01X-XXXXXXX)

---

## Bahagian 1A — Mohon Bagi Pihak Orang Lain

> Bahagian ini **hanya dipaparkan** apabila pemohon mengaktifkan toggle. Jika diaktifkan, maklumat kakitangan yang dipohonkan akan disimpan berasingan daripada rekod pemohon asal.

### Toggle Pengaktifan

| Elemen | Jenis | Lalai | Keterangan |
|--------|-------|-------|------------|
| Saya membuat permohonan bagi pihak kakitangan lain | Toggle (Boolean) | Mati (Off) | Aktifkan untuk paparkan medan di bawah |

### Medan Maklumat Kakitangan Dipohonkan

*(Dipaparkan hanya apabila toggle aktif)*

| Medan | Jenis Input | Wajib | Keterangan |
|-------|------------|-------|------------|
| Nama Penuh | Teks | Ya* | Nama penuh kakitangan yang dipohonkan |
| Jawatan & Gred | Teks | Ya* | Contoh: Pegawai Tadbir, N41 |
| No. Telefon | Input telefon | Ya* | No. telefon kakitangan berkaitan |
| Bahagian / Unit | Dropdown | Ya* | Bahagian / unit kakitangan berkaitan |
| Hubungan / Alasan Mewakili | Dropdown + Teks | Tidak | Ketua kepada Kakitangan / Rakan Seunit / Tugasan Rasmi / Lain-lain |

> *Wajib diisi apabila toggle aktif

### Peraturan Logik

- Apabila toggle **diaktifkan**: medan Bahagian 1A menjadi wajib
- Apabila toggle **dimatikan**: medan Bahagian 1A dikosongkan dan disembunyikan
- Notifikasi e-mel sepanjang proses **dihantar kepada Pemohon Asal** (Bahagian 1), bukan kepada kakitangan yang dipohonkan
- Rekod pemohon asal **tidak boleh sama** dengan kakitangan yang dipohonkan

---

## Senario Penggunaan

| Senario | Toggle 1A | Keterangan |
|---------|-----------|------------|
| Pemohon mohon untuk diri sendiri | Mati | Bahagian 1A tidak perlu diisi |
| Pemohon A mohon untuk Kakitangan B | Aktif | Isi maklumat Kakitangan B di Bahagian 1A |
| Ketua bahagian mohon untuk ahli pasukan | Aktif | Nyatakan "Ketua kepada Kakitangan" pada medan hubungan |

---

## Pangkalan Data

### Kolum dalam `loan_requests`

```sql
-- Bahagian 1: Pemohon asal
applicant_id        BIGINT UNSIGNED NOT NULL    -- FK → users.id

-- Bahagian 1A: Maklumat kakitangan dipohonkan (nullable)
on_behalf_of        JSON NULL
-- Struktur JSON:
-- {
--   "name"         : "Nama Penuh Kakitangan",
--   "position"     : "Jawatan & Gred",
--   "phone"        : "No. Telefon",
--   "unit"         : "Bahagian / Unit",
--   "relationship" : "Hubungan / Alasan"
-- }
```

### Contoh Data

```json
{
  "applicant_id": 42,
  "on_behalf_of": {
    "name": "Siti Norzahirah binti Ahmad",
    "position": "Pembantu Tadbir, N19",
    "phone": "03-2693 5188",
    "unit": "Unit Promosi Digital",
    "relationship": "Rakan Seunit"
  }
}
```

---

## Notifikasi

| Peristiwa | Penerima | Kandungan |
|-----------|----------|-----------|
| Borang disimpan (draf) | — | Tiada notifikasi |
| Permohonan berjaya dihantar | Pemohon Asal (Bahagian 1) | Pengesahan terima + nombor tiket |
| Semua kemaskini status | Pemohon Asal (Bahagian 1) | Kemaskini status terkini |

---

## Kaitan dengan Bahagian Lain

| Bahagian | Kaitan |
|----------|--------|
| Bahagian 2 — Pegawai Bertanggungjawab | Jika pemohon juga adalah pegawai bertanggungjawab, toggle di Bahagian 2 diaktifkan |
| Bahagian 4 — Wakil | Wakil pengambilan/pemulangan boleh berbeza daripada pemohon dan kakitangan dipohonkan |
| Bahagian 5 — Sokongan Ketua | E-mel kelulusan dihantar kepada Ketua Bahagian berdasarkan unit pemohon atau unit kakitangan dipohonkan |

---

*ICTServe | M05-B1 — Maklumat Pemohon | Borang C Bahagian 1 & 1A | Versi 1.0 | 16 April 2026*
