<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

use App\Livewire\M06\ButiranPermohonan;
use App\Models\AhliKumpulan;
use App\Models\KumpulanEmel;
use App\Models\PermohonanEmel;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->kumpulan = KumpulanEmel::factory()->create();
    $this->permohonan = PermohonanEmel::factory()->create([
        'user_id' => $this->user->id,
        'kumpulan_emel_id' => $this->kumpulan->id,
        'no_tiket' => 'GRP-2026-001',
        'jenis_tindakan' => 'tambah',
    ]);
});

it('renders butiran for own permohonan', function () {
    actingAs($this->user)
        ->get(route('kumpulan-emel.show', $this->permohonan->id))
        ->assertOk();
});

it('shows 404 for another user permohonan', function () {
    $other = User::factory()->create();
    $otherPermohonan = PermohonanEmel::factory()->create([
        'user_id' => $other->id,
        'kumpulan_emel_id' => $this->kumpulan->id,
    ]);

    actingAs($this->user)
        ->get(route('kumpulan-emel.show', $otherPermohonan->id))
        ->assertNotFound();
});

it('shows 404 via livewire mount for another user permohonan', function () {
    $other = User::factory()->create();
    $otherPermohonan = PermohonanEmel::factory()->create([
        'user_id' => $other->id,
        'kumpulan_emel_id' => $this->kumpulan->id,
    ]);

    actingAs($this->user);

    Livewire::test(ButiranPermohonan::class, ['id' => $otherPermohonan->id])
        ->assertStatus(404);
});

it('displays permohonan no tiket and status', function () {
    actingAs($this->user);

    Livewire::test(ButiranPermohonan::class, ['id' => $this->permohonan->id])
        ->assertSee('GRP-2026-001')
        ->assertSee('Baru');
});

it('displays ahli kumpulan', function () {
    AhliKumpulan::factory()->create([
        'permohonan_id' => $this->permohonan->id,
        'nama_ahli' => 'Ali Ahmad',
        'emel_ahli' => 'ali@example.com',
        'tindakan' => 'tambah',
    ]);

    actingAs($this->user);

    Livewire::test(ButiranPermohonan::class, ['id' => $this->permohonan->id])
        ->assertSee('Ali Ahmad')
        ->assertSee('ali@example.com');
});

it('hides catatan pentadbir section when null', function () {
    actingAs($this->user);

    Livewire::test(ButiranPermohonan::class, ['id' => $this->permohonan->id])
        ->assertDontSee('Catatan Pentadbir');
});

it('shows catatan pentadbir when present', function () {
    $this->permohonan->update(['catatan_pentadbir' => 'Permohonan tidak lengkap.']);

    actingAs($this->user);

    Livewire::test(ButiranPermohonan::class, ['id' => $this->permohonan->id])
        ->assertSee('Catatan Pentadbir')
        ->assertSee('Permohonan tidak lengkap.');
});
