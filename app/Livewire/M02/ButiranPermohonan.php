<?php

namespace App\Livewire\M02;

use App\Enums\StatusPermohonanToner;
use App\Models\LogToner;
use App\Models\PermohonanToner;
use App\Notifications\TonerDihantar;
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
#[Title('Butiran Permohonan')]
class ButiranPermohonan extends Component
{
    public int $permohonanId;

    public string $sebabPenolakan = '';

    public string $catatanLuluskan = '';

    public function mount(int $id): void
    {
        $permohonan = PermohonanToner::findOrFail($id);
        $user = Auth::user();
        $isAdmin = in_array($user->role, ['admin', 'superadmin'], true);

        if (! $isAdmin) {
            abort_unless($permohonan->user_id === $user->id, 403);
        }

        $this->permohonanId = $id;
    }

    #[Computed]
    public function permohonan(): PermohonanToner
    {
        return PermohonanToner::with(['user', 'logs.user'])
            ->findOrFail($this->permohonanId);
    }

    public function semak(): void
    {
        $user = Auth::user();
        abort_unless(in_array($user->role, ['admin', 'superadmin'], true), 403);

        $permohonan = $this->permohonan;
        abort_unless($permohonan->status === StatusPermohonanToner::Submitted, 422);

        DB::transaction(function () use ($permohonan, $user) {
            $permohonan->update(['status' => StatusPermohonanToner::Disemak]);
            LogToner::create([
                'permohonan_toner_id' => $permohonan->id,
                'tindakan' => 'Permohonan dalam semakan',
                'user_id' => $user->id,
            ]);
        });

        unset($this->permohonan);
        Flux::modals()->close();
        Flux::toast(variant: 'success', text: 'Status dikemaskini kepada Dalam Semakan.');
    }

    public function luluskan(): void
    {
        $user = Auth::user();
        abort_unless(in_array($user->role, ['admin', 'superadmin'], true), 403);

        $permohonan = $this->permohonan;
        abort_unless(
            in_array($permohonan->status, [StatusPermohonanToner::Submitted, StatusPermohonanToner::Disemak], true),
            422
        );

        $catatan = $this->catatanLuluskan;

        DB::transaction(function () use ($permohonan, $user, $catatan) {
            $permohonan->update(['status' => StatusPermohonanToner::Diluluskan]);
            LogToner::create([
                'permohonan_toner_id' => $permohonan->id,
                'tindakan' => 'Permohonan diluluskan',
                'catatan' => $catatan ?: null,
                'user_id' => $user->id,
            ]);
            $permohonan->user->notify(new TonerDiluluskan($permohonan, $catatan));
        });

        $this->catatanLuluskan = '';
        unset($this->permohonan);
        Flux::modals()->close();
        Flux::toast(variant: 'success', text: 'Permohonan berjaya diluluskan. Notifikasi dihantar kepada pemohon.');
    }

    public function tolak(): void
    {
        $this->validate(
            ['sebabPenolakan' => ['required', 'string', 'min:10']],
            [
                'sebabPenolakan.required' => 'Sila nyatakan sebab penolakan.',
                'sebabPenolakan.min' => 'Sebab penolakan mestilah sekurang-kurangnya 10 aksara.',
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

        $this->sebabPenolakan = '';
        unset($this->permohonan);
        Flux::modals()->close();
        Flux::toast(variant: 'success', text: 'Permohonan ditolak. Notifikasi dihantar kepada pemohon.');
    }

    public function hantarToner(): void
    {
        $user = Auth::user();
        abort_unless(in_array($user->role, ['admin', 'superadmin'], true), 403);

        $permohonan = $this->permohonan;
        abort_unless($permohonan->status === StatusPermohonanToner::Diluluskan, 422);

        DB::transaction(function () use ($permohonan, $user) {
            $permohonan->update(['status' => StatusPermohonanToner::Dihantar]);
            LogToner::create([
                'permohonan_toner_id' => $permohonan->id,
                'tindakan' => 'Toner dihantar kepada pemohon',
                'user_id' => $user->id,
            ]);
            $permohonan->user->notify(new TonerDihantar($permohonan));
        });

        unset($this->permohonan);
        Flux::modals()->close();
        Flux::toast(variant: 'success', text: 'Toner berjaya dihantar. Notifikasi dihantar kepada pemohon.');
    }

    public function render(): View
    {
        return view('livewire.m02.butiran-permohonan', [
            'isAdmin' => in_array(Auth::user()->role, ['admin', 'superadmin'], true),
        ]);
    }
}
