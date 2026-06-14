<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuthLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle user authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $userAgent = $request->userAgent();
            $ip = $request->ip();

            // Detect device details
            $deviceInfo = ActivityLogger::parseUserAgent($userAgent);

            // Create AuthLog record
            $authLog = AuthLog::create([
                'user_id' => $user->id,
                'login_at' => now(),
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'device' => $deviceInfo['device'],
                'platform' => $deviceInfo['platform'],
                'browser' => $deviceInfo['browser']
            ]);

            // Save auth log id in session for logout logging
            session(['auth_log_id' => $authLog->id]);

            // Log activity
            ActivityLogger::log("Login berhasil menggunakan {$deviceInfo['browser']} di {$deviceInfo['platform']} ({$deviceInfo['device']})");

            return redirect()->intended(route('dashboard'))
                ->with('success', "Selamat datang kembali, {$user->name}!");
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        // Log logout time if session has auth_log_id
        if ($logId = session('auth_log_id')) {
            $authLog = AuthLog::find($logId);
            if ($authLog) {
                $authLog->update(['logout_at' => now()]);
            }
        }

        // Log logout activity
        ActivityLogger::log("Logout dari sistem");

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
