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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;



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
            'admin.api'    => \App\Http\Middleware\AdminApiMiddleware::class,
            'service.key'  => \App\Http\Middleware\VerifyServiceApiKey::class,
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

    //  Unauthenticated (401) 
    $exceptions->render(function (AuthenticationException $e, $request) {
        Log::channel('auth')->warning('Unauthenticated access attempt', [
            'url' => $request->fullUrl(),
            'ip'  => $request->ip(),
        ]);
    });

    //Method Not Allowed (405) 
    $exceptions->render(function (MethodNotAllowedHttpException $e, $request) {
        Log::channel('security')->warning('Method not allowed', [
            'user_id' => auth()->id() ?? 'guest',
            'method'  => $request->method(),
            'url'     => $request->fullUrl(),
            'ip'      => $request->ip(),
        ]);
    });

    //  CSRF Token Mismatch (419) 
    $exceptions->render(function (TokenMismatchException $e, $request) {
        Log::channel('security')->warning('CSRF token mismatch', [
            'user_id' => auth()->id() ?? 'guest',
            'url'     => $request->fullUrl(),
            'ip'      => $request->ip(),
            'method'  => $request->method(),
        ]);
    });

    // Model Not Found 
    $exceptions->render(function (ModelNotFoundException $e, $request) {
        Log::channel('security')->warning('Model not found — possible enumeration attempt', [
            'user_id' => auth()->id() ?? 'guest',
            'model'   => $e->getModel(),
            'url'     => $request->fullUrl(),
            'ip'      => $request->ip(),
        ]);
    });

    //File Upload Too Large
    $exceptions->render(function (PostTooLargeException $e, $request) {
        Log::channel('security')->warning('File upload too large', [
            'user_id' => auth()->id() ?? 'guest',
            'url'     => $request->fullUrl(),
            'ip'      => $request->ip(),
        ]);
    });

    //  Database Query Errors 
    $exceptions->report(function (QueryException $e) {
        Log::channel('security')->critical('Database query error', [
            'user_id' => auth()->id() ?? 'guest',
            'url'     => request()->fullUrl(),
            'message' => $e->getMessage(),
        ]);
    });

    // All Other Exceptions 
    $exceptions->reportable(function (Throwable $e) {
        if ($e instanceof ValidationException) {
            return false;
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
