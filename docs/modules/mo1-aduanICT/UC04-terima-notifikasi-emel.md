# UC04 — Terima Notifikasi Emel

**Modul:** M01 — Aduan ICT  
**Versi:** 1.0  
**Tarikh:** 18 April 2026  
**Status:** Draf  

---

## 1. Maklumat Kes Penggunaan

| Perkara | Butiran |
|---|---|
| ID | UC04 |
| Nama | Terima Notifikasi Emel |
| Modul | M01 — Aduan ICT |
| Pelakon Utama | Pemohon, Pentadbir BPM |
| Pelakon Sekunder | Sistem E-mel (SMTP/Exchange), Teknician |
| Keutamaan | Tinggi |

---

## 2. Penerangan Ringkas

Sistem menghantar notifikasi emel secara automatik kepada pihak yang berkaitan pada setiap peristiwa penting dalam kitaran hayat aduan ICT — bermula dari penghantaran aduan hingga aduan ditutup.

---

## 3. Prasyarat

- Alamat e-mel pemohon dan pentadbir BPM sah dan aktif dalam sistem.
- Konfigurasi SMTP pelayan e-mel MOTAC telah ditetapkan dalam `.env` Laravel.
- Laravel Queue Worker sedang berjalan untuk memproses penghantaran emel secara asinkron.

---

## 4. Pascasyarat

- Setiap percubaan penghantaran emel direkodkan dalam jadual `notifikasi` dengan status `berjaya` atau `gagal`.
- Emel yang gagal dihantar akan dicuba semula secara automatik sehingga 3 kali (retry).

---

## 5. Jenis Notifikasi dan Pencetus

| # | Peristiwa Pencetus | Penerima | Subjek Emel |
|---|---|---|---|
| N01 | Aduan berjaya dihantar (UC01) | Pemohon | Pengesahan Aduan ICT — `#ICT-YYYY-XXX` |
| N02 | Aduan baru diterima (UC01) | Pentadbir BPM berkaitan | Aduan Baru Diterima — `#ICT-YYYY-XXX` |
| N03 | Status dikemaskini kepada Dalam Tindakan (UC06) | Pemohon | Kemaskini Status Aduan — `#ICT-YYYY-XXX` |
| N04 | Aduan ditugaskan kepada teknician (UC07) | Teknician | Aduan Ditugaskan kepada Anda — `#ICT-YYYY-XXX` |
| N05 | Aduan diselesaikan (UC06) | Pemohon | Aduan ICT Telah Selesai — `#ICT-YYYY-XXX` |

---

## 6. Aliran Utama — Penghantaran Notifikasi

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 1 | Sistem | Peristiwa berlaku (contoh: aduan dihantar, status dikemaskini). |
| 2 | Sistem | Laravel Event dicetuskan (contoh: `AduanDihantar`, `StatusDikemaskini`). |
| 3 | Sistem | Listener menangkap event dan meletakkan job notifikasi dalam Laravel Queue. |
| 4 | Sistem | Queue Worker memproses job — membina kandungan emel menggunakan Mailable class. |
| 5 | Sistem | Emel dihantar kepada penerima melalui SMTP MOTAC. |
| 6 | Sistem | Rekod notifikasi disimpan dalam jadual `notifikasi` dengan status `berjaya`. |
| 7 | Penerima | Menerima emel notifikasi dalam peti masuk. |

---

## 7. Aliran Alternatif

### 7.1 Penghantaran Emel Gagal

| Langkah | Tindakan |
|---|---|
| 5a | SMTP mengembalikan ralat atau tamat masa (timeout). |
| 5b | Sistem rekodkan kegagalan dalam jadual `notifikasi` dengan status `gagal`. |
| 5c | Laravel Queue mencuba semula penghantaran secara automatik. |
| 5d | Jika gagal selepas 3 percubaan, rekod ditandakan `gagal_kekal` dan dilog untuk semakan Superadmin. |

### 7.2 Alamat Emel Tidak Sah

| Langkah | Tindakan |
|---|---|
| 4a | Alamat emel penerima tidak wujud atau tidak aktif. |
| 4b | SMTP mengembalikan bounce notification. |
| 4c | Sistem rekodkan status `gagal` dalam jadual `notifikasi`. |
| 4d | Superadmin boleh melihat senarai notifikasi gagal melalui panel laporan. |

---

## 8. Kandungan Emel

### N01 — Emel Pengesahan kepada Pemohon

```
Subjek: Pengesahan Aduan ICT — #ICT-2026-018

Kepada [Nama Pemohon],

Aduan ICT anda telah berjaya diterima.

No. Tiket    : #ICT-2026-018
Kategori     : Masalah Rangkaian / Internet
Tarikh Mohon : 18 April 2026, 9:14 pagi
Status       : Baru

Aduan anda akan diuruskan oleh unit berkaitan dalam masa 3 hari bekerja.
Anda boleh memantau status aduan melalui pautan berikut:
[Pautan ke halaman status]

Sekian, terima kasih.
Bahagian Pengurusan Maklumat, MOTAC
```

### N02 — Emel Makluman kepada Pentadbir BPM

```
Subjek: Aduan Baru Diterima — #ICT-2026-018

Kepada Pentadbir [Unit BPM],

Aduan ICT baru telah diterima dan perlu ditindakan.

No. Tiket    : #ICT-2026-018
Pemohon      : Ahmad Kamal bin Rosli
Bahagian     : Bahagian Hal Ehwal Pelancongan
Kategori     : Masalah Rangkaian / Internet
Lokasi       : Bilik 302, Aras 3
Tarikh Mohon : 18 April 2026, 9:14 pagi

Sila log masuk ke sistem ICTServe untuk mengambil tindakan:
[Pautan ke halaman tiket]

Bahagian Pengurusan Maklumat, MOTAC
```

### N05 — Emel Penutupan kepada Pemohon

```
Subjek: Aduan ICT Telah Selesai — #ICT-2026-018

Kepada [Nama Pemohon],

Aduan ICT anda telah diselesaikan.

No. Tiket      : #ICT-2026-018
Tarikh Selesai : 19 April 2026, 2:30 petang
Catatan        : Sambungan rangkaian telah dipulihkan. Sila hubungi kami
                 jika masalah berulang.

Terima kasih kerana menggunakan perkhidmatan ICTServe.
Bahagian Pengurusan Maklumat, MOTAC
```

---

## 9. Peraturan Perniagaan

- **BR01:** Semua notifikasi emel dihantar secara asinkron melalui Laravel Queue supaya tidak melambatkan respons sistem kepada pengguna.
- **BR02:** Setiap percubaan penghantaran emel direkodkan dalam jadual `notifikasi` tanpa mengira berjaya atau gagal.
- **BR03:** Maksimum 3 percubaan ulang (retry) bagi setiap emel yang gagal, dengan selang 5 minit antara setiap percubaan.
- **BR04:** Emel menggunakan template Blade Laravel yang konsisten dengan identiti visual MOTAC.
- **BR05:** Pautan dalam emel adalah pautan terus ke halaman tiket berkaitan dalam sistem ICTServe dan mempunyai tempoh sah selama 30 hari.

---

## 10. Keperluan Data

### Rekod Disimpan (Jadual `notifikasi`)

| Lajur | Nilai |
|---|---|
| `aduan_id` | FK ke `aduan_ict.id` |
| `user_id` | FK ke `users.id` (penerima) |
| `jenis` | N01 / N02 / N03 / N04 / N05 |
| `emel_penerima` | Alamat emel penerima |
| `status_hantar` | `berjaya` / `gagal` / `gagal_kekal` |
| `hantar_pada` | Tarikh dan masa penghantaran berjaya |
| `created_at` | Tarikh rekod dicipta |

---

## 11. Antara Muka Berkaitan

- **Event:** `AduanDihantar`, `AduanBaru`, `StatusDikemaskini`, `AduanDitugaskan`, `AduanSelesai`
- **Listener:** `HantarNotifikasiAduan`
- **Mailable:** `AduanPengesahanMail`, `AduanBaruMail`, `StatusKemaskinanMail`, `AduanSelesaiMail`
- **Queue:** `php artisan queue:work --queue=notifikasi`
- **Template Blade:** `resources/views/emails/aduan/`

---

## 12. Kriteria Penerimaan

- [ ] Emel pengesahan diterima oleh pemohon dalam masa 5 minit selepas aduan dihantar.
- [ ] Emel makluman diterima oleh pentadbir BPM berkenaan dalam masa 5 minit.
- [ ] Emel kemaskini status dihantar kepada pemohon apabila status berubah.
- [ ] Emel penutupan dihantar kepada pemohon apabila aduan ditandakan selesai.
- [ ] Rekod setiap percubaan penghantaran tersimpan dalam jadual `notifikasi`.
- [ ] Emel yang gagal dicuba semula secara automatik sehingga 3 kali.
- [ ] Kandungan emel menampilkan nombor tiket, kategori, dan pautan terus ke halaman tiket.
