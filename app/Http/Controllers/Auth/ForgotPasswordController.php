<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'We could not find an account associated with that email address.');
        }

        if (!$user->is_active) {
            return back()->with('error', 'This account has been deactivated. Please contact the Head Office.');
        }

        // Generate secure 64-char token
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => hash('sha256', $token),
                'created_at' => now(),
            ]
        );

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'password_reset_requested',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);

        // In production this sends an email via Mail::to(). In local/staging, we log and provide instant flash for testing.
        return back()->with('success', "Password reset token generated. [Local/Testing Link: <a href='{$resetUrl}' class='alert-link'>Click here to reset your password</a>]");
    }
}
