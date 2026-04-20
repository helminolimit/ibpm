# M05-B2 — Pegawai Bertanggungjawab
**M05 Pinjaman ICT | Borang C Bahagian 2**

> Fasa: 1 — Permohonan | Pelaku: Pemohon | Diisi semasa: Menghantar permohonan

---

## Tujuan

Merekodkan maklumat Pegawai Bertanggungjawab — iaitu kakitangan yang **bertanggungjawab secara penuh** terhadap penggunaan, keselamatan dan keadaan peralatan pinjaman sepanjang tempoh peminjaman.

Pegawai Bertanggungjawab boleh merupakan pemohon itu sendiri, atau individu lain yang dilantik secara khusus untuk tujuan tersebut.

---

## Definisi Peranan

| Peranan | Takrifan |
|---------|----------|
| **Pemohon** | Kakitangan yang menghantar borang permohonan |
| **Pegawai Bertanggungjawab** | Kakitangan yang menanggung tanggungjawab penuh ke atas keselamatan, penggunaan dan pemulangan peralatan |

> Kedua-dua peranan ini **boleh merupakan orang yang sama** atau **individu yang berbeza**.

---

## Bahagian 2 — Maklumat Pegawai Bertanggungjawab

### Toggle Pengesahan

| Elemen | Jenis | Lalai | Keterangan |
|--------|-------|-------|------------|
| Pemohon adalah Pegawai Bertanggungjawab | Toggle (Boolean) | Aktif (On) | Jika aktif, Bahagian 2 tidak perlu diisi |

### Medan Pegawai Bertanggungjawab

*(Dipaparkan hanya apabila toggle **dimatikan** — bermakna pegawai bertanggungjawab adalah individu yang berbeza)*

| Medan | Jenis Input | Wajib | Keterangan |
|-------|------------|-------|------------|
| Nama Penuh | Teks | Ya* | Nama penuh pegawai bertanggungjawab |
| Jawatan & Gred | Teks | Ya* | Contoh: Penolong Pengarah, N44 |
| No. Telefon | Input telefon | Ya* | No. telefon untuk dihubungi |

> *Wajib diisi apabila toggle dimatikan (pegawai bertanggungjawab ≠ pemohon)

---

## Peraturan Logik & Validasi

| Keadaan | Toggle | Tindakan Sistem |
|---------|--------|-----------------|
| Pemohon = Pegawai Bertanggungjawab | Aktif | Bahagian 2 disembunyikan, data pemohon digunakan secara automatik |
| Pemohon ≠ Pegawai Bertanggungjawab | Mati | Bahagian 2 dipaparkan, medan wajib diisi |
| Toggle dimatikan semula | Mati → Aktif | Medan Bahagian 2 dikosongkan |

### Peraturan Tambahan

- Pegawai Bertanggungjawab **tidak semestinya** Pegawai Penerima peralatan (yang mengambil peralatan secara fizikal)
- Pegawai Bertanggungjawab **menanggung tanggungjawab** walaupun wakil yang mengambil atau memulangkan peralatan
- Pengesahan tandatangan (Bahagian 4 Borang C asal) direkodkan secara digital dalam sistem

---

## Tanggungjawab Pegawai Bertanggungjawab

Berdasarkan syarat-syarat Borang C MOTAC:

1. Memastikan semua peralatan yang dipinjam adalah untuk **kegunaan rasmi** sahaja
2. Bertanggungjawab terhadap **keselamatan** peralatan sepanjang tempoh pinjaman
3. Menyemak **kesempurnaan peralatan** semasa menerima dan sebelum memulangkan
4. Menanggung **kos kerosakan atau kehilangan** peralatan jika berlaku semasa dalam pinjaman
5. Memastikan peralatan dipulangkan **sebelum atau pada tarikh yang ditetapkan**

---

## Pangkalan Data

### Kolum dalam `loan_requests`

```sql
-- Bahagian 2: Pegawai Bertanggungjawab (nullable jika sama dengan pemohon)
responsible_officer     JSON NULL
-- Struktur JSON (diisi hanya jika berbeza daripada pemohon):
-- {
--   "name"     : "Nama Penuh",
--   "position" : "Jawatan & Gred",
--   "phone"    : "No. Telefon"
-- }

-- Flag semak: adakah pemohon = pegawai bertanggungjawab
applicant_is_responsible    TINYINT(1) NOT NULL DEFAULT 1
```

### Logik Pangkalan Data

```php
// Jika toggle aktif (pemohon = pegawai bertanggungjawab)
$loanRequest->applicant_is_responsible = true;
$loanRequest->responsible_officer = null;

// Jika toggle mati (pegawai bertanggungjawab berbeza)
$loanRequest->applicant_is_responsible = false;
$loanRequest->responsible_officer = [
    'name'     => $request->responsible_name,
    'position' => $request->responsible_position,
    'phone'    => $request->responsible_phone,
];
```

### Contoh Data

```json
{
  "applicant_is_responsible": false,
  "responsible_officer": {
    "name": "Ahmad Ridhuan bin Noor Affendi",
    "position": "Ketua Penolong Setiausaha, N44",
    "phone": "03-2693 5100"
  }
}
```

---

## Kaitan dengan Bahagian Lain

| Bahagian | Kaitan |
|----------|--------|
| Bahagian 1 — Pemohon | Jika toggle aktif, data Bahagian 1 digunakan sebagai Pegawai Bertanggungjawab |
| Bahagian 4 — Wakil | Wakil pengambilan/pemulangan boleh berbeza daripada Pegawai Bertanggungjawab |
| Bahagian 6 — Pengeluaran | Pegawai Bertanggungjawab menanggung tanggungjawab walaupun wakil yang menandatangani |
| Bahagian 7 — Pemulangan | Keadaan peralatan menjadi tanggungjawab Pegawai Bertanggungjawab |

---

*ICTServe | M05-B2 — Pegawai Bertanggungjawab | Borang C Bahagian 2 | Versi 1.0 | 16 April 2026*
