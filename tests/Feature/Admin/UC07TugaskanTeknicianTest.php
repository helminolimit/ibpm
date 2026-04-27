<?php

use App\Enums\StatusAduan;
use App\Events\AduanDitugaskan;
use App\Livewire\Admin\ButiranAduan;
use App\Models\AduanIct;
use App\Models\KategoriAduan;
use App\Models\StatusLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->kategoriUnitA = KategoriAduan::factory()->create(['unit_bpm' => 'Unit Infrastruktur', 'is_aktif' => true]);
    $this->kategoriUnitB = KategoriAduan::factory()->create(['unit_bpm' => 'Unit Aplikasi', 'is_aktif' => true]);
    $this->pengguna = User::factory()->create();
    $this->pentadbir = User::factory()->pentadbir('Unit Infrastruktur')->create();
    $this->superadmin = User::factory()->superadmin()->create();
    $this->teknicianA = User::factory()->teknician('Unit Infrastruktur')->create();
    $this->teknicianB = User::factory()->teknician('Unit Aplikasi')->create();
});

// ─── Happy Path ───────────────────────────────────────────────────────────────

it('pentadbir can assign teknician and pentadbir_id is updated', function () {
    Event::fake();

    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'status' => StatusAduan::Baru,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('teknicianId', (string) $this->teknicianA->id)
        ->call('tugaskanTeknician')
        ->assertHasNoErrors();

    expect($aduan->fresh()->pentadbir_id)->toBe($this->teknicianA->id);
});

it('creates a status_log entry with teknician name on assignment', function () {
    Event::fake();

    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'status' => StatusAduan::DalamProses,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('teknicianId', (string) $this->teknicianA->id)
        ->call('tugaskanTeknician')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('status_log', [
        'aduan_ict_id' => $aduan->id,
        'status_lama' => StatusAduan::DalamProses->value,
        'status' => StatusAduan::DalamProses->value,
        'user_id' => $this->pentadbir->id,
    ]);

    $log = StatusLog::where('aduan_ict_id', $aduan->id)->latest()->first();
    expect($log->catatan)->toContain('Ditugaskan kepada: '.$this->teknicianA->name);
});

it('includes arahan pentadbir in status_log catatan when provided', function () {
    Event::fake();

    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'status' => StatusAduan::Baru,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('teknicianId', (string) $this->teknicianA->id)
        ->set('catatanArahan', 'Sila bawa peralatan ganti kabel.')
        ->call('tugaskanTeknician')
        ->assertHasNoErrors();

    $log = StatusLog::where('aduan_ict_id', $aduan->id)->latest()->first();
    expect($log->catatan)->toContain('Sila bawa peralatan ganti kabel.');
});

it('dispatches AduanDitugaskan event with teknician and arahan', function () {
    Event::fake();

    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'status' => StatusAduan::Baru,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('teknicianId', (string) $this->teknicianA->id)
        ->set('catatanArahan', 'Arahan ujian.')
        ->call('tugaskanTeknician');

    Event::assertDispatched(AduanDitugaskan::class, function ($event) {
        return $event->teknician->id === $this->teknicianA->id
            && $event->catatanArahan === 'Arahan ujian.';
    });
});

it('dispatches AduanDitugaskan without arahan when catatan is empty', function () {
    Event::fake();

    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'status' => StatusAduan::Baru,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('teknicianId', (string) $this->teknicianA->id)
        ->call('tugaskanTeknician');

    Event::assertDispatched(AduanDitugaskan::class, fn ($e) => $e->catatanArahan === null);
});

// ─── Reassignment ─────────────────────────────────────────────────────────────

it('can reassign to a different teknician', function () {
    Event::fake();

    $teknicianB2 = User::factory()->teknician('Unit Infrastruktur')->create();

    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'status' => StatusAduan::DalamProses,
        'pentadbir_id' => $this->teknicianA->id,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('teknicianId', (string) $teknicianB2->id)
        ->call('tugaskanTeknician')
        ->assertHasNoErrors();

    expect($aduan->fresh()->pentadbir_id)->toBe($teknicianB2->id);
});

// ─── Superadmin ───────────────────────────────────────────────────────────────

it('superadmin can assign teknician from any unit', function () {
    Event::fake();

    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitB->id,
        'status' => StatusAduan::Baru,
    ]);

    actingAs($this->superadmin);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('teknicianId', (string) $this->teknicianB->id)
        ->call('tugaskanTeknician')
        ->assertHasNoErrors();

    expect($aduan->fresh()->pentadbir_id)->toBe($this->teknicianB->id);
});

// ─── Validation ───────────────────────────────────────────────────────────────

it('rejects tugaskan without selecting a teknician', function () {
    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'status' => StatusAduan::Baru,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('teknicianId', '')
        ->call('tugaskanTeknician')
        ->assertHasErrors(['teknicianId']);

    expect($aduan->fresh()->pentadbir_id)->toBeNull();
});

it('rejects teknician id that does not exist', function () {
    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'status' => StatusAduan::Baru,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('teknicianId', '99999')
        ->call('tugaskanTeknician')
        ->assertHasErrors(['teknicianId']);
});

it('rejects catatan arahan longer than 500 characters', function () {
    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'status' => StatusAduan::Baru,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('teknicianId', (string) $this->teknicianA->id)
        ->set('catatanArahan', str_repeat('x', 501))
        ->call('tugaskanTeknician')
        ->assertHasErrors(['catatanArahan']);
});

// ─── Authorization / BR01 ─────────────────────────────────────────────────────

it('pentadbir cannot assign teknician from a different unit', function () {
    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'status' => StatusAduan::Baru,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('teknicianId', (string) $this->teknicianB->id)
        ->call('tugaskanTeknician');

    expect($aduan->fresh()->pentadbir_id)->toBeNull();
});

// ─── Status Guard ─────────────────────────────────────────────────────────────

it('cannot assign teknician when aduan is selesai', function () {
    $aduan = AduanIct::factory()->selesai()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('teknicianId', (string) $this->teknicianA->id)
        ->call('tugaskanTeknician');

    expect($aduan->fresh()->pentadbir_id)->toBeNull();
});

// ─── Available Teknicians ─────────────────────────────────────────────────────

it('availableTeknicians returns only teknicians in the same unit as pentadbir', function () {
    actingAs($this->pentadbir);

    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    $component = Livewire::test(ButiranAduan::class, ['id' => $aduan->id]);

    $teknicians = $component->instance()->availableTeknicians;

    expect($teknicians->pluck('id'))->toContain($this->teknicianA->id);
    expect($teknicians->pluck('id'))->not->toContain($this->teknicianB->id);
});

it('availableTeknicians returns all teknicians for superadmin', function () {
    actingAs($this->superadmin);

    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    $component = Livewire::test(ButiranAduan::class, ['id' => $aduan->id]);

    $teknicians = $component->instance()->availableTeknicians;

    expect($teknicians->pluck('id'))->toContain($this->teknicianA->id);
    expect($teknicians->pluck('id'))->toContain($this->teknicianB->id);
});

it('availableTeknicians returns empty collection when no teknicians in unit', function () {
    $pentadbirEmpty = User::factory()->pentadbir('Unit Tiada Teknician')->create();

    $kategoriEmpty = KategoriAduan::factory()->create(['unit_bpm' => 'Unit Tiada Teknician', 'is_aktif' => true]);

    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $kategoriEmpty->id,
    ]);

    actingAs($pentadbirEmpty);

    $component = Livewire::test(ButiranAduan::class, ['id' => $aduan->id]);

    expect($component->instance()->availableTeknicians)->toBeEmpty();
});
