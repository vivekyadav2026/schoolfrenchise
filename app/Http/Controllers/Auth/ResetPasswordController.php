<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $hashedToken = hash('sha256', $request->token);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $hashedToken)
            ->first();

        if (!$record) {
            return back()->with('error', 'This password reset link is invalid or has expired.');
        }

        // Check 60 minute expiry
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->with('error', 'This password reset token has expired. Please request a new one.');
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'password_reset_completed',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $redirectRoute = $user->isStudent() ? 'student.login' : 'login';
        return redirect()->route($redirectRoute)->with('success', 'Your password has been reset successfully! You can now log in with your new password.');
    }
}
