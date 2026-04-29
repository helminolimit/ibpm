<?php

namespace App\Livewire\M04;

use App\Enums\RolePengguna;
use App\Mail\PermohonanPortalDiterima;
use App\Models\PermohonanPortal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Permohonan Kemaskini Portal')]
class BorangPermohonan extends Component
{
    use WithFileUploads;

    public string $url_halaman = '';

    public string $jenis_perubahan = '';

    public string $butiran_kemaskini = '';

    public $lampiran = [];

    public int $langkah = 1;

    public string $noTiket = '';

    public function seterusnya(): void
    {
        $this->validate([
            'url_halaman' => ['required', 'url'],
            'jenis_perubahan' => ['required', 'in:kandungan,konfigurasi,lain_lain'],
            'butiran_kemaskini' => ['required', 'string', 'min:10'],
            'lampiran.*' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:5120'],
        ], [
            'url_halaman.required' => 'Sila masukkan URL halaman.',
            'url_halaman.url' => 'URL halaman tidak sah.',
            'jenis_perubahan.required' => 'Sila pilih jenis perubahan.',
            'butiran_kemaskini.min' => 'Butiran kemaskini mestilah sekurang-kurangnya 10 aksara.',
        ]);

        $this->langkah = 2;
    }

    public function kembali(): void
    {
        $this->langkah = 1;
    }

    public function hantar(): void
    {
        $permohonan = PermohonanPortal::create([
            'no_tiket' => PermohonanPortal::janaNoTiket(),
            'pemohon_id' => Auth::id(),
            'url_halaman' => $this->url_halaman,
            'jenis_perubahan' => $this->jenis_perubahan,
            'butiran_kemaskini' => $this->butiran_kemaskini,
            'status' => 'diterima',
        ]);

        foreach ($this->lampiran as $fail) {
            $path = $fail->store('lampiran/m04', 'local');
            $permohonan->lampirans()->create([
                'nama_fail' => $fail->getClientOriginalName(),
                'path_fail' => $path,
                'jenis_fail' => $fail->getMimeType(),
            ]);
        }

        $permohonan->logAudits()->create([
            'pengguna_id' => Auth::id(),
            'tindakan' => 'permohonan_dihantar',
            'modul' => 'M04',
            'ip_address' => request()->ip(),
        ]);

        // Hantar email ke Pentadbir (queue)
        $pentadbir = User::where('role', RolePengguna::Pentadbir)
            ->where('bahagian', 'Unit Aplikasi Teras dan Multimedia')
            ->first();

        if ($pentadbir) {
            Mail::to($pentadbir->email)->queue(new PermohonanPortalDiterima($permohonan));

            // Simpan notifikasi
            \App\Models\NotifikasiPortal::create([
                'pengguna_id' => $pentadbir->id,
                'permohonan_portal_id' => $permohonan->id,
                'jenis' => 'permohonan_baru',
                'mesej' => 'Permohonan kemaskini portal baharu '.$permohonan->no_tiket.' telah diterima.',
            ]);
        }

        $this->noTiket = $permohonan->no_tiket;
        $this->langkah = 3;
    }

    public function render()
    {
        return view('livewire.m04.borang-permohonan');
    }
}
