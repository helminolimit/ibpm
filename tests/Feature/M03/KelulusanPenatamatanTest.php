<?php

use App\Enums\RolePengguna;
use App\Enums\StatusPermohonanPenamatan;
use App\Models\PermohonanPenamatan;
use App\Models\User;
use App\Notifications\PenatamatanNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    Notification::fake();
});

// UC-M03-05: Pelulus1 boleh luluskan permohonan peringkat 1
test('pelulus1 boleh luluskan permohonan dan status bertukar kepada menunggu kel 2', function () {
    $pelulus = User::factory()->create(['role' => RolePengguna::Pelulus1]);
    $permohonan = PermohonanPenamatan::factory()->menungguKel1()->create();

    actingAs($pelulus)
        ->patch(route('kelulusan.penamatan.lulus', $permohonan->id))
        ->assertRedirect(route('kelulusan.penamatan.index'));

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanPenamatan::MenungguKel2);

    $this->assertDatabaseHas('kelulusan_penamatan', [
        'permohonan_penamatan_id' => $permohonan->id,
        'pelulus_id' => $pelulus->id,
        'peringkat' => 'PERINGKAT_1',
        'keputusan' => 'LULUS',
    ]);
});

// UC-M03-05: Pelulus1 boleh tolak permohonan dengan catatan
test('pelulus1 boleh tolak permohonan dan status bertukar kepada ditolak', function () {
    $pelulus = User::factory()->create(['role' => RolePengguna::Pelulus1]);
    $permohonan = PermohonanPenamatan::factory()->menungguKel1()->create();

    actingAs($pelulus)
        ->patch(route('kelulusan.penamatan.tolak', $permohonan->id), [
            'catatan' => 'Maklumat tidak lengkap untuk diproses.',
        ])
        ->assertRedirect(route('kelulusan.penamatan.index'));

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanPenamatan::Ditolak);

    $this->assertDatabaseHas('kelulusan_penamatan', [
        'permohonan_penamatan_id' => $permohonan->id,
        'pelulus_id' => $pelulus->id,
        'peringkat' => 'PERINGKAT_1',
        'keputusan' => 'TOLAK',
    ]);
});

// UC-M03-04: Notifikasi dihantar kepada pemohon selepas kelulusan peringkat 1
test('notifikasi dihantar kepada pemohon selepas kelulusan peringkat 1', function () {
    $pelulus = User::factory()->create(['role' => RolePengguna::Pelulus1]);
    $pemohon = User::factory()->create();
    $permohonan = PermohonanPenamatan::factory()->menungguKel1()->create(['pemohon_id' => $pemohon->id]);

    actingAs($pelulus)
        ->patch(route('kelulusan.penamatan.lulus', $permohonan->id));

    Notification::assertSentTo($pemohon, PenatamatanNotification::class, function ($notif) {
        return $notif->jenis === 'KELULUSAN';
    });
});

// Validasi: penolakan gagal jika catatan terlalu pendek
test('penolakan gagal jika catatan kurang dari 5 aksara', function () {
    $pelulus = User::factory()->create(['role' => RolePengguna::Pelulus1]);
    $permohonan = PermohonanPenamatan::factory()->menungguKel1()->create();

    actingAs($pelulus)
        ->patch(route('kelulusan.penamatan.tolak', $permohonan->id), [
            'catatan' => 'Ok',
        ])
        ->assertSessionHasErrors('catatan');

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanPenamatan::MenungguKel1);
});

// UC-M03-06: Pentadbir boleh luluskan peringkat 2
test('pentadbir boleh luluskan permohonan peringkat 2 dan status bertukar kepada dalam proses', function () {
    $admin = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $permohonan = PermohonanPenamatan::factory()->menungguKel2()->create();

    actingAs($admin)
        ->patch(route('admin.penamatan.lulus', $permohonan->id))
        ->assertRedirect(route('admin.penamatan.index'));

    expect($permohonan->fresh()->status)->toBe(StatusPermohonanPenamatan::DalamProses);

    $this->assertDatabaseHas('kelulusan_penamatan', [
        'permohonan_penamatan_id' => $permohonan->id,
        'pelulus_id' => $admin->id,
        'peringkat' => 'PERINGKAT_2',
        'keputusan' => 'LULUS',
    ]);
});

// UC-M03-07: Pentadbir boleh tandakan selesai
test('pentadbir boleh tandakan permohonan selesai dan tarikh selesai direkod', function () {
    $admin = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $permohonan = PermohonanPenamatan::factory()->dalamProses()->create();

    actingAs($admin)
        ->patch(route('admin.penamatan.selesai', $permohonan->id))
        ->assertRedirect(route('admin.penamatan.index'));

    $fresh = $permohonan->fresh();
    expect($fresh->status)->toBe(StatusPermohonanPenamatan::Selesai);
    expect($fresh->tarikh_selesai)->not->toBeNull();
});

// UC-M03-04: Notifikasi selesai dihantar kepada pemohon
test('notifikasi selesai dihantar kepada pemohon apabila akaun ditamatkan', function () {
    $admin = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $pemohon = User::factory()->create();
    $permohonan = PermohonanPenamatan::factory()->dalamProses()->create(['pemohon_id' => $pemohon->id]);

    actingAs($admin)
        ->patch(route('admin.penamatan.selesai', $permohonan->id));

    Notification::assertSentTo($pemohon, PenatamatanNotification::class, function ($notif) {
        return $notif->jenis === 'SELESAI';
    });
});

// UC-M03-08: Log audit dicipta pada kelulusan peringkat 2
test('log audit dicipta apabila pentadbir luluskan peringkat 2', function () {
    $admin = User::factory()->create(['role' => RolePengguna::Pentadbir]);
    $permohonan = PermohonanPenamatan::factory()->menungguKel2()->create();

    actingAs($admin)
        ->patch(route('admin.penamatan.lulus', $permohonan->id));

    $this->assertDatabaseHas('log_audits', [
        'permohonan_penamatan_id' => $permohonan->id,
        'pengguna_id' => $admin->id,
        'tindakan' => 'kelulusan_peringkat_2',
        'modul' => 'M03',
    ]);
});

// Kawalan akses: pengguna biasa tidak boleh akses kelulusan
test('pengguna biasa tidak boleh akses laluan kelulusan peringkat 1', function () {
    $pengguna = User::factory()->create(['role' => RolePengguna::Pengguna]);
    $permohonan = PermohonanPenamatan::factory()->menungguKel1()->create();

    actingAs($pengguna)
        ->patch(route('kelulusan.penamatan.lulus', $permohonan->id))
        ->assertForbidden();
});

// Kawalan akses: pengguna biasa tidak boleh akses admin
test('pengguna biasa tidak boleh akses laluan admin penamatan', function () {
    $pengguna = User::factory()->create(['role' => RolePengguna::Pengguna]);
    $permohonan = PermohonanPenamatan::factory()->menungguKel2()->create();

    actingAs($pengguna)
        ->patch(route('admin.penamatan.lulus', $permohonan->id))
        ->assertForbidden();
});
