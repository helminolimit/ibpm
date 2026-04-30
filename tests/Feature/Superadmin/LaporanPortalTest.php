<?php

use App\Enums\RolePengguna;
use App\Enums\StatusPermohonanPortal;
use App\Livewire\Superadmin\LaporanPortal;
use App\Models\PermohonanPortal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// ─── Auth / Authorisation ────────────────────────────────────────────────────

test('unauthenticated user cannot access laporan portal', function () {
    $this->get(route('superadmin.laporan-portal.index'))
        ->assertRedirect(route('login'));
});

test('pengguna role gets 403 on laporan portal', function () {
    $pengguna = User::factory()->create(['role' => RolePengguna::Pengguna]);

    $this->actingAs($pengguna)
        ->get(route('superadmin.laporan-portal.index'))
        ->assertForbidden();
});

test('pentadbir role gets 403 on laporan portal', function () {
    $pentadbir = User::factory()->create(['role' => RolePengguna::Pentadbir]);

    $this->actingAs($pentadbir)
        ->get(route('superadmin.laporan-portal.index'))
        ->assertForbidden();
});

test('superadmin can access laporan portal', function () {
    $superadmin = User::factory()->create(['role' => RolePengguna::Superadmin]);

    $this->actingAs($superadmin)
        ->get(route('superadmin.laporan-portal.index'))
        ->assertSuccessful();
});

// ─── Display ─────────────────────────────────────────────────────────────────

test('laporan displays all permohonan records', function () {
    $superadmin = User::factory()->create(['role' => RolePengguna::Superadmin]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Kemaskini kandungan halaman utama.',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-002',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page2',
        'jenis_perubahan' => 'konfigurasi',
        'butiran_kemaskini' => 'Kemaskini konfigurasi halaman.',
        'status' => StatusPermohonanPortal::Selesai,
        'tarikh_selesai' => now(),
    ]);

    $this->actingAs($superadmin);

    Livewire::test(LaporanPortal::class)
        ->assertSee('#ICT-2026-001')
        ->assertSee('#ICT-2026-002');
});

test('statistik displays correct counts', function () {
    $superadmin = User::factory()->create(['role' => RolePengguna::Superadmin]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test.',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-002',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page2',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test.',
        'status' => StatusPermohonanPortal::DalamProses,
    ]);

    PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-003',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page3',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test.',
        'status' => StatusPermohonanPortal::Selesai,
        'tarikh_selesai' => now(),
    ]);

    $this->actingAs($superadmin);

    Livewire::test(LaporanPortal::class)
        ->assertSeeInOrder(['Jumlah', '3'])
        ->assertSee('Diterima')
        ->assertSee('Dalam Proses')
        ->assertSee('Selesai');
});

// ─── Filters ─────────────────────────────────────────────────────────────────

test('filter by status works', function () {
    $superadmin = User::factory()->create(['role' => RolePengguna::Superadmin]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test.',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-002',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page2',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test.',
        'status' => StatusPermohonanPortal::Selesai,
        'tarikh_selesai' => now(),
    ]);

    $this->actingAs($superadmin);

    Livewire::test(LaporanPortal::class)
        ->set('filterStatus', 'selesai')
        ->assertSee('#ICT-2026-002')
        ->assertDontSee('#ICT-2026-001');
});

test('filter by jenis works', function () {
    $superadmin = User::factory()->create(['role' => RolePengguna::Superadmin]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test.',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-002',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page2',
        'jenis_perubahan' => 'konfigurasi',
        'butiran_kemaskini' => 'Test.',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    $this->actingAs($superadmin);

    Livewire::test(LaporanPortal::class)
        ->set('filterJenis', 'konfigurasi')
        ->assertSee('#ICT-2026-002')
        ->assertDontSee('#ICT-2026-001');
});

test('filter by date range works', function () {
    $superadmin = User::factory()->create(['role' => RolePengguna::Superadmin]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    $old = PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-OLD',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/old-page',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test.',
        'status' => StatusPermohonanPortal::Diterima,
    ]);
    DB::table('permohonan_portals')
        ->where('id', $old->id)
        ->update(['created_at' => '2026-01-15 10:00:00']);

    $recent = PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-NEW',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/new-page',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test.',
        'status' => StatusPermohonanPortal::Diterima,
    ]);
    DB::table('permohonan_portals')
        ->where('id', $recent->id)
        ->update(['created_at' => '2026-04-20 10:00:00']);

    $this->actingAs($superadmin);

    Livewire::test(LaporanPortal::class)
        ->set('dari', '2026-04-01')
        ->set('hingga', '2026-04-30')
        ->assertSee('#ICT-2026-NEW')
        ->assertDontSee('#ICT-2026-OLD');
});

test('reset penapis clears all filters', function () {
    $superadmin = User::factory()->create(['role' => RolePengguna::Superadmin]);

    $this->actingAs($superadmin);

    Livewire::test(LaporanPortal::class)
        ->set('dari', '2026-01-01')
        ->set('hingga', '2026-12-31')
        ->set('filterStatus', 'selesai')
        ->set('filterJenis', 'kandungan')
        ->call('resetPenapis')
        ->assertSet('dari', '')
        ->assertSet('hingga', '')
        ->assertSet('filterStatus', '')
        ->assertSet('filterJenis', '');
});

// ─── Export ──────────────────────────────────────────────────────────────────

test('export excel returns download response', function () {
    $superadmin = User::factory()->create(['role' => RolePengguna::Superadmin]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test.',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    $this->actingAs($superadmin);

    Livewire::test(LaporanPortal::class)
        ->call('exportExcel')
        ->assertFileDownloaded();
});

test('export pdf returns download response', function () {
    $superadmin = User::factory()->create(['role' => RolePengguna::Superadmin]);
    $pemohon = User::factory()->create(['role' => RolePengguna::Pengguna]);

    PermohonanPortal::create([
        'no_tiket' => '#ICT-2026-001',
        'pemohon_id' => $pemohon->id,
        'url_halaman' => 'https://motac.gov.my/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test.',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    $this->actingAs($superadmin);

    // PDF export generates binary content that Livewire can't JSON-encode in tests.
    // We verify the PDF can be generated without errors by calling the method directly.
    $component = new LaporanPortal;
    $response = $component->exportPdf();

    expect($response->getStatusCode())->toBe(200);
    expect($response->headers->get('content-type'))->toContain('pdf');
});
