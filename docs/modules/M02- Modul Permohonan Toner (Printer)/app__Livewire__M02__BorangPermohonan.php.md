# `app/Livewire/M02/BorangPermohonan.php`

```php
<?php

namespace App\Livewire\M02;

use App\Models\PermohonanToner;
use App\Models\LogToner;
use App\Notifications\PermohonanTonerBaru;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Livewire\WithFileUploads;

class BorangPermohonan extends Component
{
    use WithFileUploads;

    // Maklumat pemohon (auto-isi)
    public string $nama_penuh      = '';
    public string $jawatan         = '';
    public string $bahagian_unit   = '';
    public string $no_telefon      = '';

    // Maklumat pencetak & toner
    public string $model_pencetak  = '';
    public string $jenama_toner    = '';
    public string $jenis_toner     = '';
    public string $no_siri_toner   = '';
    public int    $kuantiti        = 1;
    public string $lokasi_pencetak = '';
    public string $tujuan          = '';
    public string $tarikh_diperlukan = '';
    public $lampiran               = null;

    public bool $berjaya = false;
    public string $no_tiket_baru = '';

    public function mount(): void
    {
        // Auto-isi maklumat dari profil pengguna
        $pengguna = Auth::user();
        $this->nama_penuh    = $pengguna->name;
        $this->jawatan       = $pengguna->jawatan ?? '';
        $this->bahagian_unit = $pengguna->bahagian ?? '';
        $this->no_telefon    = $pengguna->no_telefon ?? '';
    }

    protected function rules(): array
    {
        return [
            'model_pencetak'    => 'required|string|max:100',
            'jenama_toner'      => 'required|string|max:100',
            'jenis_toner'       => 'required|in:hitam,cyan,magenta,kuning',
            'no_siri_toner'     => 'nullable|string|max:100',
            'kuantiti'          => 'required|integer|min:1|max:50',
            'lokasi_pencetak'   => 'required|string|max:150',
            'tujuan'            => 'required|string|min:10|max:500',
            'tarikh_diperlukan' => 'nullable|date|after_or_equal:today',
            'lampiran'          => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    protected function messages(): array
    {
        return [
            'model_pencetak.required'  => 'Sila isi model pencetak.',
            'jenama_toner.required'    => 'Sila isi jenama toner.',
            'jenis_toner.required'     => 'Sila pilih jenis toner.',
            'jenis_toner.in'           => 'Jenis toner tidak sah.',
            'kuantiti.required'        => 'Sila isi kuantiti yang diperlukan.',
            'kuantiti.min'             => 'Kuantiti minimum ialah 1.',
            'kuantiti.max'             => 'Kuantiti maksimum ialah 50 unit.',
            'lokasi_pencetak.required' => 'Sila isi lokasi pencetak.',
            'tujuan.required'          => 'Sila isi tujuan permohonan.',
            'tujuan.min'               => 'Tujuan permohonan terlalu pendek (minimum 10 aksara).',
            'lampiran.mimes'           => 'Lampiran mestilah fail JPG, PNG atau PDF.',
            'lampiran.max'             => 'Saiz lampiran tidak boleh melebihi 2MB.',
        ];
    }

    public function hantar(): void
    {
        $this->validate();

        $lampiranPath = null;
        if ($this->lampiran) {
            $lampiranPath = $this->lampiran->store('lampiran/toner', 'public');
        }

        $noTiket = PermohonanToner::janaNoTiket();

        $permohonan = PermohonanToner::create([
            'no_tiket'          => $noTiket,
            'pemohon_id'        => Auth::id(),
            'model_pencetak'    => $this->model_pencetak,
            'jenama_toner'      => $this->jenama_toner,
            'jenis_toner'       => $this->jenis_toner,
            'no_siri_toner'     => $this->no_siri_toner ?: null,
            'kuantiti_diminta'  => $this->kuantiti,
            'lokasi_pencetak'   => $this->lokasi_pencetak,
            'bahagian_pemohon'  => $this->bahagian_unit,
            'tujuan'            => $this->tujuan,
            'lampiran'          => $lampiranPath,
            'tarikh_diperlukan' => $this->tarikh_diperlukan ?: null,
            'status'            => 'submitted',
            'submitted_at'      => now(),
        ]);

        // Rekod log
        LogToner::create([
            'permohonan_id' => $permohonan->id,
            'pengguna_id'   => Auth::id(),
            'tindakan'      => 'submitted',
            'keterangan'    => 'Permohonan toner dihantar oleh pemohon.',
            'created_at'    => now(),
        ]);

        // Hantar notifikasi kepada pemohon & semua pentadbir BPM
        $pemohon    = Auth::user();
        $pentadbir  = User::whereIn('peranan', ['pentadbir', 'superadmin'])->get();

        $pemohon->notify(new PermohonanTonerBaru($permohonan, 'pemohon'));
        Notification::send($pentadbir, new PermohonanTonerBaru($permohonan, 'pentadbir'));

        $this->no_tiket_baru = $noTiket;
        $this->berjaya       = true;
        $this->reset([
            'model_pencetak', 'jenama_toner', 'jenis_toner',
            'no_siri_toner', 'kuantiti', 'lokasi_pencetak',
            'tujuan', 'tarikh_diperlukan', 'lampiran',
        ]);
    }

    public function render()
    {
        return view('livewire.m02.borang-permohonan')
            ->layout('layouts.app', ['title' => 'Permohonan Toner Printer']);
    }
}
```
