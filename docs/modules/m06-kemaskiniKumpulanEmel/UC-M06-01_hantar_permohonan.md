# UC-M06-01 — Hantar Permohonan Kumpulan Emel

## Actor
Pemohon (warga kerja MOTAC)

## Precondition
- Pengguna log masuk
- Kumpulan emel wujud dalam `kumpulan_emel`

## Flow
1. Pemohon klik **Kemaskini Group Emel** di dashboard
2. Sistem papar borang dengan field:
   - `nama_kumpulan_emel` (dropdown dari `kumpulan_emel`)
   - `jenis_tindakan` (enum: `tambah` / `buang`)
   - senarai ahli (lihat UC-M06-02 / UC-M06-03)
   - `catatan_pemohon` (textarea, optional)
3. Pemohon submit borang
4. Sistem jana `no_tiket` format `#GRP-YYYY-NNN`
5. Sistem simpan ke `permohonan_emel` dengan `status = baru`
6. Sistem hantar notifikasi kepada Pentadbir Unit Infrastruktur & Keselamatan ICT
7. Sistem hantar emel pengesahan kepada Pemohon

## Alternative Flow
- Jika kumpulan emel tidak dipilih → validasi gagal, papar mesej ralat

## Livewire Component
`App\Livewire\M06\HantarPermohonan`

## Validation Rules
```php
'nama_kumpulan_emel' => 'required|exists:kumpulan_emel,id',
'jenis_tindakan'     => 'required|in:tambah,buang',
'catatan_pemohon'    => 'nullable|string|max:500',
'ahli'               => 'required|array|min:1',
'ahli.*.emel_ahli'  => 'required|email',
'ahli.*.nama_ahli'  => 'required|string|max:255',
```

## DO NOT
- Biarkan field `ahli` kosong tanpa validasi
- Simpan `no_tiket` manual — guna helper `TicketGenerator::generate('GRP')`
