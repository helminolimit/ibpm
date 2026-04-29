<?php

use App\Enums\RolePengguna;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;

uses(RefreshDatabase::class);

function loginAs(User $user): TestResponse
{
    return test()->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);
}

it('redirects pengguna to dashboard after login', function () {
    $user = User::factory()->create();

    loginAs($user)->assertRedirect(route('dashboard'));
});

it('redirects pelulus1 to kelulusan penamatan after login', function () {
    $user = User::factory()->state(['role' => RolePengguna::Pelulus1])->create();

    loginAs($user)->assertRedirect(route('kelulusan.penamatan.index'));
});

it('redirects pentadbir to admin aduan after login', function () {
    $user = User::factory()->pentadbir()->create();

    loginAs($user)->assertRedirect(route('admin.aduan.index'));
});

it('redirects teknician to admin aduan after login', function () {
    $user = User::factory()->teknician()->create();

    loginAs($user)->assertRedirect(route('admin.aduan.index'));
});

it('redirects superadmin to pengguna management after login', function () {
    $user = User::factory()->superadmin()->create();

    loginAs($user)->assertRedirect(route('superadmin.pengguna.index'));
});

it('returns validation error for wrong credentials', function () {
    User::factory()->create(['email' => 'test@example.com']);

    $this->post('/login', ['email' => 'test@example.com', 'password' => 'wrong'])
        ->assertSessionHasErrors('email');
});

it('redirects unauthenticated user to login when accessing dashboard', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});
