<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->check()) {
            return auth()->user()->isAdmin() 
            ? redirect()->route('admin.dashboard') 
            : redirect()->route('employee.profile');
        }
        return $next($request);
    }
}
