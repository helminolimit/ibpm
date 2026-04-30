<?php

use App\Enums\RolePengguna;
use App\Enums\StatusPermohonanPortal;
use App\Livewire\Pentadbir\M04\PanelPermohonan;
use App\Mail\TugasanPortalBaru;
use App\Models\LogAuditPortal;
use App\Models\NotifikasiPortal;
use App\Models\PermohonanPortal;
use App\Models\TugasanPortal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// ─── Auth / Authorisation ────────────────────────────────────────────────────

test('pengguna role cannot tugaskan pembangun', function () {
    Mail::fake();

    $pengguna = User::factory()->create(['role' => RolePengguna::Pengguna]);
    $teknisian = User::factory()->create(['role' => RolePengguna::Teknician]);

    $permohonan = PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pengguna->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Kemaskini kandungan halaman utama.',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    $this->actingAs($pengguna)
        ->get(route('admin.kemaskini-portal.index'))
        ->assertForbidden();
});

// ─── Tugasan Pembangun ───────────────────────────────────────────────────────

test('pentadbir can tugaskan pembangun', function () {
    Mail::fake();

    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $teknisian = User::factory()->create(['role' => RolePengguna::Teknician]);
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
        ->call('bukaTugasan', $permohonan->id)
        ->set('teknisianId', $teknisian->id)
        ->set('notaTugasan', 'Sila kemaskini halaman utama.')
        ->call('tugaskanPembangun')
        ->assertHasNoErrors();

    expect(TugasanPortal::where('permohonan_portal_id', $permohonan->id)->exists())->toBeTrue();
});

test('tugasan record is saved correctly', function () {
    Mail::fake();

    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $teknisian = User::factory()->create(['role' => RolePengguna::Teknician]);
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
        ->call('bukaTugasan', $permohonan->id)
        ->set('teknisianId', $teknisian->id)
        ->set('notaTugasan', 'Nota penting.')
        ->call('tugaskanPembangun');

    $tugasan = TugasanPortal::where('permohonan_portal_id', $permohonan->id)->first();

    expect($tugasan)
        ->teknisian_id->toBe($teknisian->id)
        ->ditugaskan_oleh->toBe($pentadbir->id)
        ->nota_tugasan->toBe('Nota penting.')
        ->status_tugasan->toBe('baharu');
});

test('tugasan without nota is allowed', function () {
    Mail::fake();

    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $teknisian = User::factory()->create(['role' => RolePengguna::Teknician]);
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
        ->call('bukaTugasan', $permohonan->id)
        ->set('teknisianId', $teknisian->id)
        ->call('tugaskanPembangun')
        ->assertHasNoErrors();

    expect(TugasanPortal::where('permohonan_portal_id', $permohonan->id)->exists())->toBeTrue();
});

test('tugasan requires teknisian_id', function () {
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
        ->call('bukaTugasan', $permohonan->id)
        ->set('teknisianId', '')
        ->call('tugaskanPembangun')
        ->assertHasErrors('teknisianId');
});

// ─── Side Effects ────────────────────────────────────────────────────────────

test('email is queued to teknisian when tugasan created', function () {
    Mail::fake();

    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $teknisian = User::factory()->create(['role' => RolePengguna::Teknician]);
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
        ->call('bukaTugasan', $permohonan->id)
        ->set('teknisianId', $teknisian->id)
        ->call('tugaskanPembangun');

    Mail::assertQueued(TugasanPortalBaru::class, function ($mail) use ($teknisian) {
        return $mail->hasTo($teknisian->email);
    });
});

test('notifikasi portal is created when tugasan created', function () {
    Mail::fake();

    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $teknisian = User::factory()->create(['role' => RolePengguna::Teknician]);
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
        ->call('bukaTugasan', $permohonan->id)
        ->set('teknisianId', $teknisian->id)
        ->call('tugaskanPembangun');

    expect(
        NotifikasiPortal::where('permohonan_portal_id', $permohonan->id)
            ->where('pengguna_id', $teknisian->id)
            ->where('jenis', 'tugasan_baru')
            ->exists()
    )->toBeTrue();
});

test('log audit is created when tugasan created', function () {
    Mail::fake();

    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $teknisian = User::factory()->create(['role' => RolePengguna::Teknician]);
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
        ->call('bukaTugasan', $permohonan->id)
        ->set('teknisianId', $teknisian->id)
        ->call('tugaskanPembangun');

    expect(
        LogAuditPortal::where('permohonan_portal_id', $permohonan->id)
            ->where('tindakan', 'tugasan_dibuat')
            ->exists()
    )->toBeTrue();
});
