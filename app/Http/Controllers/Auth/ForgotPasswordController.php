<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a simulated reset link to the user's backup email.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        // 1. Generate token
        $token = Str::random(60);

        // 2. Save token to password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Mask backup email for UI display: e.g. b****1@example.com
        $backupEmail = $user->backup_email;
        $maskedBackup = 'Email Cadangan';
        if ($backupEmail) {
            $parts = explode('@', $backupEmail);
            if (count($parts) === 2) {
                $name = $parts[0];
                $domain = $parts[1];
                $maskedName = substr($name, 0, 1) . str_repeat('*', max(1, strlen($name) - 2)) . substr($name, -1);
                $maskedBackup = $maskedName . '@' . $domain;
            }
        }

        // 3. Create simulated reset link URL
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        // 4. Log audit activity
        ActivityLogger::log("Meminta reset password. Simulasi email dikirim ke email backup: {$backupEmail}");

        return back()->with([
            'status' => "Simulasi email terkirim! Tautan reset password telah dikirim ke email cadangan Anda: {$maskedBackup}",
            'simulated_link' => $resetUrl,
            'backup_email_raw' => $backupEmail,
        ]);
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->email;
        return view('auth.reset-password', compact('token', 'email'));
    }

    /**
     * Update the password in database.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Token reset password tidak valid atau telah kedaluwarsa.']);
        }

        // Update password
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Log audit activity
        ActivityLogger::log("Kata sandi berhasil direset via email cadangan untuk user: {$user->email}");

        return redirect()->route('login')->with('success', 'Kata sandi Anda berhasil diperbarui! Silakan login dengan kata sandi baru.');
    }
}
