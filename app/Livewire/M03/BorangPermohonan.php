<?php

namespace App\Livewire\M03;

use App\Models\PermohonanPenamatan;
use App\Models\User;
use App\Notifications\PenatamatanNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Permohonan Penamatan Akaun')]
class BorangPermohonan extends Component
{
    public string $pengguna_sasaran_id = '';

    public string $id_login_komputer = '';

    public string $tarikh_berkuat_kuasa = '';

    public string $jenis_tindakan = 'TAMAT';

    public string $sebab_penamatan = '';

    public $senaraiPengguna = [];

    public int $langkah = 1;

    public string $noTiket = '';

    public function mount(): void
    {
        $this->senaraiPengguna = User::where('id', '!=', Auth::id())
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function seterusnya(): void
    {
        $this->validate([
            'pengguna_sasaran_id' => ['required', 'exists:users,id'],
            'id_login_komputer' => ['required', 'string', 'max:100'],
            'tarikh_berkuat_kuasa' => ['required', 'date', 'after_or_equal:today'],
            'jenis_tindakan' => ['required', 'in:TAMAT,GANTUNG'],
            'sebab_penamatan' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'pengguna_sasaran_id.required' => 'Sila pilih pengguna sasaran.',
            'tarikh_berkuat_kuasa.after_or_equal' => 'Tarikh mestilah hari ini atau masa hadapan.',
            'sebab_penamatan.min' => 'Sebab penamatan mestilah sekurang-kurangnya 10 aksara.',
        ]);

        $this->langkah = 2;
    }

    public function kembali(): void
    {
        $this->langkah = 1;
    }

    public function hantar(): void
    {
        $permohonan = PermohonanPenamatan::create([
            'no_tiket' => PermohonanPenamatan::janaNoTiket(),
            'pemohon_id' => Auth::id(),
            'pengguna_sasaran_id' => $this->pengguna_sasaran_id,
            'id_login_komputer' => $this->id_login_komputer,
            'tarikh_berkuat_kuasa' => $this->tarikh_berkuat_kuasa,
            'jenis_tindakan' => $this->jenis_tindakan,
            'sebab_penamatan' => $this->sebab_penamatan,
            'status' => 'MENUNGGU_KEL_1',
        ]);

        $permohonan->logAudit()->create([
            'pengguna_id' => Auth::id(),
            'tindakan' => 'permohonan_dihantar',
            'modul' => 'M03',
            'ip_address' => request()->ip(),
        ]);

        $permohonan->pemohon->notify(new PenatamatanNotification($permohonan, 'HANTAR'));

        $permohonan->notifikasi()->create([
            'penerima_id' => Auth::id(),
            'jenis' => 'HANTAR',
            'tajuk' => 'Permohonan '.$permohonan->no_tiket.' diterima',
            'mesej' => 'Permohonan penamatan akaun '.$permohonan->id_login_komputer.' sedang diproses.',
            'dihantar_pada' => now(),
        ]);

        $this->noTiket = $permohonan->no_tiket;
        $this->langkah = 3;
    }

    public function render()
    {
        return view('livewire.m03.borang-permohonan');
    }
}
