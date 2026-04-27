<?php

use App\Livewire\KemaskiniProfil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders kemaskini-profil component', function () {
    $user = User::factory()->create([
        'bahagian' => null,
        'jawatan' => null,
        'no_telefon' => null,
    ]);

    $this->actingAs($user)
        ->get(route('profile.complete'))
        ->assertOk()
        ->assertSeeLivewire(KemaskiniProfil::class);
});

it('pre-fills existing profile values on mount', function () {
    $user = User::factory()->create([
        'bahagian' => 'Bahagian A',
        'jawatan' => 'Pegawai Tadbir',
        'no_telefon' => '0312345678',
        'unit_bpm' => 'Unit BPM',
    ]);

    Livewire::actingAs($user)
        ->test(KemaskiniProfil::class)
        ->assertSet('bahagian', 'Bahagian A')
        ->assertSet('jawatan', 'Pegawai Tadbir')
        ->assertSet('no_telefon', '0312345678')
        ->assertSet('unit_bpm', 'Unit BPM');
});

it('saves profile and redirects to dashboard', function () {
    $user = User::factory()->create([
        'bahagian' => null,
        'jawatan' => null,
        'no_telefon' => null,
    ]);

    Livewire::actingAs($user)
        ->test(KemaskiniProfil::class)
        ->set('bahagian', 'Bahagian Pengurusan')
        ->set('jawatan', 'Pegawai Tadbir')
        ->set('no_telefon', '0312345678')
        ->call('simpan')
        ->assertRedirect(route('dashboard'));

    expect($user->fresh())
        ->bahagian->toBe('Bahagian Pengurusan')
        ->jawatan->toBe('Pegawai Tadbir')
        ->no_telefon->toBe('0312345678');
});

it('validates required fields', function () {
    $user = User::factory()->create([
        'bahagian' => null,
        'jawatan' => null,
        'no_telefon' => null,
    ]);

    Livewire::actingAs($user)
        ->test(KemaskiniProfil::class)
        ->set('bahagian', '')
        ->set('jawatan', '')
        ->set('no_telefon', '')
        ->call('simpan')
        ->assertHasErrors(['bahagian', 'jawatan', 'no_telefon']);
});

it('allows unit_bpm to be empty', function () {
    $user = User::factory()->create([
        'bahagian' => null,
        'jawatan' => null,
        'no_telefon' => null,
    ]);

    Livewire::actingAs($user)
        ->test(KemaskiniProfil::class)
        ->set('bahagian', 'Bahagian A')
        ->set('jawatan', 'Pegawai')
        ->set('no_telefon', '0312345678')
        ->set('unit_bpm', '')
        ->call('simpan')
        ->assertHasNoErrors();
});
