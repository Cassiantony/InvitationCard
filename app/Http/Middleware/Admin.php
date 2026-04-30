<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = strtolower(str_replace(' ', '', (string) Auth::user()->role));

        if (!in_array($userRole, ['admin', 'owner', 'superadmin'], true)) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
