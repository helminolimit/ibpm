# USE CASE — USER MANAGEMENT SUPERADMIN
ICTServe · Sistem Pengurusan Perkhidmatan ICT · MOTAC

---

## UC10 — Urus Pengguna & Tetapan Sistem

**Pelakon Utama:** Superadmin
**Prasyarat:** Superadmin telah log masuk ke sistem

---

### UC10.1 — Tambah Pengguna Baharu

| Elemen | Huraian |
|---|---|
| ID | UC10.1 |
| Nama | Tambah Pengguna Baharu |
| Pelakon | Superadmin |
| Prasyarat | Superadmin log masuk |
| Pencetus | Superadmin klik butang "+ Tambah Pengguna" |

**Aliran Utama:**
1. Superadmin navigasi ke `/superadmin/users`
2. Klik "+ Tambah Pengguna"
3. Sistem paparkan borang: nama, email, jawatan, gred, bahagian, unit, peranan, status
4. Superadmin isi semua medan wajib
5. Klik "Simpan"
6. Sistem validasi input
7. Sistem cipta akaun dengan status `pending` (lalai)
8. Sistem hantar emel notifikasi kepada pengguna baharu
9. Sistem rekod dalam `audit_logs`
10. Sistem paparkan mesej kejayaan

**Aliran Alternatif:**
- **6a** — Email sudah wujud → sistem paparkan ralat "Email telah didaftarkan"
- **6b** — Medan wajib kosong → sistem highlight medan berkenaan

**Pasca Syarat:** Pengguna baharu dalam senarai dengan status pending

---

### UC10.2 — Edit Maklumat Pengguna

| Elemen | Huraian |
|---|---|
| ID | UC10.2 |
| Nama | Edit Maklumat Pengguna |
| Pelakon | Superadmin |
| Prasyarat | Pengguna sudah wujud dalam sistem |
| Pencetus | Superadmin klik "Edit" pada baris pengguna |

**Aliran Utama:**
1. Superadmin klik "Edit" pada pengguna sasaran
2. Sistem paparkan borang edit dengan data semasa
3. Superadmin ubah medan yang diperlukan
4. Klik "Kemaskini"
5. Sistem validasi dan simpan perubahan
6. Sistem rekod dalam `audit_logs`
7. Jika peranan berubah → sistem hantar emel notifikasi

**Aliran Alternatif:**
- **5a** — Email ditukar kepada email yang sudah wujud → ralat duplikat

---

### UC10.3 — Tukar Status Pengguna

| Elemen | Huraian |
|---|---|
| ID | UC10.3 |
| Nama | Tukar Status Pengguna |
| Pelakon | Superadmin |
| Prasyarat | Pengguna wujud dalam sistem |
| Pencetus | Superadmin klik toggle status |

**Aliran Utama:**
1. Superadmin klik toggle status pengguna (aktif / tidak aktif)
2. Sistem paparkan dialog pengesahan
3. Superadmin sahkan tindakan
4. Sistem kemaskini status
5. Sistem hantar emel notifikasi kepada pengguna berkenaan
6. Sistem rekod dalam `audit_logs`

**Pengecualian:**
- Superadmin tidak boleh nyah-aktifkan akaun sendiri

---

### UC10.4 — Luluskan Akaun Pending

| Elemen | Huraian |
|---|---|
| ID | UC10.4 |
| Nama | Luluskan Akaun Pending |
| Pelakon | Superadmin |
| Prasyarat | Terdapat pengguna dengan status `pending` |
| Pencetus | Superadmin klik "Lulus" |

**Aliran Utama:**
1. Superadmin lihat pengguna dengan status pending dalam senarai
2. Klik "Lulus"
3. Sistem tukar status kepada `aktif`
4. Sistem hantar emel akaun diaktifkan
5. Sistem rekod dalam `audit_logs`

---

### UC10.5 — Padam Pengguna

| Elemen | Huraian |
|---|---|
| ID | UC10.5 |
| Nama | Padam Pengguna |
| Pelakon | Superadmin |
| Prasyarat | Pengguna wujud dan bukan Superadmin aktif |
| Pencetus | Superadmin klik "Padam" |

**Aliran Utama:**
1. Superadmin klik "Padam" pada pengguna sasaran
2. Sistem paparkan dialog pengesahan dengan nama pengguna
3. Superadmin sahkan "Ya, Padam"
4. Sistem laksanakan **soft delete** (isi `deleted_at`)
5. Sistem rekod dalam `audit_logs`
6. Pengguna disingkir dari senarai aktif

**Pengecualian:**
- Sistem tidak benarkan padam akaun Superadmin
- Rekod tidak dihapus secara kekal dari database

---

### UC10.6 — Urus Peranan & Akses Modul

| Elemen | Huraian |
|---|---|
| ID | UC10.6 |
| Nama | Konfigurasi Peranan & Akses Modul |
| Pelakon | Superadmin |
| Prasyarat | Superadmin log masuk |
| Pencetus | Superadmin navigasi ke tab "Peranan & Akses" |

**Aliran Utama:**
1. Superadmin buka tab "Peranan & Akses"
2. Sistem paparkan senarai peranan dengan modul yang boleh diakses
3. Superadmin klik peranan yang ingin dikonfigurasikan
4. Sistem paparkan checkbox akses per modul (lihat/cipta/kemaskini/padam)
5. Superadmin tanda/nyah-tanda kebenaran
6. Klik "Simpan Konfigurasi"
7. Sistem kemaskini `role_module_access`
8. Sistem rekod dalam `audit_logs`

---

### UC10.7 — Cari & Tapis Pengguna

| Elemen | Huraian |
|---|---|
| ID | UC10.7 |
| Nama | Cari & Tapis Pengguna |
| Pelakon | Superadmin |
| Prasyarat | Superadmin dalam halaman Pengurusan Pengguna |
| Pencetus | Superadmin isi kotak carian atau pilih penapis |

**Aliran Utama:**
1. Superadmin taip nama/emel/jawatan dalam kotak carian
2. Sistem tapis secara real-time (Livewire)
3. Superadmin pilih peranan dari dropdown
4. Superadmin pilih unit dari dropdown
5. Superadmin pilih status dari dropdown
6. Sistem paparkan hasil yang sepadan

---

## Carta Alir — Tambah Pengguna

```
Superadmin
    |
    v
Klik "+ Tambah Pengguna"
    |
    v
Isi Borang Pengguna
    |
    v
[Validasi Input] --GAGAL--> Papar Ralat Medan
    |
  LULUS
    |
    v
Cipta Akaun (Status: Pending)
    |
    v
Hantar Emel Notifikasi
    |
    v
Rekod Audit Log
    |
    v
Papar Mesej Kejayaan
```

---

## Rujukan

- Carta Organisasi BPM MOTAC (Kemaskini 20 Februari 2026)
- Spesifikasi Keperluan Sistem ICTServe v1.0 (15 April 2026)
- Seksyen 10: Urus Pengguna & Tetapan Sistem
