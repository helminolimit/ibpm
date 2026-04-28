# 04 — Livewire Components
## M03 Penamatan Akaun Login Komputer

Cipta 2 Livewire component. Component hanya uruskan state UI — JANGAN simpan logik bisnes di sini.

---

## Component 1: Borang Permohonan (Pemohon)
**Fail PHP:** `app/Livewire/M03/BorangPermohonan.php`  
**Fail Blade:** `resources/views/livewire/m03/borang-permohonan.blade.php`

```php
<?php
namespace App\Livewire\M03;

use Livewire\Component;
use App\Models\User;
use App\Models\PermohonanPenamatan;
use App\Http\Requests\PenatamatanAkaunRequest;
use App\Notifications\PenatamatanNotification;
use Illuminate\Support\Facades\Auth;

class BorangPermohonan extends Component
{
    // Medan borang — diikat terus dengan wire:model dalam Blade
    public string $pengguna_sasaran_id   = '';
    public string $id_login_komputer     = '';
    public string $tarikh_berkuat_kuasa  = '';
    public string $jenis_tindakan        = 'TAMAT';
    public string $sebab_penamatan       = '';

    // Senarai pengguna untuk dropdown pilihan sasaran
    public $senaraiPengguna = [];

    // Langkah wizard semasa (1: Maklumat, 2: Sahkan, 3: Selesai)
    public int $langkah = 1;

    // Muat senarai pengguna untuk dropdown apabila component dimulakan
    public function mount(): void
    {
        $this->senaraiPengguna = User::where('id', '!=', Auth::id())
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    // Pindah ke langkah 2 (semakan) — sahkan data dahulu
    public function seterusnya(): void
    {
        $this->validate([
            'pengguna_sasaran_id'  => 'required|exists:users,id',
            'id_login_komputer'    => 'required|string|max:100',
            'tarikh_berkuat_kuasa' => 'required|date|after_or_equal:today',
            'jenis_tindakan'       => 'required|in:TAMAT,GANTUNG',
            'sebab_penamatan'      => 'required|string|min:10|max:1000',
        ]);

        $this->langkah = 2;
    }

    // Kembali ke langkah 1 untuk edit semula
    public function kembali(): void
    {
        $this->langkah = 1;
    }

    // Hantar permohonan — simpan ke DB dan jana tiket
    // Selepas berjaya, pindah ke langkah 3 (selesai)
    public function hantar(): void
    {
        $permohonan = PermohonanPenamatan::create([
            'no_tiket'             => PermohonanPenamatan::janaNoTiket(),
            'pemohon_id'           => Auth::id(),
            'pengguna_sasaran_id'  => $this->pengguna_sasaran_id,
            'id_login_komputer'    => $this->id_login_komputer,
            'tarikh_berkuat_kuasa' => $this->tarikh_berkuat_kuasa,
            'jenis_tindakan'       => $this->jenis_tindakan,
            'sebab_penamatan'      => $this->sebab_penamatan,
            'status'               => 'MENUNGGU_KEL_1',
        ]);

        $permohonan->logAudit()->create([
            'pengguna_id' => Auth::id(),
            'tindakan'    => 'permohonan_dihantar',
            'modul'       => 'M03',
            'ip_address'  => request()->ip(),
        ]);

        $permohonan->pemohon->notify(new PenatamatanNotification($permohonan, 'HANTAR'));

        $this->langkah = 3;
        $this->dispatch('permohonan-berjaya', tiket: $permohonan->no_tiket);
    }

    public function render()
    {
        return view('livewire.m03.borang-permohonan');
    }
}
```

### Blade: `resources/views/livewire/m03/borang-permohonan.blade.php`

3 blok `@if($langkah === N)`:

- **Langkah 1** — Borang: `select` pengguna_sasaran (loop `$senaraiPengguna`), `input` id_login_komputer, `select` jenis_tindakan, `input[type=date]` tarikh_berkuat_kuasa, `textarea` sebab_penamatan. Setiap medan ada `@error`. Butang `wire:click="seterusnya"`.
- **Langkah 2** — Semakan: `<dl>` papar nilai, butang `wire:click="kembali"` + `wire:click="hantar" wire:loading.attr="disabled"`.
- **Langkah 3** — Berjaya: mesej hijau + link balik ke senarai.

---

## Component 2: Senarai Admin dengan Filter
**Fail PHP:** `app/Livewire/M03/AdminSenarai.php`

```php
<?php
namespace App\Livewire\M03;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PermohonanPenamatan;

class AdminSenarai extends Component
{
    use WithPagination;

    public string $filterStatus = '';
    public string $carian       = '';

    // Reset halaman apabila filter berubah — elak halaman kosong
    public function updatingFilterStatus(): void { $this->resetPage(); }
    public function updatingCarian(): void        { $this->resetPage(); }

    public function render()
    {
        $senarai = PermohonanPenamatan::with(['pemohon','penggunaSasaran'])
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->carian, fn($q) => $q->where('no_tiket','like','%'.$this->carian.'%')
                ->orWhereHas('pemohon', fn($u) => $u->where('name','like','%'.$this->carian.'%')))
            ->latest()
            ->paginate(20);

        return view('livewire.m03.admin-senarai', compact('senarai'));
    }
}
```
