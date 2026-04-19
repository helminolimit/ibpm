<?php

namespace App\Livewire\M02;

use App\Enums\JenisToner;
use App\Enums\StatusPermohonanToner;
use App\Models\LampiranToner;
use App\Models\LogToner;
use App\Models\PermohonanToner;
use App\Models\User;
use App\Notifications\PengesahanPermohonanToner;
use App\Notifications\PermohonanTonerBaru;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Permohonan Toner Baharu')]
class BorangPermohonan extends Component
{
    use WithFileUploads;

    public string $nama = '';

    public string $jawatan = '';

    public string $bahagian = '';

    public string $no_telefon = '';

    public string $model_pencetak = '';

    public string $jenama_toner = '';

    public string $jenis_toner = '';

    public string $no_siri_toner = '';

    public int $kuantiti = 1;

    public string $lokasi_pencetak = '';

    public string $tujuan = '';

    public string $tarikh_diperlukan = '';

    /** @var array<int, TemporaryUploadedFile> */
    public array $lampiranFiles = [];

    public function mount(): void
    {
        $user = Auth::user();

        $this->nama = $user->name;
        $this->jawatan = $user->jawatan ?? '';
        $this->bahagian = $user->bahagian ?? '';
        $this->no_telefon = $user->no_telefon ?? '';
    }

    /** @return array<string, mixed> */
    protected function rules(): array
    {
        return [
            'model_pencetak' => ['required', 'string', 'max:100'],
            'jenama_toner' => ['required', 'string', 'max:100'],
            'jenis_toner' => ['required', 'in:hitam,cyan,magenta,kuning'],
            'no_siri_toner' => ['nullable', 'string', 'max:100'],
            'kuantiti' => ['required', 'integer', 'min:1', 'max:50'],
            'lokasi_pencetak' => ['required', 'string', 'max:150'],
            'tujuan' => ['required', 'string', 'min:10', 'max:500'],
            'tarikh_diperlukan' => ['nullable', 'date', 'after_or_equal:today'],
            'lampiranFiles.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'jenis_toner.in' => 'Sila pilih jenis toner yang sah.',
            'kuantiti.min' => 'Kuantiti minimum ialah 1.',
            'kuantiti.max' => 'Kuantiti maksimum ialah 50.',
            'tujuan.min' => 'Tujuan mestilah sekurang-kurangnya 10 aksara.',
            'tujuan.max' => 'Tujuan tidak boleh melebihi 500 aksara.',
            'tarikh_diperlukan.after_or_equal' => 'Tarikh diperlukan mesti hari ini atau selepas.',
            'lampiranFiles.*.mimes' => 'Lampiran mestilah fail JPG, PNG atau PDF.',
            'lampiranFiles.*.max' => 'Saiz lampiran tidak boleh melebihi 2MB.',
        ];
    }

    public function hantar(): void
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            $permohonan = PermohonanToner::create([
                'no_tiket' => PermohonanToner::janaNoTiket(),
                'user_id' => Auth::id(),
                'model_pencetak' => $validated['model_pencetak'],
                'jenama_toner' => $validated['jenama_toner'],
                'jenis_toner' => $validated['jenis_toner'],
                'no_siri_toner' => $validated['no_siri_toner'] ?: null,
                'kuantiti' => $validated['kuantiti'],
                'lokasi_pencetak' => $validated['lokasi_pencetak'],
                'tujuan' => $validated['tujuan'],
                'tarikh_diperlukan' => $validated['tarikh_diperlukan'] ?: null,
                'status' => StatusPermohonanToner::Submitted,
            ]);

            LogToner::create([
                'permohonan_toner_id' => $permohonan->id,
                'tindakan' => 'submitted',
                'user_id' => Auth::id(),
            ]);

            foreach ($this->lampiranFiles as $fail) {
                $path = $fail->store('lampiran-toner', 'public');

                LampiranToner::create([
                    'permohonan_toner_id' => $permohonan->id,
                    'nama_fail' => $fail->getClientOriginalName(),
                    'path' => $path,
                    'jenis_fail' => $fail->getMimeType(),
                    'saiz' => $fail->getSize(),
                ]);
            }

            $permohonan->load('user');

            Auth::user()->notify(new PengesahanPermohonanToner($permohonan));

            User::where('role', 'admin')
                ->each(fn (User $admin) => $admin->notify(new PermohonanTonerBaru($permohonan)));
        });

        Flux::modals()->close();

        Flux::toast(
            variant: 'success',
            text: 'Permohonan toner berjaya dihantar.',
        );

        $this->reset(['model_pencetak', 'jenama_toner', 'jenis_toner', 'no_siri_toner', 'lokasi_pencetak', 'tujuan', 'tarikh_diperlukan', 'lampiranFiles']);
        $this->kuantiti = 1;
    }

    public function render(): View
    {
        return view('livewire.m02.borang-permohonan', [
            'jenisToner' => JenisToner::cases(),
        ]);
    }
}
