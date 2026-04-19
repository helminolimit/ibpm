<?php

use App\Enums\StatusPermohonanToner;
use App\Livewire\M02\Admin\SenaraiAdmin;
use App\Models\PermohonanToner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('redirects guest to login when accessing admin senarai', function () {
    $this->get(route('m02.admin.senarai'))->assertRedirect(route('login'));
});

it('returns 403 for non-admin user', function () {
    $user = User::factory()->create(['role' => 'user']);

    Livewire::actingAs($user)
        ->test(SenaraiAdmin::class)
        ->assertForbidden();
});

it('renders admin senarai for admin user', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('m02.admin.senarai'))
        ->assertStatus(200)
        ->assertSeeLivewire(SenaraiAdmin::class);
});

it('admin sees all permohonan from all users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $p1 = PermohonanToner::factory()->create(['user_id' => $user1->id]);
    $p2 = PermohonanToner::factory()->create(['user_id' => $user2->id]);

    Livewire::actingAs($admin)
        ->test(SenaraiAdmin::class)
        ->assertSee($p1->no_tiket)
        ->assertSee($p2->no_tiket);
});

it('filters by no_tiket search', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $target = PermohonanToner::factory()->create(['no_tiket' => 'TON-2026-001']);
    $other = PermohonanToner::factory()->create(['no_tiket' => 'TON-2026-099']);

    Livewire::actingAs($admin)
        ->test(SenaraiAdmin::class)
        ->set('search', 'TON-2026-001')
        ->assertSee('TON-2026-001')
        ->assertDontSee('TON-2026-099');
});

it('filters by model_pencetak search', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    PermohonanToner::factory()->create([
        'no_tiket' => 'TON-2026-011',
        'model_pencetak' => 'HP LaserJet Pro M404n',
    ]);
    PermohonanToner::factory()->create([
        'no_tiket' => 'TON-2026-012',
        'model_pencetak' => 'Canon LBP6030',
    ]);

    Livewire::actingAs($admin)
        ->test(SenaraiAdmin::class)
        ->set('search', 'HP LaserJet')
        ->assertSee('TON-2026-011')
        ->assertDontSee('TON-2026-012');
});

it('filters by status', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $submitted = PermohonanToner::factory()->submitted()->create();
    $ditolak = PermohonanToner::factory()->ditolak()->create();

    Livewire::actingAs($admin)
        ->test(SenaraiAdmin::class)
        ->set('filterStatus', StatusPermohonanToner::Submitted->value)
        ->assertSee($submitted->no_tiket)
        ->assertDontSee($ditolak->no_tiket);
});

it('shows Proses button for submitted records', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    PermohonanToner::factory()->submitted()->create(['no_tiket' => 'TON-2026-101']);

    Livewire::actingAs($admin)
        ->test(SenaraiAdmin::class)
        ->assertSee('Proses');
});

it('shows Lihat button for non-submitted records', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    PermohonanToner::factory()->diluluskan()->create(['no_tiket' => 'TON-2026-201']);

    Livewire::actingAs($admin)
        ->test(SenaraiAdmin::class)
        ->assertSee('Lihat');
});

it('shows empty state when no permohonan found', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(SenaraiAdmin::class)
        ->assertSee('Tiada permohonan dijumpai.');
});
