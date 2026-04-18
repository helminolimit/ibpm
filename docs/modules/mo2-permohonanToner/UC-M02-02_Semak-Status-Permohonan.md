# UC-M02-02 — Semak Status Permohonan

| Perkara | Butiran |
|---|---|
| ID | UC-M02-02 |
| Modul | M02 — Permohonan Toner (Printer) |
| Pelakon Utama | Pemohon |
| Prasyarat | Pemohon telah log masuk dan mempunyai sekurang-kurangnya satu permohonan |
| Hasil Utama | Pemohon dapat lihat status terkini dan sejarah permohonan |
| Keutamaan | Tinggi |

---

## Aliran Utama

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 1 | Pemohon | Log masuk ke sistem ICTServe |
| 2 | Sistem | Papar Dashboard dengan jadual **Permohonan Terkini Saya** |
| 3 | Pemohon | Lihat senarai permohonan toner dengan status terkini |
| 4 | Pemohon | Klik **Lihat** pada mana-mana rekod permohonan |
| 5 | Sistem | Papar halaman butiran permohonan |
| 6 | Pemohon | Semak maklumat penuh: status, tarikh kemaskini, catatan pentadbir, sejarah log |

---

## Aliran Alternatif

### A1 — Tiada Permohonan

| Langkah | Tindakan |
|---|---|
| 3a | Jadual kosong — sistem papar teks *"Tiada permohonan dijumpai."* |
| 3b | Pemohon boleh klik **Permohonan Baru** untuk memulakan permohonan |

### A2 — Guna Fungsi Carian

| Langkah | Tindakan |
|---|---|
| 3a | Pemohon taip no. tiket atau nama model pencetak dalam kotak carian |
| 3b | Sistem tapis senarai secara real-time (Livewire `wire:model.live`) |
| 3c | Pemohon pilih rekod yang berkaitan |

---

## Maklumat Yang Dipapar (Halaman Butiran)

### Maklumat Permohonan

| Medan | Contoh |
|---|---|
| No. Tiket | #TON-2026-001 |
| Model Pencetak | HP LaserJet Pro M404n |
| Jenama Toner | HP |
| Jenis / Warna | Hitam |
| Kuantiti Diminta | 2 unit |
| Kuantiti Diluluskan | 2 unit *(selepas diluluskan)* |
| Lokasi Pencetak | Bilik 3.01, Tingkat 3 |
| Bahagian | Bahagian Pengurusan Maklumat |
| Tujuan | Toner hampir habis... |
| Tarikh Mohon | 18 April 2026 |
| Catatan Pentadbir | *(diisi oleh pentadbir)* |

### Status & Warna Badge

| Status | Label | Warna |
|---|---|---|
| `submitted` | Dihantar | Biru |
| `reviewing` | Dalam Semakan | Oren |
| `approved` | Diluluskan | Hijau |
| `delivered` | Toner Dihantar | Hijau Tua |
| `rejected` | Ditolak | Merah |
| `pending_stock` | Menunggu Stok | Kuning |

### Sejarah Log (Audit Trail)

| Tarikh / Masa | Pengguna | Tindakan |
|---|---|---|
| 18 Apr 2026, 09:15 | Ahmad Kamal | Permohonan dihantar |
| 18 Apr 2026, 14:30 | Mohd Yusof (Pentadbir) | Diluluskan — 2 unit |
| 19 Apr 2026, 10:00 | Mohd Yusof (Pentadbir) | Toner dihantar |

---

## Kawalan Akses

- Pemohon **hanya** boleh lihat permohonan milik sendiri (`pemohon_id = Auth::id()`)
- Pentadbir dan Superadmin boleh lihat semua permohonan
- Akses tidak sah → sistem kembalikan HTTP 403

---

## Kod Berkaitan

| Fail | Keterangan |
|---|---|
| `app/Livewire/M02/SenaraiPermohonan.php` | Senarai dengan carian & tapis |
| `app/Livewire/M02/ButiranPermohonan.php` | Paparan butiran + kawalan akses |
| `resources/views/livewire/m02/senarai-permohonan.blade.php` | Jadual senarai |
| `app/Models/PermohonanToner.php` | Method `labelStatus()`, `warnaStatus()` |
| `app/Models/LogToner.php` | Sejarah audit trail |
