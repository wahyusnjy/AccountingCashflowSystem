<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Log a user activity in the database.
     *
     * @param string $activity
     * @return ActivityLog
     */
    public static function log(string $activity): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => $activity,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    /**
     * Parse User-Agent string to extract Browser, OS, and Device details.
     *
     * @param string|null $userAgentString
     * @return array{browser: string, platform: string, device: string}
     */
    public static function parseUserAgent(?string $userAgentString): array
    {
        $browser = "Unknown Browser";
        $platform = "Unknown OS";
        $deviceType = "Desktop";

        if (empty($userAgentString)) {
            return [
                'browser' => $browser,
                'platform' => $platform,
                'device' => $deviceType,
            ];
        }

        // Detect Platform / OS
        if (preg_match('/android/i', $userAgentString)) {
            $platform = 'Android';
            $deviceType = 'Mobile';
        } elseif (preg_match('/ipad/i', $userAgentString)) {
            $platform = 'iOS (iPad)';
            $deviceType = 'Tablet';
        } elseif (preg_match('/iphone/i', $userAgentString)) {
            $platform = 'iOS (iPhone)';
            $deviceType = 'Mobile';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgentString)) {
            $platform = 'macOS';
        } elseif (preg_match('/windows|win32/i', $userAgentString)) {
            $platform = 'Windows';
        } elseif (preg_match('/linux/i', $userAgentString)) {
            $platform = 'Linux';
        }

        // Detect Browser
        if (preg_match('/chrome/i', $userAgentString) && !preg_match('/edge|edg/i', $userAgentString) && !preg_match('/opr/i', $userAgentString)) {
            $browser = 'Chrome';
        } elseif (preg_match('/safari/i', $userAgentString) && !preg_match('/chrome/i', $userAgentString)) {
            $browser = 'Safari';
        } elseif (preg_match('/firefox/i', $userAgentString)) {
            $browser = 'Firefox';
        } elseif (preg_match('/edge|edg/i', $userAgentString)) {
            $browser = 'Edge';
        } elseif (preg_match('/opera|opr/i', $userAgentString)) {
            $browser = 'Opera';
        }

        return [
            'browser' => $browser,
            'platform' => $platform,
            'device' => $deviceType,
        ];
    }
}
