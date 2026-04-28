# M04 — Kemaskini Portal

Modul kemaskini kandungan dan konfigurasi portal web MOTAC.

---

## Stack

| Layer | Tech |
|---|---|
| Backend | Laravel 13 |
| Frontend | Livewire 4 + Blade + Tailwind CSS |
| Database | MySQL / SQLite |
| Auth | Laravel Breeze |

---

## Peranan & Akses

| Peranan | Akses |
|---|---|
| Pemohon | Hantar, semak status, lihat sejarah |
| Pentadbir (Unit Aplikasi Teras & Multimedia) | Urus permohonan, tugaskan pembangun, kemaskini status |
| Superadmin | Semua akses + jana laporan |

---

## Aliran Sistem

```
Pemohon hantar permohonan
  → Notifikasi emel ke Pentadbir Unit Aplikasi
    → Pentadbir tugaskan pembangun web
      → Status dikemaskini (Diterima / Dalam Proses / Selesai)
        → Notifikasi emel ke Pemohon
```

---

## Entiti Database

```
permohonan_portal       - rekod utama permohonan
tugasan_portal          - tugasan kepada pembangun
log_audit               - jejak tindakan sistem
notifikasi              - rekod notifikasi emel
lampiran                - fail yang dimuat naik
```

### Medan Wajib `permohonan_portal`

| Medan | Jenis | Keterangan |
|---|---|---|
| no_tiket | string | Auto-generate, contoh: #ICT-2024-001 |
| url_halaman | string | URL halaman yang perlu dikemaskini |
| jenis_perubahan | enum | Kandungan / Konfigurasi / Lain-lain |
| butiran_kemaskini | text | Huraian perubahan |
| status | enum | Diterima / Dalam Proses / Selesai |

---

## Use Cases

| ID | Kes Penggunaan | Pelakon |
|---|---|---|
| UC01 | Log masuk sistem | Semua |
| UC02 | Hantar permohonan kemaskini | Pemohon |
| UC03 | Semak status permohonan | Pemohon |
| UC04 | Terima notifikasi emel | Pemohon / Pentadbir |
| UC05 | Lihat sejarah permohonan | Pemohon |
| UC06 | Urus dan kemaskini status | Pentadbir |
| UC07 | Tugaskan pembangun web | Pentadbir |
| UC08 | Jana laporan kemaskini | Superadmin |

---

## Struktur Branch Git

```
main
└── feature/m04-kemaskini-portal
    ├── feature/m04-model-migration
    ├── feature/m04-livewire-form
    ├── feature/m04-admin-panel
    └── feature/m04-notifikasi-emel
```

> Merge ke `main` hanya selepas UAT lulus. Tiada merge langsung dari feature ke main.

---

## Notifikasi Emel

Dua emel dihantar secara automatik:

1. **Permohonan diterima** — kepada Pentadbir Unit Aplikasi Teras & Multimedia
2. **Status dikemaskini** — kepada Pemohon

Konfigurasi SMTP dalam `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=mail.motac.gov.my
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

---

## Kriteria Penerimaan

- [ ] Pemohon boleh hantar permohonan dengan lampiran
- [ ] No. tiket dijana automatik
- [ ] Pentadbir terima notifikasi dalam masa 5 minit
- [ ] Status boleh dikemaskini oleh Pentadbir
- [ ] Pemohon terima notifikasi bila status berubah
- [ ] Sejarah permohonan boleh dilihat
- [ ] Laporan boleh dieksport PDF / Excel

---

## Larangan

- Jangan merge terus ke `main` tanpa PR
- Jangan simpan kelayakan dalam kod
- Jangan buat migration tanpa rollback
- Jangan skip validasi borang

---

*ICTServe — Bahagian Pengurusan Maklumat, MOTAC | Versi 1.0 | April 2026*
