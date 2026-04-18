# UC02 — Semak Status Aduan

**Modul:** M01 — Aduan ICT  
**Versi:** 1.0  
**Tarikh:** 18 April 2026  
**Status:** Draf  

---

## 1. Maklumat Kes Penggunaan

| Perkara | Butiran |
|---|---|
| ID | UC02 |
| Nama | Semak Status Aduan |
| Modul | M01 — Aduan ICT |
| Pelakon Utama | Pemohon |
| Pelakon Sekunder | - |
| Keutamaan | Tinggi |

---

## 2. Penerangan Ringkas

Pemohon menyemak status terkini bagi aduan ICT yang telah dihantar. Sistem memaparkan senarai tiket milik pemohon beserta status semasa, tarikh kemaskini, dan catatan tindakan daripada pentadbir BPM.

---

## 3. Prasyarat

- Pemohon telah log masuk ke sistem ICTServe.
- Pemohon mempunyai sekurang-kurangnya satu aduan yang pernah dihantar.

---

## 4. Pascasyarat

- Tiada perubahan data berlaku — ini adalah operasi baca sahaja.
- Rekod paparan tidak dilog dalam sistem audit.

---

## 5. Aliran Utama

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 1 | Pemohon | Pilih menu **Senarai Saya** pada sidebar dashboard. |
| 2 | Sistem | Papar jadual senarai aduan milik pemohon yang sedang log masuk, disusun mengikut tarikh terkini. |
| 3 | Pemohon | Semak lajur **Status** untuk melihat status terkini setiap tiket. |
| 4 | Pemohon | Klik ikon **Lihat** atau nombor tiket untuk melihat butiran penuh sesebuah aduan. |
| 5 | Sistem | Papar halaman butiran tiket mengandungi: maklumat aduan, sejarah perubahan status, catatan pentadbir, dan lampiran yang dimuat naik. |

---

## 6. Aliran Alternatif

### 6.1 Tiada Aduan Ditemui

| Langkah | Tindakan |
|---|---|
| 2a | Pemohon belum pernah menghantar sebarang aduan. |
| 2b | Sistem papar mesej: *"Anda belum mempunyai sebarang aduan. Klik di sini untuk membuat aduan baru."* |

### 6.2 Carian dan Penapisan

| Langkah | Tindakan |
|---|---|
| 3a | Pemohon menggunakan penapis status (Semua / Baru / Dalam Tindakan / Selesai) untuk menapis senarai. |
| 3b | Sistem kemaskini jadual secara real-time menggunakan Livewire tanpa muat semula halaman. |

---

## 7. Peraturan Perniagaan

- **BR01:** Pemohon hanya boleh melihat aduan yang dihantar oleh dirinya sendiri. Pentadbir dan Superadmin boleh melihat semua aduan mengikut skop peranan.
- **BR02:** Status aduan dipaparkan dalam kod warna: **Biru** = Baru, **Amber** = Dalam Tindakan, **Hijau** = Selesai.
- **BR03:** Catatan tindakan dari pentadbir dipaparkan dalam urutan kronologi terbaru di atas.

---

## 8. Keperluan Data

### Output Dipaparkan

| Maklumat | Sumber |
|---|---|
| No. tiket | `aduan_ict.no_tiket` |
| Kategori aduan | `kategori_aduan.nama_kategori` |
| Tarikh mohon | `aduan_ict.created_at` |
| Status semasa | `aduan_ict.status` |
| Tarikh kemaskini | `aduan_ict.updated_at` |
| Catatan pentadbir | `status_log.catatan` |
| Sejarah status | Jadual `status_log` |
| Lampiran | Jadual `lampiran_aduan` |

---

## 9. Antara Muka Berkaitan

- **Halaman:** `/senarai-saya` — senarai semua aduan pemohon
- **Halaman:** `/permohonan/aduan-ict/{id}` — butiran satu tiket
- **Komponen Livewire:** `SenaraiAduan.php`
- **Controller:** `AduanIctController@index`, `AduanIctController@show`

---

## 10. Kriteria Penerimaan

- [ ] Senarai aduan memaparkan aduan milik pemohon yang sedang log masuk sahaja.
- [ ] Setiap baris memaparkan: No. Tiket, Kategori, Tarikh Mohon, Status, dan butang Tindakan.
- [ ] Status dipaparkan dalam kod warna yang betul.
- [ ] Penapis status berfungsi tanpa muat semula halaman.
- [ ] Halaman butiran memaparkan sejarah perubahan status secara kronologi.
- [ ] Lampiran yang dimuat naik boleh dilihat atau dimuat turun dari halaman butiran.
