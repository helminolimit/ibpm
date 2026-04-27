# UC07 — Tugaskan kepada Teknician

**Modul:** M01 — Aduan ICT  
**Versi:** 1.0  
**Tarikh:** 18 April 2026  
**Status:** Draf  
**Hubungan:** `«extend»` UC06 — Kemaskini Status Aduan  

---

## 1. Maklumat Kes Penggunaan

| Perkara | Butiran |
|---|---|
| ID | UC07 |
| Nama | Tugaskan kepada Teknician |
| Modul | M01 — Aduan ICT |
| Pelakon Utama | Pentadbir BPM |
| Pelakon Sekunder | Teknician, Sistem E-mel |
| Hubungan | Extend dari UC06 |
| Keutamaan | Sederhana |

---

## 2. Penerangan Ringkas

Pentadbir BPM menugaskan aduan ICT kepada teknician tertentu dalam unit mereka untuk tindakan lapangan. Penugasan ini berlaku semasa atau selepas kemaskini status kepada `dalam_tindakan` (UC06). Teknician yang ditugaskan akan menerima notifikasi emel sebagai arahan kerja.

---

## 3. Prasyarat

- Pentadbir BPM telah log masuk dan membuka halaman butiran tiket aduan.
- Status aduan adalah `dalam_tindakan` atau `baru`.
- Terdapat sekurang-kurangnya satu teknician yang aktif dalam unit pentadbir berkenaan.

---

## 4. Pascasyarat

- Lajur `pentadbir_id` dalam jadual `aduan_ict` dikemaskini dengan ID teknician yang ditugaskan.
- Rekod penugasan disimpan dalam jadual `status_log`.
- Notifikasi emel dihantar kepada teknician yang ditugaskan.
- Nama teknician dipapar pada halaman butiran tiket sebagai penanggung jawab.

---

## 5. Aliran Utama

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 1 | Pentadbir BPM | Buka halaman butiran tiket aduan. |
| 2 | Pentadbir BPM | Klik butang **Tugaskan Teknician** pada panel tindakan. |
| 3 | Sistem | Papar senarai dropdown teknician aktif dalam unit pentadbir berkenaan. |
| 4 | Pentadbir BPM | Pilih teknician dari senarai. |
| 5 | Pentadbir BPM | (Pilihan) Isi catatan arahan tambahan untuk teknician. |
| 6 | Pentadbir BPM | Klik **Tugaskan**. |
| 7 | Sistem | Kemaskini lajur `pentadbir_id` dalam `aduan_ict` dengan ID teknician. |
| 8 | Sistem | Simpan rekod dalam `status_log` dengan catatan penugasan. |
| 9 | Sistem | Cetuskan event `AduanDitugaskan`. |
| 10 | Sistem | Hantar emel notifikasi kepada teknician mengandungi butiran aduan dan arahan tindakan. |
| 11 | Sistem | Papar nama teknician yang ditugaskan pada halaman butiran tiket. |

---

## 6. Aliran Alternatif

### 6.1 Tiada Teknician Aktif

| Langkah | Tindakan |
|---|---|
| 3a | Tiada teknician yang aktif dalam unit berkenaan. |
| 3b | Sistem papar mesej: *"Tiada teknician tersedia dalam unit ini. Sila hubungi Superadmin."* |
| 3c | Pentadbir boleh menangani sendiri aduan tersebut tanpa penugasan formal. |

### 6.2 Tukar Penugasan

| Langkah | Tindakan |
|---|---|
| 2a | Tiket sudah mempunyai teknician yang ditugaskan tetapi perlu ditukar. |
| 2b | Pentadbir klik butang **Tukar Teknician**. |
| 2c | Sistem papar dropdown dengan teknician semasa dipilih secara awal. |
| 2d | Pentadbir pilih teknician baru dan isi sebab pertukaran. |
| 2e | Sistem kemaskini penugasan dan hantar notifikasi kepada teknician baru. |
| 2f | Sistem rekod perubahan penugasan dalam `status_log`. |

---

## 7. Peraturan Perniagaan

- **BR01:** Pentadbir BPM hanya boleh menugaskan teknician yang berada dalam unit mereka sendiri.
- **BR02:** Seorang teknician boleh ditugaskan lebih dari satu aduan pada masa yang sama.
- **BR03:** Penugasan teknician tidak mengubah status aduan — status kekal `dalam_tindakan` kecuali pentadbir mengubahnya secara berasingan.
- **BR04:** Rekod setiap penugasan dan pertukaran penugasan direkodkan dalam `status_log` untuk tujuan audit.
- **BR05:** Teknician yang ditugaskan menerima notifikasi emel tetapi tidak mempunyai akses untuk mengubah status aduan — hanya boleh melihat butiran tiket yang ditugaskan kepada mereka (jika fungsi ini dilaksanakan pada fasa akan datang).

---

## 8. Kandungan Emel kepada Teknician

```
Subjek: Aduan Ditugaskan kepada Anda — #ICT-2026-018

Kepada [Nama Teknician],

Aduan ICT berikut telah ditugaskan kepada anda untuk tindakan.

No. Tiket    : #ICT-2026-018
Pemohon      : Ahmad Kamal bin Rosli
Bahagian     : Bahagian Hal Ehwal Pelancongan
Kategori     : Masalah Rangkaian / Internet
Lokasi       : Bilik 302, Aras 3
Keterangan   : [Keterangan ringkas masalah]

Arahan Pentadbir:
[Catatan arahan dari pentadbir, jika ada]

Sila ambil tindakan dalam masa 1 hari bekerja.
Untuk butiran lanjut, log masuk ke ICTServe:
[Pautan ke halaman tiket]

Bahagian Pengurusan Maklumat, MOTAC
```

---

## 9. Keperluan Data

### Input

| Medan | Jenis | Wajib | Catatan |
|---|---|---|---|
| Teknician | Dropdown | Ya | Senarai dari `users` dengan peranan teknician dalam unit |
| Catatan arahan | Textarea | Tidak | Arahan tambahan untuk teknician |

### Rekod Dikemaskini (Jadual `aduan_ict`)

| Lajur | Nilai |
|---|---|
| `pentadbir_id` | ID teknician yang ditugaskan |
| `updated_at` | Masa kemaskini |

### Rekod Baru (Jadual `status_log`)

| Lajur | Nilai |
|---|---|
| `aduan_id` | FK ke `aduan_ict.id` |
| `user_id` | ID pentadbir yang membuat penugasan |
| `status_lama` | Status semasa (tidak berubah) |
| `status_baru` | Status semasa (tidak berubah) |
| `catatan` | "Ditugaskan kepada: [nama teknician]. [catatan arahan]" |
| `created_at` | Masa penugasan |

---

## 10. Antara Muka Berkaitan

- **Halaman:** `/admin/aduan/{id}` — panel penugasan teknician dalam butiran tiket
- **Controller:** `Admin/AduanIctController@assign`
- **Endpoint:** `PATCH /admin/aduan/{id}/assign`
- **Event:** `AduanDitugaskan`
- **Mailable:** `AduanDitugaskanMail`

---

## 11. Kriteria Penerimaan

- [ ] Dropdown teknician memaparkan hanya teknician aktif dalam unit pentadbir berkenaan.
- [ ] Teknician berjaya ditugaskan dan nama dipapar pada halaman tiket.
- [ ] Rekod penugasan disimpan dalam `status_log`.
- [ ] Emel notifikasi dihantar kepada teknician yang ditugaskan dalam masa 5 minit.
- [ ] Fungsi tukar teknician berjaya dengan rekod audit yang lengkap.
- [ ] Penugasan tidak mengubah status aduan secara automatik.
