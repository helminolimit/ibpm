<?php

use App\Enums\JenisToner;
use App\Livewire\M02\Admin\InventoriStok;
use App\Models\LogToner;
use App\Models\StokToner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// ─── Access Control ──────────────────────────────────────────────────────────

it('renders inventori stok page for admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('m02.admin.inventori-stok'))
        ->assertStatus(200)
        ->assertSeeLivewire(InventoriStok::class);
});

it('renders inventori stok page for superadmin', function () {
    $superadmin = User::factory()->create(['role' => 'superadmin']);

    $this->actingAs($superadmin)
        ->get(route('m02.admin.inventori-stok'))
        ->assertStatus(200)
        ->assertSeeLivewire(InventoriStok::class);
});

it('forbids regular user from accessing inventori stok', function () {
    $user = User::factory()->create(['role' => 'user']);

    Livewire::actingAs($user)
        ->test(InventoriStok::class)
        ->assertForbidden();
});

it('redirects unauthenticated user to login', function () {
    $this->get(route('m02.admin.inventori-stok'))
        ->assertRedirectToRoute('login');
});

// ─── Tambah Stok Baru ────────────────────────────────────────────────────────

it('admin can add a new stok record', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(InventoriStok::class)
        ->set('modelToner', 'CF217A')
        ->set('jenama', 'HP')
        ->set('jenisToner', 'hitam')
        ->set('warna', '')
        ->set('kuantitiAda', 10)
        ->set('kuantitiMinimum', 3)
        ->call('simpan')
        ->assertHasNoErrors();

    expect(StokToner::where('model_toner', 'CF217A')->where('jenama', 'HP')->exists())->toBeTrue();
});

it('creates a log entry when stok is added', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(InventoriStok::class)
        ->set('modelToner', 'TN2380')
        ->set('jenama', 'Brother')
        ->set('jenisToner', 'hitam')
        ->set('kuantitiAda', 5)
        ->set('kuantitiMinimum', 2)
        ->call('simpan');

    expect(LogToner::where('tindakan', 'stock_updated')->exists())->toBeTrue();
    expect(LogToner::where('tindakan', 'stock_updated')->first()->permohonan_toner_id)->toBeNull();
});

it('validates required fields when adding stok', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(InventoriStok::class)
        ->set('modelToner', '')
        ->set('jenama', '')
        ->set('jenisToner', '')
        ->set('kuantitiAda', 0)
        ->set('kuantitiMinimum', 1)
        ->call('simpan')
        ->assertHasErrors(['modelToner', 'jenama', 'jenisToner']);
});

// ─── Semak Duplikasi ─────────────────────────────────────────────────────────

it('rejects duplicate model+jenama+jenis combination', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    StokToner::factory()->create([
        'model_toner' => 'CF217A',
        'jenama' => 'HP',
        'jenis_toner' => JenisToner::Hitam,
        'kuantiti_ada' => 5,
        'kuantiti_minimum' => 2,
    ]);

    Livewire::actingAs($admin)
        ->test(InventoriStok::class)
        ->set('modelToner', 'CF217A')
        ->set('jenama', 'HP')
        ->set('jenisToner', 'hitam')
        ->set('kuantitiAda', 10)
        ->set('kuantitiMinimum', 3)
        ->call('simpan')
        ->assertHasErrors(['modelToner']);
});

// ─── Kemaskini Stok ──────────────────────────────────────────────────────────

it('admin can edit an existing stok record', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $stok = StokToner::factory()->create([
        'model_toner' => 'CE285A',
        'jenama' => 'HP',
        'jenis_toner' => JenisToner::Hitam,
        'kuantiti_ada' => 2,
        'kuantiti_minimum' => 3,
    ]);

    Livewire::actingAs($admin)
        ->test(InventoriStok::class)
        ->call('bukaEdit', $stok->id)
        ->assertSet('editId', $stok->id)
        ->assertSet('modelToner', 'CE285A')
        ->set('kuantitiAda', 15)
        ->call('simpan')
        ->assertHasNoErrors();

    expect($stok->fresh()->kuantiti_ada)->toBe(15);
});

it('allows editing to same combination when editId matches', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $stok = StokToner::factory()->create([
        'model_toner' => 'CF217A',
        'jenama' => 'HP',
        'jenis_toner' => JenisToner::Hitam,
        'kuantiti_ada' => 5,
        'kuantiti_minimum' => 2,
    ]);

    Livewire::actingAs($admin)
        ->test(InventoriStok::class)
        ->call('bukaEdit', $stok->id)
        ->set('kuantitiAda', 20)
        ->call('simpan')
        ->assertHasNoErrors();

    expect($stok->fresh()->kuantiti_ada)->toBe(20);
});

// ─── Status Stok ─────────────────────────────────────────────────────────────

it('stok model returns stokHabis true when kuantiti is 0', function () {
    $stok = StokToner::factory()->create([
        'jenis_toner' => JenisToner::Hitam,
        'kuantiti_ada' => 0,
        'kuantiti_minimum' => 5,
    ]);

    expect($stok->stokHabis())->toBeTrue();
    expect($stok->stokRendah())->toBeFalse();
});

it('stok model returns stokRendah true when kuantiti is at or below minimum', function () {
    $stok = StokToner::factory()->create([
        'jenis_toner' => JenisToner::Hitam,
        'kuantiti_ada' => 3,
        'kuantiti_minimum' => 5,
    ]);

    expect($stok->stokRendah())->toBeTrue();
    expect($stok->stokHabis())->toBeFalse();
});

it('stok model returns neither stokRendah nor stokHabis when stok is sufficient', function () {
    $stok = StokToner::factory()->create([
        'jenis_toner' => JenisToner::Hitam,
        'kuantiti_ada' => 10,
        'kuantiti_minimum' => 5,
    ]);

    expect($stok->stokRendah())->toBeFalse();
    expect($stok->stokHabis())->toBeFalse();
});

// ─── Search & Filter ─────────────────────────────────────────────────────────

it('filters by jenis toner', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    StokToner::factory()->create(['jenis_toner' => JenisToner::Hitam, 'model_toner' => 'A001', 'jenama' => 'HP', 'kuantiti_ada' => 5, 'kuantiti_minimum' => 2]);
    StokToner::factory()->create(['jenis_toner' => JenisToner::Cyan, 'model_toner' => 'B001', 'jenama' => 'Canon', 'kuantiti_ada' => 5, 'kuantiti_minimum' => 2]);

    $component = Livewire::actingAs($admin)
        ->test(InventoriStok::class)
        ->set('filterJenis', 'hitam');

    expect($component->get('stok')->total())->toBe(1);
});

it('searches by model toner and jenama', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    StokToner::factory()->create(['jenis_toner' => JenisToner::Hitam, 'model_toner' => 'CF217A', 'jenama' => 'HP', 'kuantiti_ada' => 5, 'kuantiti_minimum' => 2]);
    StokToner::factory()->create(['jenis_toner' => JenisToner::Hitam, 'model_toner' => 'TN2380', 'jenama' => 'Brother', 'kuantiti_ada' => 5, 'kuantiti_minimum' => 2]);

    $component = Livewire::actingAs($admin)
        ->test(InventoriStok::class)
        ->set('search', 'HP');

    expect($component->get('stok')->total())->toBe(1);
});
