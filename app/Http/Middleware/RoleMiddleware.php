<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            if (in_array('student', $roles)) {
                return redirect()->route('student.login')
                    ->with('error', 'Please log in with your Student Registration / Roll No to continue.');
            }
            return redirect()->route('login')
                ->with('error', 'Please log in to access this portal.');
        }

        $user = Auth::user();

        // Check if user account is active
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated. Please contact the Head Office.');
        }

        // Super Admin has master authorization for all roles
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has any of the authorized roles
        if (!empty($roles) && !$user->hasAnyRole($roles)) {
            abort(403, 'Unauthorized Access: You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
