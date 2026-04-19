<?php

use App\Enums\StatusPermohonanToner;
use App\Livewire\M02\ButiranPermohonan;
use App\Models\PermohonanToner;
use App\Models\User;
use App\Notifications\TonerDihantar;
use App\Notifications\TonerDiluluskan;
use App\Notifications\TonerDitolak;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// ──────────────────────────────────────────────
// semak
// ──────────────────────────────────────────────

it('admin can semak a submitted permohonan', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ButiranPermohonan::class, ['id' => $permohonan->id])
        ->call('semak');

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanToner::Disemak);

    $this->assertDatabaseHas('log_toner', [
        'permohonan_toner_id' => $permohonan->id,
        'tindakan' => 'Permohonan dalam semakan',
        'user_id' => $admin->id,
    ]);
});

// ──────────────────────────────────────────────
// luluskan
// ──────────────────────────────────────────────

it('admin can luluskan a submitted permohonan', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ButiranPermohonan::class, ['id' => $permohonan->id])
        ->call('luluskan');

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanToner::Diluluskan);

    Notification::assertSentTo($user, TonerDiluluskan::class);
});

it('admin can luluskan a disemak permohonan', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->disemak()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ButiranPermohonan::class, ['id' => $permohonan->id])
        ->call('luluskan');

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanToner::Diluluskan);

    Notification::assertSentTo($user, TonerDiluluskan::class);
});

it('luluskan creates a log entry', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ButiranPermohonan::class, ['id' => $permohonan->id])
        ->set('catatanLuluskan', 'Sedia untuk penghantaran.')
        ->call('luluskan');

    $this->assertDatabaseHas('log_toner', [
        'permohonan_toner_id' => $permohonan->id,
        'tindakan' => 'Permohonan diluluskan',
        'catatan' => 'Sedia untuk penghantaran.',
        'user_id' => $admin->id,
    ]);
});

// ──────────────────────────────────────────────
// tolak
// ──────────────────────────────────────────────

it('admin can tolak a permohonan with reason', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ButiranPermohonan::class, ['id' => $permohonan->id])
        ->set('sebabPenolakan', 'Model pencetak tidak disokong oleh stok semasa.')
        ->call('tolak');

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanToner::Ditolak);

    Notification::assertSentTo($user, TonerDitolak::class);
});

it('tolak requires sebabPenolakan', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ButiranPermohonan::class, ['id' => $permohonan->id])
        ->set('sebabPenolakan', '')
        ->call('tolak')
        ->assertHasErrors(['sebabPenolakan']);

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanToner::Submitted);
});

it('tolak creates a log entry with reason as catatan', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ButiranPermohonan::class, ['id' => $permohonan->id])
        ->set('sebabPenolakan', 'Maklumat pencetak tidak lengkap dan tidak sah.')
        ->call('tolak');

    $this->assertDatabaseHas('log_toner', [
        'permohonan_toner_id' => $permohonan->id,
        'tindakan' => 'Permohonan ditolak',
        'catatan' => 'Maklumat pencetak tidak lengkap dan tidak sah.',
        'user_id' => $admin->id,
    ]);
});

// ──────────────────────────────────────────────
// hantarToner
// ──────────────────────────────────────────────

it('admin can hantar toner for diluluskan permohonan', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->diluluskan()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ButiranPermohonan::class, ['id' => $permohonan->id])
        ->call('hantarToner');

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanToner::Dihantar);

    Notification::assertSentTo($user, TonerDihantar::class);
});

it('hantarToner creates a log entry', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->diluluskan()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ButiranPermohonan::class, ['id' => $permohonan->id])
        ->call('hantarToner');

    $this->assertDatabaseHas('log_toner', [
        'permohonan_toner_id' => $permohonan->id,
        'tindakan' => 'Toner dihantar kepada pemohon',
        'user_id' => $admin->id,
    ]);
});

// ──────────────────────────────────────────────
// Authorization
// ──────────────────────────────────────────────

it('pemohon cannot luluskan own permohonan', function () {
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(ButiranPermohonan::class, ['id' => $permohonan->id])
        ->call('luluskan')
        ->assertForbidden();
});

it('pemohon cannot tolak own permohonan', function () {
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(ButiranPermohonan::class, ['id' => $permohonan->id])
        ->set('sebabPenolakan', 'Cuba tolak sendiri permohonan ini.')
        ->call('tolak')
        ->assertForbidden();
});

it('pemohon cannot hantar toner', function () {
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->diluluskan()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(ButiranPermohonan::class, ['id' => $permohonan->id])
        ->call('hantarToner')
        ->assertForbidden();
});
