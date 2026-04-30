<?php

namespace App\Livewire\M06;

use App\Models\PermohonanEmel;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Butiran Permohonan Kumpulan Emel')]
class ButiranPermohonan extends Component
{
    public int $permohonanId;

    public function mount(int $id): void
    {
        abort_unless(
            PermohonanEmel::where('id', $id)->where('user_id', Auth::id())->exists(),
            404
        );

        $this->permohonanId = $id;
    }

    #[Computed]
    public function permohonan(): PermohonanEmel
    {
        return PermohonanEmel::with(['kumpulanEmel', 'ahliKumpulan', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($this->permohonanId);
    }

    public function render()
    {
        return view('livewire.m06.butiran-permohonan');
    }
}
