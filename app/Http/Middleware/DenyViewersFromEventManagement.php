<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DenyViewersFromEventManagement
{
    /**
     * Viewers may only use invitation scanning routes and profile; block all other app areas that use this middleware.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->isViewer()) {
            abort(403, 'Viewers can only scan invitations for their assigned organizer.');
        }

        return $next($request);
    }
}
