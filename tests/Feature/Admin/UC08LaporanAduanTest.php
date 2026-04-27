<?php

use App\Enums\StatusAduan;
use App\Livewire\Admin\LaporanAduan;
use App\Models\AduanIct;
use App\Models\KategoriAduan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->kategoriUnitA = KategoriAduan::factory()->create(['unit_bpm' => 'Unit Infrastruktur', 'is_aktif' => true]);
    $this->kategoriUnitB = KategoriAduan::factory()->create(['unit_bpm' => 'Unit Aplikasi', 'is_aktif' => true]);
    $this->pengguna = User::factory()->create();
    $this->pentadbir = User::factory()->pentadbir('Unit Infrastruktur')->create();
    $this->superadmin = User::factory()->superadmin()->create();
    $this->teknician = User::factory()->teknician('Unit Infrastruktur')->create();
});

// ─── Access Control ───────────────────────────────────────────────────────────

it('redirects unauthenticated users to login', function () {
    $this->get(route('admin.laporan.index'))->assertRedirect(route('login'));
});

it('blocks pengguna biasa with 403', function () {
    actingAs($this->pengguna)
        ->get(route('admin.laporan.index'))
        ->assertForbidden();
});

it('blocks teknician with 403', function () {
    actingAs($this->teknician)
        ->get(route('admin.laporan.index'))
        ->assertForbidden();
});

it('allows pentadbir to access laporan page', function () {
    actingAs($this->pentadbir)
        ->get(route('admin.laporan.index'))
        ->assertOk();
});

it('allows superadmin to access laporan page', function () {
    actingAs($this->superadmin)
        ->get(route('admin.laporan.index'))
        ->assertOk();
});

// ─── Report Generation ────────────────────────────────────────────────────────

it('shows no results before generating', function () {
    actingAs($this->pentadbir);

    AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    Livewire::test(LaporanAduan::class)
        ->assertSet('hasGenerated', false)
        ->assertDontSee('Bahagian A');
});

it('generates report after calling janaLaporan', function () {
    actingAs($this->pentadbir);

    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    Livewire::test(LaporanAduan::class)
        ->set('tarikhDari', now()->startOfMonth()->toDateString())
        ->set('tarikhHingga', now()->toDateString())
        ->call('janaLaporan')
        ->assertSet('hasGenerated', true)
        ->assertSee('Bahagian A')
        ->assertSee($aduan->no_tiket);
});

it('validates that tarikhDari is required', function () {
    actingAs($this->pentadbir);

    Livewire::test(LaporanAduan::class)
        ->set('tarikhDari', '')
        ->set('tarikhHingga', now()->toDateString())
        ->call('janaLaporan')
        ->assertHasErrors(['tarikhDari' => 'required'])
        ->assertSet('hasGenerated', false);
});

it('validates that tarikhHingga must be after tarikhDari', function () {
    actingAs($this->pentadbir);

    Livewire::test(LaporanAduan::class)
        ->set('tarikhDari', '2026-04-30')
        ->set('tarikhHingga', '2026-04-01')
        ->call('janaLaporan')
        ->assertHasErrors(['tarikhHingga'])
        ->assertSet('hasGenerated', false);
});

// ─── Unit Scoping ─────────────────────────────────────────────────────────────

it('pentadbir only sees aduan from their unit in report', function () {
    actingAs($this->pentadbir);

    $aduanUnitA = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    $aduanUnitB = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitB->id,
    ]);

    Livewire::test(LaporanAduan::class)
        ->set('tarikhDari', now()->subMonth()->toDateString())
        ->set('tarikhHingga', now()->toDateString())
        ->call('janaLaporan')
        ->assertSee($aduanUnitA->no_tiket)
        ->assertDontSee($aduanUnitB->no_tiket);
});

it('superadmin sees aduan from all units', function () {
    actingAs($this->superadmin);

    $aduanUnitA = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    $aduanUnitB = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitB->id,
    ]);

    Livewire::test(LaporanAduan::class)
        ->set('tarikhDari', now()->subMonth()->toDateString())
        ->set('tarikhHingga', now()->toDateString())
        ->call('janaLaporan')
        ->assertSee($aduanUnitA->no_tiket)
        ->assertSee($aduanUnitB->no_tiket);
});

it('superadmin can filter by specific unit', function () {
    actingAs($this->superadmin);

    $aduanUnitA = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    $aduanUnitB = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitB->id,
    ]);

    Livewire::test(LaporanAduan::class)
        ->set('tarikhDari', now()->subMonth()->toDateString())
        ->set('tarikhHingga', now()->toDateString())
        ->set('filterUnit', 'Unit Infrastruktur')
        ->call('janaLaporan')
        ->assertSee($aduanUnitA->no_tiket)
        ->assertDontSee($aduanUnitB->no_tiket);
});

// ─── Filters ──────────────────────────────────────────────────────────────────

it('filters by date range', function () {
    actingAs($this->pentadbir);

    $aduanDalamTempoh = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'created_at' => '2026-03-15',
    ]);

    $aduanLuarTempoh = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'created_at' => '2026-01-10',
    ]);

    Livewire::test(LaporanAduan::class)
        ->set('tarikhDari', '2026-03-01')
        ->set('tarikhHingga', '2026-03-31')
        ->call('janaLaporan')
        ->assertSee($aduanDalamTempoh->no_tiket)
        ->assertDontSee($aduanLuarTempoh->no_tiket);
});

it('filters by status', function () {
    actingAs($this->pentadbir);

    $aduanSelesai = AduanIct::factory()->selesai()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    $aduanBaru = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    Livewire::test(LaporanAduan::class)
        ->set('tarikhDari', now()->subMonth()->toDateString())
        ->set('tarikhHingga', now()->toDateString())
        ->set('filterStatus', StatusAduan::Selesai->value)
        ->call('janaLaporan')
        ->assertSee($aduanSelesai->no_tiket)
        ->assertDontSee($aduanBaru->no_tiket);
});

// ─── Statistics ───────────────────────────────────────────────────────────────

it('calculates correct statistics', function () {
    actingAs($this->pentadbir);

    AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'status' => StatusAduan::Baru,
    ]);

    AduanIct::factory()->dalamProses()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    AduanIct::factory()->selesai()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    $component = Livewire::test(LaporanAduan::class)
        ->set('tarikhDari', now()->subMonth()->toDateString())
        ->set('tarikhHingga', now()->toDateString())
        ->call('janaLaporan');

    expect($component->instance()->jumlahAduan)->toBe(3);
    expect($component->instance()->jumlahBaru)->toBe(1);
    expect($component->instance()->jumlahDalamProses)->toBe(1);
    expect($component->instance()->jumlahSelesai)->toBe(1);
    expect($component->instance()->kadarPenyelesaian)->toBe('33.3%');
});

// ─── Period Warning ───────────────────────────────────────────────────────────

it('shows period warning when range exceeds 12 months', function () {
    actingAs($this->pentadbir);

    Livewire::test(LaporanAduan::class)
        ->set('tarikhDari', '2024-01-01')
        ->set('tarikhHingga', '2026-04-01')
        ->call('janaLaporan')
        ->assertSet('periodoLuasWarning', true)
        ->assertSet('hasGenerated', false);
});

it('generates report after confirming period warning', function () {
    actingAs($this->pentadbir);

    Livewire::test(LaporanAduan::class)
        ->set('tarikhDari', '2024-01-01')
        ->set('tarikhHingga', '2026-04-01')
        ->call('confirmJanaLaporan')
        ->assertSet('periodoLuasWarning', false)
        ->assertSet('hasGenerated', true);
});

it('resets period warning when batal is clicked', function () {
    actingAs($this->pentadbir);

    Livewire::test(LaporanAduan::class)
        ->set('tarikhDari', '2024-01-01')
        ->set('tarikhHingga', '2026-04-01')
        ->call('janaLaporan')
        ->assertSet('periodoLuasWarning', true)
        ->call('batalPeriodoLuas')
        ->assertSet('periodoLuasWarning', false);
});

// ─── Empty State ──────────────────────────────────────────────────────────────

it('shows empty state when no records match', function () {
    actingAs($this->pentadbir);

    Livewire::test(LaporanAduan::class)
        ->set('tarikhDari', '2020-01-01')
        ->set('tarikhHingga', '2020-01-31')
        ->call('janaLaporan')
        ->assertSee('Tiada rekod ditemui untuk tempoh dan kriteria yang dipilih.');
});

// ─── Export ───────────────────────────────────────────────────────────────────

it('exports excel file', function () {
    actingAs($this->pentadbir);

    AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    Livewire::test(LaporanAduan::class)
        ->set('tarikhDari', now()->subMonth()->toDateString())
        ->set('tarikhHingga', now()->toDateString())
        ->call('janaLaporan')
        ->call('exportExcel')
        ->assertFileDownloaded();
});

it('exports pdf file', function () {
    actingAs($this->pentadbir);

    AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    Livewire::test(LaporanAduan::class)
        ->set('tarikhDari', now()->subMonth()->toDateString())
        ->set('tarikhHingga', now()->toDateString())
        ->call('janaLaporan')
        ->call('exportPdf')
        ->assertFileDownloaded();
});
