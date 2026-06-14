<?php

namespace App\Http\Middleware;

use App\Models\SecurityLog;
use App\Services\SecurityLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminApiMiddleware
{
    public function __construct(protected SecurityLogger $logger) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (!$request->user()->isAdmin()) {

            $this->logger->critical(
                SecurityLog::EVENT_UNAUTHORIZED,
                "Unauthorized admin API access attempt by: " . $request->user()->email,
                [
                    'user_id'    => $request->user()->id,
                    'user_email' => $request->user()->email,
                    'url'        => $request->fullUrl(),
                    'method'     => $request->method(),
                ]
            );

            return response()->json([
                'message' => 'Access denied. Admins only.',
            ], 403);
        }

        return $next($request);
    }
}