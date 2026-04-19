<?php

namespace App\Livewire\M02\Admin;

use App\Enums\StatusPermohonanToner;
use App\Models\LogToner;
use App\Models\PenghantaranToner;
use App\Models\PermohonanToner;
use App\Models\StokToner;
use App\Notifications\TonerDihantar;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Rekod Penghantaran Toner')]
class RekodHantar extends Component
{
    public int $permohonanId;

    public int $kuantitiDihantar = 1;

    public string $catatan = '';

    public function mount(int $id): void
    {
        $user = Auth::user();
        abort_unless(in_array($user->role, ['admin', 'superadmin'], true), 403);

        $permohonan = PermohonanToner::findOrFail($id);
        abort_unless(
            $permohonan->status === StatusPermohonanToner::Diluluskan,
            403,
            'Permohonan ini tidak layak untuk direkodkan penghantaran.'
        );

        $this->permohonanId = $id;
        $this->kuantitiDihantar = $permohonan->kuantiti_diluluskan ?? $permohonan->kuantiti;
    }

    #[Computed]
    public function permohonan(): PermohonanToner
    {
        return PermohonanToner::with(['user', 'logs.user'])
            ->findOrFail($this->permohonanId);
    }

    public function simpan(): void
    {
        $this->validate(
            [
                'kuantitiDihantar' => ['required', 'integer', 'min:1'],
                'catatan' => ['nullable', 'string', 'max:300'],
            ],
            [
                'kuantitiDihantar.required' => 'Sila isikan kuantiti yang dihantar.',
                'kuantitiDihantar.min' => 'Kuantiti dihantar mestilah sekurang-kurangnya 1.',
                'catatan.max' => 'Catatan tidak boleh melebihi 300 aksara.',
            ]
        );

        $user = Auth::user();
        abort_unless(in_array($user->role, ['admin', 'superadmin'], true), 403);

        $permohonan = $this->permohonan;
        abort_unless($permohonan->status === StatusPermohonanToner::Diluluskan, 403);

        $kuantiti = $this->kuantitiDihantar;
        $catatan = $this->catatan;

        DB::transaction(function () use ($permohonan, $user, $kuantiti, $catatan) {
            PenghantaranToner::create([
                'permohonan_toner_id' => $permohonan->id,
                'dihantar_oleh' => $user->id,
                'kuantiti_dihantar' => $kuantiti,
                'catatan' => $catatan ?: null,
                'tarikh_hantar' => now(),
            ]);

            StokToner::where('jenis_toner', $permohonan->jenis_toner->value)
                ->first()
                ?->kurangkanStok($kuantiti);

            $permohonan->update(['status' => StatusPermohonanToner::Dihantar]);

            LogToner::create([
                'permohonan_toner_id' => $permohonan->id,
                'tindakan' => 'Toner dihantar',
                'catatan' => "Toner dihantar: {$kuantiti} unit.",
                'user_id' => $user->id,
            ]);

            $permohonan->user->notify(new TonerDihantar($permohonan, $kuantiti));
        });

        Flux::toast(variant: 'success', text: 'Rekod penghantaran berjaya disimpan. Notifikasi dihantar kepada pemohon.');

        $this->redirectRoute('m02.admin.senarai', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.m02.admin.rekod-hantar');
    }
}
