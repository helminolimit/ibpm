# M01 — Modul Aduan ICT

**Sistem:** ICTServe — Sistem Pengurusan Perkhidmatan ICT  
**Organisasi:** Bahagian Pengurusan Maklumat, MOTAC  
**Versi:** 1.0  
**Tarikh:** 18 April 2026  

---

## Penerangan Modul

Modul Aduan ICT (M01) membolehkan warga kerja MOTAC membuat aduan berkaitan peralatan dan perkhidmatan ICT secara dalam talian. Aduan akan dihalakan secara automatik kepada unit BPM yang berkaitan berdasarkan kategori aduan yang dipilih.

---

## Senarai Dokumen Proses

| Fail | ID | Nama Proses | Pelakon | Keutamaan |
|---|---|---|---|---|
| `UC01-hantar-aduan-ict.md` | UC01 | Hantar Aduan ICT | Pemohon | Tinggi |
| `UC02-semak-status-aduan.md` | UC02 | Semak Status Aduan | Pemohon | Tinggi |
| `UC03-muat-naik-lampiran.md` | UC03 | Muat Naik Lampiran | Pemohon | Sederhana |
| `UC04-terima-notifikasi-emel.md` | UC04 | Terima Notifikasi Emel | Pemohon, Pentadbir | Tinggi |
| `UC05-terima-semak-aduan.md` | UC05 | Terima dan Semak Aduan | Pentadbir BPM | Tinggi |
| `UC06-kemaskini-status-aduan.md` | UC06 | Kemaskini Status Aduan | Pentadbir BPM | Tinggi |
| `UC07-tugaskan-teknician.md` | UC07 | Tugaskan kepada Teknician | Pentadbir BPM | Sederhana |
| `UC08-jana-laporan-aduan.md` | UC08 | Jana Laporan Aduan | Pentadbir, Superadmin | Sederhana |

---

## Hubungan Antara Use Case

```
UC01 ──include──► UC03
UC06 ──extend───► UC07
```

---

## Aliran Keseluruhan Modul

```
Pemohon                    Sistem                    Pentadbir BPM
   │                          │                            │
   ├── UC01: Hantar aduan ───►│                            │
   │                          ├── Jana tiket #ICT-YYYY-XXX │
   │                          ├── Simpan dalam DB          │
   │◄── UC04: Emel pengesahan ┤                            │
   │                          ├── UC04: Emel notifikasi ──►│
   │                          │                            │
   │                          │    UC05: Semak aduan ◄─────┤
   │                          │    UC06: Kemaskini status ◄─┤
   │◄── UC04: Emel kemaskini ─┤    UC07: Tugaskan teknician─┤
   │                          │                            │
   ├── UC02: Semak status ───►│                            │
   │◄── Papar status terkini ─┤                            │
   │                          │                            │
   │◄── UC04: Emel selesai ───┤◄── UC08: Jana laporan ─────┤
```

---

## Teknologi Berkaitan

| Komponen | Teknologi |
|---|---|
| Backend | Laravel 13 |
| Frontend | Livewire 4 + Blade + Tailwind CSS |
| Pangkalan Data | MySQL / SQLite |
| Notifikasi Emel | Laravel Mail + Queue (SMTP MOTAC) |
| Eksport Laporan | DomPDF (PDF), Maatwebsite Excel (xlsx) |
| Storage | Laravel Storage (`storage/app/aduan/`) |

---

## Jadual Pangkalan Data Berkaitan

| Jadual | Fungsi |
|---|---|
| `users` | Data pengguna — pemohon dan pentadbir |
| `aduan_ict` | Rekod utama aduan ICT |
| `kategori_aduan` | Kategori aduan dan unit BPM penerima |
| `lampiran_aduan` | Metadata fail lampiran |
| `status_log` | Sejarah perubahan status aduan |
| `notifikasi` | Rekod percubaan penghantaran emel |
