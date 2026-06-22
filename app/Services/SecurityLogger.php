<?php

namespace App\Services;

use App\Models\SecurityLog;
use Illuminate\Http\Request;

class SecurityLogger
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    // Main log method 
    public function log(
        string $eventType,
        string $severity,
        string $description,
        ?int $userId = null,
        array $metadata = []
    ): void {
        SecurityLog::create([
            'user_id'     => $userId ?? (auth()->check() ? auth()->id() : null),
            'event_type'  => $eventType,
            'severity'    => $severity,
            'ip_address'  => $this->request->ip(),
            'user_agent'  => $this->request->userAgent(),
            'url'         => $this->request->fullUrl(),
            'description' => $description,
            'metadata'    => $metadata,
        ]);
    }

    // ── Shortcut methods ──────────────────────────────────
    public function info(string $event, string $desc, array $meta = []): void
    {
        $this->log($event, SecurityLog::SEVERITY_INFO, $desc, null, $meta);
    }

    public function warning(string $event, string $desc, array $meta = []): void
    {
        $this->log($event, SecurityLog::SEVERITY_WARNING, $desc, null, $meta);
    }

    public function critical(string $event, string $desc, array $meta = []): void
    {
        $this->log($event, SecurityLog::SEVERITY_CRITICAL, $desc, null, $meta);
    }

    // ── Alert check — suspicious activity ─────────────────
    public function checkAndAlert(string $eventType, string $ip, int $threshold = 5): bool
    {
        
        $count = SecurityLog::where('event_type', $eventType)
            ->where('ip_address', $ip)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();

        return $count >= $threshold;
    }
}