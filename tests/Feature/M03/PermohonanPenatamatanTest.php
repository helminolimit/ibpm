<?php

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

// UC-M03-01: Pemohon boleh hantar borang penamatan
test('pemohon boleh hantar permohonan penamatan akaun', function () {
    $pemohon = User::factory()->create();
    $sasaran = User::factory()->create();

    actingAs($pemohon)
        ->post(route('penamatan-akaun.store'), [
            'pengguna_sasaran_id' => $sasaran->id,
            'id_login_komputer' => 'test.user',
            'tarikh_berkuat_kuasa' => now()->addDay()->format('Y-m-d'),
            'jenis_tindakan' => 'TAMAT',
            'sebab_penamatan' => 'Kakitangan telah bersara dari perkhidmatan.',
        ])
        ->assertRedirect(route('penamatan-akaun.index'));

    $this->assertDatabaseHas('permohonan_penamatan', [
        'pemohon_id' => $pemohon->id,
        'pengguna_sasaran_id' => $sasaran->id,
        'id_login_komputer' => 'test.user',
        'status' => 'MENUNGGU_KEL_1',
    ]);
});

// UC-M03-02: No tiket dijana automatik format PAK-YYYY-NNN
test('nombor tiket dijana automatik dengan format PAK-YYYY-NNN', function () {
    $pemohon = User::factory()->create();
    $sasaran = User::factory()->create();

    actingAs($pemohon)
        ->post(route('penamatan-akaun.store'), [
            'pengguna_sasaran_id' => $sasaran->id,
            'id_login_komputer' => 'user.bersara',
            'tarikh_berkuat_kuasa' => now()->addDay()->format('Y-m-d'),
            'jenis_tindakan' => 'TAMAT',
            'sebab_penamatan' => 'Kakitangan telah bersara dari perkhidmatan.',
        ]);

    $permohonan = PermohonanPenamatan::where('pemohon_id', $pemohon->id)->first();

    expect($permohonan->no_tiket)->toMatch('/^PAK-\d{4}-\d{3}$/');
});

// UC-M03-02: Tiket kedua adalah nombor berikutnya
test('tiket dijana secara berurutan', function () {
    $pemohon = User::factory()->create();
    $sasaran = User::factory()->create();

    $data = [
        'pengguna_sasaran_id' => $sasaran->id,
        'id_login_komputer' => 'user.satu',
        'tarikh_berkuat_kuasa' => now()->addDay()->format('Y-m-d'),
        'jenis_tindakan' => 'TAMAT',
        'sebab_penamatan' => 'Kakitangan telah bersara dari perkhidmatan.',
    ];

    actingAs($pemohon)->post(route('penamatan-akaun.store'), $data);
    actingAs($pemohon)->post(route('penamatan-akaun.store'), array_merge($data, ['id_login_komputer' => 'user.dua']));

    $tikets = PermohonanPenamatan::where('pemohon_id', $pemohon->id)
        ->orderBy('id')
        ->pluck('no_tiket')
        ->toArray();

    expect($tikets[0])->toEndWith('-001');
    expect($tikets[1])->toEndWith('-002');
});

// UC-M03-03: Pemohon boleh semak status permohonan sendiri
test('pemohon boleh lihat butiran permohonan sendiri', function () {
    $pemohon = User::factory()->create();
    $permohonan = PermohonanPenamatan::factory()->create(['pemohon_id' => $pemohon->id]);

    actingAs($pemohon)
        ->get(route('penamatan-akaun.show', $permohonan))
        ->assertOk()
        ->assertSee($permohonan->no_tiket);
});

// UC-M03-03: Pemohon TIDAK boleh lihat permohonan orang lain
test('pemohon tidak boleh lihat permohonan orang lain', function () {
    $pemohon1 = User::factory()->create();
    $pemohon2 = User::factory()->create();
    $permohonan = PermohonanPenamatan::factory()->create(['pemohon_id' => $pemohon2->id]);

    actingAs($pemohon1)
        ->get(route('penamatan-akaun.show', $permohonan))
        ->assertNotFound();
});

// UC-M03-04: Notifikasi emel dihantar selepas permohonan dihantar
test('notifikasi emel dihantar kepada pemohon selepas hantar permohonan', function () {
    $pemohon = User::factory()->create();
    $sasaran = User::factory()->create();

    actingAs($pemohon)
        ->post(route('penamatan-akaun.store'), [
            'pengguna_sasaran_id' => $sasaran->id,
            'id_login_komputer' => 'user.test',
            'tarikh_berkuat_kuasa' => now()->addDay()->format('Y-m-d'),
            'jenis_tindakan' => 'TAMAT',
            'sebab_penamatan' => 'Kakitangan telah bersara dari perkhidmatan.',
        ]);

    Notification::assertSentTo($pemohon, PenatamatanNotification::class, function ($notif) {
        return $notif->jenis === 'HANTAR';
    });
});

// Validasi: pemohon tidak boleh hantar borang tanpa sebab
test('validasi gagal jika sebab penamatan kurang dari 10 aksara', function () {
    $pemohon = User::factory()->create();
    $sasaran = User::factory()->create();

    actingAs($pemohon)
        ->post(route('penamatan-akaun.store'), [
            'pengguna_sasaran_id' => $sasaran->id,
            'id_login_komputer' => 'user.test',
            'tarikh_berkuat_kuasa' => now()->addDay()->format('Y-m-d'),
            'jenis_tindakan' => 'TAMAT',
            'sebab_penamatan' => 'Pendek',
        ])
        ->assertSessionHasErrors('sebab_penamatan');
});

// Validasi: pemohon tidak boleh mohon penamatan akaun sendiri
test('pemohon tidak boleh mohon penamatan akaun sendiri', function () {
    $pemohon = User::factory()->create();

    actingAs($pemohon)
        ->post(route('penamatan-akaun.store'), [
            'pengguna_sasaran_id' => $pemohon->id,
            'id_login_komputer' => 'user.test',
            'tarikh_berkuat_kuasa' => now()->addDay()->format('Y-m-d'),
            'jenis_tindakan' => 'TAMAT',
            'sebab_penamatan' => 'Kakitangan telah bersara dari perkhidmatan.',
        ])
        ->assertSessionHasErrors('pengguna_sasaran_id');
});

// UC-M03-08: Log audit dicipta apabila permohonan dihantar
test('log audit dicipta apabila permohonan dihantar', function () {
    $pemohon = User::factory()->create();
    $sasaran = User::factory()->create();

    actingAs($pemohon)
        ->post(route('penamatan-akaun.store'), [
            'pengguna_sasaran_id' => $sasaran->id,
            'id_login_komputer' => 'user.audit',
            'tarikh_berkuat_kuasa' => now()->addDay()->format('Y-m-d'),
            'jenis_tindakan' => 'TAMAT',
            'sebab_penamatan' => 'Kakitangan telah bersara dari perkhidmatan.',
        ]);

    $permohonan = PermohonanPenamatan::where('pemohon_id', $pemohon->id)->first();

    $this->assertDatabaseHas('log_audits', [
        'permohonan_penamatan_id' => $permohonan->id,
        'pengguna_id' => $pemohon->id,
        'tindakan' => 'permohonan_dihantar',
        'modul' => 'M03',
    ]);
});
