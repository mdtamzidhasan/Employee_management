<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PasswordConfiguration;
use App\Models\SecurityLog;
use App\Services\SecurityLogger;
use Illuminate\Http\Request;

class PasswordConfigController extends Controller
{
    public function __construct(protected SecurityLogger $logger) {}

    public function show()
    {
        $config = PasswordConfiguration::getConfig();
        return view('admin.password-config', compact('config'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'min_length'             => ['required', 'integer', 'min:8',  'max:32'],
            'max_length'             => ['required', 'integer', 'min:32', 'max:128'],
            'min_words'              => ['required', 'integer', 'min:0',  'max:10'],
            'require_uppercase'      => ['required', 'boolean'],
            'require_lowercase'      => ['required', 'boolean'],
            'require_number'         => ['required', 'boolean'],
            'require_special_char'   => ['required', 'boolean'],
            'password_expiry_days'   => ['required', 'integer', 'min:1',  'max:365'],
            'change_cooldown_hours'  => ['required', 'integer', 'min:1',  'max:168'],
            'password_history_count' => ['required', 'integer', 'min:1',  'max:20'],
        ]);

        $oldConfig = PasswordConfiguration::getConfig()->toArray();

        PasswordConfiguration::getConfig()->update($validated);

        $this->logger->warning(
            SecurityLog::EVENT_CONFIG_CHANGED,
            "Password configuration changed by admin: " . auth()->user()->email,
            [
                'admin_id'     => auth()->id(),
                'admin_email'  => auth()->user()->email,
                'old_config'   => $oldConfig,
                'new_config'   => $validated,
            ]
        );

        return back()->with('success', 'Password configuration saved successfully.');
    }
}