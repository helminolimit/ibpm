<?php

use App\Enums\StatusPermohonanPortal;
use App\Models\PermohonanPortal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('milikPemohon scope filters by authenticated user id', function () {
    // Create two users
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    // Create applications for both users
    $applicationA1 = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-001',
        'pemohon_id' => $userA->id,
        'url_halaman' => 'https://example.com/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update 1',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    $applicationA2 = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-002',
        'pemohon_id' => $userA->id,
        'url_halaman' => 'https://example.com/page2',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update 2',
        'status' => StatusPermohonanPortal::DalamProses,
    ]);

    $applicationB = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-003',
        'pemohon_id' => $userB->id,
        'url_halaman' => 'https://example.com/page3',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update 3',
        'status' => StatusPermohonanPortal::Selesai,
    ]);

    // Authenticate as user A
    $this->actingAs($userA);

    // Query with milikPemohon scope
    $results = PermohonanPortal::milikPemohon()->get();

    // Assert only user A's applications are returned
    expect($results)->toHaveCount(2);
    expect($results->pluck('id')->toArray())->toContain($applicationA1->id, $applicationA2->id);
    expect($results->pluck('id')->toArray())->not->toContain($applicationB->id);
});

test('milikPemohon scope returns empty collection when user has no applications', function () {
    // Create a user with no applications
    $user = User::factory()->create();

    // Create an application for another user
    $otherUser = User::factory()->create();
    PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-001',
        'pemohon_id' => $otherUser->id,
        'url_halaman' => 'https://example.com/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    // Authenticate as user with no applications
    $this->actingAs($user);

    // Query with milikPemohon scope
    $results = PermohonanPortal::milikPemohon()->get();

    // Assert empty collection is returned
    expect($results)->toBeEmpty();
});

test('milikPemohon scope includes all statuses', function () {
    // Create a user
    $user = User::factory()->create();

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

    // Query with milikPemohon scope
    $results = PermohonanPortal::milikPemohon()->get();

    // Assert all three applications are returned
    expect($results)->toHaveCount(3);
    expect($results->pluck('status')->toArray())->toContain(
        StatusPermohonanPortal::Diterima,
        StatusPermohonanPortal::DalamProses,
        StatusPermohonanPortal::Selesai
    );
});

test('milikPemohon scope can be chained with other query methods', function () {
    // Create a user
    $user = User::factory()->create();

    // Create multiple applications
    PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-001',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://example.com/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update 1',
        'status' => StatusPermohonanPortal::Diterima,
        'created_at' => now()->subDays(2),
    ]);

    $latest = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-002',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://example.com/page2',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update 2',
        'status' => StatusPermohonanPortal::DalamProses,
        'created_at' => now(),
    ]);

    // Authenticate as user
    $this->actingAs($user);

    // Query with milikPemohon scope chained with latest()
    $result = PermohonanPortal::milikPemohon()->latest()->first();

    // Assert the latest application is returned
    expect($result->id)->toBe($latest->id);
});

test('carian scope filters by ticket number', function () {
    // Create a user
    $user = User::factory()->create();
    $this->actingAs($user);

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

    // Query with carian scope
    $results = PermohonanPortal::carian('2024-001')->get();

    // Assert only matching application is returned
    expect($results)->toHaveCount(1);
    expect($results->first()->id)->toBe($matching->id);
});

test('carian scope filters by page URL', function () {
    // Create a user
    $user = User::factory()->create();
    $this->actingAs($user);

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

    // Query with carian scope
    $results = PermohonanPortal::carian('special-page')->get();

    // Assert only matching application is returned
    expect($results)->toHaveCount(1);
    expect($results->first()->id)->toBe($matching->id);
});

test('carian scope uses OR logic for ticket and URL', function () {
    // Create a user
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create application with specific ticket and URL
    $application = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-001',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://example.com/page',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    // Query with ticket number search
    $resultsByTicket = PermohonanPortal::carian('ICT')->get();
    expect($resultsByTicket)->toHaveCount(1);
    expect($resultsByTicket->first()->id)->toBe($application->id);

    // Query with URL search
    $resultsByUrl = PermohonanPortal::carian('example')->get();
    expect($resultsByUrl)->toHaveCount(1);
    expect($resultsByUrl->first()->id)->toBe($application->id);
});

test('carian scope performs partial match', function () {
    // Create a user
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create application
    $application = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-001',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://example.com/page',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    // Query with partial ticket number
    $results = PermohonanPortal::carian('2024')->get();

    // Assert application is returned
    expect($results)->toHaveCount(1);
    expect($results->first()->id)->toBe($application->id);
});

test('carian scope is case insensitive', function () {
    // Create a user
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create application with URL containing uppercase
    $application = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-001',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://Example.com/Page',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    // Query with lowercase search
    $results = PermohonanPortal::carian('example')->get();

    // Assert application is returned (MySQL LIKE is case-insensitive by default)
    expect($results)->toHaveCount(1);
    expect($results->first()->id)->toBe($application->id);
});

test('carian scope can be chained with milikPemohon scope', function () {
    // Create two users
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    // Create applications for both users with similar ticket numbers
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

    // Query with both scopes
    $results = PermohonanPortal::milikPemohon()->carian('2024')->get();

    // Assert only user A's application is returned
    expect($results)->toHaveCount(1);
    expect($results->first()->id)->toBe($applicationA->id);
});

test('carian scope returns empty collection when no matches found', function () {
    // Create a user
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create application
    PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-001',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://example.com/page',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    // Query with non-matching search
    $results = PermohonanPortal::carian('nonexistent')->get();

    // Assert empty collection is returned
    expect($results)->toBeEmpty();
});
