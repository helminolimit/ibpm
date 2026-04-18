<?php

use App\Enums\StatusAduan;
use App\Livewire\Permohonan\SenaraiAduan;
use App\Models\AduanIct;
use App\Models\KategoriAduan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->kategori = KategoriAduan::factory()->create(['is_aktif' => true]);
});

it('redirects unauthenticated users to login', function () {
    $this->get(route('senarai-saya'))->assertRedirect(route('login'));
});

it('renders the list for authenticated users', function () {
    actingAs($this->user)
        ->get(route('senarai-saya'))
        ->assertOk();
});

it('shows only the authenticated user own aduan', function () {
    actingAs($this->user);

    $myAduan = AduanIct::factory()->create([
        'user_id' => $this->user->id,
        'kategori_aduan_id' => $this->kategori->id,
        'tajuk' => 'Aduan saya sendiri',
    ]);

    $otherUser = User::factory()->create();
    $otherAduan = AduanIct::factory()->create([
        'user_id' => $otherUser->id,
        'kategori_aduan_id' => $this->kategori->id,
        'tajuk' => 'Aduan orang lain',
    ]);

    Livewire::test(SenaraiAduan::class)
        ->assertSee($myAduan->no_tiket)
        ->assertDontSee($otherAduan->no_tiket);
});

it('shows empty state message when user has no aduan', function () {
    actingAs($this->user);

    Livewire::test(SenaraiAduan::class)
        ->assertSee('Anda belum mempunyai sebarang aduan.');
});

it('shows empty filter message when search yields no results', function () {
    actingAs($this->user);

    AduanIct::factory()->create([
        'user_id' => $this->user->id,
        'kategori_aduan_id' => $this->kategori->id,
    ]);

    Livewire::test(SenaraiAduan::class)
        ->set('search', 'xyznotfound999')
        ->assertSee('Tiada aduan ditemui berdasarkan carian semasa.');
});

it('filters aduan by status', function () {
    actingAs($this->user);

    $aduanBaru = AduanIct::factory()->create([
        'user_id' => $this->user->id,
        'kategori_aduan_id' => $this->kategori->id,
        'status' => StatusAduan::Baru,
    ]);

    $aduanSelesai = AduanIct::factory()->selesai()->create([
        'user_id' => $this->user->id,
        'kategori_aduan_id' => $this->kategori->id,
    ]);

    Livewire::test(SenaraiAduan::class)
        ->set('filterStatus', StatusAduan::Selesai->value)
        ->assertSee($aduanSelesai->no_tiket)
        ->assertDontSee($aduanBaru->no_tiket);
});

it('filters aduan by no tiket search', function () {
    actingAs($this->user);

    $aduan = AduanIct::factory()->create([
        'user_id' => $this->user->id,
        'kategori_aduan_id' => $this->kategori->id,
        'no_tiket' => 'ICT-2026-001',
    ]);

    Livewire::test(SenaraiAduan::class)
        ->set('search', 'ICT-2026-001')
        ->assertSee('ICT-2026-001');
});

it('filters aduan by tajuk search', function () {
    actingAs($this->user);

    $aduan1 = AduanIct::factory()->create([
        'user_id' => $this->user->id,
        'kategori_aduan_id' => $this->kategori->id,
        'tajuk' => 'Komputer rosak teruk',
        'no_tiket' => 'ICT-2026-010',
    ]);

    $aduan2 = AduanIct::factory()->create([
        'user_id' => $this->user->id,
        'kategori_aduan_id' => $this->kategori->id,
        'tajuk' => 'Printer tidak berfungsi',
        'no_tiket' => 'ICT-2026-011',
    ]);

    Livewire::test(SenaraiAduan::class)
        ->set('search', 'Komputer')
        ->assertSee('ICT-2026-010')
        ->assertDontSee('ICT-2026-011');
});

it('resets to page 1 when search changes', function () {
    actingAs($this->user);

    Livewire::test(SenaraiAduan::class)
        ->set('search', 'test')
        ->assertSet('paginators.page', 1);
});

it('sorts aduan by no tiket', function () {
    actingAs($this->user);

    AduanIct::factory()->create([
        'user_id' => $this->user->id,
        'kategori_aduan_id' => $this->kategori->id,
        'no_tiket' => 'ICT-2026-001',
    ]);

    AduanIct::factory()->create([
        'user_id' => $this->user->id,
        'kategori_aduan_id' => $this->kategori->id,
        'no_tiket' => 'ICT-2026-002',
    ]);

    Livewire::test(SenaraiAduan::class)
        ->call('sort', 'no_tiket')
        ->assertSet('sortBy', 'no_tiket')
        ->assertSet('sortDirection', 'asc');
});

it('ignores invalid sort columns', function () {
    actingAs($this->user);

    Livewire::test(SenaraiAduan::class)
        ->set('sortBy', 'created_at')
        ->call('sort', 'invalid_column')
        ->assertSet('sortBy', 'created_at');
});
