# UC03 — Muat Naik Lampiran

**Modul:** M01 — Aduan ICT  
**Versi:** 1.0  
**Tarikh:** 18 April 2026  
**Status:** Draf  
**Hubungan:** `«include»` UC01 — Hantar Aduan ICT  

---

## 1. Maklumat Kes Penggunaan

| Perkara | Butiran |
|---|---|
| ID | UC03 |
| Nama | Muat Naik Lampiran |
| Modul | M01 — Aduan ICT |
| Pelakon Utama | Pemohon |
| Hubungan | Include dari UC01 |
| Keutamaan | Sederhana |

---

## 2. Penerangan Ringkas

Pemohon memuat naik fail sokongan seperti gambar kerosakan atau dokumen berkaitan semasa mengisi borang aduan ICT (UC01). Lampiran bersifat pilihan tetapi disarankan untuk membantu pentadbir memahami masalah dengan lebih jelas.

---

## 3. Prasyarat

- UC01 (Hantar Aduan ICT) sedang dalam proses — borang aduan telah dibuka.
- Pemohon mempunyai fail yang hendak dilampirkan.

---

## 4. Pascasyarat

**Jika berjaya:**
- Fail disimpan dalam direktori storage Laravel: `storage/app/aduan/{tahun}/{bulan}/`.
- Metadata fail direkodkan dalam jadual `lampiran_aduan`.
- Pratonton nama fail dipaparkan dalam borang aduan.

**Jika gagal:**
- Fail tidak disimpan.
- Mesej ralat dipaparkan kepada pemohon.
- Proses UC01 boleh diteruskan tanpa lampiran.

---

## 5. Aliran Utama

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 1 | Pemohon | Klik kawasan muat naik (*upload zone*) atau seret fail ke atas kawasan tersebut dalam borang aduan. |
| 2 | Sistem | Papar dialog pemilihan fail peanti atau terima fail yang diseret. |
| 3 | Pemohon | Pilih fail dari peanti (JPG, PNG, atau PDF). |
| 4 | Sistem | Semak jenis fail dan saiz fail secara client-side (Livewire). |
| 5 | Sistem | Muat naik fail ke pelayan secara sementara (`tmp` storage). |
| 6 | Sistem | Papar pratonton fail: nama fail, saiz, dan ikon jenis fail. Sertakan butang **×** untuk buang fail. |
| 7 | Pemohon | (Pilihan) Ulangi langkah 1–6 untuk lampiran tambahan. |
| 8 | Sistem | Apabila borang aduan dihantar (UC01 langkah 11), pindahkan fail dari `tmp` ke direktori tetap dan simpan metadata dalam jadual `lampiran_aduan`. |

---

## 6. Aliran Alternatif

### 6.1 Jenis Fail Tidak Disokong

| Langkah | Tindakan |
|---|---|
| 4a | Fail bukan JPG, PNG, atau PDF (contoh: `.exe`, `.docx`, `.zip`). |
| 4b | Sistem papar mesej: *"Jenis fail tidak disokong. Hanya JPG, PNG, dan PDF dibenarkan."* |
| 4c | Fail tidak dimuat naik. Pemohon boleh pilih fail lain. |

### 6.2 Saiz Fail Melebihi Had

| Langkah | Tindakan |
|---|---|
| 4a | Saiz fail melebihi 5MB. |
| 4b | Sistem papar mesej: *"Fail melebihi had saiz 5MB. Sila kompres atau pilih fail lain."* |
| 4c | Fail tidak dimuat naik. |

### 6.3 Buang Lampiran

| Langkah | Tindakan |
|---|---|
| 6a | Pemohon klik butang **×** bersebelahan fail yang telah dimuat naik. |
| 6b | Sistem padam fail dari `tmp` storage dan alih keluar pratonton dari borang. |

### 6.4 Aduan Tidak Jadi Dihantar

| Langkah | Tindakan |
|---|---|
| 8a | Pemohon menutup borang atau membatalkan penghantaran aduan. |
| 8b | Sistem membersihkan fail sementara dari `tmp` storage melalui Laravel scheduled job. |

---

## 7. Peraturan Perniagaan

- **BR01:** Fail yang dibenarkan adalah JPG, PNG, dan PDF sahaja.
- **BR02:** Saiz maksimum setiap fail adalah 5MB.
- **BR03:** Bilangan fail lampiran maksimum bagi setiap aduan adalah 5 fail.
- **BR04:** Nama fail asal disimpan dalam pangkalan data untuk rujukan. Fail disimpan dengan nama unik yang dijana sistem untuk mengelak konflik.
- **BR05:** Fail dalam `tmp` storage akan dipadam secara automatik setiap 24 jam jika aduan tidak dihantar.

---

## 8. Keperluan Data

### Input

| Medan | Jenis | Had |
|---|---|---|
| Fail lampiran | File | JPG, PNG, PDF — maks. 5MB |

### Rekod Disimpan (Jadual `lampiran_aduan`)

| Lajur | Nilai |
|---|---|
| `aduan_id` | FK ke `aduan_ict.id` |
| `nama_fail` | Nama asal fail dari peanti |
| `path_fail` | Laluan penuh fail dalam storage |
| `jenis_fail` | MIME type (image/jpeg, image/png, application/pdf) |
| `saiz_fail` | Saiz dalam bytes |
| `created_at` | Tarikh dan masa muat naik |

---

## 9. Antara Muka Berkaitan

- **Komponen:** Bahagian muat naik dalam borang UC01
- **Livewire:** `AduanIctForm.php` — method `updatedLampiran()`
- **Storage:** `storage/app/aduan/` (tetap), `storage/app/tmp/` (sementara)
- **Validation:** `AduanIctRequest.php` — rule `mimes:jpg,jpeg,png,pdf|max:5120`

---

## 10. Kriteria Penerimaan

- [ ] Kawasan muat naik menyokong pilih fail dan seret-lepas (drag and drop).
- [ ] Fail JPG, PNG, dan PDF berjaya dimuat naik.
- [ ] Fail dengan jenis lain ditolak dengan mesej ralat yang jelas.
- [ ] Fail melebihi 5MB ditolak dengan mesej ralat yang jelas.
- [ ] Pratonton nama fail dan saiz dipaparkan selepas muat naik berjaya.
- [ ] Butang × berfungsi untuk membuang fail yang telah dipilih.
- [ ] Metadata fail disimpan dalam `lampiran_aduan` selepas aduan dihantar.
- [ ] Fail sementara dibersihkan jika aduan tidak dihantar.
