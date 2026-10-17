<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isSuperAdmin() || $user->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            }
            if ($user->isCentre()) {
                return redirect()->intended('/centre/dashboard');
            }
        }
        return view('pages.centre-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        if (Auth::attempt([$field => $credentials['email'], 'password' => $credentials['password'], 'is_active' => true], $request->filled('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isSuperAdmin() || $user->isAdmin()) {
                return redirect()->intended('/admin/dashboard')->with('success', "Welcome back, {$user->name}!");
            }
            if ($user->isCentre()) {
                return redirect()->intended('/centre/dashboard')->with('success', "Welcome to your Centre Dashboard!");
            }

            return redirect()->route('home')->with('success', "Logged in successfully.");
        }

        return back()->withInput($request->only('email', 'remember'))
            ->with('error', 'Invalid login credentials or your account is inactive. Please contact Head Office.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out securely.');
    }
}
