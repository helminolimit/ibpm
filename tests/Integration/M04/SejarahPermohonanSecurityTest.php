<?php

use App\Enums\RolePengguna;
use App\Enums\StatusPermohonanPortal;
use App\Models\PermohonanPortal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('users cannot access other users applications via query manipulation', function () {
    // Create two users
    $userA = User::factory()->create(['role' => RolePengguna::Pengguna]);
    $userB = User::factory()->create(['role' => RolePengguna::Pengguna]);

    // Create application for user A
    $applicationA = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-001',
        'pemohon_id' => $userA->id,
        'url_halaman' => 'https://example.com/page1',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Sensitive data for user A',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    // Authenticate as user B
    $this->actingAs($userB);

    // Attempt to query directly with milikPemohon scope
    $results = PermohonanPortal::milikPemohon()->get();

    // Assert: User B cannot see user A's application
    expect($results)->toHaveCount(0);
    expect($results->pluck('id')->contains($applicationA->id))->toBeFalse();

    // Verify the security filter is applied at database level
    // by checking that even a direct query with the scope returns nothing
    $directQuery = PermohonanPortal::milikPemohon()
        ->where('id', $applicationA->id)
        ->first();

    expect($directQuery)->toBeNull();
});

test('search input is protected against SQL injection', function () {
    // Create a user
    $user = User::factory()->create(['role' => RolePengguna::Pengguna]);

    // Create applications for the user
    $application = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-001',
        'pemohon_id' => $user->id,
        'url_halaman' => 'https://example.com/page',
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    // Authenticate as user
    $this->actingAs($user);

    // Test via HTTP route as specified — SQL injection attempt via search parameter
    $response = $this->get('/kemaskini-portal/sejarah?carian='.urlencode("'; DROP TABLE users; --"));

    // Assert: No error occurs (query builder escapes input)
    $response->assertStatus(200);

    // Assert: Search treats input as literal string (no results match the injection string)
    // Assert: No database tables are dropped
    expect(DB::table('users')->count())->toBeGreaterThan(0);
    expect(DB::table('permohonan_portals')->count())->toBeGreaterThan(0);

    // Verify the application still exists
    expect(PermohonanPortal::find($application->id))->not->toBeNull();
});

test('search results are escaped to prevent XSS', function () {
    // Create a user
    $user = User::factory()->create(['role' => RolePengguna::Pengguna]);

    // Create application with URL containing potential XSS payload
    $xssPayload = "<script>alert('xss')</script>";
    $application = PermohonanPortal::create([
        'no_tiket' => '#ICT-2024-001',
        'pemohon_id' => $user->id,
        'url_halaman' => "https://example.com/{$xssPayload}",
        'jenis_perubahan' => 'kandungan',
        'butiran_kemaskini' => 'Test update',
        'status' => StatusPermohonanPortal::Diterima,
    ]);

    // Authenticate as user
    $this->actingAs($user);

    // Get the page response
    $response = $this->get('/kemaskini-portal/sejarah');

    // Assert: Script tags are escaped in HTML output
    // Blade's {{ }} syntax automatically escapes output

    // The unescaped version should NOT be present (would be dangerous)
    $response->assertDontSee($xssPayload, false);

    // Verify the escaped version is present in the HTML
    // Laravel/Blade escapes using htmlspecialchars with ENT_QUOTES
    $response->assertSee('&lt;script&gt;', false);
    $response->assertSee('&lt;/script&gt;', false);

    // Verify the full escaped payload is in the response
    $escapedPayload = htmlspecialchars($xssPayload, ENT_QUOTES, 'UTF-8');
    $response->assertSee($escapedPayload, false);
});
