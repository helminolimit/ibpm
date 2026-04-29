<?php

namespace App\Http\Responses;

use App\Enums\RolePengguna;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $route = match (auth()->user()->role) {
            RolePengguna::Superadmin => 'superadmin.pengguna.index',
            RolePengguna::Pentadbir => 'admin.aduan.index',
            RolePengguna::Teknician => 'admin.aduan.index',
            RolePengguna::Pelulus1 => 'kelulusan.penamatan.index',
            default => 'dashboard',
        };

        return redirect()->intended(route($route));
    }
}
