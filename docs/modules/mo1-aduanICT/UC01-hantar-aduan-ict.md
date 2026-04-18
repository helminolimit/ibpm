# UC01 — Hantar Aduan ICT

**Modul:** M01 — Aduan ICT  
**Versi:** 1.0  
**Tarikh:** 18 April 2026  
**Status:** Draf  

---

## 1. Maklumat Kes Penggunaan

| Perkara | Butiran |
|---|---|
| ID | UC01 |
| Nama | Hantar Aduan ICT |
| Modul | M01 — Aduan ICT |
| Pelakon Utama | Pemohon (warga kerja MOTAC) |
| Pelakon Sekunder | Sistem E-mel, Pentadbir BPM |
| Keutamaan | Tinggi |

---

## 2. Penerangan Ringkas

Pemohon mengisi dan menghantar borang aduan ICT secara dalam talian melalui portal ICTServe. Sistem akan menjana nombor tiket automatik dan menghalakan aduan kepada unit BPM yang berkaitan berdasarkan kategori aduan yang dipilih.

---

## 3. Prasyarat (Preconditions)

- Pemohon telah log masuk ke sistem ICTServe dengan akaun MOTAC yang sah.
- Sistem e-mel MOTAC berfungsi untuk penghantaran notifikasi.
- Terdapat sekurang-kurangnya satu kategori aduan yang aktif dalam sistem.

---

## 4. Pascasyarat (Postconditions)

- Rekod aduan berjaya disimpan dalam jadual `aduan_ict` dengan status `baru`.
- Nombor tiket unik telah dijana dalam format `#ICT-YYYY-XXX`.
- Rekod awal disimpan dalam jadual `status_log`.
- Notifikasi emel dihantar kepada pemohon (pengesahan) dan pentadbir BPM berkenaan (makluman aduan baru).

---

## 5. Aliran Utama (Main Flow)

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 1 | Pemohon | Pilih menu **Permohonan Baru** → **Aduan ICT** pada dashboard. |
| 2 | Sistem | Papar borang aduan ICT dengan medan: kategori, lokasi, tajuk, keterangan, dan lampiran. Maklumat pemohon (nama, bahagian, jawatan) diisi automatik dari profil. |
| 3 | Pemohon | Pilih **Kategori Aduan** dari senarai dropdown. |
| 4 | Sistem | Papar nama unit BPM penerima di bawah dropdown berdasarkan kategori yang dipilih. |
| 5 | Pemohon | Isi medan **Lokasi / Bilik**, **Tajuk Aduan**, dan **Keterangan Masalah**. |
| 6 | Pemohon | (Pilihan) Muat naik fail lampiran berupa gambar atau dokumen sokongan. |
| 7 | Pemohon | Klik butang **Seterusnya** untuk ke halaman semakan. |
| 8 | Sistem | Papar ringkasan maklumat aduan untuk disemak oleh pemohon. |
| 9 | Pemohon | Klik butang **Hantar Aduan** untuk mengesahkan penghantaran. |
| 10 | Sistem | Jalankan validasi server-side ke atas semua medan wajib. |
| 11 | Sistem | Simpan rekod aduan dalam jadual `aduan_ict` dengan status `baru`. |
| 12 | Sistem | Jana nombor tiket automatik format `#ICT-YYYY-XXX`. |
| 13 | Sistem | Simpan rekod awal dalam jadual `status_log`. |
| 14 | Sistem | Hantar emel pengesahan kepada pemohon mengandungi nombor tiket dan butiran aduan. |
| 15 | Sistem | Hantar notifikasi emel kepada pentadbir BPM unit berkaitan bahawa aduan baru telah diterima. |
| 16 | Sistem | Papar halaman kejayaan dengan nombor tiket dan pautan untuk memantau status. |

---

## 6. Aliran Alternatif

### 6.1 Validasi Borang Gagal (Langkah 10)

| Langkah | Tindakan |
|---|---|
| 10a | Sistem mengesan medan wajib tidak diisi atau format tidak sah. |
| 10b | Sistem papar mesej ralat bersebelahan medan berkaitan. |
| 10c | Pemohon betulkan maklumat dan ulang langkah 5–9. |

### 6.2 Muat Naik Lampiran Gagal (Langkah 6)

| Langkah | Tindakan |
|---|---|
| 6a | Fail lampiran melebihi had saiz 5MB atau format tidak disokong. |
| 6b | Sistem papar mesej amaran: *"Fail tidak disokong atau melebihi 5MB."* |
| 6c | Pemohon pilih fail lain yang memenuhi syarat atau teruskan tanpa lampiran. |

### 6.3 Penghantaran Emel Gagal (Langkah 14–15)

| Langkah | Tindakan |
|---|---|
| 14a | Sistem e-mel tidak responsif atau ralat SMTP. |
| 14b | Sistem rekodkan kegagalan dalam jadual `notifikasi` dengan status `gagal`. |
| 14c | Sistem cuba semula penghantaran emel (retry) secara automatik menggunakan Laravel Queue. |
| 14d | Rekod aduan tetap disimpan dan tiket tetap berjaya dijana. |

---

## 7. Peraturan Perniagaan (Business Rules)

- **BR01:** Nombor tiket dijana secara automatik oleh sistem. Pemohon tidak boleh memilih atau mengubah nombor tiket.
- **BR02:** Setiap aduan mesti dikaitkan dengan satu kategori sahaja. Kategori menentukan unit BPM penerima secara automatik.
- **BR03:** Maklumat pemohon (nama, bahagian, jawatan, e-mel) diambil terus dari profil pengguna dan tidak boleh diubah dalam borang.
- **BR04:** Fail lampiran yang dibenarkan: JPG, PNG, PDF sahaja. Had saiz: 5MB setiap fail.
- **BR05:** Aduan yang berjaya dihantar tidak boleh dipadam oleh pemohon. Hanya Pentadbir atau Superadmin yang boleh membatalkan aduan.

---

## 8. Keperluan Data

### Input

| Medan | Jenis | Wajib | Catatan |
|---|---|---|---|
| Kategori aduan | Dropdown | Ya | Menentukan unit BPM penerima |
| Lokasi / Bilik | Text | Ya | Contoh: Bilik 302, Aras 3 |
| Tajuk aduan | Text | Ya | Maks. 255 aksara |
| Keterangan masalah | Textarea | Ya | Tiada had aksara |
| No. telefon | Text | Ya | Untuk dihubungi jika perlu |
| Lampiran | File | Tidak | JPG, PNG, PDF — maks. 5MB |

### Output

| Data | Destinasi |
|---|---|
| Rekod aduan | Jadual `aduan_ict` |
| Rekod log status | Jadual `status_log` |
| Rekod lampiran | Jadual `lampiran_aduan` + storage |
| Rekod notifikasi | Jadual `notifikasi` |
| Emel pengesahan | Peti masuk pemohon |
| Emel makluman | Peti masuk pentadbir BPM |

---

## 9. Antara Muka Berkaitan

- **Halaman:** `/permohonan/aduan-ict/create` — borang aduan (Langkah 1)
- **Halaman:** `/permohonan/aduan-ict/semakan` — halaman semakan (Langkah 8)
- **Halaman:** `/permohonan/aduan-ict/berjaya` — halaman kejayaan (Langkah 16)
- **Komponen Livewire:** `AduanIctForm.php`
- **Controller:** `AduanIctController@store`
- **Event:** `AduanDihantar`, `AduanBaru`

---

## 10. Kriteria Penerimaan (Acceptance Criteria)

- [ ] Borang aduan berjaya dipaparkan selepas pemohon log masuk.
- [ ] Dropdown kategori memaparkan unit BPM penerima secara automatik apabila kategori dipilih.
- [ ] Sistem menolak penghantaran jika mana-mana medan wajib kosong.
- [ ] Nombor tiket dijana dalam format `#ICT-YYYY-XXX` dan unik.
- [ ] Rekod aduan disimpan dalam pangkalan data dengan status `baru`.
- [ ] Emel pengesahan diterima oleh pemohon dalam masa 5 minit.
- [ ] Emel notifikasi diterima oleh pentadbir BPM berkenaan dalam masa 5 minit.
- [ ] Halaman kejayaan memaparkan nombor tiket yang betul.
