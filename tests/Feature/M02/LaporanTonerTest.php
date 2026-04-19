<?php

use App\Enums\JenisToner;
use App\Enums\StatusPermohonanToner;
use App\Livewire\M02\Admin\LaporanToner;
use App\Models\PermohonanToner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

it('redirects guest to login', function () {
    $this->get(route('m02.admin.laporan'))->assertRedirect(route('login'));
});

it('returns 403 for non-admin user', function () {
    $user = User::factory()->create(['role' => 'user']);

    Livewire::actingAs($user)
        ->test(LaporanToner::class)
        ->assertForbidden();
});

it('renders laporan page for admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('m02.admin.laporan'))
        ->assertStatus(200)
        ->assertSeeLivewire(LaporanToner::class);
});

it('renders laporan page for superadmin', function () {
    $superadmin = User::factory()->create(['role' => 'superadmin']);

    $this->actingAs($superadmin)
        ->get(route('m02.admin.laporan'))
        ->assertStatus(200);
});

it('defaults to current month date range on mount', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $component = Livewire::actingAs($admin)->test(LaporanToner::class);

    expect($component->get('tarikhDari'))->toBe(now()->startOfMonth()->toDateString());
    expect($component->get('tarikhHingga'))->toBe(now()->toDateString());
});

it('filters records by date range', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $inRange = PermohonanToner::factory()->create([
        'created_at' => now()->startOfMonth(),
    ]);
    $outOfRange = PermohonanToner::factory()->create([
        'created_at' => now()->subMonths(2),
    ]);

    Livewire::actingAs($admin)
        ->test(LaporanToner::class)
        ->set('tarikhDari', now()->startOfMonth()->toDateString())
        ->set('tarikhHingga', now()->toDateString())
        ->assertSee($inRange->no_tiket)
        ->assertDontSee($outOfRange->no_tiket);
});

it('filters records by jenis toner', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $hitam = PermohonanToner::factory()->create(['jenis_toner' => JenisToner::Hitam]);
    $cyan = PermohonanToner::factory()->create(['jenis_toner' => JenisToner::Cyan]);

    Livewire::actingAs($admin)
        ->test(LaporanToner::class)
        ->set('filterJenis', JenisToner::Hitam->value)
        ->assertSee($hitam->no_tiket)
        ->assertDontSee($cyan->no_tiket);
});

it('filters records by status', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $submitted = PermohonanToner::factory()->submitted()->create();
    $ditolak = PermohonanToner::factory()->ditolak()->create();

    Livewire::actingAs($admin)
        ->test(LaporanToner::class)
        ->set('filterStatus', StatusPermohonanToner::Submitted->value)
        ->assertSee($submitted->no_tiket)
        ->assertDontSee($ditolak->no_tiket);
});

it('filters by search on no_tiket', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $target = PermohonanToner::factory()->create(['no_tiket' => 'TON-2026-888']);
    PermohonanToner::factory()->create(['no_tiket' => 'TON-2026-999']);

    Livewire::actingAs($admin)
        ->test(LaporanToner::class)
        ->set('search', 'TON-2026-888')
        ->assertSee('TON-2026-888')
        ->assertDontSee('TON-2026-999');
});

it('shows correct statistik counts', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    PermohonanToner::factory()->submitted()->create();
    PermohonanToner::factory()->diluluskan()->create(['kuantiti_diluluskan' => 2]);
    PermohonanToner::factory()->dihantar()->create(['kuantiti_diluluskan' => 3]);
    PermohonanToner::factory()->ditolak()->create();

    $component = Livewire::actingAs($admin)
        ->test(LaporanToner::class)
        ->set('tarikhDari', now()->subDay()->toDateString())
        ->set('tarikhHingga', now()->addDay()->toDateString());

    $statistik = $component->get('statistik');

    expect($statistik['jumlah'])->toBe(4);
    expect($statistik['diluluskan'])->toBe(2);
    expect($statistik['dihantar'])->toBe(1);
    expect($statistik['ditolak'])->toBe(1);
    expect($statistik['jumlahUnit'])->toBe(3);
});

it('shows empty state when no records match', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(LaporanToner::class)
        ->set('tarikhDari', '2020-01-01')
        ->set('tarikhHingga', '2020-01-31')
        ->assertSee('Tiada rekod permohonan dalam tempoh yang dipilih.');
});

it('exports excel file', function () {
    Excel::fake();
    $this->freezeTime();

    $admin = User::factory()->create(['role' => 'admin']);
    $expectedFilename = 'laporan-toner-'.now()->format('Ymd-His').'.xlsx';

    Livewire::actingAs($admin)
        ->test(LaporanToner::class)
        ->call('exportExcel');

    Excel::assertDownloaded($expectedFilename);
});
