<?php

use App\Enums\RolePengguna;
use App\Enums\StatusPengguna;
use App\Livewire\Superadmin\SenaraiPengguna;
use App\Mail\AkaunAktif;
use App\Mail\AkaunBaharu;
use App\Mail\AkaunTidakAktif;
use App\Mail\PerananDikemaskini;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('superadmin dapat lihat senarai pengguna', function () {
    $superadmin = User::factory()->superadmin()->create();

    $this->actingAs($superadmin)
        ->get(route('superadmin.pengguna.index'))
        ->assertOk();
});

it('bukan superadmin dapat 403', function () {
    $pentadbir = User::factory()->pentadbir()->create();

    $this->actingAs($pentadbir)
        ->get(route('superadmin.pengguna.index'))
        ->assertForbidden();
});

it('superadmin boleh tambah pengguna baharu', function () {
    Mail::fake();
    $superadmin = User::factory()->superadmin()->create();

    Livewire::actingAs($superadmin)
        ->test(SenaraiPengguna::class)
        ->call('bukaTambah')
        ->set('name', 'Ahmad Hafizi')
        ->set('email', 'hafizi@motac.gov.my')
        ->set('jawatan', 'Pegawai Teknologi Maklumat')
        ->set('bahagian', 'Bahagian ICT')
        ->set('role', RolePengguna::Pengguna->value)
        ->set('status', StatusPengguna::Pending->value)
        ->set('password', 'password123')
        ->call('simpanPengguna')
        ->assertHasNoErrors();

    expect(User::where('email', 'hafizi@motac.gov.my')->exists())->toBeTrue();
    expect(AuditLog::where('action', 'Tambah Pengguna')->exists())->toBeTrue();
    Mail::assertQueued(AkaunBaharu::class);
});

it('validation gagal jika email sudah wujud', function () {
    $superadmin = User::factory()->superadmin()->create();
    User::factory()->create(['email' => 'existing@motac.gov.my']);

    Livewire::actingAs($superadmin)
        ->test(SenaraiPengguna::class)
        ->call('bukaTambah')
        ->set('name', 'Test')
        ->set('email', 'existing@motac.gov.my')
        ->set('jawatan', 'Pegawai')
        ->set('bahagian', 'Bahagian A')
        ->set('password', 'password123')
        ->call('simpanPengguna')
        ->assertHasErrors(['email']);
});

it('superadmin boleh kemaskini maklumat pengguna', function () {
    Mail::fake();
    $superadmin = User::factory()->superadmin()->create();
    $pengguna = User::factory()->create();

    Livewire::actingAs($superadmin)
        ->test(SenaraiPengguna::class)
        ->call('bukaEdit', $pengguna->id)
        ->set('name', 'Nama Baharu')
        ->set('jawatan', 'Jawatan Baharu')
        ->call('kemaskiniPengguna')
        ->assertHasNoErrors();

    expect($pengguna->fresh()->name)->toBe('Nama Baharu');
    expect(AuditLog::where('action', 'Kemaskini Pengguna')->exists())->toBeTrue();
});

it('penghantaran emel peranan apabila peranan bertukar', function () {
    Mail::fake();
    $superadmin = User::factory()->superadmin()->create();
    $pengguna = User::factory()->create(['role' => RolePengguna::Pengguna]);

    Livewire::actingAs($superadmin)
        ->test(SenaraiPengguna::class)
        ->call('bukaEdit', $pengguna->id)
        ->set('role', RolePengguna::Pentadbir->value)
        ->call('kemaskiniPengguna')
        ->assertHasNoErrors();

    Mail::assertQueued(PerananDikemaskini::class);
});

it('superadmin boleh lulus akaun pending', function () {
    Mail::fake();
    $superadmin = User::factory()->superadmin()->create();
    $pending = User::factory()->create(['status' => StatusPengguna::Pending]);

    Livewire::actingAs($superadmin)
        ->test(SenaraiPengguna::class)
        ->call('lulusPending', $pending->id);

    expect($pending->fresh()->status)->toBe(StatusPengguna::Aktif);
    expect(AuditLog::where('action', 'Lulus Akaun')->exists())->toBeTrue();
    Mail::assertQueued(AkaunAktif::class);
});

it('superadmin boleh togol status pengguna', function () {
    Mail::fake();
    $superadmin = User::factory()->superadmin()->create();
    $pengguna = User::factory()->create(['status' => StatusPengguna::Aktif]);

    Livewire::actingAs($superadmin)
        ->test(SenaraiPengguna::class)
        ->call('konfirmStatus', $pengguna->id)
        ->call('togolStatus');

    expect($pengguna->fresh()->status)->toBe(StatusPengguna::TidakAktif);
    expect(AuditLog::where('action', 'Togol Status')->exists())->toBeTrue();
    Mail::assertQueued(AkaunTidakAktif::class);
});

it('superadmin tidak boleh nyahaktifkan akaun sendiri', function () {
    $superadmin = User::factory()->superadmin()->create(['status' => StatusPengguna::Aktif]);

    Livewire::actingAs($superadmin)
        ->test(SenaraiPengguna::class)
        ->call('konfirmStatus', $superadmin->id)
        ->call('togolStatus');

    expect($superadmin->fresh()->status)->toBe(StatusPengguna::Aktif);
});

it('superadmin boleh padam pengguna dengan soft delete', function () {
    $superadmin = User::factory()->superadmin()->create();
    $pengguna = User::factory()->create();

    Livewire::actingAs($superadmin)
        ->test(SenaraiPengguna::class)
        ->call('konfirmPadam', $pengguna->id, $pengguna->name)
        ->call('padamPengguna');

    expect(User::find($pengguna->id))->toBeNull();
    expect(User::withTrashed()->find($pengguna->id))->not->toBeNull();
    expect(AuditLog::where('action', 'Padam Pengguna')->exists())->toBeTrue();
});

it('tidak boleh padam superadmin aktif', function () {
    $superadmin = User::factory()->superadmin()->create(['status' => StatusPengguna::Aktif]);
    $target = User::factory()->superadmin()->create(['status' => StatusPengguna::Aktif]);

    Livewire::actingAs($superadmin)
        ->test(SenaraiPengguna::class)
        ->call('konfirmPadam', $target->id, $target->name)
        ->call('padamPengguna');

    expect(User::find($target->id))->not->toBeNull();
});
