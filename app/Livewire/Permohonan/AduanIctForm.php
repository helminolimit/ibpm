<?php

namespace App\Livewire\Permohonan;

use App\Enums\StatusAduan;
use App\Events\AduanBaru;
use App\Events\AduanDihantar;
use App\Models\AduanIct;
use App\Models\KategoriAduan;
use App\Models\LampiranAduan;
use App\Models\StatusLog;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Hantar Aduan ICT')]
class AduanIctForm extends Component
{
    use WithFileUploads;

    public int $step = 1;

    public ?int $kategoriId = null;

    public string $lokasi = '';

    public string $tajuk = '';

    public string $keterangan = '';

    public string $noTelefon = '';

    public $lampiran = null;

    public ?string $noTiket = null;

    protected function rules(): array
    {
        return [
            'kategoriId' => ['required', 'exists:kategori_aduan,id'],
            'lokasi' => ['required', 'string', 'max:255'],
            'tajuk' => ['required', 'string', 'max:255'],
            'keterangan' => ['required', 'string'],
            'noTelefon' => ['required', 'string', 'max:20'],
            'lampiran' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    protected function messages(): array
    {
        return [
            'kategoriId.required' => 'Sila pilih kategori aduan.',
            'kategoriId.exists' => 'Kategori aduan tidak sah.',
            'lokasi.required' => 'Sila isi lokasi / bilik.',
            'tajuk.required' => 'Sila isi tajuk aduan.',
            'tajuk.max' => 'Tajuk aduan tidak boleh melebihi 255 aksara.',
            'keterangan.required' => 'Sila isi keterangan masalah.',
            'noTelefon.required' => 'Sila isi no. telefon.',
            'lampiran.mimes' => 'Fail lampiran mestilah JPG, PNG, atau PDF.',
            'lampiran.max' => 'Saiz fail lampiran tidak boleh melebihi 5MB.',
        ];
    }

    public function mount(): void
    {
        $this->noTelefon = Auth::user()->no_telefon ?? '';
    }

    #[Computed]
    public function kategoris()
    {
        return KategoriAduan::aktif()->orderBy('nama')->get();
    }

    #[Computed]
    public function selectedKategori(): ?KategoriAduan
    {
        if (! $this->kategoriId) {
            return null;
        }

        return $this->kategoris->firstWhere('id', $this->kategoriId);
    }

    public function teruskan(): void
    {
        $this->validate();
        $this->step = 2;
    }

    public function balik(): void
    {
        $this->step = 1;
    }

    public function hantar(): void
    {
        $this->validate();

        DB::transaction(function () {
            $noTiket = AduanIct::generateNoTiket();

            $aduan = AduanIct::create([
                'no_tiket' => $noTiket,
                'user_id' => Auth::id(),
                'kategori_aduan_id' => $this->kategoriId,
                'lokasi' => $this->lokasi,
                'tajuk' => $this->tajuk,
                'keterangan' => $this->keterangan,
                'no_telefon' => $this->noTelefon,
                'status' => StatusAduan::Baru,
            ]);

            StatusLog::create([
                'aduan_ict_id' => $aduan->id,
                'status' => StatusAduan::Baru,
                'catatan' => 'Aduan diterima.',
                'user_id' => Auth::id(),
            ]);

            if ($this->lampiran) {
                $path = $this->lampiran->store('lampiran-aduan', 'public');

                LampiranAduan::create([
                    'aduan_ict_id' => $aduan->id,
                    'nama_fail' => $this->lampiran->getClientOriginalName(),
                    'path' => $path,
                    'jenis_fail' => $this->lampiran->getMimeType(),
                    'saiz' => $this->lampiran->getSize(),
                ]);
            }

            AduanDihantar::dispatch($aduan);
            AduanBaru::dispatch($aduan);

            $this->noTiket = $noTiket;
        });

        Flux::modal('confirm-hantar')->close();

        $this->step = 3;
    }

    public function render()
    {
        return view('livewire.permohonan.aduan-ict-form');
    }
}
