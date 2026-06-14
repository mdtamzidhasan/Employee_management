<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.custom' => App\Http\Middleware\AuthMiddleware::class,
            'admin' => App\Http\Middleware\AdminMiddleware::class,
            'guest.custom' => App\Http\Middleware\GuestMiddleware::class,
            'admin.api'    => \App\Http\Middleware\AdminApiMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

    // Unauthorized Access (403) 
    $exceptions->render(function (AccessDeniedHttpException $e, $request) {
        Log::channel('security')->critical('Unauthorized access attempt', [
            'user_id' => auth()->id() ?? 'guest',
            'email'   => auth()->user()?->email ?? 'guest',
            'url'     => $request->fullUrl(),
            'ip'      => $request->ip(),
        ]);
    });

    // Rate Limit Exceeded (429) 
    $exceptions->render(function (ThrottleRequestsException $e, $request) {
        Log::channel('security')->critical('Rate limit exceeded', [
            'user_id' => auth()->id() ?? 'guest',
            'url'     => $request->fullUrl(),
            'ip'      => $request->ip(),
        ]);
    });

    // Page Not Found (404) 
    $exceptions->render(function (NotFoundHttpException $e, $request) {
        Log::channel('security')->warning('Page not found', [
            'user_id' => auth()->id() ?? 'guest',
            'url'     => $request->fullUrl(),
            'ip'      => $request->ip(),
        ]);
    });

    // Unauthenticated (401) 
    $exceptions->render(function (AuthenticationException $e, $request) {
        Log::channel('auth')->warning('Unauthenticated access attempt', [
            'url' => $request->fullUrl(),
            'ip'  => $request->ip(),
        ]);
    });

    //All Other Exceptions 
    $exceptions->reportable(function (Throwable $e) {
        if ($e instanceof ValidationException) {
            return false; // Validation error skip
        }

        Log::channel('security')->error('Unexpected exception', [
            'user_id'   => auth()->id() ?? 'guest',
            'url'       => request()->fullUrl(),
            'ip'        => request()->ip(),
            'exception' => get_class($e),
            'message'   => $e->getMessage(),
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
        ]);
    });

})->create();
