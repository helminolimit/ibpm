<?php

namespace App\Livewire\M02\Admin;

use App\Enums\StatusPermohonanToner;
use App\Models\LogToner;
use App\Models\PermohonanToner;
use App\Notifications\TonerDiluluskan;
use App\Notifications\TonerDitolak;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Proses Permohonan Toner')]
class ProsesPermohonan extends Component
{
    public int $permohonanId;

    public int $kuantitiDiluluskan = 1;

    public string $catatanLuluskan = '';

    public string $sebabPenolakan = '';

    public function mount(int $id): void
    {
        $user = Auth::user();
        abort_unless(in_array($user->role, ['admin', 'superadmin'], true), 403);

        $permohonan = PermohonanToner::findOrFail($id);

        $this->permohonanId = $id;
        $this->kuantitiDiluluskan = $permohonan->kuantiti;
    }

    #[Computed]
    public function permohonan(): PermohonanToner
    {
        return PermohonanToner::with(['user', 'logs.user'])
            ->findOrFail($this->permohonanId);
    }

    public function luluskan(): void
    {
        $this->validate(
            [
                'kuantitiDiluluskan' => ['required', 'integer', 'min:1', 'max:99'],
                'catatanLuluskan' => ['nullable', 'string', 'max:500'],
            ],
            [
                'kuantitiDiluluskan.required' => 'Sila isikan kuantiti yang diluluskan.',
                'kuantitiDiluluskan.min' => 'Kuantiti diluluskan mestilah sekurang-kurangnya 1.',
                'kuantitiDiluluskan.max' => 'Kuantiti diluluskan tidak boleh melebihi 99.',
            ]
        );

        $user = Auth::user();
        abort_unless(in_array($user->role, ['admin', 'superadmin'], true), 403);

        $permohonan = $this->permohonan;
        abort_unless(
            in_array($permohonan->status, [StatusPermohonanToner::Submitted, StatusPermohonanToner::Disemak], true),
            422
        );

        $kuantiti = $this->kuantitiDiluluskan;
        $catatan = $this->catatanLuluskan;

        DB::transaction(function () use ($permohonan, $user, $kuantiti, $catatan) {
            $permohonan->update([
                'status' => StatusPermohonanToner::Diluluskan,
                'kuantiti_diluluskan' => $kuantiti,
            ]);
            LogToner::create([
                'permohonan_toner_id' => $permohonan->id,
                'tindakan' => 'Permohonan diluluskan',
                'catatan' => $catatan ?: null,
                'user_id' => $user->id,
            ]);
            $permohonan->user->notify(new TonerDiluluskan($permohonan, $catatan));
        });

        Flux::toast(variant: 'success', text: 'Permohonan berjaya diluluskan. Notifikasi dihantar kepada pemohon.');

        $this->redirectRoute('m02.admin.senarai', navigate: true);
    }

    public function tandaPendingStock(): void
    {
        $this->validate(
            [
                'kuantitiDiluluskan' => ['required', 'integer', 'min:1', 'max:99'],
            ],
            [
                'kuantitiDiluluskan.required' => 'Sila isikan kuantiti stok yang tersedia.',
                'kuantitiDiluluskan.min' => 'Kuantiti mestilah sekurang-kurangnya 1.',
            ]
        );

        $user = Auth::user();
        abort_unless(in_array($user->role, ['admin', 'superadmin'], true), 403);

        $permohonan = $this->permohonan;
        abort_unless(
            in_array($permohonan->status, [StatusPermohonanToner::Submitted, StatusPermohonanToner::Disemak], true),
            422
        );

        $kuantiti = $this->kuantitiDiluluskan;

        DB::transaction(function () use ($permohonan, $user, $kuantiti) {
            $permohonan->update([
                'status' => StatusPermohonanToner::PendingStock,
                'kuantiti_diluluskan' => $kuantiti,
            ]);
            LogToner::create([
                'permohonan_toner_id' => $permohonan->id,
                'tindakan' => 'Diluluskan — menunggu stok',
                'catatan' => "Stok tersedia: {$kuantiti} unit. Menunggu stok tambahan.",
                'user_id' => $user->id,
            ]);
        });

        unset($this->permohonan);
        Flux::modals()->close();
        Flux::toast(variant: 'warning', text: 'Permohonan ditandakan Menunggu Stok.');
    }

    public function luluskanDariPendingStock(): void
    {
        $user = Auth::user();
        abort_unless(in_array($user->role, ['admin', 'superadmin'], true), 403);

        $permohonan = $this->permohonan;
        abort_unless($permohonan->status === StatusPermohonanToner::PendingStock, 422);

        $catatan = $this->catatanLuluskan;

        DB::transaction(function () use ($permohonan, $user, $catatan) {
            $permohonan->update(['status' => StatusPermohonanToner::Diluluskan]);
            LogToner::create([
                'permohonan_toner_id' => $permohonan->id,
                'tindakan' => 'Permohonan diluluskan (stok tiba)',
                'catatan' => $catatan ?: null,
                'user_id' => $user->id,
            ]);
            $permohonan->user->notify(new TonerDiluluskan($permohonan, $catatan));
        });

        Flux::toast(variant: 'success', text: 'Permohonan diluluskan. Notifikasi dihantar kepada pemohon.');

        $this->redirectRoute('m02.admin.senarai', navigate: true);
    }

    public function tolak(): void
    {
        $this->validate(
            [
                'sebabPenolakan' => ['required', 'string', 'min:10', 'max:500'],
            ],
            [
                'sebabPenolakan.required' => 'Sila nyatakan sebab penolakan.',
                'sebabPenolakan.min' => 'Sebab penolakan mestilah sekurang-kurangnya 10 aksara.',
                'sebabPenolakan.max' => 'Sebab penolakan tidak boleh melebihi 500 aksara.',
            ]
        );

        $user = Auth::user();
        abort_unless(in_array($user->role, ['admin', 'superadmin'], true), 403);

        $permohonan = $this->permohonan;
        abort_unless(
            in_array($permohonan->status, [StatusPermohonanToner::Submitted, StatusPermohonanToner::Disemak], true),
            422
        );

        $sebab = $this->sebabPenolakan;

        DB::transaction(function () use ($permohonan, $user, $sebab) {
            $permohonan->update(['status' => StatusPermohonanToner::Ditolak]);
            LogToner::create([
                'permohonan_toner_id' => $permohonan->id,
                'tindakan' => 'Permohonan ditolak',
                'catatan' => $sebab,
                'user_id' => $user->id,
            ]);
            $permohonan->user->notify(new TonerDitolak($permohonan, $sebab));
        });

        Flux::toast(variant: 'danger', text: 'Permohonan ditolak. Notifikasi dihantar kepada pemohon.');

        $this->redirectRoute('m02.admin.senarai', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.m02.admin.proses-permohonan');
    }
}
