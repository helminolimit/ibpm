<?php

namespace App\Livewire\M04;

use App\Models\PermohonanPortal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Title('Butiran Permohonan')]
class ButiranPermohonan extends Component
{
    public PermohonanPortal $permohonan;

    public function mount(int $permohonanId): void
    {
        $this->permohonan = PermohonanPortal::with([
            'pemohon',
            'pentadbir',
            'lampirans',
            'logAudits.pengguna',
        ])
            ->where('pemohon_id', Auth::id())
            ->findOrFail($permohonanId);
    }

    public function muatTurunLampiran(int $lampiranId): StreamedResponse
    {
        $lampiran = $this->permohonan->lampirans()->findOrFail($lampiranId);

        return Storage::disk('local')->download($lampiran->path_fail, $lampiran->nama_fail);
    }

    public function render()
    {
        return view('livewire.m04.butiran-permohonan');
    }
}
