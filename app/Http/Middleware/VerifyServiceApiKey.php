<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use App\Models\SecurityLog;
use App\Services\SecurityLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyServiceApiKey
{
    public function __construct(protected SecurityLogger $logger) {}

    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-Service-Key');

        if (!$key) {
            $this->logger->warning(
                SecurityLog::EVENT_UNAUTHORIZED,
                "Internal API access attempt without service key",
                ['url' => $request->fullUrl(), 'ip' => $request->ip()]
            );

            return response()->json(['message' => 'Service API key required'], 401);
        }

        $hashedKey = hash('sha256', $key);

        $apiKey = ApiKey::where('key', $hashedKey)
                         ->where('is_active', true)
                         ->first();

        if (!$apiKey) {
            $this->logger->critical(
                SecurityLog::EVENT_UNAUTHORIZED,
                "Internal API access attempt with invalid service key",
                ['url' => $request->fullUrl(), 'ip' => $request->ip()]
            );

            return response()->json(['message' => 'Invalid or inactive service key'], 403);
        }

        $apiKey->update([
            'last_used_at' => now(),
            'last_used_ip' => $request->ip(),
        ]);

        return $next($request);
    }
}