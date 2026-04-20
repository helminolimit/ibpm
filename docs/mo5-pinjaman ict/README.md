# M05 — Pinjaman Peralatan ICT
**ICTServe | Sistem Pengurusan Perkhidmatan ICT MOTAC**

> Rujukan Borang Rasmi: PK.(S).KPK.08.(L3) — Borang C
> Keutamaan: Tinggi | Unit Pentadbir: Unit Operasi, Teknikal & Khidmat Pengguna

---

## Struktur Modul

Modul M05 dibahagikan kepada **8 sub-modul** mengikut bahagian dalam Borang C, dikelompokkan kepada 4 fasa utama proses peminjaman:

---

### Fasa 1 — Permohonan (Oleh Pemohon)

| Fail | Bahagian Borang C | Penerangan |
|------|-------------------|------------|
| [M05-B1_Maklumat_Pemohon.md](M05-B1_Maklumat_Pemohon.md) | Bahagian 1 + 1A | Maklumat pemohon & pilihan mohon bagi pihak orang lain |
| [M05-B2_Pegawai_Bertanggungjawab.md](M05-B2_Pegawai_Bertanggungjawab.md) | Bahagian 2 | Maklumat pegawai bertanggungjawab ke atas peralatan |
| [M05-B3_Maklumat_Peralatan.md](M05-B3_Maklumat_Peralatan.md) | Bahagian 3 | Jenis peralatan, kuantiti, tujuan & tempoh pinjaman |
| [M05-B4_Wakil_Pengambilan_Pemulangan.md](M05-B4_Wakil_Pengambilan_Pemulangan.md) | Bahagian 4 (Baharu) | Maklumat wakil ambil dan/atau wakil pulang |

### Fasa 2 — Kelulusan (Oleh Ketua Bahagian)

| Fail | Bahagian Borang C | Penerangan |
|------|-------------------|------------|
| [M05-B5_Sokongan_Ketua_Bahagian.md](M05-B5_Sokongan_Ketua_Bahagian.md) | Bahagian 5 | Sokongan Ketua Bahagian melalui e-mel tanpa login |

### Fasa 3 — Pengeluaran Peralatan (Oleh BPM)

| Fail | Bahagian Borang C | Penerangan |
|------|-------------------|------------|
| [M05-B6_Rekod_Pengeluaran.md](M05-B6_Rekod_Pengeluaran.md) | Bahagian 6 | Rekod pengeluaran peralatan — Pegawai Pengeluar & Penerima |
| [M05-B8_Maklumat_Peminjaman_Terperinci.md](M05-B8_Maklumat_Peminjaman_Terperinci.md) | Bahagian 8 | Rekod terperinci peralatan: jenama, siri, aksesori |

### Fasa 4 — Pemulangan Peralatan (Oleh BPM)

| Fail | Bahagian Borang C | Penerangan |
|------|-------------------|------------|
| [M05-B7_Rekod_Pemulangan.md](M05-B7_Rekod_Pemulangan.md) | Bahagian 7 | Rekod pemulangan — semakan keadaan & pengesahan BPM |

---

## Aliran Proses Keseluruhan

```
FASA 1 — PERMOHONAN
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Pemohon log masuk sistem
        ↓
Bahagian 1   → Isi maklumat diri
Bahagian 1A  → [Pilihan] Mohon bagi pihak orang lain
Bahagian 2   → Nyatakan Pegawai Bertanggungjawab
Bahagian 3   → Pilih peralatan, tempoh & tujuan
Bahagian 4   → [Pilihan] Daftarkan wakil ambil / pulang
        ↓
Hantar permohonan → Sistem jana No. Tiket #ICT-YYYY-XXX

FASA 2 — KELULUSAN
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Bahagian 5   → E-mel dihantar kepada Ketua Bahagian
               Ketua Bahagian tekan pautan → Sokong / Tidak Disokong
               (tanpa perlu log masuk sistem)
        ↓
[Tidak Disokong] → Notifikasi pemohon → Tamat
[Disokong]       → Permohonan ke Pentadbir BPM

FASA 3 — PENGELUARAN
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Bahagian 8   → Pentadbir rekod jenama, model, siri & aksesori
Bahagian 6   → Pegawai Pengeluar serah kepada Pemohon / Wakil
               Kedua-dua pihak tandatangan rekod
               Status → Dipinjam

FASA 4 — PEMULANGAN
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Bahagian 7   → Pemohon / Wakil pulangkan peralatan
               BPM semak keadaan & rekodkan
               Status → Selesai
```

---

## Status Permohonan

| Status | Fasa | Penerangan |
|--------|------|------------|
| `Menunggu Sokongan` | 2 | Menunggu keputusan Ketua Bahagian |
| `Tidak Disokong` | 2 | Ditolak Ketua Bahagian |
| `Dalam Tindakan` | 3 | Disokong, BPM sedang proses |
| `Dipinjam` | 3→4 | Peralatan telah dikeluarkan |
| `Lewat Pulang` | 4 | Melepasi tarikh dijangka pulang |
| `Selesai` | 4 | Peralatan berjaya dipulangkan |

---

## Rujukan Syarat-syarat Pinjaman

Berdasarkan borang rasmi MOTAC PK.(S).KPK.08.(L3):

1. Tertakluk kepada ketersediaan peralatan — konsep **First Come, First Serve**
2. Diuruskan dalam tempoh **3 hari bekerja** dari tarikh permohonan lengkap diterima
3. Pemohon wajib semak kesempurnaan peralatan semasa pengambilan dan sebelum pemulangan
4. Kehilangan atau kerosakan adalah tanggungjawab Pegawai Bertanggungjawab

---

*ICTServe | M05 — Pinjaman Peralatan ICT | Versi 1.0 | 16 April 2026*
