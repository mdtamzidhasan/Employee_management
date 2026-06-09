<?php

namespace App\Http\Middleware;

use App\Models\SecurityLog;
use App\Services\SecurityLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function __construct(protected SecurityLogger $logger) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isAdmin()) {

            // Unauthorized access log 
            $this->logger->critical(
                SecurityLog::EVENT_UNAUTHORIZED,
                "Unauthorized admin access attempt by: " . auth()->user()->email,
                [
                    'user_id'    => auth()->id(),
                    'user_email' => auth()->user()->email,
                    'user_role'  => auth()->user()->role,
                    'url'        => $request->fullUrl(),
                    'method'     => $request->method(),
                ]
            );

            abort(403, 'Access denied. Admins only.');
        }

        return $next($request);
    }
}