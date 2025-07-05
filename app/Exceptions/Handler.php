<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handle 500 errors for Inertia requests
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->header('X-Inertia')) {
                // Untuk Inertia requests, render halaman 500 custom
                return Inertia::render('ServerError');
            }
        });

        // Handle 403 errors for Inertia requests
        $this->renderable(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if ($request->header('X-Inertia') || $request->wantsJson()) {
                // Untuk Inertia requests, render halaman 403 custom
                return Inertia::render('Unauthorized');
            }
        });

        // Handle 403 errors for Inertia requests (alternative)
        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, Request $request) {
            if ($request->header('X-Inertia') || $request->wantsJson()) {
                // Untuk Inertia requests, render halaman 403 custom
                return Inertia::render('Unauthorized');
            }
        });

        // Handle 403 errors for Inertia requests (HTTP status)
        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            if (($request->header('X-Inertia') || $request->wantsJson()) && $e->getStatusCode() === 403) {
                return Inertia::render('Unauthorized');
            }
        });
    }
} 