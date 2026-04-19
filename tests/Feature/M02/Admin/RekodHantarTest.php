<?php

use App\Enums\StatusPermohonanToner;
use App\Livewire\M02\Admin\RekodHantar;
use App\Models\PermohonanToner;
use App\Models\StokToner;
use App\Models\User;
use App\Notifications\TonerDihantar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('redirects guest to login when accessing rekod hantar', function () {
    $permohonan = PermohonanToner::factory()->diluluskan()->create();

    $this->get(route('m02.admin.hantar', $permohonan->id))->assertRedirect(route('login'));
});

it('returns 403 for non-admin user on mount', function () {
    $user = User::factory()->create(['role' => 'user']);
    $permohonan = PermohonanToner::factory()->diluluskan()->create();

    Livewire::actingAs($user)
        ->test(RekodHantar::class, ['id' => $permohonan->id])
        ->assertForbidden();
});

it('returns 403 when permohonan status is not Diluluskan', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $permohonan = PermohonanToner::factory()->submitted()->create();

    Livewire::actingAs($admin)
        ->test(RekodHantar::class, ['id' => $permohonan->id])
        ->assertForbidden();
});

it('renders rekod hantar page for admin with diluluskan permohonan', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $permohonan = PermohonanToner::factory()->diluluskan()->create(['kuantiti_diluluskan' => 3]);

    $this->actingAs($admin)
        ->get(route('m02.admin.hantar', $permohonan->id))
        ->assertStatus(200)
        ->assertSeeLivewire(RekodHantar::class);
});

it('pre-fills kuantitiDihantar with kuantiti_diluluskan on mount', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $permohonan = PermohonanToner::factory()->diluluskan()->create(['kuantiti_diluluskan' => 4]);

    Livewire::actingAs($admin)
        ->test(RekodHantar::class, ['id' => $permohonan->id])
        ->assertSet('kuantitiDihantar', 4);
});

// ──────────────────────────────────────────────
// simpan
// ──────────────────────────────────────────────

it('simpan creates a penghantaran_toner record', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->diluluskan()->create([
        'user_id' => $user->id,
        'kuantiti_diluluskan' => 2,
    ]);

    Livewire::actingAs($admin)
        ->test(RekodHantar::class, ['id' => $permohonan->id])
        ->set('kuantitiDihantar', 2)
        ->call('simpan');

    $this->assertDatabaseHas('penghantaran_toner', [
        'permohonan_toner_id' => $permohonan->id,
        'dihantar_oleh' => $admin->id,
        'kuantiti_dihantar' => 2,
    ]);
});

it('simpan updates permohonan status to Dihantar', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->diluluskan()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(RekodHantar::class, ['id' => $permohonan->id])
        ->call('simpan');

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanToner::Dihantar);
});

it('simpan creates a log entry', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->diluluskan()->create([
        'user_id' => $user->id,
        'kuantiti_diluluskan' => 3,
    ]);

    Livewire::actingAs($admin)
        ->test(RekodHantar::class, ['id' => $permohonan->id])
        ->set('kuantitiDihantar', 3)
        ->call('simpan');

    $this->assertDatabaseHas('log_toner', [
        'permohonan_toner_id' => $permohonan->id,
        'tindakan' => 'Toner dihantar',
        'catatan' => 'Toner dihantar: 3 unit.',
        'user_id' => $admin->id,
    ]);
});

it('simpan sends TonerDihantar notification to pemohon', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->diluluskan()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(RekodHantar::class, ['id' => $permohonan->id])
        ->call('simpan');

    Notification::assertSentTo($user, TonerDihantar::class);
});

it('simpan decrements stok_toner kuantiti_ada', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->diluluskan()->create([
        'user_id' => $user->id,
        'jenis_toner' => 'hitam',
        'kuantiti_diluluskan' => 2,
    ]);

    StokToner::create(['jenis_toner' => 'hitam', 'kuantiti_ada' => 10]);

    Livewire::actingAs($admin)
        ->test(RekodHantar::class, ['id' => $permohonan->id])
        ->set('kuantitiDihantar', 2)
        ->call('simpan');

    $this->assertDatabaseHas('stok_toner', [
        'jenis_toner' => 'hitam',
        'kuantiti_ada' => 8,
    ]);
});

it('simpan validates kuantitiDihantar min 1', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $permohonan = PermohonanToner::factory()->diluluskan()->create(['kuantiti_diluluskan' => 2]);

    Livewire::actingAs($admin)
        ->test(RekodHantar::class, ['id' => $permohonan->id])
        ->set('kuantitiDihantar', 0)
        ->call('simpan')
        ->assertHasErrors(['kuantitiDihantar']);

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanToner::Diluluskan);
});

it('simpan validates catatan max 300 characters', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $permohonan = PermohonanToner::factory()->diluluskan()->create(['kuantiti_diluluskan' => 1]);

    Livewire::actingAs($admin)
        ->test(RekodHantar::class, ['id' => $permohonan->id])
        ->set('catatan', str_repeat('x', 301))
        ->call('simpan')
        ->assertHasErrors(['catatan']);
});

it('simpan stores optional catatan on the penghantaran record', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->diluluskan()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(RekodHantar::class, ['id' => $permohonan->id])
        ->set('catatan', 'Diserahkan terus kepada pemohon.')
        ->call('simpan');

    $this->assertDatabaseHas('penghantaran_toner', [
        'permohonan_toner_id' => $permohonan->id,
        'catatan' => 'Diserahkan terus kepada pemohon.',
    ]);
});
