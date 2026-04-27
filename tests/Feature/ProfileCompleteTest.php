<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects to kemaskini-profil when profile is incomplete', function () {
    $user = User::factory()->create([
        'bahagian' => null,
        'jawatan' => null,
        'no_telefon' => null,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('profile.complete'));
});

it('allows access to dashboard when profile is complete', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});

it('allows access to kemaskini-profil when profile is incomplete', function () {
    $user = User::factory()->create([
        'bahagian' => null,
        'jawatan' => null,
        'no_telefon' => null,
    ]);

    $this->actingAs($user)
        ->get(route('profile.complete'))
        ->assertOk();
});

it('allows access to settings profile when profile is incomplete', function () {
    $user = User::factory()->create([
        'bahagian' => null,
        'jawatan' => null,
        'no_telefon' => null,
    ]);

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk();
});

it('redirects admin routes when profile is incomplete', function () {
    $user = User::factory()->pentadbir()->create([
        'jawatan' => null,
        'no_telefon' => null,
    ]);

    $this->actingAs($user)
        ->get(route('admin.aduan.index'))
        ->assertRedirect(route('profile.complete'));
});

it('redirects superadmin routes when profile is incomplete', function () {
    $user = User::factory()->superadmin()->create([
        'bahagian' => null,
        'jawatan' => null,
        'no_telefon' => null,
    ]);

    $this->actingAs($user)
        ->get(route('superadmin.pengguna.index'))
        ->assertRedirect(route('profile.complete'));
});

it('isProfileComplete returns false when bahagian is null', function () {
    $user = User::factory()->make(['bahagian' => null]);

    expect($user->isProfileComplete())->toBeFalse();
});

it('isProfileComplete returns false when jawatan is empty string', function () {
    $user = User::factory()->make(['jawatan' => '']);

    expect($user->isProfileComplete())->toBeFalse();
});

it('isProfileComplete returns false when no_telefon is whitespace', function () {
    $user = User::factory()->make(['no_telefon' => '   ']);

    expect($user->isProfileComplete())->toBeFalse();
});

it('isProfileComplete returns true when all required fields are filled', function () {
    $user = User::factory()->make([
        'bahagian' => 'Bahagian A',
        'jawatan' => 'Pegawai',
        'no_telefon' => '0312345678',
    ]);

    expect($user->isProfileComplete())->toBeTrue();
});
