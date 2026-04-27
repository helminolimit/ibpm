<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($request->routeIs('profile.complete', 'logout', 'login', 'login.store',
            'password.*', 'verification.*', 'two-factor.*')) {
            return $next($request);
        }

        if ($request->is('settings/*') || $request->is('settings')) {
            return $next($request);
        }

        if (! $user->isProfileComplete()) {
            return redirect()->route('profile.complete');
        }

        return $next($request);
    }
}
