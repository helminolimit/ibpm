# M05-B5 — Sokongan Ketua Bahagian
**M05 Pinjaman ICT | Borang C Bahagian 5**

> Fasa: 2 — Kelulusan | Pelaku: Ketua Bahagian (Gred 41+) | Kaedah: Pautan e-mel tanpa login sistem

---

## Tujuan

Mendapatkan sokongan rasmi Ketua Bahagian atau pegawai sekurang-kurangnya Gred 41 sebelum permohonan pinjaman peralatan ICT dapat diproses oleh pentadbir BPM. Kelulusan dilakukan sepenuhnya melalui e-mel — **Ketua Bahagian tidak perlu log masuk ke sistem ICTServe**.

---

## Aliran Kelulusan

```
Pemohon hantar permohonan (Bahagian 1–4 lengkap)
        ↓
Sistem jana token selamat (SHA-256, sah 72 jam)
        ↓
E-mel notifikasi dihantar kepada Ketua Bahagian
(mengandungi ringkasan permohonan + pautan kelulusan)
        ↓
Ketua Bahagian buka e-mel → tekan pautan
        ↓
Halaman kelulusan dipaparkan (tanpa login)
Maklumat permohonan penuh ditunjukkan
        ↓
Ketua Bahagian isi catatan (jika perlu) → pilih keputusan
        ↓
[SOKONG]          → Status: Dalam Tindakan
                  → Notifikasi kepada Pemohon + Pentadbir BPM
[TIDAK DISOKONG]  → Status: Tidak Disokong
                  → Notifikasi kepada Pemohon (beserta sebab)
        ↓
Token ditanda sebagai digunakan — pautan tidak aktif lagi
```

---

## E-mel Notifikasi kepada Ketua Bahagian

### Kandungan E-mel

| Elemen | Kandungan |
|--------|-----------|
| Subjek | `[ICTServe] Permohonan Pinjaman ICT Memerlukan Sokongan Anda — #{No. Tiket}` |
| Penghantar | `noreply@ictserve.motac.gov.my` |
| Kepada | E-mel Ketua Bahagian berdasarkan unit pemohon |

### Maklumat dalam Badan E-mel

| Maklumat | Sumber |
|----------|--------|
| No. Tiket | Auto-jana sistem |
| Nama Pemohon | Bahagian 1 |
| Bahagian / Unit | Bahagian 1 |
| Peralatan Dipohon | Bahagian 3 (senarai ringkas) |
| Tujuan | Bahagian 3 |
| Tempoh Pinjaman | Bahagian 3 (tarikh pinjam – tarikh pulang) |
| Wakil Pengambilan | Bahagian 4A (jika ada) |
| Wakil Pemulangan | Bahagian 4B (jika ada) |
| Pautan Kelulusan | Token URL selamat |
| Tempoh Sah Pautan | 72 jam dari masa e-mel dihantar |

### Contoh Format E-mel

```
Subjek: [ICTServe] Permohonan Pinjaman ICT Memerlukan Sokongan Anda — #ICT-2026-047

Assalamualaikum / Selamat sejahtera,

Permohonan pinjaman peralatan ICT berikut memerlukan sokongan Tuan/Puan:

  No. Tiket    : #ICT-2026-047
  Pemohon      : Ahmad Kamal bin Razali (S41, Unit Promosi Digital)
  Peralatan    : Laptop × 1, Projektor × 1
  Tujuan       : Pembentangan Program Lancongan 2026
  Tempoh       : 18 April 2026 – 20 April 2026 (3 hari)
  Wakil Ambil  : Mohd Yusof bin Hamzah
  Wakil Pulang : Tiada (pemohon sendiri)

Sila tekan pautan berikut untuk melihat maklumat penuh dan berikan keputusan:

  https://ictserve.motac.gov.my/semak/ICT-2026-047?token=a3f8b2c1...

  Pautan ini sah selama 72 jam. Anda tidak perlu log masuk ke sistem.

Sebarang pertanyaan: Unit Operasi, Teknikal & Khidmat Pengguna, BPM MOTAC.
```

---

## Halaman Kelulusan (Tanpa Login)

### Maklumat yang Dipaparkan

Halaman kelulusan memaparkan **maklumat permohonan penuh** untuk semakan Ketua Bahagian:

| Seksyen | Maklumat |
|---------|----------|
| Header | ICTServe logo + lencana "Akses Selamat" + No. Tiket |
| Maklumat Permohonan | No. tiket, tarikh mohon, status semasa |
| Maklumat Pemohon | Nama, jawatan, bahagian, telefon |
| Dipohon Bagi | Maklumat Bahagian 1A (jika ada) |
| Pegawai Bertanggungjawab | Nama & jawatan (Bahagian 2) |
| Peralatan & Tempoh | Senarai peralatan, tujuan, lokasi, tarikh (Bahagian 3) |
| Wakil | Wakil pengambilan & pemulangan (Bahagian 4) |

### Medan Tindakan

| Elemen | Jenis | Wajib | Keterangan |
|--------|-------|-------|------------|
| Catatan / Sebab | Textarea | Ya (jika tolak) | Wajib diisi jika memilih Tidak Disokong |
| Butang Sokong | Butang (hijau) | — | Mengesahkan sokongan |
| Butang Tidak Disokong | Butang (merah) | — | Menolak permohonan |

### Paparan Selepas Keputusan

**Jika Disokong:**
```
✓ Permohonan Disokong

Terima kasih. Keputusan anda telah direkodkan.
Permohonan #ICT-2026-047 akan diproses oleh Unit Operasi,
Teknikal & Khidmat Pengguna, BPM.

Pemohon akan dimaklumkan melalui e-mel.
Rekod diambil pada: 16 Apr 2026, 9:31 pagi
```

**Jika Tidak Disokong:**
```
✕ Permohonan Tidak Disokong

Keputusan anda telah direkodkan.
Pemohon akan dimaklumkan melalui e-mel beserta catatan
yang diberikan.

Rekod diambil pada: 16 Apr 2026, 9:31 pagi
```

---

## Keselamatan Token

| Ciri | Perincian |
|------|-----------|
| Algoritma | SHA-256 (`ticket_id + APP_SECRET + timestamp`) |
| Tempoh sah | 72 jam dari masa e-mel dihantar |
| Penggunaan | One-time use — tidak aktif selepas keputusan dibuat |
| Protokol | HTTPS sahaja |
| Log audit | IP pelayar, masa keputusan, catatan — disimpan dalam pangkalan data |
| Token luput | Pautan papar mesej "Pautan telah tamat tempoh" |
| Token digunakan | Pautan papar mesej "Keputusan telah diberikan sebelum ini" |

---

## Status Selepas Keputusan

| Keputusan | Status Permohonan | Notifikasi |
|-----------|------------------|------------|
| Sokong | `Dalam Tindakan` | Pemohon + Pentadbir Unit Operasi, Teknikal & Khidmat Pengguna |
| Tidak Disokong | `Tidak Disokong` | Pemohon (beserta catatan sebab penolakan) |

---

## Pangkalan Data

### Kolum dalam `loan_requests`

```sql
-- Token kelulusan
approval_token          VARCHAR(64) NULL
token_expires_at        TIMESTAMP NULL
token_used_at           TIMESTAMP NULL

-- Rekod keputusan
approval_decision       ENUM('sokong', 'tolak') NULL
approval_note           TEXT NULL
approval_ip             VARCHAR(45) NULL
approved_by_name        VARCHAR(255) NULL    -- Nama Ketua Bahagian (dari borang atau input)
approved_at             TIMESTAMP NULL
```

### Logik Jana Token (Laravel)

```php
// Jana token semasa permohonan dihantar
$token = hash('sha256', $loanRequest->id . config('app.key') . now()->timestamp);
$loanRequest->update([
    'approval_token'    => $token,
    'token_expires_at'  => now()->addHours(72),
]);

// URL pautan kelulusan
$approvalUrl = route('loan.approve', [
    'ticket' => $loanRequest->ticket_number,
    'token'  => $token,
]);
```

### Middleware Pengesahan Token

```php
// Semak token sah, belum digunakan, belum luput
public function handle($request, Closure $next)
{
    $loan = LoanRequest::where('ticket_number', $request->ticket)
                       ->where('approval_token', $request->token)
                       ->first();

    if (!$loan)                              abort(404, 'Pautan tidak sah.');
    if ($loan->token_used_at)                abort(403, 'Keputusan telah diberikan.');
    if ($loan->token_expires_at < now())     abort(403, 'Pautan telah tamat tempoh.');

    return $next($request);
}
```

---

## Kaitan dengan Bahagian Lain

| Bahagian | Kaitan |
|----------|--------|
| Bahagian 1–4 | Maklumat dari semua bahagian ini dipaparkan dalam halaman kelulusan |
| Bahagian 6 — Pengeluaran | Hanya boleh dilaksanakan selepas keputusan "Sokong" diberikan |
| Bahagian 7 — Pemulangan | Tidak berkaitan secara langsung dengan Bahagian 5 |

---

*ICTServe | M05-B5 — Sokongan Ketua Bahagian | Borang C Bahagian 5 | Versi 1.0 | 16 April 2026*
