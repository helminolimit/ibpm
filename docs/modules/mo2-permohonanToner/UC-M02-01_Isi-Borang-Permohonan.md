# UC-M02-01 — Isi Borang Permohonan Toner

| Perkara | Butiran |
|---|---|
| ID | UC-M02-01 |
| Modul | M02 — Permohonan Toner (Printer) |
| Pelakon Utama | Pemohon |
| Prasyarat | Pengguna telah log masuk ke sistem ICTServe |
| Hasil Utama | Permohonan berjaya dihantar, No. Tiket dijana automatik |
| Hasil Alternatif | Permohonan gagal dihantar — maklumat tidak lengkap |
| Keutamaan | Tinggi |

---

## Aliran Utama

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 1 | Pemohon | Klik menu **Permohonan Baru** → pilih **Toner Printer** |
| 2 | Sistem | Papar borang permohonan, auto-isi nama, jawatan, bahagian, no. telefon dari profil pengguna |
| 3 | Pemohon | Isi medan wajib: model pencetak, jenama toner, jenis toner, kuantiti, lokasi pencetak, tujuan |
| 4 | Pemohon | Isi medan pilihan: no. siri toner, tarikh diperlukan, lampiran (foto/PDF) |
| 5 | Pemohon | Klik butang **Hantar Permohonan** |
| 6 | Sistem | Sahkan semua medan wajib diisi dan format betul |
| 7 | Sistem | Jana No. Tiket automatik dalam format `#TON-YYYY-NNN` |
| 8 | Sistem | Simpan rekod permohonan dengan status `submitted` |
| 9 | Sistem | Rekod log: `tindakan = submitted` dalam jadual `log_toner` |
| 10 | Sistem | Hantar notifikasi emel kepada Pemohon (pengesahan) dan Pentadbir BPM |
| 11 | Sistem | Papar mesej berjaya beserta No. Tiket yang dijana |

---

## Aliran Alternatif

### A1 — Medan Wajib Tidak Diisi

| Langkah | Tindakan |
|---|---|
| 6a | Sistem kesan medan wajib kosong atau format tidak sah |
| 6b | Sistem papar mesej ralat di bawah medan berkaitan |
| 6c | Permohonan **tidak** dihantar, pengguna dikembalikan ke borang |

### A2 — Saiz / Format Lampiran Tidak Sah

| Langkah | Tindakan |
|---|---|
| 4a | Pengguna muat naik fail melebihi 2MB atau bukan JPG/PNG/PDF |
| 4b | Sistem papar ralat: *"Lampiran mestilah fail JPG, PNG atau PDF. Maksimum 2MB."* |
| 4c | Pengguna ganti fail atau teruskan tanpa lampiran |

---

## Spesifikasi Borang

| # | Medan | Jenis | Wajib | Peraturan |
|---|---|---|---|---|
| 1 | Nama Penuh | Text | Ya | Auto-isi, read-only |
| 2 | Jawatan & Gred | Text | Ya | Auto-isi, read-only |
| 3 | Bahagian / Unit | Text | Ya | Auto-isi, read-only |
| 4 | No. Telefon | Text | Ya | Auto-isi, read-only |
| 5 | Model Pencetak | Text | Ya | Max 100 aksara |
| 6 | Jenama Toner | Text | Ya | Max 100 aksara |
| 7 | Jenis / Warna Toner | Dropdown | Ya | `hitam`, `cyan`, `magenta`, `kuning` |
| 8 | No. Siri / Kod Toner | Text | Tidak | Max 100 aksara |
| 9 | Kuantiti Diperlukan | Number | Ya | Min: 1, Max: 50 |
| 10 | Lokasi Pencetak | Text | Ya | Max 150 aksara |
| 11 | Tujuan Permohonan | Textarea | Ya | Min: 10, Max: 500 aksara |
| 12 | Tarikh Diperlukan | Date | Tidak | Mesti hari ini atau selepas |
| 13 | Lampiran | File | Tidak | JPG, PNG, PDF — Maks 2MB |

---

## Format No. Tiket

```
#TON-YYYY-NNN

TON  = kod modul toner
YYYY = tahun semasa (cth: 2026)
NNN  = nombor urutan 3 digit, reset setiap tahun

Contoh: #TON-2026-001
```

---

## Notifikasi Emel

### Kepada Pemohon

```
Subjek: [ICTServe] Permohonan Toner Anda Telah Diterima — #TON-2026-001

No. Tiket       : #TON-2026-001
Model Pencetak  : HP LaserJet Pro M404n
Jenis Toner     : Hitam
Kuantiti        : 2 unit
Status          : Dihantar

Permohonan anda akan disemak dalam masa 3 hari bekerja.
```

### Kepada Pentadbir BPM

```
Subjek: [ICTServe] Permohonan Toner Baru — #TON-2026-001

Pemohon         : Ahmad Kamal
Bahagian        : Bahagian Pengurusan Maklumat
Model Pencetak  : HP LaserJet Pro M404n
Jenis Toner     : Hitam
Kuantiti        : 2 unit
```

---

## Kod Berkaitan

| Fail | Keterangan |
|---|---|
| `app/Livewire/M02/BorangPermohonan.php` | Logik borang dan penghantaran |
| `resources/views/livewire/m02/borang-permohonan.blade.php` | Paparan borang |
| `app/Models/PermohonanToner.php` | Method `janaNoTiket()` |
| `app/Notifications/PermohonanTonerBaru.php` | Notifikasi emel |
| `database/migrations/2026_04_02_000002_create_permohonan_toner_table.php` | Skema jadual |
