<?php

use App\Enums\StatusPermohonanToner;
use App\Livewire\M02\Admin\ProsesPermohonan;
use App\Models\PermohonanToner;
use App\Models\User;
use App\Notifications\TonerDiluluskan;
use App\Notifications\TonerDitolak;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('redirects guest to login when accessing admin proses', function () {
    $permohonan = PermohonanToner::factory()->create();

    $this->get(route('m02.admin.proses', $permohonan->id))->assertRedirect(route('login'));
});

it('returns 403 for non-admin user on mount', function () {
    $user = User::factory()->create(['role' => 'user']);
    $permohonan = PermohonanToner::factory()->submitted()->create();

    Livewire::actingAs($user)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->assertForbidden();
});

it('renders proses page for admin with submitted permohonan', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $permohonan = PermohonanToner::factory()->submitted()->create();

    $this->actingAs($admin)
        ->get(route('m02.admin.proses', $permohonan->id))
        ->assertStatus(200)
        ->assertSeeLivewire(ProsesPermohonan::class);
});

it('pre-fills kuantitiDiluluskan with the permohonan kuantiti on mount', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $permohonan = PermohonanToner::factory()->submitted()->create(['kuantiti' => 3]);

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->assertSet('kuantitiDiluluskan', 3);
});

// ──────────────────────────────────────────────
// luluskan
// ──────────────────────────────────────────────

it('luluskan updates status to Diluluskan and sets kuantiti_diluluskan', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id, 'kuantiti' => 5]);

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->set('kuantitiDiluluskan', 3)
        ->call('luluskan');

    $fresh = $permohonan->fresh();
    expect($fresh->status)->toBe(StatusPermohonanToner::Diluluskan);
    expect($fresh->kuantiti_diluluskan)->toBe(3);
});

it('luluskan creates a log entry', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->set('catatanLuluskan', 'Stok mencukupi.')
        ->call('luluskan');

    $this->assertDatabaseHas('log_toner', [
        'permohonan_toner_id' => $permohonan->id,
        'tindakan' => 'Permohonan diluluskan',
        'catatan' => 'Stok mencukupi.',
        'user_id' => $admin->id,
    ]);
});

it('luluskan sends TonerDiluluskan notification to pemohon', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->call('luluskan');

    Notification::assertSentTo($user, TonerDiluluskan::class);
});

it('luluskan validates kuantitiDiluluskan min 1', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $permohonan = PermohonanToner::factory()->submitted()->create();

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->set('kuantitiDiluluskan', 0)
        ->call('luluskan')
        ->assertHasErrors(['kuantitiDiluluskan']);

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanToner::Submitted);
});

it('luluskan validates kuantitiDiluluskan max 99', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $permohonan = PermohonanToner::factory()->submitted()->create();

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->set('kuantitiDiluluskan', 100)
        ->call('luluskan')
        ->assertHasErrors(['kuantitiDiluluskan']);
});

it('non-admin cannot call luluskan', function () {
    $user = User::factory()->create(['role' => 'user']);
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->assertForbidden();
});

// ──────────────────────────────────────────────
// tandaPendingStock
// ──────────────────────────────────────────────

it('tandaPendingStock updates status to PendingStock', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id, 'kuantiti' => 5]);

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->set('kuantitiDiluluskan', 2)
        ->call('tandaPendingStock');

    $fresh = $permohonan->fresh();
    expect($fresh->status)->toBe(StatusPermohonanToner::PendingStock);
    expect($fresh->kuantiti_diluluskan)->toBe(2);
});

it('tandaPendingStock creates a log entry', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->call('tandaPendingStock');

    $this->assertDatabaseHas('log_toner', [
        'permohonan_toner_id' => $permohonan->id,
        'tindakan' => 'Diluluskan — menunggu stok',
        'user_id' => $admin->id,
    ]);
});

it('tandaPendingStock does not send a notification', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->call('tandaPendingStock');

    Notification::assertNothingSent();
});

// ──────────────────────────────────────────────
// luluskanDariPendingStock
// ──────────────────────────────────────────────

it('luluskanDariPendingStock updates status to Diluluskan', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->pendingStock()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->call('luluskanDariPendingStock');

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanToner::Diluluskan);
});

it('luluskanDariPendingStock creates a log entry', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->pendingStock()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->call('luluskanDariPendingStock');

    $this->assertDatabaseHas('log_toner', [
        'permohonan_toner_id' => $permohonan->id,
        'tindakan' => 'Permohonan diluluskan (stok tiba)',
        'user_id' => $admin->id,
    ]);
});

it('luluskanDariPendingStock sends TonerDiluluskan notification', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->pendingStock()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->call('luluskanDariPendingStock');

    Notification::assertSentTo($user, TonerDiluluskan::class);
});

it('luluskanDariPendingStock returns 422 when status is not PendingStock', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->call('luluskanDariPendingStock')
        ->assertStatus(422);
});

// ──────────────────────────────────────────────
// tolak
// ──────────────────────────────────────────────

it('tolak updates status to Ditolak', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->set('sebabPenolakan', 'Model pencetak tidak disokong oleh stok semasa.')
        ->call('tolak');

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanToner::Ditolak);
});

it('tolak creates a log entry with reason', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->set('sebabPenolakan', 'Maklumat pencetak tidak lengkap dan tidak sah.')
        ->call('tolak');

    $this->assertDatabaseHas('log_toner', [
        'permohonan_toner_id' => $permohonan->id,
        'tindakan' => 'Permohonan ditolak',
        'catatan' => 'Maklumat pencetak tidak lengkap dan tidak sah.',
        'user_id' => $admin->id,
    ]);
});

it('tolak sends TonerDitolak notification to pemohon', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $permohonan = PermohonanToner::factory()->submitted()->create(['user_id' => $user->id]);

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->set('sebabPenolakan', 'Model pencetak tidak disokong oleh stok semasa.')
        ->call('tolak');

    Notification::assertSentTo($user, TonerDitolak::class);
});

it('tolak requires sebabPenolakan', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $permohonan = PermohonanToner::factory()->submitted()->create();

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->set('sebabPenolakan', '')
        ->call('tolak')
        ->assertHasErrors(['sebabPenolakan']);

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanToner::Submitted);
});

it('tolak validates sebabPenolakan min 10 characters', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $permohonan = PermohonanToner::factory()->submitted()->create();

    Livewire::actingAs($admin)
        ->test(ProsesPermohonan::class, ['id' => $permohonan->id])
        ->set('sebabPenolakan', 'Pendek')
        ->call('tolak')
        ->assertHasErrors(['sebabPenolakan']);

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanToner::Submitted);
});
