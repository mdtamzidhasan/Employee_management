<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityLog;
use App\Models\User;

class SecurityLogController extends Controller
{
    public function index()
    {
        $logs = SecurityLog::with('user')
            ->latest()
            ->paginate(20);

        $stats = [
            'total_today'    => SecurityLog::whereDate('created_at', today())->count(),
            'failed_logins'  => SecurityLog::where('event_type', SecurityLog::EVENT_LOGIN_FAILED)
                                    ->whereDate('created_at', today())->count(),
            'critical_today' => SecurityLog::where('severity', SecurityLog::SEVERITY_CRITICAL)
                                    ->whereDate('created_at', today())->count(),
            'locked_accounts' => User::whereNotNull('locked_until')
                                    ->where('locked_until', '>', now())
                                    ->count(),
        ];

        return view('admin.security-logs', compact('logs', 'stats'));
    }
}
