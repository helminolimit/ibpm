<?php

use App\Enums\RolePengguna;
use App\Enums\StatusPermohonanPortal;
use App\Livewire\Pentadbir\M04\PanelPermohonan;
use App\Mail\StatusPortalDikemaskini;
use App\Models\LogAuditPortal;
use App\Models\NotifikasiPortal;
use App\Models\PermohonanPortal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// ─── Auth / Authorisation ────────────────────────────────────────────────────

test('unauthenticated user cannot access admin panel', function () {
    $this->get(route('admin.kemaskini-portal.index'))
        ->assertRedirect(route('login'));
});

test('pengguna role gets 403 on admin panel', function () {
    $pengguna = User::factory()->create(['role' => RolePengguna::Pengguna]);

    $this->actingAs($pengguna)
        ->get(route('admin.kemaskini-portal.index'))
        ->assertForbidden();
});

test('pentadbir can access admin panel', function () {
    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);

    $this->actingAs($pentadbir)
        ->get(route('admin.kemaskini-portal.index'))
        ->assertSuccessful();
});

test('superadmin can access admin panel', function () {
    $superadmin = User::factory()->create(['role' => RolePengguna::Superadmin]);

    $this->actingAs($superadmin)
        ->get(route('admin.kemaskini-portal.index'))
        ->assertSuccessful();
});

// ─── Status Update ───────────────────────────────────────────────────────────

test('pentadbir can update status from diterima to dalam_proses', function () {
    Mail::fake();

    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    $permohonan = PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Kemaskini kandungan halaman utama.',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    $this->actingAs($pentadbir);

    Livewire::test(PanelPermohonan::class)
        ->call('bukaPilihStatus', $permohonan->id)
        ->set('statusBaru', 'dalam_proses')
        ->call('kemaskiniStatus')
        ->assertHasNoErrors();

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanPortal::DalamProses);
});

test('pentadbir can update status from dalam_proses to selesai', function () {
    Mail::fake();

    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    $permohonan = PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Kemaskini kandungan halaman utama.',
        'status' => StatusPermohonanPortal::DalamProses,
    ]);

    $this->actingAs($pentadbir);

    Livewire::test(PanelPermohonan::class)
        ->call('bukaPilihStatus', $permohonan->id)
        ->set('statusBaru', 'selesai')
        ->call('kemaskiniStatus')
        ->assertHasNoErrors();

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanPortal::Selesai);
});

test('tarikh_selesai is set when status becomes selesai', function () {
    Mail::fake();

    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    $permohonan = PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Kemaskini kandungan halaman utama.',
        'status' => StatusPermohonanPortal::DalamProses,
    ]);

    $this->actingAs($pentadbir);

    Livewire::test(PanelPermohonan::class)
        ->call('bukaPilihStatus', $permohonan->id)
        ->set('statusBaru', 'selesai')
        ->call('kemaskiniStatus');

    expect($permohonan->fresh()->tarikh_selesai)->not->toBeNull();
});

test('tarikh_selesai is null when status is not selesai', function () {
    Mail::fake();

    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    $permohonan = PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Kemaskini kandungan halaman utama.',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    $this->actingAs($pentadbir);

    Livewire::test(PanelPermohonan::class)
        ->call('bukaPilihStatus', $permohonan->id)
        ->set('statusBaru', 'dalam_proses')
        ->call('kemaskiniStatus');

    expect($permohonan->fresh()->tarikh_selesai)->toBeNull();
});

test('pentadbir_id is recorded on status update', function () {
    Mail::fake();

    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    $permohonan = PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Kemaskini kandungan halaman utama.',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    $this->actingAs($pentadbir);

    Livewire::test(PanelPermohonan::class)
        ->call('bukaPilihStatus', $permohonan->id)
        ->set('statusBaru', 'dalam_proses')
        ->call('kemaskiniStatus');

    expect($permohonan->fresh()->pentadbir_id)->toBe($pentadbir->id);
});

// ─── Backward Status Rejection ───────────────────────────────────────────────

test('backward status is rejected — selesai to diterima', function () {
    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    $permohonan = PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Kemaskini kandungan halaman utama.',
        'status' => StatusPermohonanPortal::Selesai,
    ]);

    $this->actingAs($pentadbir);

    Livewire::test(PanelPermohonan::class)
        ->call('bukaPilihStatus', $permohonan->id)
        ->set('statusBaru', 'diterima')
        ->call('kemaskiniStatus')
        ->assertHasErrors('statusBaru');

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanPortal::Selesai);
});

test('backward status is rejected — selesai to dalam_proses', function () {
    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    $permohonan = PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Kemaskini kandungan halaman utama.',
        'status' => StatusPermohonanPortal::Selesai,
    ]);

    $this->actingAs($pentadbir);

    Livewire::test(PanelPermohonan::class)
        ->call('bukaPilihStatus', $permohonan->id)
        ->set('statusBaru', 'dalam_proses')
        ->call('kemaskiniStatus')
        ->assertHasErrors('statusBaru');

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanPortal::Selesai);
});

test('backward status is rejected — dalam_proses to diterima', function () {
    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    $permohonan = PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Kemaskini kandungan halaman utama.',
        'status' => StatusPermohonanPortal::DalamProses,
    ]);

    $this->actingAs($pentadbir);

    Livewire::test(PanelPermohonan::class)
        ->call('bukaPilihStatus', $permohonan->id)
        ->set('statusBaru', 'diterima')
        ->call('kemaskiniStatus')
        ->assertHasErrors('statusBaru');

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanPortal::DalamProses);
});

// ─── Observer Side-Effects ───────────────────────────────────────────────────

test('email is queued when status changes', function () {
    Mail::fake();

    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    $permohonan = PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Kemaskini kandungan halaman utama.',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    $this->actingAs($pentadbir);

    Livewire::test(PanelPermohonan::class)
        ->call('bukaPilihStatus', $permohonan->id)
        ->set('statusBaru', 'dalam_proses')
        ->call('kemaskiniStatus');

    Mail::assertQueued(StatusPortalDikemaskini::class);
});

test('notifikasi portal is created when status changes', function () {
    Mail::fake();

    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    $permohonan = PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Kemaskini kandungan halaman utama.',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    $this->actingAs($pentadbir);

    Livewire::test(PanelPermohonan::class)
        ->call('bukaPilihStatus', $permohonan->id)
        ->set('statusBaru', 'dalam_proses')
        ->call('kemaskiniStatus');

    expect(
        NotifikasiPortal::where('permohonan_portal_id', $permohonan->id)
            ->where('jenis', 'status_dikemaskini')
            ->exists()
    )->toBeTrue();
});

test('log audit is created when status changes', function () {
    Mail::fake();

    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    $permohonan = PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Kemaskini kandungan halaman utama.',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    $this->actingAs($pentadbir);

    Livewire::test(PanelPermohonan::class)
        ->call('bukaPilihStatus', $permohonan->id)
        ->set('statusBaru', 'dalam_proses')
        ->call('kemaskiniStatus');

    expect(
        LogAuditPortal::where('permohonan_portal_id', $permohonan->id)
            ->where('tindakan', 'status_dikemaskini')
            ->exists()
    )->toBeTrue();
});
