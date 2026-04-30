# UC-M06-04 — Semak Status Permohonan

## Actor
Pemohon

## Precondition
- Pemohon log masuk
- Permohonan sudah dihantar (UC-M06-01)

## Flow
1. Pemohon klik **Senarai Saya** di sidebar
2. Sistem papar senarai permohonan M06 milik pemohon
3. Pemohon klik no tiket untuk lihat butiran
4. Sistem papar:
   - Status semasa (badge berwarna)
   - Senarai ahli dalam permohonan
   - Catatan pentadbir (jika ada)
   - Log aktiviti permohonan

## Status & Warna Badge
| Status | Warna Tailwind |
|--------|---------------|
| Baru | `bg-gray-100 text-gray-700` |
| Dalam Tindakan | `bg-yellow-100 text-yellow-700` |
| Selesai | `bg-green-100 text-green-700` |
| Ditolak | `bg-red-100 text-red-700` |

## Livewire Component
`App\Livewire\M06\SenaraiPermohonan`

## Query
```php
PermohonanEmel::where('user_id', auth()->id())
    ->with(['ahliKumpulan', 'kumpulanEmel'])
    ->latest()
    ->paginate(10);
```

## DO NOT
- Papar permohonan pengguna lain kepada pemohon
- Benarkan pemohon edit permohonan setelah `status != baru`
