# M06 — Kemaskini Kumpulan Emel

## Stack
- Backend: Laravel 13
- Frontend: Livewire 4 + Blade + Tailwind CSS
- Database: MySQL / SQLite
- Auth: Laravel Breeze

## Roles
| Role | Akses |
|------|-------|
| Pemohon | Hantar, semak status |
| Pentadbir (Unit Infrastruktur & Keselamatan ICT) | Lulus, kemaskini pelayan, notifikasi |
| Superadmin | Semua fungsi + laporan |

## Use Cases
| ID | Nama | Actor |
|----|------|-------|
| UC-M06-01 | Hantar permohonan | Pemohon |
| UC-M06-02 | Tambah ahli kumpulan | Pemohon |
| UC-M06-03 | Buang ahli kumpulan | Pemohon |
| UC-M06-04 | Semak status permohonan | Pemohon |
| UC-M06-05 | Semak dan lulus permohonan | Pentadbir |
| UC-M06-06 | Kemaskini kumpulan pelayan | Pentadbir |
| UC-M06-07 | Hantar notifikasi emel | Sistem/Pentadbir |
| UC-M06-08 | Urus pengguna dan kumpulan | Superadmin |
| UC-M06-09 | Jana laporan log perubahan | Superadmin |

## Tables
- `permohonan_emel` — rekod permohonan
- `ahli_kumpulan` — senarai ahli tambah/buang
- `kumpulan_emel` — direktori kumpulan
- `notifikasi` — shared dengan modul lain
- `log_audit` — shared dengan modul lain

## Branch
`feature/m06-kumpulan-emel`

## DO NOT
- Buat migration baru untuk `notifikasi` dan `log_audit` — sudah ada dalam M01
- Duplicate logic notifikasi — guna `NotificationService` shared
- Hard-code nama unit dalam kod — guna `pentadbir_roles` table
