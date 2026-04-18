# UC-M02-03 — Terima Notifikasi Emel

| Perkara | Butiran |
|---|---|
| ID | UC-M02-03 |
| Modul | M02 — Permohonan Toner (Printer) |
| Pelakon Utama | Pemohon, Pentadbir BPM |
| Prasyarat | Permohonan dihantar / status dikemaskini oleh pentadbir |
| Hasil Utama | Emel notifikasi berjaya dihantar dalam masa 5 minit |
| Keutamaan | Tinggi |

---

## Aliran Utama

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 1 | Sistem | Kesan peristiwa yang mencetuskan notifikasi (hantar, luluskan, hantar toner, tolak) |
| 2 | Sistem | Tambah job notifikasi ke dalam queue Laravel |
| 3 | Sistem | Queue worker proses job dan hantar emel melalui SMTP MOTAC |
| 4 | Penerima | Terima emel dalam peti masuk dalam masa < 5 minit |
| 5 | Penerima | Klik pautan dalam emel untuk akses sistem secara terus |

---

## Pencetus Notifikasi

| Peristiwa | Kelas Notifikasi | Penerima |
|---|---|---|
| Permohonan baru dihantar | `PermohonanTonerBaru` | Pemohon + semua Pentadbir BPM |
| Permohonan diluluskan | `TonerDiluluskan` | Pemohon |
| Toner dihantar kepada pemohon | `TonerDihantar` | Pemohon |
| Permohonan ditolak | `TonerDitolak` | Pemohon |
| Stok toner rendah | *(amaran dalaman)* | Pentadbir BPM |

---

## Templat Emel

### 1. Permohonan Baru Diterima (→ Pemohon)

```
Subjek : [ICTServe] Permohonan Toner Anda Telah Diterima — #TON-2026-001

Salam hormat,

Permohonan toner anda telah berjaya dihantar dengan butiran berikut:

  No. Tiket       : #TON-2026-001
  Model Pencetak  : HP LaserJet Pro M404n
  Jenis Toner     : Hitam
  Kuantiti        : 2 unit
  Status          : Dihantar

Permohonan anda akan disemak dalam masa 3 hari bekerja.

[ Semak Status ]

Sistem ICTServe | BPM MOTAC
```

### 2. Permohonan Baru Diterima (→ Pentadbir BPM)

```
Subjek : [ICTServe] Permohonan Toner Baru — #TON-2026-001

Salam hormat,

Terdapat permohonan toner baharu yang memerlukan tindakan anda.

  No. Tiket       : #TON-2026-001
  Pemohon         : Ahmad Kamal
  Bahagian        : Bahagian Pengurusan Maklumat
  Model Pencetak  : HP LaserJet Pro M404n
  Jenis Toner     : Hitam
  Kuantiti        : 2 unit

[ Lihat Permohonan ]

Sistem ICTServe | BPM MOTAC
```

### 3. Permohonan Diluluskan (→ Pemohon)

```
Subjek : [ICTServe] Permohonan Toner Diluluskan — #TON-2026-001

Salam hormat,

Permohonan toner anda telah DILULUSKAN.

  No. Tiket           : #TON-2026-001
  Kuantiti Diluluskan : 2 unit
  Catatan Pentadbir   : (jika ada)

Toner akan diserahkan kepada anda tidak lama lagi.

[ Semak Status ]

Sistem ICTServe | BPM MOTAC
```

### 4. Toner Dihantar (→ Pemohon)

```
Subjek : [ICTServe] Toner Telah Dihantar — #TON-2026-001

Salam hormat,

Toner untuk permohonan anda telah berjaya dihantar.

  No. Tiket          : #TON-2026-001
  Kuantiti Dihantar  : 2 unit
  Tarikh Dihantar    : 19 April 2026

Sila hubungi Unit Operasi, Teknikal & Khidmat Pengguna
sekiranya terdapat sebarang pertanyaan.

[ Lihat Butiran ]

Sistem ICTServe | BPM MOTAC
```

### 5. Permohonan Ditolak (→ Pemohon)

```
Subjek : [ICTServe] Permohonan Toner Ditolak — #TON-2026-001

Salam hormat,

Maaf, permohonan toner anda telah DITOLAK.

  No. Tiket        : #TON-2026-001
  Sebab Penolakan  : Model pencetak tidak disokong / maklumat tidak lengkap

Sila hubungi Unit Operasi, Teknikal & Khidmat Pengguna
untuk maklumat lanjut atau hantar permohonan baharu.

[ Hantar Permohonan Baru ]

Sistem ICTServe | BPM MOTAC
```

---

## Spesifikasi Teknikal

| Perkara | Butiran |
|---|---|
| Mekanisme | Laravel Notification + Queue (`ShouldQueue`) |
| Saluran | `mail` + `database` (in-app notification) |
| Driver Queue | `database` / `redis` (ikut konfigurasi `.env`) |
| SMTP | Pelayan emel MOTAC (Exchange / Office 365) |
| Masa penghantaran | < 5 minit selepas peristiwa |
| Retry | 3 kali jika gagal |

---

## Kod Berkaitan

| Fail | Keterangan |
|---|---|
| `app/Notifications/PermohonanTonerBaru.php` | Notifikasi permohonan baru |
| `app/Notifications/TonerDiluluskan.php` | Notifikasi diluluskan |
| `app/Notifications/TonerDihantar.php` | Notifikasi toner dihantar |
| `app/Notifications/TonerDitolak.php` | Notifikasi ditolak |
