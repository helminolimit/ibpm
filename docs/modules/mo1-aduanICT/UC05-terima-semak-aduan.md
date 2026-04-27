# UC05 — Terima dan Semak Aduan

**Modul:** M01 — Aduan ICT  
**Versi:** 1.0  
**Tarikh:** 18 April 2026  
**Status:** Draf  

---

## 1. Maklumat Kes Penggunaan

| Perkara | Butiran |
|---|---|
| ID | UC05 |
| Nama | Terima dan Semak Aduan |
| Modul | M01 — Aduan ICT |
| Pelakon Utama | Pentadbir BPM |
| Pelakon Sekunder | - |
| Keutamaan | Tinggi |

---

## 2. Penerangan Ringkas

Pentadbir BPM menyemak aduan ICT yang masuk ke unit mereka berdasarkan peranan yang ditetapkan. Pentadbir boleh melihat senarai aduan, menapis mengikut status, dan membuka butiran tiket untuk memahami masalah yang dilaporkan sebelum mengambil tindakan.

---

## 3. Prasyarat

- Pentadbir BPM telah log masuk dengan akaun yang mempunyai peranan `pentadbir`.
- Terdapat sekurang-kurangnya satu aduan yang dihalakan ke unit pentadbir berkenaan.
- Sistem menghalakan aduan berdasarkan `unit_penerima` dalam jadual `kategori_aduan` yang sepadan dengan unit pentadbir.

---

## 4. Pascasyarat

- Tiada perubahan data berlaku dalam UC05 — ini adalah operasi semakan sahaja.
- Tindakan lanjut (kemaskini status, tugaskan teknician) dilakukan dalam UC06 dan UC07.

---

## 5. Aliran Utama

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 1 | Pentadbir BPM | Log masuk ke sistem ICTServe dan akses menu **Aduan ICT** pada sidebar. |
| 2 | Sistem | Papar dashboard senarai aduan yang dihalakan ke unit pentadbir berkenaan, disusun mengikut: aduan `baru` dahulu, kemudian `dalam_tindakan`, dan `selesai` di bawah. |
| 3 | Sistem | Papar kad statistik ringkasan: jumlah aduan hari ini, aduan dalam tindakan, aduan selesai bulan ini, dan purata masa penyelesaian. |
| 4 | Pentadbir BPM | Semak senarai aduan dan kenal pasti tiket yang perlu ditindakan (status: `baru`). |
| 5 | Pentadbir BPM | Klik butang **Semak** atau nombor tiket untuk membuka halaman butiran tiket. |
| 6 | Sistem | Papar halaman butiran tiket mengandungi: maklumat pemohon, kategori, lokasi, keterangan masalah, lampiran, dan sejarah status. |
| 7 | Pentadbir BPM | Semak keterangan dan lampiran untuk memahami masalah yang dilaporkan. |
| 8 | Pentadbir BPM | Teruskan ke UC06 untuk mengambil tindakan atau menutup halaman jika tiket tidak memerlukan tindakan segera. |

---

## 6. Aliran Alternatif

### 6.1 Tiada Aduan Baru

| Langkah | Tindakan |
|---|---|
| 2a | Tiada aduan baru dalam unit pentadbir berkenaan. |
| 2b | Sistem papar mesej: *"Tiada aduan baru. Semua aduan telah ditindakan."* |
| 2c | Pentadbir masih boleh melihat aduan `dalam_tindakan` dan `selesai`. |

### 6.2 Penapisan dan Carian

| Langkah | Tindakan |
|---|---|
| 4a | Pentadbir menggunakan penapis (Semua / Baru / Dalam Tindakan / Selesai) atau kotak carian nombor tiket / nama pemohon. |
| 4b | Sistem kemaskini senarai secara real-time tanpa muat semula halaman. |

### 6.3 Aduan Bukan Bidang Unit

| Langkah | Tindakan |
|---|---|
| 7a | Pentadbir mendapati aduan berada di luar skop bidang unit mereka. |
| 7b | Pentadbir boleh memberikan catatan maklum balas kepada pemohon melalui UC06 (kemaskini status dengan catatan penjelasan). |

---

## 7. Peraturan Perniagaan

- **BR01:** Pentadbir BPM hanya boleh melihat aduan yang dihalakan ke unit mereka berdasarkan `unit_penerima` dalam `kategori_aduan`. Pentadbir tidak boleh melihat aduan unit lain.
- **BR02:** Superadmin boleh melihat semua aduan dari semua unit tanpa sekatan.
- **BR03:** Aduan dipaparkan mengikut susunan keutamaan: `baru` dahulu, diikuti `dalam_tindakan`, kemudian `selesai`.
- **BR04:** Bilangan aduan baru dipaparkan sebagai lencana merah pada menu sidebar sebagai peringatan visual.

---

## 8. Keperluan Data

### Data Dipaparkan dalam Senarai

| Maklumat | Sumber |
|---|---|
| No. tiket | `aduan_ict.no_tiket` |
| Nama pemohon | `users.name` |
| Bahagian pemohon | `users.bahagian` |
| Kategori aduan | `kategori_aduan.nama_kategori` |
| Lokasi | `aduan_ict.lokasi` |
| Tarikh mohon | `aduan_ict.created_at` |
| Status | `aduan_ict.status` |

### Data Dipaparkan dalam Butiran Tiket

| Maklumat | Sumber |
|---|---|
| Semua maklumat borang aduan | `aduan_ict` |
| Maklumat penuh pemohon | `users` |
| Sejarah perubahan status | `status_log` |
| Fail lampiran | `lampiran_aduan` |

---

## 9. Antara Muka Berkaitan

- **Halaman:** `/admin/aduan` — senarai aduan pentadbir
- **Halaman:** `/admin/aduan/{id}` — butiran satu tiket
- **Komponen Livewire:** `Admin/SenaraiAduan.php`
- **Controller:** `Admin/AduanIctController@index`, `@show`
- **Middleware:** `role:pentadbir,superadmin`

---

## 10. Kriteria Penerimaan

- [ ] Pentadbir hanya melihat aduan yang dihalakan ke unit mereka sahaja.
- [ ] Kad statistik memaparkan jumlah aduan hari ini, dalam tindakan, selesai, dan purata masa penyelesaian.
- [ ] Aduan baru dipapar di atas senarai dengan lencana `Baru` berwarna biru.
- [ ] Penapis status dan carian berfungsi dengan betul.
- [ ] Halaman butiran tiket memaparkan semua maklumat aduan termasuk lampiran dan sejarah status.
- [ ] Bilangan aduan baru dipapar sebagai lencana merah pada menu sidebar.
