<?php

use App\Enums\StatusPermohonanToner;
use App\Livewire\M02\BorangPermohonan;
use App\Models\PermohonanToner;
use App\Models\User;
use App\Notifications\PengesahanPermohonanToner;
use App\Notifications\PermohonanTonerBaru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

$validData = fn () => [
    'model_pencetak' => 'HP LaserJet Pro M404n',
    'jenama_toner' => 'HP',
    'jenis_toner' => 'hitam',
    'no_siri_toner' => '',
    'kuantiti' => 2,
    'lokasi_pencetak' => 'Tingkat 3, Bilik 304',
    'tujuan' => 'Toner habis dan perlu diganti untuk kegunaan pejabat.',
    'tarikh_diperlukan' => '',
];

it('renders borang permohonan for authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('m02.permohonan-baru'))
        ->assertStatus(200)
        ->assertSeeLivewire(BorangPermohonan::class);
});

it('auto-fills profile fields on mount', function () {
    $user = User::factory()->create([
        'name' => 'Ahmad Kamal',
        'jawatan' => 'Pegawai Teknologi Maklumat',
        'bahagian' => 'Bahagian Pengurusan Maklumat',
        'no_telefon' => '0123456789',
    ]);

    Livewire::actingAs($user)
        ->test(BorangPermohonan::class)
        ->assertSet('nama', 'Ahmad Kamal')
        ->assertSet('jawatan', 'Pegawai Teknologi Maklumat')
        ->assertSet('bahagian', 'Bahagian Pengurusan Maklumat')
        ->assertSet('no_telefon', '0123456789');
});

it('submits borang and creates permohonan with correct no_tiket format', function () use ($validData) {
    Notification::fake();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(BorangPermohonan::class)
        ->set($validData())
        ->call('hantar');

    $this->assertDatabaseHas('permohonan_toner', [
        'user_id' => $user->id,
        'model_pencetak' => 'HP LaserJet Pro M404n',
        'jenama_toner' => 'HP',
        'jenis_toner' => 'hitam',
        'kuantiti' => 2,
        'status' => StatusPermohonanToner::Submitted->value,
    ]);

    $permohonan = PermohonanToner::where('user_id', $user->id)->first();
    expect($permohonan->no_tiket)->toMatch('/^TON-\d{4}-\d{3}$/');
});

it('creates log entry on submit', function () use ($validData) {
    Notification::fake();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(BorangPermohonan::class)
        ->set($validData())
        ->call('hantar');

    $permohonan = PermohonanToner::where('user_id', $user->id)->first();

    $this->assertDatabaseHas('log_toner', [
        'permohonan_toner_id' => $permohonan->id,
        'tindakan' => 'submitted',
        'user_id' => $user->id,
    ]);
});

it('stores lampiran on submit', function () use ($validData) {
    Notification::fake();
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('toner.pdf', 500, 'application/pdf');

    Livewire::actingAs($user)
        ->test(BorangPermohonan::class)
        ->set($validData())
        ->set('lampiranFiles', [$file])
        ->call('hantar');

    $permohonan = PermohonanToner::where('user_id', $user->id)->first();

    $this->assertDatabaseHas('lampiran_toner', [
        'permohonan_toner_id' => $permohonan->id,
        'nama_fail' => 'toner.pdf',
    ]);
});

it('sends confirmation notification to user on submit', function () use ($validData) {
    Notification::fake();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(BorangPermohonan::class)
        ->set($validData())
        ->call('hantar');

    Notification::assertSentTo($user, PengesahanPermohonanToner::class);
});

it('sends new toner notification to admin on submit', function () use ($validData) {
    Notification::fake();

    $user = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($user)
        ->test(BorangPermohonan::class)
        ->set($validData())
        ->call('hantar');

    Notification::assertSentTo($admin, PermohonanTonerBaru::class);
});

it('fails validation when required fields are missing', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(BorangPermohonan::class)
        ->call('hantar')
        ->assertHasErrors([
            'model_pencetak',
            'jenama_toner',
            'jenis_toner',
            'lokasi_pencetak',
            'tujuan',
        ]);
});

it('fails validation when kuantiti is out of range', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(BorangPermohonan::class)
        ->set('kuantiti', 0)
        ->call('hantar')
        ->assertHasErrors(['kuantiti']);

    Livewire::actingAs($user)
        ->test(BorangPermohonan::class)
        ->set('kuantiti', 51)
        ->call('hantar')
        ->assertHasErrors(['kuantiti']);
});

it('fails validation when tujuan is too short or too long', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(BorangPermohonan::class)
        ->set('tujuan', 'Pendek')
        ->call('hantar')
        ->assertHasErrors(['tujuan']);

    Livewire::actingAs($user)
        ->test(BorangPermohonan::class)
        ->set('tujuan', str_repeat('a', 501))
        ->call('hantar')
        ->assertHasErrors(['tujuan']);
});

it('fails validation when tarikh_diperlukan is in the past', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(BorangPermohonan::class)
        ->set('tarikh_diperlukan', now()->subDay()->format('Y-m-d'))
        ->call('hantar')
        ->assertHasErrors(['tarikh_diperlukan']);
});

it('passes validation when tarikh_diperlukan is today', function () use ($validData) {
    Notification::fake();

    $user = User::factory()->create();

    $data = array_merge($validData(), ['tarikh_diperlukan' => now()->format('Y-m-d')]);

    Livewire::actingAs($user)
        ->test(BorangPermohonan::class)
        ->set($data)
        ->call('hantar')
        ->assertHasNoErrors(['tarikh_diperlukan']);
});

it('resets form fields after successful submission', function () use ($validData) {
    Notification::fake();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(BorangPermohonan::class)
        ->set($validData())
        ->call('hantar')
        ->assertSet('model_pencetak', '')
        ->assertSet('jenama_toner', '')
        ->assertSet('kuantiti', 1);
});
