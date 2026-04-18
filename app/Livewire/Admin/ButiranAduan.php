<?php

namespace App\Livewire\Admin;

use App\Enums\StatusAduan;
use App\Events\AduanSelesai;
use App\Events\StatusDikemaskini;
use App\Models\AduanIct;
use App\Models\StatusLog;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Butiran Aduan — Pentadbir')]
class ButiranAduan extends Component
{
    public int $aduanId;

    public string $statusBaru = '';

    public string $catatanTindakan = '';

    public function mount(int $id): void
    {
        $user = Auth::user();

        $query = AduanIct::where('id', $id);

        if ($user->isPentadbir()) {
            $query->whereHas(
                'kategori',
                fn ($k) => $k->where('unit_bpm', $user->bahagian)
            );
        }

        abort_unless($query->exists(), 404);

        $this->aduanId = $id;
    }

    #[Computed]
    public function aduan(): AduanIct
    {
        $user = Auth::user();

        return AduanIct::with([
            'kategori',
            'user',
            'lampiran',
            'statusLogs' => fn ($q) => $q->with('user')->orderByDesc('created_at'),
        ])
            ->when($user->isPentadbir(), fn ($q) => $q->whereHas(
                'kategori',
                fn ($k) => $k->where('unit_bpm', $user->bahagian)
            ))
            ->findOrFail($this->aduanId);
    }

    /** @return StatusAduan[] */
    public function availableStatuses(): array
    {
        return match ($this->aduan->status) {
            StatusAduan::Baru => [StatusAduan::DalamProses, StatusAduan::Selesai],
            StatusAduan::DalamProses => [StatusAduan::Selesai],
            default => [],
        };
    }

    public function kemaskiniStatus(): void
    {
        $availableValues = array_map(fn ($s) => $s->value, $this->availableStatuses());

        $this->validate([
            'statusBaru' => ['required', Rule::in($availableValues)],
            'catatanTindakan' => ['required', 'min:10'],
        ], [
            'statusBaru.required' => 'Sila pilih status.',
            'statusBaru.in' => 'Status yang dipilih tidak sah.',
            'catatanTindakan.required' => 'Sila isi catatan tindakan sebelum menyimpan.',
            'catatanTindakan.min' => 'Catatan tindakan mestilah sekurang-kurangnya 10 aksara.',
        ]);

        $aduan = AduanIct::with('kategori')->findOrFail($this->aduanId);
        $user = Auth::user();

        if ($user->isPentadbir()) {
            abort_unless($aduan->kategori->unit_bpm === $user->bahagian, 403);
        }

        $statusLama = $aduan->status;
        $statusBaru = StatusAduan::from($this->statusBaru);

        $aduan->update([
            'status' => $statusBaru,
            'pentadbir_id' => $user->id,
            'catatan_pentadbir' => $this->catatanTindakan,
            'tarikh_selesai' => $statusBaru === StatusAduan::Selesai ? now() : null,
        ]);

        StatusLog::create([
            'aduan_ict_id' => $aduan->id,
            'status_lama' => $statusLama->value,
            'status' => $statusBaru->value,
            'catatan' => $this->catatanTindakan,
            'user_id' => $user->id,
        ]);

        if ($statusBaru === StatusAduan::Selesai) {
            AduanSelesai::dispatch($aduan->fresh());
        } else {
            StatusDikemaskini::dispatch($aduan->fresh(), $statusBaru);
        }

        $this->reset('statusBaru', 'catatanTindakan');
        unset($this->aduan);

        Flux::modals()->close();
        Flux::toast(variant: 'success', text: 'Status aduan berjaya dikemaskini.');
    }

    public function bukaSemulaAduan(): void
    {
        $aduan = AduanIct::with('kategori')->findOrFail($this->aduanId);
        $user = Auth::user();

        abort_unless($aduan->status === StatusAduan::Selesai, 422);

        if ($user->isPentadbir()) {
            abort_unless($aduan->kategori->unit_bpm === $user->bahagian, 403);
        }

        $statusLama = $aduan->status;

        $aduan->update([
            'status' => StatusAduan::DalamProses,
            'pentadbir_id' => $user->id,
            'tarikh_selesai' => null,
        ]);

        StatusLog::create([
            'aduan_ict_id' => $aduan->id,
            'status_lama' => $statusLama->value,
            'status' => StatusAduan::DalamProses->value,
            'catatan' => 'Dibuka semula',
            'user_id' => $user->id,
        ]);

        StatusDikemaskini::dispatch($aduan->fresh(), StatusAduan::DalamProses);

        unset($this->aduan);

        Flux::modals()->close();
        Flux::toast(variant: 'success', text: 'Aduan berjaya dibuka semula.');
    }

    public function render()
    {
        return view('livewire.admin.butiran-aduan');
    }
}
