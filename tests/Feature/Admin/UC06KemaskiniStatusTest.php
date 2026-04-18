<?php

use App\Enums\StatusAduan;
use App\Events\AduanSelesai;
use App\Events\StatusDikemaskini;
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
});

// ─── Kemaskini Status: Baru → Dalam Proses ────────────────────────────────────

it('pentadbir can update status from baru to dalam_proses', function () {
    Event::fake();

    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'status' => StatusAduan::Baru,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('statusBaru', StatusAduan::DalamProses->value)
        ->set('catatanTindakan', 'Sedang menyiasat masalah rangkaian di lokasi berkenaan.')
        ->call('kemaskiniStatus')
        ->assertHasNoErrors();

    expect($aduan->fresh()->status)->toBe(StatusAduan::DalamProses);
    expect($aduan->fresh()->pentadbir_id)->toBe($this->pentadbir->id);
    expect($aduan->fresh()->tarikh_selesai)->toBeNull();

    $this->assertDatabaseHas('status_log', [
        'aduan_ict_id' => $aduan->id,
        'status_lama' => StatusAduan::Baru->value,
        'status' => StatusAduan::DalamProses->value,
        'catatan' => 'Sedang menyiasat masalah rangkaian di lokasi berkenaan.',
        'user_id' => $this->pentadbir->id,
    ]);

    Event::assertDispatched(StatusDikemaskini::class);
    Event::assertNotDispatched(AduanSelesai::class);
});

// ─── Kemaskini Status: Dalam Proses → Selesai ────────────────────────────────

it('pentadbir can update status to selesai and tarikh_selesai is set', function () {
    Event::fake();

    $aduan = AduanIct::factory()->dalamProses()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('statusBaru', StatusAduan::Selesai->value)
        ->set('catatanTindakan', 'Masalah telah diselesaikan, komputer diganti baharu.')
        ->call('kemaskiniStatus')
        ->assertHasNoErrors();

    $fresh = $aduan->fresh();

    expect($fresh->status)->toBe(StatusAduan::Selesai);
    expect($fresh->tarikh_selesai)->not->toBeNull();

    $this->assertDatabaseHas('status_log', [
        'aduan_ict_id' => $aduan->id,
        'status_lama' => StatusAduan::DalamProses->value,
        'status' => StatusAduan::Selesai->value,
    ]);

    Event::assertDispatched(AduanSelesai::class);
    Event::assertNotDispatched(StatusDikemaskini::class);
});

// ─── Kemaskini Status: Baru → Selesai (kes mudah) ────────────────────────────

it('pentadbir can skip to selesai directly from baru', function () {
    Event::fake();

    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'status' => StatusAduan::Baru,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('statusBaru', StatusAduan::Selesai->value)
        ->set('catatanTindakan', 'Isu kecil, diselesaikan dengan restart komputer.')
        ->call('kemaskiniStatus')
        ->assertHasNoErrors();

    expect($aduan->fresh()->status)->toBe(StatusAduan::Selesai);
    expect($aduan->fresh()->tarikh_selesai)->not->toBeNull();

    Event::assertDispatched(AduanSelesai::class);
});

// ─── Validation ───────────────────────────────────────────────────────────────

it('rejects kemaskini without selecting a status', function () {
    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('statusBaru', '')
        ->set('catatanTindakan', 'Tindakan telah diambil dengan segera.')
        ->call('kemaskiniStatus')
        ->assertHasErrors(['statusBaru']);

    expect($aduan->fresh()->status)->toBe(StatusAduan::Baru);
});

it('rejects catatan tindakan shorter than 10 characters', function () {
    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('statusBaru', StatusAduan::DalamProses->value)
        ->set('catatanTindakan', 'Pendek')
        ->call('kemaskiniStatus')
        ->assertHasErrors(['catatanTindakan']);

    expect($aduan->fresh()->status)->toBe(StatusAduan::Baru);
});

it('rejects empty catatan tindakan', function () {
    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('statusBaru', StatusAduan::DalamProses->value)
        ->set('catatanTindakan', '')
        ->call('kemaskiniStatus')
        ->assertHasErrors(['catatanTindakan']);
});

it('rejects invalid status transition for selesai aduan', function () {
    $aduan = AduanIct::factory()->selesai()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('statusBaru', StatusAduan::Selesai->value)
        ->set('catatanTindakan', 'Cuba kemaskini status yang tidak dibenarkan.')
        ->call('kemaskiniStatus')
        ->assertHasErrors(['statusBaru']);
});

// ─── Buka Semula ──────────────────────────────────────────────────────────────

it('pentadbir can reopen a selesai aduan', function () {
    Event::fake();

    $aduan = AduanIct::factory()->selesai()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->call('bukaSemulaAduan');

    $fresh = $aduan->fresh();

    expect($fresh->status)->toBe(StatusAduan::DalamProses);
    expect($fresh->tarikh_selesai)->toBeNull();

    $this->assertDatabaseHas('status_log', [
        'aduan_ict_id' => $aduan->id,
        'status_lama' => StatusAduan::Selesai->value,
        'status' => StatusAduan::DalamProses->value,
        'catatan' => 'Dibuka semula',
    ]);

    Event::assertDispatched(StatusDikemaskini::class);
});

it('cannot reopen an aduan that is not selesai', function () {
    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
        'status' => StatusAduan::Baru,
    ]);

    actingAs($this->pentadbir);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->call('bukaSemulaAduan');

    expect($aduan->fresh()->status)->toBe(StatusAduan::Baru);
});

// ─── Authorization ────────────────────────────────────────────────────────────

it('pentadbir cannot access aduan from another unit to update status', function () {
    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitB->id,
    ]);

    actingAs($this->pentadbir)
        ->get(route('admin.aduan.show', $aduan->id))
        ->assertNotFound();
});

it('superadmin can update status of aduan from any unit', function () {
    Event::fake();

    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitB->id,
        'status' => StatusAduan::Baru,
    ]);

    actingAs($this->superadmin);

    Livewire::test(ButiranAduan::class, ['id' => $aduan->id])
        ->set('statusBaru', StatusAduan::DalamProses->value)
        ->set('catatanTindakan', 'Superadmin menangani aduan dari unit lain.')
        ->call('kemaskiniStatus')
        ->assertHasNoErrors();

    expect($aduan->fresh()->status)->toBe(StatusAduan::DalamProses);
});

it('pengguna biasa is blocked from admin panel', function () {
    actingAs($this->pengguna)
        ->get(route('admin.aduan.index'))
        ->assertForbidden();
});

// ─── Status Log ───────────────────────────────────────────────────────────────

it('creates one status_log entry per status change', function () {
    Event::fake();

    $aduan = AduanIct::factory()->create([
        'user_id' => $this->pengguna->id,
        'kategori_aduan_id' => $this->kategoriUnitA->id,
    ]);

    actingAs($this->pentadbir);
    $component = Livewire::test(ButiranAduan::class, ['id' => $aduan->id]);

    $component
        ->set('statusBaru', StatusAduan::DalamProses->value)
        ->set('catatanTindakan', 'Mula menyiasat masalah rangkaian pejabat.')
        ->call('kemaskiniStatus');

    $component
        ->set('statusBaru', StatusAduan::Selesai->value)
        ->set('catatanTindakan', 'Kabel rangkaian telah diganti, masalah selesai.')
        ->call('kemaskiniStatus');

    expect(StatusLog::where('aduan_ict_id', $aduan->id)->count())->toBe(2);
});
