<?php

namespace App\Livewire\M06;

use App\Enums\JenisTindakan;
use App\Enums\StatusPermohonanEmel;
use App\Events\PermohonanEmelDihantar;
use App\Models\AhliKumpulan;
use App\Models\KumpulanEmel;
use App\Models\PermohonanEmel;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Hantar Permohonan Kumpulan Emel')]
class HantarPermohonan extends Component
{
    public int $step = 1;

    public ?int $kumpulanEmelId = null;

    public string $jenisTindakan = '';

    public string $catatanPemohon = '';

    /** @var array<int, array{nama_ahli: string, emel_ahli: string}> */
    public array $ahli = [];

    public ?string $noTiket = null;

    protected function rules(): array
    {
        $namaAhliRule = $this->jenisTindakan === 'tambah'
            ? ['required', 'string', 'max:255']
            : ['nullable', 'string', 'max:255'];

        return [
            'kumpulanEmelId' => ['required', 'exists:kumpulan_emel,id'],
            'jenisTindakan' => ['required', 'in:tambah,buang'],
            'catatanPemohon' => ['nullable', 'string', 'max:500'],
            'ahli' => ['required', 'array', 'min:1'],
            'ahli.*.nama_ahli' => $namaAhliRule,
            'ahli.*.emel_ahli' => ['required', 'email'],
        ];
    }

    protected function messages(): array
    {
        return [
            'kumpulanEmelId.required' => 'Sila pilih kumpulan emel.',
            'kumpulanEmelId.exists' => 'Kumpulan emel tidak sah.',
            'jenisTindakan.required' => 'Sila pilih jenis tindakan.',
            'jenisTindakan.in' => 'Jenis tindakan tidak sah.',
            'catatanPemohon.max' => 'Catatan tidak boleh melebihi 500 aksara.',
            'ahli.required' => 'Sila tambah sekurang-kurangnya seorang ahli.',
            'ahli.min' => 'Sila tambah sekurang-kurangnya seorang ahli.',
            'ahli.*.nama_ahli.required' => 'Sila isi nama ahli.',
            'ahli.*.emel_ahli.required' => 'Sila isi emel ahli.',
            'ahli.*.emel_ahli.email' => 'Format emel tidak sah.',
        ];
    }

    public function mount(): void
    {
        $this->ahli = [['nama_ahli' => '', 'emel_ahli' => '']];
    }

    #[Computed]
    public function kumpulanEmels()
    {
        return KumpulanEmel::orderBy('nama_kumpulan')->get();
    }

    #[Computed]
    public function selectedKumpulan(): ?KumpulanEmel
    {
        if (! $this->kumpulanEmelId) {
            return null;
        }

        return $this->kumpulanEmels->firstWhere('id', $this->kumpulanEmelId);
    }

    #[Computed]
    public function jenisTindakanLabel(): string
    {
        if (! $this->jenisTindakan) {
            return '-';
        }

        return JenisTindakan::from($this->jenisTindakan)->label();
    }

    #[Computed]
    public function sectionLabel(): string
    {
        return match ($this->jenisTindakan) {
            'tambah' => 'Senarai Ahli Untuk Ditambah',
            'buang' => 'Senarai Ahli Untuk Dibuang',
            default => 'Senarai Ahli',
        };
    }

    public function updatedJenisTindakan(): void
    {
        $this->ahli = [['nama_ahli' => '', 'emel_ahli' => '']];
        $this->resetErrorBag('ahli');
    }

    public function tambahAhli(): void
    {
        $this->ahli[] = ['nama_ahli' => '', 'emel_ahli' => ''];
    }

    public function buangAhli(int $i): void
    {
        array_splice($this->ahli, $i, 1);
        $this->ahli = array_values($this->ahli);
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
            $noTiket = PermohonanEmel::generateNoTiket();

            $permohonan = PermohonanEmel::create([
                'no_tiket' => $noTiket,
                'user_id' => Auth::id(),
                'kumpulan_emel_id' => $this->kumpulanEmelId,
                'jenis_tindakan' => $this->jenisTindakan,
                'status' => StatusPermohonanEmel::Baru,
                'catatan_pemohon' => $this->catatanPemohon ?: null,
            ]);

            foreach ($this->ahli as $row) {
                AhliKumpulan::create([
                    'permohonan_id' => $permohonan->id,
                    'nama_ahli' => $row['nama_ahli'],
                    'emel_ahli' => $row['emel_ahli'],
                    'tindakan' => $this->jenisTindakan,
                ]);
            }

            PermohonanEmelDihantar::dispatch($permohonan);

            $this->noTiket = $noTiket;
        });

        Flux::modal('confirm-hantar')->close();

        $this->step = 3;
    }

    public function render()
    {
        return view('livewire.m06.hantar-permohonan');
    }
}
