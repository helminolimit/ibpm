<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

use App\Enums\StatusPermohonanEmel;
use App\Livewire\M06\HantarPermohonan;
use App\Models\KumpulanEmel;
use App\Models\PermohonanEmel;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create([
        'bahagian' => 'Bahagian Pengurusan Maklumat',
        'jawatan' => 'Pegawai Teknologi Maklumat',
        'no_telefon' => '03-88891234',
    ]);
    $this->kumpulan = KumpulanEmel::factory()->create();
});

it('renders for authenticated user', function () {
    actingAs($this->user)
        ->get(route('kumpulan-emel.create'))
        ->assertOk();
});

it('redirects unauthenticated users', function () {
    $this->get(route('kumpulan-emel.create'))
        ->assertRedirect(route('login'));
});

it('mounts with one empty ahli row', function () {
    actingAs($this->user);

    Livewire::test(HantarPermohonan::class)
        ->assertSet('step', 1)
        ->assertSet('ahli', [['nama_ahli' => '', 'emel_ahli' => '']]);
});

it('adds a new ahli row', function () {
    actingAs($this->user);

    Livewire::test(HantarPermohonan::class)
        ->call('tambahAhli')
        ->assertCount('ahli', 2);
});

it('removes an ahli row', function () {
    actingAs($this->user);

    Livewire::test(HantarPermohonan::class)
        ->call('tambahAhli')
        ->assertCount('ahli', 2)
        ->call('buangAhli', 1)
        ->assertCount('ahli', 1);
});

it('fails validation when kumpulan not selected', function () {
    actingAs($this->user);

    Livewire::test(HantarPermohonan::class)
        ->set('jenisTindakan', 'tambah')
        ->set('ahli', [['nama_ahli' => 'Ali', 'emel_ahli' => 'ali@example.com']])
        ->call('teruskan')
        ->assertHasErrors(['kumpulanEmelId' => 'required']);
});

it('fails validation when jenis tindakan not selected', function () {
    actingAs($this->user);

    Livewire::test(HantarPermohonan::class)
        ->set('kumpulanEmelId', $this->kumpulan->id)
        ->set('ahli', [['nama_ahli' => 'Ali', 'emel_ahli' => 'ali@example.com']])
        ->call('teruskan')
        ->assertHasErrors(['jenisTindakan' => 'required']);
});

it('fails validation when ahli list is empty', function () {
    actingAs($this->user);

    Livewire::test(HantarPermohonan::class)
        ->set('kumpulanEmelId', $this->kumpulan->id)
        ->set('jenisTindakan', 'tambah')
        ->set('ahli', [])
        ->call('teruskan')
        ->assertHasErrors(['ahli' => 'required']);
});

it('fails validation when ahli email is invalid', function () {
    actingAs($this->user);

    Livewire::test(HantarPermohonan::class)
        ->set('kumpulanEmelId', $this->kumpulan->id)
        ->set('jenisTindakan', 'tambah')
        ->set('ahli', [['nama_ahli' => 'Ali', 'emel_ahli' => 'bukan-emel']])
        ->call('teruskan')
        ->assertHasErrors(['ahli.0.emel_ahli' => 'email']);
});

it('advances to step 2 on valid input', function () {
    actingAs($this->user);

    Livewire::test(HantarPermohonan::class)
        ->set('kumpulanEmelId', $this->kumpulan->id)
        ->set('jenisTindakan', 'tambah')
        ->set('ahli', [['nama_ahli' => 'Ali Ahmad', 'emel_ahli' => 'ali@example.com']])
        ->call('teruskan')
        ->assertSet('step', 2)
        ->assertHasNoErrors();
});

it('returns to step 1 on balik', function () {
    actingAs($this->user);

    Livewire::test(HantarPermohonan::class)
        ->set('kumpulanEmelId', $this->kumpulan->id)
        ->set('jenisTindakan', 'tambah')
        ->set('ahli', [['nama_ahli' => 'Ali Ahmad', 'emel_ahli' => 'ali@example.com']])
        ->call('teruskan')
        ->assertSet('step', 2)
        ->call('balik')
        ->assertSet('step', 1);
});

it('creates permohonan_emel and ahli_kumpulan records on submit', function () {
    actingAs($this->user);

    Livewire::test(HantarPermohonan::class)
        ->set('kumpulanEmelId', $this->kumpulan->id)
        ->set('jenisTindakan', 'tambah')
        ->set('ahli', [
            ['nama_ahli' => 'Ali Ahmad', 'emel_ahli' => 'ali@example.com'],
            ['nama_ahli' => 'Siti Rahimah', 'emel_ahli' => 'siti@example.com'],
        ])
        ->call('teruskan')
        ->call('hantar')
        ->assertSet('step', 3);

    $this->assertDatabaseCount('permohonan_emel', 1);
    $this->assertDatabaseCount('ahli_kumpulan', 2);

    $permohonan = PermohonanEmel::first();
    expect($permohonan->user_id)->toBe($this->user->id)
        ->and($permohonan->kumpulan_emel_id)->toBe($this->kumpulan->id)
        ->and($permohonan->status)->toBe(StatusPermohonanEmel::Baru)
        ->and($permohonan->jenis_tindakan->value)->toBe('tambah');
});

it('generates ticket in GRP-YYYY-NNN format', function () {
    actingAs($this->user);

    Livewire::test(HantarPermohonan::class)
        ->set('kumpulanEmelId', $this->kumpulan->id)
        ->set('jenisTindakan', 'buang')
        ->set('ahli', [['nama_ahli' => 'Ali Ahmad', 'emel_ahli' => 'ali@example.com']])
        ->call('teruskan')
        ->call('hantar');

    $noTiket = PermohonanEmel::first()->no_tiket;
    expect($noTiket)->toMatch('/^GRP-\d{4}-\d{3}$/');
});

it('status defaults to baru on submission', function () {
    actingAs($this->user);

    Livewire::test(HantarPermohonan::class)
        ->set('kumpulanEmelId', $this->kumpulan->id)
        ->set('jenisTindakan', 'tambah')
        ->set('ahli', [['nama_ahli' => 'Ali Ahmad', 'emel_ahli' => 'ali@example.com']])
        ->call('teruskan')
        ->call('hantar');

    expect(PermohonanEmel::first()->status)->toBe(StatusPermohonanEmel::Baru);
});
