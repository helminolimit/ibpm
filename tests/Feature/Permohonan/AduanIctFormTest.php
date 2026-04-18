<?php

use App\Enums\StatusAduan;
use App\Events\AduanBaru;
use App\Events\AduanDihantar;
use App\Livewire\Permohonan\AduanIctForm;
use App\Models\AduanIct;
use App\Models\KategoriAduan;
use App\Models\LampiranAduan;
use App\Models\StatusLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->kategori = KategoriAduan::factory()->create([
        'nama' => 'Kerosakan Perkakasan',
        'unit_bpm' => 'Unit Infrastruktur',
        'emel_unit' => 'infrastruktur@motac.gov.my',
        'is_aktif' => true,
    ]);
});

it('redirects unauthenticated users to login', function () {
    $this->get(route('aduan-ict.create'))->assertRedirect(route('login'));
});

it('renders the form for authenticated users', function () {
    actingAs($this->user)
        ->get(route('aduan-ict.create'))
        ->assertOk();
});

it('shows step 1 form by default', function () {
    actingAs($this->user);

    Livewire::test(AduanIctForm::class)
        ->assertSet('step', 1)
        ->assertSee('Hantar Aduan ICT')
        ->assertSee('Kategori Aduan');
});

it('validates all required fields on step 1', function () {
    actingAs($this->user);

    Livewire::test(AduanIctForm::class)
        ->call('teruskan')
        ->assertHasErrors(['kategoriId', 'lokasi', 'tajuk', 'keterangan', 'noTelefon']);
});

it('advances to step 2 when all fields are valid', function () {
    actingAs($this->user);

    Livewire::test(AduanIctForm::class)
        ->set('kategoriId', $this->kategori->id)
        ->set('lokasi', 'Bilik 302, Aras 3')
        ->set('tajuk', 'Komputer tidak boleh hidupkan')
        ->set('keterangan', 'Komputer rosak sejak semalam, tidak boleh dihidupkan.')
        ->set('noTelefon', '03-88891234')
        ->call('teruskan')
        ->assertSet('step', 2)
        ->assertHasNoErrors();
});

it('can go back from step 2 to step 1', function () {
    actingAs($this->user);

    Livewire::test(AduanIctForm::class)
        ->set('step', 2)
        ->call('balik')
        ->assertSet('step', 1);
});

it('creates aduan record and advances to step 3 on submit', function () {
    actingAs($this->user);
    Event::fake([AduanDihantar::class, AduanBaru::class]);

    Livewire::test(AduanIctForm::class)
        ->set('kategoriId', $this->kategori->id)
        ->set('lokasi', 'Bilik 302, Aras 3')
        ->set('tajuk', 'Komputer tidak boleh hidupkan')
        ->set('keterangan', 'Komputer rosak sejak semalam.')
        ->set('noTelefon', '03-88891234')
        ->set('step', 2)
        ->call('hantar')
        ->assertSet('step', 3)
        ->assertSet('noTiket', fn ($value) => str_starts_with($value, 'ICT-'));

    $this->assertDatabaseHas('aduan_ict', [
        'user_id' => $this->user->id,
        'kategori_aduan_id' => $this->kategori->id,
        'lokasi' => 'Bilik 302, Aras 3',
        'tajuk' => 'Komputer tidak boleh hidupkan',
        'status' => StatusAduan::Baru->value,
    ]);
});

it('creates a status log entry with status baru on submit', function () {
    actingAs($this->user);
    Event::fake();

    Livewire::test(AduanIctForm::class)
        ->set('kategoriId', $this->kategori->id)
        ->set('lokasi', 'Bilik 101')
        ->set('tajuk', 'Masalah rangkaian')
        ->set('keterangan', 'Internet lambat.')
        ->set('noTelefon', '03-12345678')
        ->set('step', 2)
        ->call('hantar');

    $aduan = AduanIct::first();
    expect(StatusLog::where('aduan_ict_id', $aduan->id)->where('status', StatusAduan::Baru->value)->exists())->toBeTrue();
});

it('generates ticket number in ICT-YYYY-XXX format', function () {
    actingAs($this->user);
    Event::fake();

    Livewire::test(AduanIctForm::class)
        ->set('kategoriId', $this->kategori->id)
        ->set('lokasi', 'Bilik 101')
        ->set('tajuk', 'Test tiket')
        ->set('keterangan', 'Keterangan.')
        ->set('noTelefon', '03-12345678')
        ->set('step', 2)
        ->call('hantar');

    $aduan = AduanIct::first();
    expect($aduan->no_tiket)->toMatch('/^ICT-\d{4}-\d{3,}$/');
});

it('fires AduanDihantar and AduanBaru events on submit', function () {
    actingAs($this->user);
    Event::fake([AduanDihantar::class, AduanBaru::class]);

    Livewire::test(AduanIctForm::class)
        ->set('kategoriId', $this->kategori->id)
        ->set('lokasi', 'Bilik 101')
        ->set('tajuk', 'Test event')
        ->set('keterangan', 'Keterangan.')
        ->set('noTelefon', '03-12345678')
        ->set('step', 2)
        ->call('hantar');

    Event::assertDispatched(AduanDihantar::class);
    Event::assertDispatched(AduanBaru::class);
});

it('saves attachment when file is uploaded', function () {
    actingAs($this->user);
    Event::fake();
    Storage::fake('public');

    $file = UploadedFile::fake()->create('bukti.pdf', 1024, 'application/pdf');

    Livewire::test(AduanIctForm::class)
        ->set('kategoriId', $this->kategori->id)
        ->set('lokasi', 'Bilik 101')
        ->set('tajuk', 'Aduan dengan lampiran')
        ->set('keterangan', 'Keterangan.')
        ->set('noTelefon', '03-12345678')
        ->set('lampirans', [$file])
        ->set('step', 2)
        ->call('hantar');

    $aduan = AduanIct::first();
    $this->assertDatabaseHas('lampiran_aduan', [
        'aduan_ict_id' => $aduan->id,
        'nama_fail' => 'bukti.pdf',
    ]);
});

it('stores attachment in aduan/year/month directory', function () {
    actingAs($this->user);
    Event::fake();
    Storage::fake('public');

    $file = UploadedFile::fake()->create('gambar.jpg', 512, 'image/jpeg');

    Livewire::test(AduanIctForm::class)
        ->set('kategoriId', $this->kategori->id)
        ->set('lokasi', 'Bilik 101')
        ->set('tajuk', 'Aduan dengan lampiran')
        ->set('keterangan', 'Keterangan.')
        ->set('noTelefon', '03-12345678')
        ->set('lampirans', [$file])
        ->set('step', 2)
        ->call('hantar');

    $lampiran = LampiranAduan::first();
    expect($lampiran->path)->toStartWith('aduan/'.now()->format('Y/m'));
});

it('saves multiple attachments to database', function () {
    actingAs($this->user);
    Event::fake();
    Storage::fake('public');

    $file1 = UploadedFile::fake()->create('bukti1.pdf', 512, 'application/pdf');
    $file2 = UploadedFile::fake()->create('gambar.jpg', 512, 'image/jpeg');

    Livewire::test(AduanIctForm::class)
        ->set('kategoriId', $this->kategori->id)
        ->set('lokasi', 'Bilik 101')
        ->set('tajuk', 'Aduan dua lampiran')
        ->set('keterangan', 'Keterangan.')
        ->set('noTelefon', '03-12345678')
        ->set('lampirans', [$file1, $file2])
        ->set('step', 2)
        ->call('hantar');

    $aduan = AduanIct::first();
    expect(LampiranAduan::where('aduan_ict_id', $aduan->id)->count())->toBe(2);
});

it('rejects attachment larger than 5MB', function () {
    actingAs($this->user);

    $largeFile = UploadedFile::fake()->create('besar.pdf', 6144, 'application/pdf');

    Livewire::test(AduanIctForm::class)
        ->set('lampiranBaru', $largeFile)
        ->assertHasErrors(['lampiranBaru']);
});

it('rejects unsupported file type', function () {
    actingAs($this->user);

    $badFile = UploadedFile::fake()->create('script.exe', 100, 'application/octet-stream');

    Livewire::test(AduanIctForm::class)
        ->set('lampiranBaru', $badFile)
        ->assertHasErrors(['lampiranBaru']);
});

it('adds a valid file to lampiran list via updatedLampiranBaru', function () {
    actingAs($this->user);
    Storage::fake('public');

    $file = UploadedFile::fake()->create('bukti.pdf', 512, 'application/pdf');

    Livewire::test(AduanIctForm::class)
        ->set('lampiranBaru', $file)
        ->assertSet('lampiranBaru', null)
        ->assertSet('lampirans', fn ($value) => count($value) === 1);
});

it('can remove an attachment from the list', function () {
    actingAs($this->user);
    Storage::fake('public');

    $file = UploadedFile::fake()->create('bukti.pdf', 512, 'application/pdf');

    Livewire::test(AduanIctForm::class)
        ->set('lampirans', [$file])
        ->call('removeLampiran', 0)
        ->assertSet('lampirans', []);
});

it('rejects more than 5 attachments', function () {
    actingAs($this->user);
    Storage::fake('public');

    $files = collect(range(1, 5))
        ->map(fn ($i) => UploadedFile::fake()->create("file{$i}.pdf", 100, 'application/pdf'))
        ->all();

    $component = Livewire::test(AduanIctForm::class)
        ->set('lampirans', $files);

    $extra = UploadedFile::fake()->create('extra.pdf', 100, 'application/pdf');

    $component->set('lampiranBaru', $extra)
        ->assertHasErrors(['lampiranBaru'])
        ->assertSet('lampirans', fn ($value) => count($value) === 5);
});

it('shows unit BPM when category is selected', function () {
    actingAs($this->user);

    Livewire::test(AduanIctForm::class)
        ->set('kategoriId', $this->kategori->id)
        ->assertSee('Unit Infrastruktur');
});
