<?php

use App\Enums\StatusAduan;
use App\Livewire\Permohonan\ButiranAduan;
use App\Models\AduanIct;
use App\Models\KategoriAduan;
use App\Models\LampiranAduan;
use App\Models\StatusLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->kategori = KategoriAduan::factory()->create([
        'nama' => 'Kerosakan Perkakasan',
        'unit_bpm' => 'Unit Infrastruktur',
        'is_aktif' => true,
    ]);
    $this->aduan = AduanIct::factory()->create([
        'user_id' => $this->user->id,
        'kategori_aduan_id' => $this->kategori->id,
        'tajuk' => 'Komputer tidak boleh dihidupkan',
        'lokasi' => 'Bilik 302, Aras 3',
        'keterangan' => 'Skrin hitam sejak pagi.',
        'no_telefon' => '03-88891234',
        'status' => StatusAduan::Baru,
    ]);
});

it('redirects unauthenticated users to login', function () {
    $this->get(route('aduan-ict.show', $this->aduan->id))->assertRedirect(route('login'));
});

it('renders the detail page for the ticket owner', function () {
    actingAs($this->user)
        ->get(route('aduan-ict.show', $this->aduan->id))
        ->assertOk();
});

it('shows ticket details', function () {
    actingAs($this->user);

    Livewire::test(ButiranAduan::class, ['id' => $this->aduan->id])
        ->assertSee($this->aduan->no_tiket)
        ->assertSee('Komputer tidak boleh dihidupkan')
        ->assertSee('Bilik 302, Aras 3')
        ->assertSee('Kerosakan Perkakasan')
        ->assertSee('Unit Infrastruktur');
});

it('shows status badge', function () {
    actingAs($this->user);

    Livewire::test(ButiranAduan::class, ['id' => $this->aduan->id])
        ->assertSee(StatusAduan::Baru->label());
});

it('shows status log history', function () {
    actingAs($this->user);

    StatusLog::create([
        'aduan_ict_id' => $this->aduan->id,
        'status' => StatusAduan::Baru,
        'catatan' => 'Aduan diterima.',
        'user_id' => $this->user->id,
    ]);

    StatusLog::create([
        'aduan_ict_id' => $this->aduan->id,
        'status' => StatusAduan::DalamProses,
        'catatan' => 'Sedang disemak oleh pegawai.',
        'user_id' => $this->user->id,
    ]);

    Livewire::test(ButiranAduan::class, ['id' => $this->aduan->id])
        ->assertSee('Aduan diterima.')
        ->assertSee('Sedang disemak oleh pegawai.')
        ->assertSee(StatusAduan::DalamProses->label());
});

it('shows attachment details', function () {
    actingAs($this->user);

    LampiranAduan::create([
        'aduan_ict_id' => $this->aduan->id,
        'nama_fail' => 'bukti-kerosakan.pdf',
        'path' => 'lampiran-aduan/bukti-kerosakan.pdf',
        'jenis_fail' => 'application/pdf',
        'saiz' => 512000,
    ]);

    Livewire::test(ButiranAduan::class, ['id' => $this->aduan->id])
        ->assertSee('bukti-kerosakan.pdf');
});

it('returns 404 when another user tries to view the ticket', function () {
    $otherUser = User::factory()->create();
    actingAs($otherUser);

    Livewire::test(ButiranAduan::class, ['id' => $this->aduan->id])
        ->assertStatus(404);
});

it('returns 404 for non-existent ticket', function () {
    actingAs($this->user);

    Livewire::test(ButiranAduan::class, ['id' => 99999])
        ->assertStatus(404);
});

it('shows pemohon maklumat', function () {
    actingAs($this->user);

    Livewire::test(ButiranAduan::class, ['id' => $this->aduan->id])
        ->assertSee($this->user->name)
        ->assertSee($this->user->email);
});
