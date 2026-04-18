# UC-M02-05 — Rekod Penghantaran Toner

| Perkara | Butiran |
|---|---|
| ID | UC-M02-05 |
| Modul | M02 — Permohonan Toner (Printer) |
| Pelakon Utama | Pentadbir BPM (Unit Operasi, Teknikal & Khidmat Pengguna) |
| Prasyarat | Permohonan mempunyai status `approved` |
| Hasil Utama | Rekod penghantaran disimpan, stok dikurangkan, status dikemaskini ke `delivered` |
| Keutamaan | Tinggi |

---

## Aliran Utama

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 1 | Pentadbir | Sediakan toner fizikal untuk diserahkan kepada pemohon |
| 2 | Pentadbir | Navigasi ke senarai permohonan pentadbir |
| 3 | Pentadbir | Cari permohonan dengan status `approved` |
| 4 | Pentadbir | Klik **Hantar** pada rekod berkaitan |
| 5 | Sistem | Papar borang rekod penghantaran dengan maklumat permohonan |
| 6 | Pentadbir | Sahkan kuantiti toner yang dihantar (auto-isi dari kuantiti diluluskan) |
| 7 | Pentadbir | Isi catatan (pilihan) |
| 8 | Pentadbir | Klik **Simpan Rekod Penghantaran** |
| 9 | Sistem | Jalankan `DB::transaction`: |
| | | — Cipta rekod dalam jadual `penghantaran_toner` |
| | | — Kurangkan `kuantiti_ada` dalam jadual `stok_toner` |
| | | — Kemaskini status permohonan: `approved` → `delivered` |
| | | — Rekod log: `tindakan = delivered` |
| 10 | Sistem | Hantar notifikasi emel `TonerDihantar` kepada Pemohon |
| 11 | Sistem | Redirect ke senarai permohonan dengan mesej berjaya |

---

## Aliran Alternatif

### A1 — Status Bukan `approved`

| Langkah | Tindakan |
|---|---|
| 4a | Pentadbir cuba akses halaman rekod hantar untuk permohonan bukan `approved` |
| 4b | Sistem kembalikan HTTP 403 — *"Permohonan ini tidak layak untuk direkodkan penghantaran."* |

### A2 — Penghantaran Separa (Kuantiti Berbeza)

| Langkah | Tindakan |
|---|---|
| 6a | Pentadbir ubah kuantiti dihantar (kurang dari kuantiti diluluskan) |
| 6b | Sistem terima nilai baru — stok dikurangkan mengikut kuantiti dihantar sahaja |

---

## Peraturan Validasi

| Medan | Peraturan |
|---|---|
| Kuantiti dihantar | Wajib, integer, minimum 1 |
| Catatan | Pilihan, maksimum 300 aksara |

---

## Kesan pada Pangkalan Data

### `penghantaran_toner` — rekod baru

| Medan | Nilai |
|---|---|
| `permohonan_id` | ID permohonan berkaitan |
| `dihantar_oleh` | ID pentadbir yang merekod |
| `kuantiti_dihantar` | Kuantiti sebenar dihantar |
| `catatan` | Catatan pentadbir (jika ada) |
| `tarikh_hantar` | Masa semasa (`now()`) |

### `stok_toner` — dikemaskini

| Medan | Perubahan |
|---|---|
| `kuantiti_ada` | Dikurangkan sebanyak `kuantiti_dihantar` |

### `permohonan_toner` — dikemaskini

| Medan | Nilai Baru |
|---|---|
| `status` | `delivered` |

### `log_toner` — rekod baru

| Medan | Nilai |
|---|---|
| `tindakan` | `delivered` |
| `keterangan` | `"Toner dihantar: {kuantiti} unit."` |

---

## Perubahan Status

```
approved  ──[Rekod Hantar]──►  delivered
```

---

## Notifikasi Emel kepada Pemohon

```
Subjek : [ICTServe] Toner Telah Dihantar — #TON-2026-001

No. Tiket          : #TON-2026-001
Kuantiti Dihantar  : 2 unit
Tarikh Dihantar    : 19 April 2026

Sila hubungi Unit Operasi, Teknikal & Khidmat Pengguna
sekiranya terdapat sebarang pertanyaan.
```

---

## Kawalan Akses

- Hanya `pentadbir` dan `superadmin` boleh akses
- Middleware: `role:pentadbir,superadmin`
- Guard tambahan: status mesti `approved` — semak dalam `mount()`

---

## Kod Berkaitan

| Fail | Keterangan |
|---|---|
| `app/Livewire/M02/Admin/RekodHantar.php` | Logik rekod penghantaran + DB transaction |
| `app/Models/StokToner.php` | Method `kurangkanStok()` |
| `app/Models/PenghantaranToner.php` | Model rekod penghantaran |
| `app/Notifications/TonerDihantar.php` | Notifikasi emel kepada pemohon |
