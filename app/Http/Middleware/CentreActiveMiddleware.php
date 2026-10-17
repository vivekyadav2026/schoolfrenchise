<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CentreActiveMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Super Admin bypasses centre restrictions
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $centre = $user->centre;
        if (!$centre) {
            abort(403, 'No franchise centre is associated with this user account.');
        }

        if ($centre->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', "Your franchise centre [{$centre->centre_code}] is currently {$centre->status}. Please contact Central Board.");
        }

        return $next($request);
    }
}
