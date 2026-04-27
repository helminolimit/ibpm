# USER MANAGEMENT — SUPERADMIN
ICTServe · Sistem Pengurusan Perkhidmatan ICT · MOTAC

---

## Skop

Modul ini membolehkan **Superadmin** mengurus semua akaun pengguna, menetapkan peranan, memantau log audit, dan mengkonfigurasi tetapan sistem.

---

## Stack

| Lapisan | Teknologi |
|---|---|
| Backend | Laravel 13 |
| Frontend | Livewire 4 + Blade + Tailwind CSS |
| Auth | Laravel Breeze / Starter Kit |
| DB | MySQL / SQLite |

---

## Struktur Database

```
users
  id, name, jawatan, gred, bahagian, unit, email,
  password, role_id, status [aktif|tidak_aktif|pending],
  last_login_at, created_at, updated_at

roles
  id, name [superadmin|pentadbir|pengguna],
  unit_id (nullable), created_at

role_module_access
  id, role_id, module_code [M01–M06], can_view,
  can_create, can_update, can_delete

audit_logs
  id, user_id, action, module, description,
  ip_address, created_at
```

---

## Peranan & Akses Modul

| Peranan | M01 | M02 | M03 | M04 | M05 | M06 | Urus Pengguna |
|---|---|---|---|---|---|---|---|
| Superadmin | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Pentadbir — Unit Operasi, Teknikal & Khidmat Pengguna | ✓ | ✓ | — | — | ✓ | — | — |
| Pentadbir — Unit Aplikasi Teras dan Multimedia | ✓ | — | — | ✓ | — | — | — |
| Pentadbir — Unit Infrastruktur & Keselamatan ICT | — | — | ✓ | — | — | — | — |
| Pentadbir — Unit Aplikasi Sokongan & Pentadbiran | — | — | ✓ | — | — | ✓ | — |
| Pengguna / Pemohon | hantar sahaja | hantar sahaja | hantar sahaja | hantar sahaja | hantar sahaja | hantar sahaja | — |

---

## Routes (Prefix: `/superadmin/users`)

```
GET    /superadmin/users              index    — senarai pengguna
GET    /superadmin/users/create       create   — borang tambah
POST   /superadmin/users              store    — simpan pengguna baru
GET    /superadmin/users/{id}/edit    edit     — borang edit
PUT    /superadmin/users/{id}         update   — kemaskini
DELETE /superadmin/users/{id}         destroy  — padam (soft delete)
PATCH  /superadmin/users/{id}/status  toggle   — aktif/tidak aktif
GET    /superadmin/roles              roles    — pengurusan peranan
GET    /superadmin/audit-logs         logs     — log audit
```

---

## Validasi (UserRequest)

```php
'name'     => 'required|string|max:100',
'email'    => 'required|email|unique:users,email',
'jawatan'  => 'required|string|max:100',
'gred'     => 'nullable|string|max:20',
'bahagian' => 'required|string|max:100',
'unit'     => 'nullable|string|max:100',
'role_id'  => 'required|exists:roles,id',
'status'   => 'required|in:aktif,tidak_aktif,pending',
```

---

## Email Notifikasi

| Trigger | Penerima | Template |
|---|---|---|
| Akaun baharu dicipta | Pengguna baharu | `emails.akaun_baharu` |
| Status ditukar ke aktif | Pengguna berkenaan | `emails.akaun_aktif` |
| Status ditukar ke tidak aktif | Pengguna berkenaan | `emails.akaun_tidak_aktif` |
| Peranan dikemaskini | Pengguna berkenaan | `emails.peranan_kemaskini` |

---

## Apa TIDAK Boleh Dibuat

- Jangan padam hard-delete pengguna — guna soft delete (`deleted_at`)
- Jangan tukar peranan superadmin kepada peranan lain pada akaun sendiri
- Jangan import CSV tanpa validasi duplikat email
- Jangan skip log audit untuk sebarang tindakan kritikal
- Jangan benarkan pengguna dengan status `pending` log masuk

---

## Kriteria Penerimaan

- Superadmin boleh tambah, edit, tukar status pengguna
- Filter mengikut peranan, unit, dan status berfungsi
- Setiap tindakan kritikal direkod dalam `audit_logs`
- Notifikasi emel dihantar dalam masa 5 minit
- Pengguna pending tidak boleh akses sistem
