<?php

use App\Enums\StatusPermohonanToner;
use App\Livewire\M02\ButiranPermohonan;
use App\Livewire\M02\SenaraiPermohonan;
use App\Models\LogToner;
use App\Models\PermohonanToner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// ──────────────────────────────────────────────
// Senarai Permohonan
// ──────────────────────────────────────────────

it('redirects guest to login when accessing senarai', function () {
    $this->get(route('m02.senarai'))->assertRedirect(route('login'));
});

it('renders senarai permohonan for authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('m02.senarai'))
        ->assertStatus(200)
        ->assertSeeLivewire(SenaraiPermohonan::class);
});

it('shows only own permohonan to pemohon', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $own = PermohonanToner::factory()->create(['user_id' => $user->id]);
    PermohonanToner::factory()->create(['user_id' => $other->id]);

    Livewire::actingAs($user)
        ->test(SenaraiPermohonan::class)
        ->assertSee($own->no_tiket)
        ->assertDontSee(PermohonanToner::where('user_id', $other->id)->value('no_tiket'));
});

it('shows all permohonan to admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    $p1 = PermohonanToner::factory()->create(['user_id' => $admin->id]);
    $p2 = PermohonanToner::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(SenaraiPermohonan::class)
        ->assertSee($p1->no_tiket)
        ->assertSee($p2->no_tiket);
});

it('filters by no_tiket search', function () {
    $user = User::factory()->create();

    $target = PermohonanToner::factory()->create([
        'user_id' => $user->id,
        'no_tiket' => 'TON-2026-001',
    ]);
    $other = PermohonanToner::factory()->create([
        'user_id' => $user->id,
        'no_tiket' => 'TON-2026-099',
    ]);

    Livewire::actingAs($user)
        ->test(SenaraiPermohonan::class)
        ->set('search', 'TON-2026-001')
        ->assertSee('TON-2026-001')
        ->assertDontSee('TON-2026-099');
});

it('filters by model_pencetak search', function () {
    $user = User::factory()->create();

    PermohonanToner::factory()->create([
        'user_id' => $user->id,
        'no_tiket' => 'TON-2026-011',
        'model_pencetak' => 'HP LaserJet Pro M404n',
    ]);
    PermohonanToner::factory()->create([
        'user_id' => $user->id,
        'no_tiket' => 'TON-2026-012',
        'model_pencetak' => 'Canon LBP6030',
    ]);

    Livewire::actingAs($user)
        ->test(SenaraiPermohonan::class)
        ->set('search', 'HP LaserJet')
        ->assertSee('TON-2026-011')
        ->assertDontSee('TON-2026-012');
});

it('filters by status', function () {
    $user = User::factory()->create();

    $submitted = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);
    $ditolak = PermohonanToner::factory()->ditolak()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(SenaraiPermohonan::class)
        ->set('filterStatus', StatusPermohonanToner::Submitted->value)
        ->assertSee($submitted->no_tiket)
        ->assertDontSee($ditolak->no_tiket);
});

it('shows empty state when no permohonan found', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(SenaraiPermohonan::class)
        ->assertSee('Tiada permohonan dijumpai.');
});

it('shows empty state when search yields no results', function () {
    $user = User::factory()->create();

    PermohonanToner::factory()->create([
        'user_id' => $user->id,
        'model_pencetak' => 'HP LaserJet Pro M404n',
    ]);

    Livewire::actingAs($user)
        ->test(SenaraiPermohonan::class)
        ->set('search', 'ZZZ-nothing-matches')
        ->assertSee('Tiada permohonan dijumpai.');
});

// ──────────────────────────────────────────────
// Butiran Permohonan
// ──────────────────────────────────────────────

it('redirects guest to login when accessing butiran', function () {
    $permohonan = PermohonanToner::factory()->create();

    $this->get(route('m02.butiran', $permohonan->id))->assertRedirect(route('login'));
});

it('renders butiran permohonan for the owner', function () {
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('m02.butiran', $permohonan->id))
        ->assertStatus(200)
        ->assertSeeLivewire(ButiranPermohonan::class);
});

it('returns 403 when pemohon tries to view another user butiran', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $permohonan = PermohonanToner::factory()->create(['user_id' => $other->id]);

    Livewire::actingAs($user)
        ->test(ButiranPermohonan::class, ['id' => $permohonan->id])
        ->assertForbidden();
});

it('allows admin to view any butiran', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ButiranPermohonan::class, ['id' => $permohonan->id])
        ->assertStatus(200)
        ->assertSee($permohonan->no_tiket);
});

it('displays permohonan details on butiran page', function () {
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->create([
        'user_id' => $user->id,
        'model_pencetak' => 'HP LaserJet Pro M404n',
        'jenama_toner' => 'HP',
        'kuantiti' => 3,
    ]);

    Livewire::actingAs($user)
        ->test(ButiranPermohonan::class, ['id' => $permohonan->id])
        ->assertSee($permohonan->no_tiket)
        ->assertSee('HP LaserJet Pro M404n')
        ->assertSee('3 unit');
});

it('displays log history on butiran page', function () {
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->create(['user_id' => $user->id]);

    LogToner::create([
        'permohonan_toner_id' => $permohonan->id,
        'tindakan' => 'Permohonan dihantar',
        'user_id' => $user->id,
    ]);

    Livewire::actingAs($user)
        ->test(ButiranPermohonan::class, ['id' => $permohonan->id])
        ->assertSee('Permohonan dihantar');
});

it('shows empty log state when no logs exist', function () {
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(ButiranPermohonan::class, ['id' => $permohonan->id])
        ->assertSee('Tiada sejarah log.');
});
