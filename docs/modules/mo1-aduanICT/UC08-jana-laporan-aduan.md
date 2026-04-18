# UC08 — Jana Laporan Aduan

**Modul:** M01 — Aduan ICT  
**Versi:** 1.0  
**Tarikh:** 18 April 2026  
**Status:** Draf  

---

## 1. Maklumat Kes Penggunaan

| Perkara | Butiran |
|---|---|
| ID | UC08 |
| Nama | Jana Laporan Aduan |
| Modul | M01 — Aduan ICT |
| Pelakon Utama | Pentadbir BPM, Superadmin |
| Pelakon Sekunder | - |
| Keutamaan | Sederhana |

---

## 2. Penerangan Ringkas

Pentadbir BPM dan Superadmin boleh menjana laporan statistik dan senarai aduan ICT mengikut parameter yang dipilih seperti tempoh tarikh, unit penerima, kategori, atau status. Laporan boleh dieksport dalam format PDF atau Excel.

---

## 3. Prasyarat

- Pengguna telah log masuk dengan peranan `pentadbir` atau `superadmin`.
- Terdapat rekod aduan dalam pangkalan data untuk tempoh yang dipilih.

---

## 4. Pascasyarat

- Laporan dipapar di skrin atau dimuat turun dalam format yang dipilih.
- Tiada rekod data diubah — ini adalah operasi baca sahaja.
- Log audit tidak diperlukan untuk operasi jana laporan biasa.

---

## 5. Aliran Utama

| Langkah | Pelakon | Tindakan |
|---|---|---|
| 1 | Pentadbir / Superadmin | Pilih menu **Jana Laporan** pada sidebar. |
| 2 | Sistem | Papar halaman laporan dengan borang parameter penapisan. |
| 3 | Pengguna | Tetapkan parameter laporan: tarikh dari, tarikh hingga, kategori (pilihan), status (pilihan), unit (Superadmin sahaja). |
| 4 | Pengguna | Klik **Jana Laporan**. |
| 5 | Sistem | Query pangkalan data berdasarkan parameter yang dipilih. |
| 6 | Sistem | Papar laporan dalam halaman: ringkasan statistik dan jadual senarai aduan. |
| 7 | Pengguna | Semak laporan di skrin. |
| 8 | Pengguna | (Pilihan) Klik **Eksport PDF** atau **Eksport Excel** untuk memuat turun laporan. |
| 9 | Sistem | Jana fail PDF atau Excel dan mula muat turun automatik. |

---

## 6. Aliran Alternatif

### 6.1 Tiada Data dalam Tempoh Dipilih

| Langkah | Tindakan |
|---|---|
| 6a | Tiada rekod aduan ditemui untuk parameter yang dipilih. |
| 6b | Sistem papar mesej: *"Tiada rekod ditemui untuk tempoh dan kriteria yang dipilih."* |
| 6c | Pengguna boleh ubah parameter dan jana semula. |

### 6.2 Tempoh Terlalu Luas

| Langkah | Tindakan |
|---|---|
| 4a | Pengguna memilih tempoh melebihi 12 bulan. |
| 4b | Sistem papar amaran: *"Laporan dengan tempoh melebihi 12 bulan mungkin mengambil masa. Teruskan?"* |
| 4c | Pengguna mengesahkan dan sistem meneruskan query. |

---

## 7. Kandungan Laporan

### Bahagian A — Ringkasan Statistik

| Metrik | Penerangan |
|---|---|
| Jumlah aduan | Bilangan keseluruhan aduan dalam tempoh |
| Aduan selesai | Bilangan aduan yang berstatus `selesai` |
| Aduan dalam tindakan | Bilangan aduan yang masih `dalam_tindakan` |
| Aduan baru / belum ditindakan | Bilangan aduan yang masih `baru` |
| Purata masa penyelesaian | Purata hari dari tarikh mohon hingga `tarikh_selesai` |
| Kadar penyelesaian | Peratusan aduan selesai berbanding jumlah keseluruhan |

### Bahagian B — Pecahan mengikut Kategori

Jadual bilangan aduan bagi setiap kategori aduan dalam tempoh yang dipilih.

### Bahagian C — Senarai Aduan Terperinci

Jadual lengkap mengandungi: No. Tiket, Pemohon, Kategori, Lokasi, Tarikh Mohon, Tarikh Selesai, Status, dan Penanggung Jawab.

---

## 8. Parameter Penapisan

| Parameter | Jenis | Wajib | Catatan |
|---|---|---|---|
| Tarikh dari | Date picker | Ya | Tarikh mula laporan |
| Tarikh hingga | Date picker | Ya | Tarikh akhir laporan |
| Kategori | Dropdown multi-pilih | Tidak | Kosong = semua kategori |
| Status | Dropdown multi-pilih | Tidak | Kosong = semua status |
| Unit penerima | Dropdown | Tidak | Superadmin sahaja; Pentadbir = unit sendiri |

---

## 9. Peraturan Perniagaan

- **BR01:** Pentadbir BPM hanya boleh menjana laporan untuk unit mereka sendiri. Superadmin boleh menjana laporan untuk semua unit atau memilih unit tertentu.
- **BR02:** Tempoh laporan maksimum adalah 12 bulan sekali jana. Untuk tempoh lebih panjang, perlu jana dalam beberapa peringkat.
- **BR03:** Laporan PDF dijana menggunakan library Laravel DomPDF dengan templat berlogo MOTAC.
- **BR04:** Laporan Excel dijana menggunakan Laravel Excel (Maatwebsite) dalam format `.xlsx`.
- **BR05:** Fail laporan yang dijana tidak disimpan di pelayan — ia dijanakan secara terus untuk muat turun (streaming download).

---

## 10. Keperluan Data

### Query Utama (Contoh)

```sql
SELECT
  a.no_tiket,
  u.name AS pemohon,
  u.bahagian,
  k.nama_kategori,
  a.lokasi,
  a.status,
  a.created_at AS tarikh_mohon,
  a.tarikh_selesai,
  DATEDIFF(a.tarikh_selesai, a.created_at) AS hari_selesai
FROM aduan_ict a
JOIN users u ON a.user_id = u.id
JOIN kategori_aduan k ON a.kategori_id = k.id
WHERE a.created_at BETWEEN :tarikh_dari AND :tarikh_hingga
  AND (:kategori IS NULL OR a.kategori_id = :kategori)
  AND (:status IS NULL OR a.status = :status)
  AND k.unit_penerima = :unit_penerima -- untuk pentadbir
ORDER BY a.created_at DESC;
```

---

## 11. Antara Muka Berkaitan

- **Halaman:** `/admin/laporan` — halaman jana laporan
- **Controller:** `Admin/LaporanController@index`, `@generate`, `@exportPdf`, `@exportExcel`
- **Library:** `barryvdh/laravel-dompdf` (PDF), `maatwebsite/excel` (Excel)
- **Middleware:** `role:pentadbir,superadmin`

---

## 12. Kriteria Penerimaan

- [ ] Borang parameter laporan dipaparkan dengan medan tarikh, kategori, dan status.
- [ ] Pentadbir hanya dapat menjana laporan untuk unit mereka; Superadmin untuk semua unit.
- [ ] Laporan memaparkan ringkasan statistik dan senarai aduan terperinci.
- [ ] Eksport PDF berjaya dimuat turun dengan format dan logo MOTAC yang betul.
- [ ] Eksport Excel berjaya dimuat turun dalam format `.xlsx` yang boleh dibuka.
- [ ] Laporan dengan tiada data memaparkan mesej yang sesuai.
- [ ] Masa jana laporan tidak melebihi 10 saat untuk data 12 bulan.
