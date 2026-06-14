<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AuthLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display the audit log viewer.
     */
    public function index(Request $request)
    {
        $userId = $request->input('user_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Fetch users for filtering dropdown
        $usersList = User::orderBy('name')->get();

        // 1. Query Auth Logs (login/logout histories)
        $authLogsQuery = AuthLog::with('user')->orderBy('login_at', 'desc');

        // 2. Query Action Activity Logs
        $activityLogsQuery = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Apply filters
        if ($userId) {
            $authLogsQuery->where('user_id', $userId);
            $activityLogsQuery->where('user_id', $userId);
        }

        if ($startDate) {
            $authLogsQuery->whereDate('login_at', '>=', $startDate);
            $activityLogsQuery->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $authLogsQuery->whereDate('login_at', '<=', $endDate);
            $activityLogsQuery->whereDate('created_at', '<=', $endDate);
        }

        $authLogs = $authLogsQuery->paginate(15, ['*'], 'auth_page')->withQueryString();
        $activityLogs = $activityLogsQuery->paginate(15, ['*'], 'activity_page')->withQueryString();

        return view('pages.audit.index', compact('authLogs', 'activityLogs', 'usersList'));
    }
}
