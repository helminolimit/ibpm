<?php

namespace App\Livewire\Admin;

use App\Models\AduanIct;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Butiran Aduan — Pentadbir')]
class ButiranAduan extends Component
{
    public int $aduanId;

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

    public function render()
    {
        return view('livewire.admin.butiran-aduan');
    }
}
