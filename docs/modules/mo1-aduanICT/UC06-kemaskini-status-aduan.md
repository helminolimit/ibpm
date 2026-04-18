# UC06 — Kemaskini Status Aduan

**Modul:** M01 — Aduan ICT  
**Versi:** 1.0  
**Tarikh:** 18 April 2026  
**Status:** Draf  

---

## 1. Maklumat Kes Penggunaan

| Perkara | Butiran |
|---|---|
| ID | UC06 |
| Nama | Kemaskini Status Aduan |
| Modul | M01 — Aduan ICT |
| Pelakon Utama | Pentadbir BPM |
| Pelakon Sekunder | Sistem E-mel, Pemohon |
| Keutamaan | Tinggi |

---

## 2. Penerangan Ringkas

Pentadbir BPM mengubah status aduan ICT semasa memproses tiket yang diterima. Setiap perubahan status direkodkan dalam `status_log` dan mencetuskan notifikasi emel automatik kepada pemohon. Terdapat tiga status utama dalam kitaran hayat aduan: `baru` → `dalam_tindakan` → `selesai`.

---

## 3. Prasyarat

- Pentadbir BPM telah log masuk dengan peranan `pentadbir` atau `superadmin`.
- Tiket aduan yang hendak dikemaskini wujud dalam sistem dan dihalakan ke unit pentadbir berkenaan.
- Pentadbir telah menyemak butiran aduan (UC05) sebelum mengubah status.

---

## 4. Pascasyarat

- Status aduan dalam jadual `aduan_ict` telah dikemaskini.
- Rekod perubahan status disimpan dalam jadual `status_log` beserta catatan dan masa tindakan.
- Notifikasi emel dihantar kepada pemohon memaklumkan perubahan status.
- Jika status dikemaskini kepada `selesai`, lajur `tarikh_selesai` dalam `aduan_ict` diisi.

---

## 5. Aliran Kitaran Status

```
[baru] ──► [dalam_tindakan] ──► [selesai]
              ▲                    │
              └────────────────────┘
              (boleh buka semula jika perlu)
```

| Transisi | Dibenarkan | Catatan |
|---|---|---|
| `baru` → `dalam_tindakan` | Ya | Pentadbir mula menangani aduan |
| `dalam_tindakan` → `selesai` | Ya | Masalah telah diselesaikan |
| `baru` → `selesai` | Ya | Kes mudah yang selesai serta-merta |
| `selesai` → `dalam_tindakan` | Ya (Pentadbir/Superadmin sahaja) | Aduan dibuka semula jika masalah berulang |

---

## 6. Aliran Utama — Kemaskini kepada "Dalam Tindakan"

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 1 | Pentadbir BPM | Buka halaman butiran tiket aduan (dari UC05). |
| 2 | Pentadbir BPM | Klik butang **Kemaskini Status**. |
| 3 | Sistem | Papar panel kemaskini: dropdown status, medan catatan tindakan. |
| 4 | Pentadbir BPM | Pilih status `Dalam Tindakan` dan isi catatan tindakan yang diambil. |
| 5 | Pentadbir BPM | Klik **Simpan**. |
| 6 | Sistem | Kemaskini lajur `status` dalam jadual `aduan_ict`. |
| 7 | Sistem | Simpan rekod baru dalam jadual `status_log` (status lama, status baru, catatan, masa, pentadbir). |
| 8 | Sistem | Cetuskan event `StatusDikemaskini`. |
| 9 | Sistem | Hantar emel notifikasi kepada pemohon. |
| 10 | Sistem | Kemaskini paparan halaman tiket — status baharu dipapar. |

---

## 7. Aliran Kemaskini kepada "Selesai"

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 1–3 | (sama seperti aliran utama) | |
| 4 | Pentadbir BPM | Pilih status `Selesai` dan isi catatan ringkasan tindakan yang telah diambil. |
| 5 | Pentadbir BPM | Klik **Simpan**. |
| 6 | Sistem | Kemaskini `status` kepada `selesai` dan isi `tarikh_selesai` dengan masa semasa. |
| 7 | Sistem | Simpan rekod baru dalam `status_log`. |
| 8 | Sistem | Cetuskan event `AduanSelesai`. |
| 9 | Sistem | Hantar emel penutupan kepada pemohon. |
| 10 | Sistem | Papar badge `Selesai` berwarna hijau pada halaman tiket. Butang kemaskini status diubah menjadi **Buka Semula**. |

---

## 8. Aliran Alternatif

### 8.1 Catatan Tindakan Dikosongkan

| Langkah | Tindakan |
|---|---|
| 5a | Pentadbir cuba simpan tanpa mengisi medan catatan. |
| 5b | Sistem papar amaran: *"Sila isi catatan tindakan sebelum menyimpan."* |
| 5c | Pentadbir isi catatan dan simpan semula. |

### 8.2 Buka Semula Aduan yang Selesai

| Langkah | Tindakan |
|---|---|
| 1a | Pemohon atau pentadbir mendapati masalah berulang selepas aduan ditutup. |
| 1b | Pentadbir klik butang **Buka Semula** pada halaman tiket. |
| 1c | Sistem kemaskini status kepada `dalam_tindakan` dan kosongkan `tarikh_selesai`. |
| 1d | Sistem rekod perubahan dalam `status_log` dengan catatan "Dibuka semula". |
| 1e | Sistem hantar notifikasi kepada pemohon bahawa aduan dibuka semula. |

---

## 9. Peraturan Perniagaan

- **BR01:** Pentadbir BPM hanya boleh mengubah status aduan dalam unit mereka. Superadmin boleh ubah status mana-mana aduan.
- **BR02:** Catatan tindakan adalah wajib semasa mengubah status — minimum 10 aksara.
- **BR03:** Apabila status dikemaskini kepada `selesai`, lajur `tarikh_selesai` diisi secara automatik dan tidak boleh diubah secara manual.
- **BR04:** Setiap perubahan status menghasilkan satu rekod baru dalam `status_log`. Rekod lama tidak dipadam atau diubah.
- **BR05:** Pemohon tidak boleh mengubah status aduan mereka sendiri.

---

## 10. Keperluan Data

### Input

| Medan | Jenis | Wajib | Catatan |
|---|---|---|---|
| Status baru | Dropdown | Ya | `dalam_tindakan` / `selesai` |
| Catatan tindakan | Textarea | Ya | Minimum 10 aksara |

### Rekod Dikemaskini (Jadual `aduan_ict`)

| Lajur | Nilai |
|---|---|
| `status` | Status baru |
| `catatan_pentadbir` | Catatan terkini |
| `pentadbir_id` | ID pentadbir yang ambil tindakan |
| `tarikh_selesai` | Diisi jika status = `selesai` |
| `updated_at` | Masa kemaskini automatik |

### Rekod Baru (Jadual `status_log`)

| Lajur | Nilai |
|---|---|
| `aduan_id` | FK ke `aduan_ict.id` |
| `user_id` | ID pentadbir yang membuat perubahan |
| `status_lama` | Status sebelum perubahan |
| `status_baru` | Status selepas perubahan |
| `catatan` | Catatan tindakan pentadbir |
| `created_at` | Masa perubahan dilakukan |

---

## 11. Antara Muka Berkaitan

- **Halaman:** `/admin/aduan/{id}` — panel kemaskini status dalam butiran tiket
- **Controller:** `Admin/AduanIctController@updateStatus`
- **Request:** `Admin/KemaskiniStatusRequest`
- **Event:** `StatusDikemaskini`, `AduanSelesai`
- **Endpoint:** `PATCH /admin/aduan/{id}/status`

---

## 12. Kriteria Penerimaan

- [ ] Dropdown status memaparkan pilihan yang sesuai berdasarkan status semasa.
- [ ] Sistem menolak kemaskini jika catatan tindakan kosong.
- [ ] Rekod perubahan status tersimpan dalam `status_log` dengan maklumat lengkap.
- [ ] `tarikh_selesai` diisi secara automatik apabila status dikemaskini kepada `selesai`.
- [ ] Emel notifikasi dihantar kepada pemohon dalam masa 5 minit selepas status dikemaskini.
- [ ] Sejarah status dipaparkan dalam susunan kronologi pada halaman butiran tiket.
- [ ] Butang **Buka Semula** muncul pada tiket yang berstatus `selesai`.
- [ ] Pemohon tidak mempunyai akses untuk mengubah status aduan mereka.
