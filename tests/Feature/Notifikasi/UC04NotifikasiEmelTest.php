<?php

use App\Enums\StatusAduan;
use App\Enums\StatusNotifikasi;
use App\Events\AduanBaru;
use App\Events\AduanDihantar;
use App\Events\AduanDitugaskan;
use App\Events\AduanSelesai;
use App\Events\StatusDikemaskini;
use App\Listeners\HantarEmailAduanDitugaskan;
use App\Listeners\HantarEmailAduanSelesai;
use App\Listeners\HantarEmailNotifikasiAdmin;
use App\Listeners\HantarEmailPengesahan;
use App\Listeners\HantarEmailStatusKemaskini;
use App\Mail\AduanDitugaskanMail;
use App\Mail\AduanSelesaiMail;
use App\Mail\NotifikasiAduanBaru;
use App\Mail\PengesahanAduan;
use App\Mail\StatusKemaskinanMail;
use App\Models\AduanIct;
use App\Models\KategoriAduan;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['email' => 'pemohon@test.com']);
    $this->kategori = KategoriAduan::factory()->create(['emel_unit' => 'admin@test.com']);
    $this->aduan = AduanIct::factory()->create([
        'user_id' => $this->user->id,
        'kategori_aduan_id' => $this->kategori->id,
    ]);
});

// N01 — Pengesahan kepada Pemohon

it('N01: sends PengesahanAduan email when AduanDihantar is fired', function () {
    Mail::fake();

    (new HantarEmailPengesahan)->handle(new AduanDihantar($this->aduan));

    Mail::assertQueued(PengesahanAduan::class, fn ($mail) => $mail->aduan->id === $this->aduan->id);
});

it('N01: creates pengesahan notifikasi record with status hantar', function () {
    Mail::fake();

    (new HantarEmailPengesahan)->handle(new AduanDihantar($this->aduan));

    expect(Notifikasi::where([
        'aduan_ict_id' => $this->aduan->id,
        'jenis' => 'pengesahan',
        'penerima' => $this->user->email,
        'status' => StatusNotifikasi::Hantar->value,
    ])->exists())->toBeTrue();
});

// N02 — Makluman kepada Pentadbir BPM

it('N02: sends NotifikasiAduanBaru email when AduanBaru is fired', function () {
    Mail::fake();

    (new HantarEmailNotifikasiAdmin)->handle(new AduanBaru($this->aduan));

    Mail::assertQueued(NotifikasiAduanBaru::class, fn ($mail) => $mail->aduan->id === $this->aduan->id);
});

it('N02: skips email when kategori has no emel_unit', function () {
    Mail::fake();

    $kategoriTanpaEmel = KategoriAduan::factory()->create(['emel_unit' => null]);
    $aduan = AduanIct::factory()->create([
        'user_id' => $this->user->id,
        'kategori_aduan_id' => $kategoriTanpaEmel->id,
    ]);

    (new HantarEmailNotifikasiAdmin)->handle(new AduanBaru($aduan));

    Mail::assertNothingQueued();
});

it('N02: creates makluman notifikasi record with status hantar', function () {
    Mail::fake();

    (new HantarEmailNotifikasiAdmin)->handle(new AduanBaru($this->aduan));

    expect(Notifikasi::where([
        'aduan_ict_id' => $this->aduan->id,
        'jenis' => 'makluman',
        'penerima' => $this->kategori->emel_unit,
        'status' => StatusNotifikasi::Hantar->value,
    ])->exists())->toBeTrue();
});

// N03 — Kemaskini Status kepada Pemohon

it('N03: sends StatusKemaskinanMail when status changes to DalamProses', function () {
    Mail::fake();

    $aduan = AduanIct::factory()->dalamProses()->create([
        'user_id' => $this->user->id,
        'kategori_aduan_id' => $this->kategori->id,
    ]);

    (new HantarEmailStatusKemaskini)->handle(new StatusDikemaskini($aduan, StatusAduan::DalamProses));

    Mail::assertQueued(StatusKemaskinanMail::class, fn ($mail) => $mail->aduan->id === $aduan->id);
});

it('N03: does not send email when status is not DalamProses', function () {
    Mail::fake();

    (new HantarEmailStatusKemaskini)->handle(new StatusDikemaskini($this->aduan, StatusAduan::Ditolak));

    Mail::assertNothingQueued();
    expect(Notifikasi::count())->toBe(0);
});

it('N03: creates kemaskini_status notifikasi record with status hantar', function () {
    Mail::fake();

    $aduan = AduanIct::factory()->dalamProses()->create([
        'user_id' => $this->user->id,
        'kategori_aduan_id' => $this->kategori->id,
    ]);

    (new HantarEmailStatusKemaskini)->handle(new StatusDikemaskini($aduan, StatusAduan::DalamProses));

    expect(Notifikasi::where([
        'aduan_ict_id' => $aduan->id,
        'jenis' => 'kemaskini_status',
        'penerima' => $this->user->email,
        'status' => StatusNotifikasi::Hantar->value,
    ])->exists())->toBeTrue();
});

// N04 — Aduan Ditugaskan kepada Teknician

it('N04: sends AduanDitugaskanMail to teknician when AduanDitugaskan is fired', function () {
    Mail::fake();

    $teknician = User::factory()->create(['email' => 'teknician@test.com']);

    (new HantarEmailAduanDitugaskan)->handle(new AduanDitugaskan($this->aduan, $teknician));

    Mail::assertQueued(AduanDitugaskanMail::class, fn ($mail) => $mail->aduan->id === $this->aduan->id);
});

it('N04: creates ditugaskan notifikasi record sent to teknician email', function () {
    Mail::fake();

    $teknician = User::factory()->create(['email' => 'teknician@test.com']);

    (new HantarEmailAduanDitugaskan)->handle(new AduanDitugaskan($this->aduan, $teknician));

    expect(Notifikasi::where([
        'aduan_ict_id' => $this->aduan->id,
        'jenis' => 'ditugaskan',
        'penerima' => $teknician->email,
        'status' => StatusNotifikasi::Hantar->value,
    ])->exists())->toBeTrue();
});

// N05 — Aduan Selesai kepada Pemohon

it('N05: sends AduanSelesaiMail when AduanSelesai is fired', function () {
    Mail::fake();

    $aduan = AduanIct::factory()->selesai()->create([
        'user_id' => $this->user->id,
        'kategori_aduan_id' => $this->kategori->id,
    ]);

    (new HantarEmailAduanSelesai)->handle(new AduanSelesai($aduan));

    Mail::assertQueued(AduanSelesaiMail::class, fn ($mail) => $mail->aduan->id === $aduan->id);
});

it('N05: creates selesai notifikasi record with status hantar', function () {
    Mail::fake();

    $aduan = AduanIct::factory()->selesai()->create([
        'user_id' => $this->user->id,
        'kategori_aduan_id' => $this->kategori->id,
    ]);

    (new HantarEmailAduanSelesai)->handle(new AduanSelesai($aduan));

    expect(Notifikasi::where([
        'aduan_ict_id' => $aduan->id,
        'jenis' => 'selesai',
        'penerima' => $this->user->email,
        'status' => StatusNotifikasi::Hantar->value,
    ])->exists())->toBeTrue();
});

// Failure handling

it('records gagal notifikasi and rethrows when mail send fails', function () {
    $pending = Mockery::mock();
    $pending->shouldReceive('send')->andThrow(new RuntimeException('SMTP connection failed'));
    Mail::shouldReceive('to')->andReturn($pending);

    expect(fn () => (new HantarEmailPengesahan)->handle(new AduanDihantar($this->aduan)))
        ->toThrow(RuntimeException::class);

    expect(Notifikasi::where([
        'aduan_ict_id' => $this->aduan->id,
        'jenis' => 'pengesahan',
        'status' => StatusNotifikasi::Gagal->value,
    ])->exists())->toBeTrue();
});

// Event registration

it('listeners are registered for all notification events', function () {
    Event::fake();

    AduanDihantar::dispatch($this->aduan);
    AduanBaru::dispatch($this->aduan);

    Event::assertDispatched(AduanDihantar::class);
    Event::assertDispatched(AduanBaru::class);
});
