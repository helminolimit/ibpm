<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

use App\Enums\StatusPermohonanEmel;
use App\Livewire\M06\SenaraiPermohonan;
use App\Models\KumpulanEmel;
use App\Models\PermohonanEmel;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->kumpulan = KumpulanEmel::factory()->create();
});

it('renders for authenticated user', function () {
    actingAs($this->user)
        ->get(route('kumpulan-emel.index'))
        ->assertOk();
});

it('redirects unauthenticated users', function () {
    $this->get(route('kumpulan-emel.index'))
        ->assertRedirect(route('login'));
});

it('shows only the current user permohonan', function () {
    $other = User::factory()->create();

    PermohonanEmel::factory()->create(['user_id' => $this->user->id, 'kumpulan_emel_id' => $this->kumpulan->id, 'no_tiket' => 'GRP-2026-001']);
    PermohonanEmel::factory()->create(['user_id' => $other->id, 'kumpulan_emel_id' => $this->kumpulan->id, 'no_tiket' => 'GRP-2026-002']);

    actingAs($this->user);

    Livewire::test(SenaraiPermohonan::class)
        ->assertSee('GRP-2026-001')
        ->assertDontSee('GRP-2026-002');
});

it('filters by status', function () {
    PermohonanEmel::factory()->create([
        'user_id' => $this->user->id,
        'kumpulan_emel_id' => $this->kumpulan->id,
        'no_tiket' => 'GRP-2026-001',
        'status' => StatusPermohonanEmel::Baru,
    ]);
    PermohonanEmel::factory()->create([
        'user_id' => $this->user->id,
        'kumpulan_emel_id' => $this->kumpulan->id,
        'no_tiket' => 'GRP-2026-002',
        'status' => StatusPermohonanEmel::Selesai,
    ]);

    actingAs($this->user);

    Livewire::test(SenaraiPermohonan::class)
        ->set('filterStatus', 'baru')
        ->assertSee('GRP-2026-001')
        ->assertDontSee('GRP-2026-002');
});

it('searches by no tiket', function () {
    PermohonanEmel::factory()->create([
        'user_id' => $this->user->id,
        'kumpulan_emel_id' => $this->kumpulan->id,
        'no_tiket' => 'GRP-2026-001',
    ]);
    PermohonanEmel::factory()->create([
        'user_id' => $this->user->id,
        'kumpulan_emel_id' => $this->kumpulan->id,
        'no_tiket' => 'GRP-2026-099',
    ]);

    actingAs($this->user);

    Livewire::test(SenaraiPermohonan::class)
        ->set('search', 'GRP-2026-001')
        ->assertSee('GRP-2026-001')
        ->assertDontSee('GRP-2026-099');
});

it('shows empty state when no permohonan', function () {
    actingAs($this->user);

    Livewire::test(SenaraiPermohonan::class)
        ->assertSee('Tiada permohonan ditemui');
});
