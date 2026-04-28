# M03 — Penamatan Akaun Login Komputer
## README Handoff untuk Claude Code

> **Sistem:** ICTServe · MOTAC  
> **Stack:** Laravel 13 · Livewire 4 · Blade · Tailwind CSS · MySQL  
> **Branch:** `feature/M03-penamatan-akaun`  
> **Keutamaan:** TINGGI

---

## Cara Guna README Ini

Baca fail-fail berikut mengikut urutan sebelum tulis sebarang kod:

| Urutan | Fail | Fungsi |
|--------|------|--------|
| 1 | `01-migration.md` | Skema DB — cipta jadual dahulu |
| 2 | `02-models.md` | Eloquent models + relationships |
| 3 | `03-controllers.md` | Controllers + aliran status |
| 4 | `04-livewire.md` | Livewire components + borang |
| 5 | `05-notifications.md` | Notifikasi emel + queue |
| 6 | `06-routes-views.md` | Routes + struktur Blade views |
| 6 | `07 — UI Design Handoff` | Rujukan UI design |

---

## Gambaran Modul

Modul M03 memproses penamatan atau penggantungan akaun login komputer kakitangan MOTAC.  
Permohonan melalui **dua peringkat kelulusan** sebelum akaun ditamatkan.

```
Pemohon → [hantar] → Gred 41+ → [lulus] → Pentadbir ICT → [lulus+tindakan] → SELESAI
                                    ↓ tolak
                               Pemohon dimaklum (emel)
```

---

## Peranan & Middleware

| Peranan | Nilai dalam DB | Middleware Laravel |
|---------|---------------|-------------------|
| Pemohon / Pegawai | `pemohon` | `auth` |
| Pegawai Gred 41+ | `pelulus_1` | `auth, role:pelulus_1` |
| Pentadbir ICT (Unit Operasi) | `pentadbir` | `auth, role:pentadbir` |
| Superadmin | `superadmin` | `auth, role:superadmin` |

---

## Status Enum (Penting — guna tepat-tepat)

```php
// Nilai enum untuk kolum `status` dalam jadual permohonan_penamatan
DRAF              // Borang disimpan, belum dihantar
MENUNGGU_KEL_1    // Menunggu kelulusan Pegawai Gred 41+
MENUNGGU_KEL_2    // Menunggu kelulusan Pentadbir ICT
DALAM_PROSES      // Pentadbir sedang laksanakan penamatan
SELESAI           // Akaun berjaya ditamatkan
DITOLAK           // Ditolak di mana-mana peringkat
```

---

## Kes Penggunaan Ringkas (8 UC)

| ID | Nama | Pelakon | Jenis |
|----|------|---------|-------|
| UC-M03-01 | Isi borang penamatan akaun | Pemohon | Manual |
| UC-M03-02 | Jana nombor tiket automatik | Sistem | Auto |
| UC-M03-03 | Semak status permohonan | Pemohon | Manual |
| UC-M03-04 | Terima notifikasi emel | Semua | Auto |
| UC-M03-05 | Luluskan permohonan peringkat 1 | Gred 41+ | Manual |
| UC-M03-06 | Luluskan permohonan peringkat 2 | Pentadbir ICT | Manual |
| UC-M03-07 | Tamatkan akaun dalam sistem | Pentadbir ICT | Manual |
| UC-M03-08 | Jana rekod log audit | Sistem | Auto (include UC-07) |

---

## Entiti Pangkalan Data (4 jadual)

```
permohonan_penamatan   ← jadual utama M03
kelulusan              ← rekod kelulusan dua peringkat
log_audits             ← rekod audit semua tindakan
notifikasi             ← rekod emel yang dihantar
```

---

## Peraturan Wajib

- JANGAN push terus ke `main` — guna branch `feature/M03-penamatan-akaun`
- JANGAN kongsi migration dengan modul lain
- JANGAN hardcode nama jadual — guna `$table` dalam model
- JANGAN simpan logik bisnes dalam Blade atau Livewire component
- SEMUA emel MESTI melalui queue (`ShouldQueue`)
- SEMUA tindakan kritikal MESTI dicatat dalam `log_audits`
- Nombor tiket format: `PAK-YYYY-NNN` (contoh: `PAK-2024-001`)

---

## Rujukan

- ERD & Use Case visual: `m03_use_case_penamatan_akaun.html`
- Handoff HTML penuh: `index.html`, `usecases.html`, `erd.html`, `impl.html`
- Spesifikasi sistem: `Spesifikasi_Keperluan_Sistem_ICTServe.pdf`
