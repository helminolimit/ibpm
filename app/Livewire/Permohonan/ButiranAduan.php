<?php

namespace App\Livewire\Permohonan;

use App\Models\AduanIct;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Butiran Aduan')]
class ButiranAduan extends Component
{
    public int $aduanId;

    public function mount(int $id): void
    {
        abort_unless(
            AduanIct::where('id', $id)->where('user_id', Auth::id())->exists(),
            404
        );

        $this->aduanId = $id;
    }

    #[Computed]
    public function aduan(): AduanIct
    {
        return AduanIct::with([
            'kategori',
            'lampiran',
            'statusLogs' => fn ($q) => $q->with('user')->orderByDesc('created_at'),
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($this->aduanId);
    }

    public function render()
    {
        return view('livewire.permohonan.butiran-aduan');
    }
}
