<?php

use App\Enums\StatusAduan;
use App\Livewire\Admin\ButiranAduan;
use App\Livewire\Admin\SenaraiAduan;
use App\Models\AduanIct;
use App\Models\KategoriAduan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

const UNIT_A = 'Unit Infrastruktur';
const UNIT_B = 'Unit Aplikasi';

beforeEach(function () {
    $this->kategoriUnitA = KategoriAduan::factory()->create(['unit_bpm' => UNIT_A, 'is_aktif' => true]);
    $this->kategoriUnitB = KategoriAduan::factory()->create(['unit_bpm' => UNIT_B, 'is_aktif' => true]);
    $this->pengguna = User::factory()->create();
    $this->pentadbir = User::factory()->pentadbir(UNIT_A)->create();
    $this->superadmin = User::factory()->superadmin()->create();
    $this->teknician = User::factory()->teknician(UNIT_A)->create();
});

// ─── Access Control ───────────────────────────────────────────────────────────

it('redirects unauthenticated users to login', function () {
    $this->get(route('admin.aduan.index'))->assertRedirect(route('login'));
});

it('blocks pengguna biasa with 403', function () {
    actingAs($this->pengguna)
        ->get(route('admin.aduan.index'))
        ->assertForbidden();
});

it('allows pentadbir to access the admin list', function () {
    actingAs($this->pentadbir)
        ->get(route('admin.aduan.index'))
        ->assertOk();
});

it('allows superadmin to access the admin list', function () {
    actingAs($this->superadmin)
        ->get(route('admin.aduan.index'))
        ->assertOk();
});

it('allows teknician to access the admin aduan list', function () {
    actingAs($this->teknician)
        ->get(route('admin.aduan.index'))
        ->assertOk();
});

it('blocks teknician from accessing laporan', function () {
    actingAs($this->teknician)
        ->get(route('admin.laporan.index'))
        ->assertForbidden();
});

// ─── Unit Scoping ─────────────────────────────────────────────────────────────

it('pentadbir only sees aduan from their unit', function () {
    $aduanUnitA = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    $aduanUnitB = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitB->id,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(SenaraiAduan::class)
        ->assertSee($aduanUnitA->no_tiket)
        ->assertDontSee($aduanUnitB->no_tiket);
});

it('superadmin sees aduan from all units', function () {
    $aduanUnitA = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    $aduanUnitB = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitB->id,
    ]);

    actingAs($this->superadmin);

    Livewire::test(SenaraiAduan::class)
        ->assertSee($aduanUnitA->no_tiket)
        ->assertSee($aduanUnitB->no_tiket);
});

// ─── Search & Filter ──────────────────────────────────────────────────────────

it('filters by no tiket search', function () {
    actingAs($this->pentadbir);

    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'no_tiket' => 'ICT-2026-099',
    ]);

    Livewire::test(SenaraiAduan::class)
        ->set('search', 'ICT-2026-099')
        ->assertSee('ICT-2026-099');
});

it('filters by pemohon name search', function () {
    actingAs($this->pentadbir);

    $namedUser = User::factory()->create(['name' => 'Ahmad Nabil']);

    $aduan = AduanIct::factory()->create([
        'user_id' => $namedUser->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    Livewire::test(SenaraiAduan::class)
        ->set('search', 'Ahmad Nabil')
        ->assertSee($aduan->no_tiket);
});

it('filters by status', function () {
    actingAs($this->pentadbir);

    $aduanBaru = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'status' => StatusAduan::Baru,
    ]);

    $aduanSelesai = AduanIct::factory()->selesai()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    Livewire::test(SenaraiAduan::class)
        ->set('filterStatus', StatusAduan::Selesai->value)
        ->assertSee($aduanSelesai->no_tiket)
        ->assertDontSee($aduanBaru->no_tiket);
});

it('shows empty state message when there are no aduan', function () {
    actingAs($this->pentadbir);

    Livewire::test(SenaraiAduan::class)
        ->assertSee('Tiada aduan baru. Semua aduan telah ditindakan.');
});

// ─── Stats Cards ──────────────────────────────────────────────────────────────

it('stats: jumlahHariIni counts only today aduan scoped to unit', function () {
    actingAs($this->pentadbir);

    AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'created_at' => now(),
    ]);

    AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitB->id,
        'created_at' => now(),
    ]);

    $component = Livewire::test(SenaraiAduan::class);

    expect($component->instance()->jumlahHariIni)->toBe(1);
});

it('stats: jumlahDalamProses counts dalam_proses scoped to unit', function () {
    actingAs($this->pentadbir);

    AduanIct::factory()->dalamProses()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'status' => StatusAduan::Baru,
    ]);

    $component = Livewire::test(SenaraiAduan::class);

    expect($component->instance()->jumlahDalamProses)->toBe(1);
});

// ─── Detail Page ──────────────────────────────────────────────────────────────

it('pentadbir can view detail of aduan from own unit', function () {
    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    actingAs($this->pentadbir)
        ->get(route('admin.aduan.show', $aduan->id))
        ->assertOk();
});

it('pentadbir cannot view detail of aduan from another unit', function () {
    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitB->id,
    ]);

    actingAs($this->pentadbir)
        ->get(route('admin.aduan.show', $aduan->id))
        ->assertNotFound();
});

it('superadmin can view detail of aduan from any unit', function () {
    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitB->id,
    ]);

    actingAs($this->superadmin)
        ->get(route('admin.aduan.show', $aduan->id))
        ->assertOk();
});

it('teknician only sees aduan assigned to them', function () {
    $aduanDitugaskan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'pentadbir_id' => $this->teknician->id,
    ]);

    $aduanLain = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'pentadbir_id' => null,
    ]);

    actingAs($this->teknician);

    Livewire::test(SenaraiAduan::class)
        ->assertSee($aduanDitugaskan->no_tiket)
        ->assertDontSee($aduanLain->no_tiket);
});

it('teknician can view detail of aduan assigned to them', function () {
    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'pentadbir_id' => $this->teknician->id,
    ]);

    actingAs($this->teknician)
        ->get(route('admin.aduan.show', $aduan->id))
        ->assertOk();
});

it('teknician cannot view detail of aduan not assigned to them', function () {
    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'pentadbir_id' => null,
    ]);

    actingAs($this->teknician)
        ->get(route('admin.aduan.show', $aduan->id))
        ->assertNotFound();
});

it('detail page shows submitter info, not admin info', function () {
    $submitter = User::factory()->create(['name' => 'Amirah Zahra']);

    $aduan = AduanIct::factory()->create([
        'user_id' => $submitter->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->assertSee('Amirah Zahra')
        ->assertDontSee($this->pentadbir->name);
});
