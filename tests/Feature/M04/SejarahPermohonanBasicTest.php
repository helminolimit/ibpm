<?php

use App\Enums\RolePengguna;
use App\Enums\StatusPermohonanPortal;
use App\Models\PermohonanPortal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('unauthenticated users are redirected to login', function () {
    $response = $this->get('/kemaskini-portal/sejarah');

    $response->assertRedirect(route('login'));
});

test('non-pengguna users receive 403 forbidden', function () {
    // Create a user with pentadbir role
    $user = User::factory()->create([
        'role' => RolePengguna::Pentadbir,
    ]);

    $this->actingAs($user);

    $response = $this->get('/kemaskini-portal/sejarah');

    $response->assertStatus(403);
});

test('authenticated user can access sejarah permohonan page', function () {
    // Create a regular user (pengguna role)
    $user = User::factory()->create([
        'role' => RolePengguna::Pengguna,
    ]);

    $this->actingAs($user);

    $response = $this->get('/kemaskini-portal/sejarah');

    $response->assertStatus(200);
    $response->assertSeeLivewire(\App\Livewire\M04\SejarahPermohonan::class);
});

test('pengguna sees only their own applications', function () {
    // Create two users
    $userA = User::factory()->create(['role' => RolePengguna::Pengguna]);
    $userB = User::factory()->create(['role' => RolePengguna::Pengguna]);

    // Create applications for both users
    $applicationA = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-001',
        'pemohon_id' => $userA->id,
        'url_halaman' => 'https://example.com/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update 1',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    $applicationB = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-002',
        'pemohon_id' => $userB->id,
        'url_halaman' => 'https://example.com/page2',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update 2',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    // Authenticate as user A
    $this->actingAs($userA);

    Livewire::test(\App\Livewire\M04\SejarahPermohonan::class)
        ->assertSee($applicationA->no_tiket)
        ->assertDontSee($applicationB->no_tiket);
});

test('all application statuses are displayed', function () {
    // Create a user
    $user = User::factory()->create(['role' => RolePengguna::Pengguna]);

    // Create applications with different statuses
    $diterima = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-001',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://example.com/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update 1',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    $dalamProses = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-002',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://example.com/page2',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update 2',
        'status' => StatusPermohonanPortal::DalamProses,
    ]);

    $selesai = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-003',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://example.com/page3',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update 3',
        'status' => StatusPermohonanPortal::Selesai,
    ]);

    // Authenticate as user
    $this->actingAs($user);

    Livewire::test(\App\Livewire\M04\SejarahPermohonan::class)
        ->assertSee($diterima->no_tiket)
        ->assertSee($dalamProses->no_tiket)
        ->assertSee($selesai->no_tiket);
});

test('search filters by ticket number', function () {
    // Create a user
    $user = User::factory()->create(['role' => RolePengguna::Pengguna]);

    // Create applications with different ticket numbers
    $matching = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-001',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://example.com/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update 1',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    $notMatching = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-999',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://example.com/page2',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update 2',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    // Authenticate as user
    $this->actingAs($user);

    Livewire::test(\App\Livewire\M04\SejarahPermohonan::class)
        ->set('carian', '2024-001')
        ->assertSee($matching->no_tiket)
        ->assertDontSee($notMatching->no_tiket);
});

test('search filters by page URL', function () {
    // Create a user
    $user = User::factory()->create(['role' => RolePengguna::Pengguna]);

    // Create applications with different URLs
    $matching = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-001',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://example.com/special-page',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update 1',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    $notMatching = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-002',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://example.com/other-page',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update 2',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    // Authenticate as user
    $this->actingAs($user);

    Livewire::test(\App\Livewire\M04\SejarahPermohonan::class)
        ->set('carian', 'special-page')
        ->assertSee($matching->no_tiket)
        ->assertDontSee($notMatching->no_tiket);
});

test('empty state is displayed when no applications exist', function () {
    // Create a user with no applications
    $user = User::factory()->create(['role' => RolePengguna::Pengguna]);

    // Authenticate as user
    $this->actingAs($user);

    Livewire::test(\App\Livewire\M04\SejarahPermohonan::class)
        ->assertSee('Tiada rekod ditemui.');
});

test('empty state is displayed when search has no results', function () {
    // Create a user
    $user = User::factory()->create(['role' => RolePengguna::Pengguna]);

    // Create an application
    PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-001',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://example.com/page',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    // Authenticate as user
    $this->actingAs($user);

    Livewire::test(\App\Livewire\M04\SejarahPermohonan::class)
        ->set('carian', 'nonexistent')
        ->assertSee('Tiada rekod ditemui.');
});

test('applications are paginated at 15 per page', function () {
    // Create a user
    $user = User::factory()->create(['role' => RolePengguna::Pengguna]);

    // Create 20 applications
    for ($i = 1; $i <= 20; $i++) {
        PermohonanPortal::create([
            'no_tiket' => sprintf('#ICT-2024-%03d', $i),
            'pemohon_id' => $user->id,
            'url_halaman' => "https://example.com/page{$i}",
            'jenis_perubahan' => 'kandungan',
            'butiran_kemaskini' => "Test update {$i}",
            'status' => StatusPermohonanPortal::Diterima,
        ]);
    }

    // Authenticate as user
    $this->actingAs($user);

    $component = Livewire::test(\App\Livewire\M04\SejarahPermohonan::class);

    // Check that we have pagination
    $senarai = $component->get('senarai');
    expect($senarai->total())->toBe(20);
    expect($senarai->perPage())->toBe(15);
    expect($senarai->count())->toBe(15); // First page has 15 items
});

test('applications are sorted by latest first', function () {
    // Create a user
    $user = User::factory()->create(['role' => RolePengguna::Pengguna]);

    // Create applications with different dates
    $oldest = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-001',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://example.com/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update 1',
        'status' => StatusPermohonanPortal::Diterima,
        'created_at' => now()->subDays(2),
    ]);

    $middle = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-002',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://example.com/page2',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update 2',
        'status' => StatusPermohonanPortal::Diterima,
        'created_at' => now()->subDays(1),
    ]);

    $latest = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-003',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://example.com/page3',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update 3',
        'status' => StatusPermohonanPortal::Diterima,
        'created_at' => now(),
    ]);

    // Authenticate as user
    $this->actingAs($user);

    $component = Livewire::test(\App\Livewire\M04\SejarahPermohonan::class);

    $senarai = $component->get('senarai');
    $items = $senarai->items();

    // Assert applications are sorted by latest first
    expect($items[0]->id)->toBe($latest->id);
    expect($items[1]->id)->toBe($middle->id);
    expect($items[2]->id)->toBe($oldest->id);
});

test('pagination resets to page 1 when search changes', function () {
    // Create a user
    $user = User::factory()->create(['role' => RolePengguna::Pengguna]);

    // Create 20 applications
    for ($i = 1; $i <= 20; $i++) {
        PermohonanPortal::create([
            'no_tiket' => sprintf('#ICT-2024-%03d', $i),
            'pemohon_id' => $user->id,
            'url_halaman' => "https://example.com/page{$i}",
            'jenis_perubahan' => 'kandungan',
            'butiran_kemaskini' => "Test update {$i}",
            'status' => StatusPermohonanPortal::Diterima,
        ]);
    }

    // Authenticate as user
    $this->actingAs($user);

    $component = Livewire::test(\App\Livewire\M04\SejarahPermohonan::class)
        ->call('gotoPage', 2, 'page') // Go to page 2
        ->set('carian', '2024-001'); // Change search

    // Verify we're back on page 1 by checking the paginator
    $senarai = $component->get('senarai');
    expect($senarai->currentPage())->toBe(1);
});
