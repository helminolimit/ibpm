<?php

namespace App\Livewire;

use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Kemaskini Profil')]
class KemaskiniProfil extends Component
{
    public string $bahagian = '';

    public string $unit_bpm = '';

    public string $jawatan = '';

    public string $no_telefon = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->bahagian = $user->bahagian ?? '';
        $this->unit_bpm = $user->unit_bpm ?? '';
        $this->jawatan = $user->jawatan ?? '';
        $this->no_telefon = $user->no_telefon ?? '';
    }

    protected function rules(): array
    {
        return [
            'bahagian' => ['required', 'string', 'max:255'],
            'unit_bpm' => ['nullable', 'string', 'max:255'],
            'jawatan' => ['required', 'string', 'max:255'],
            'no_telefon' => ['required', 'string', 'max:20'],
        ];
    }

    public function simpan(): void
    {
        $validated = $this->validate();

        $user = Auth::user();
        $user->fill($validated);
        $user->save();

        Flux::toast(variant: 'success', text: 'Profil anda telah berjaya dikemaskini.');

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.kemaskini-profil')
            ->layout('layouts.auth');
    }
}
