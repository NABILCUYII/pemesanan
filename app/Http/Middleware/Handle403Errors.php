<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class Handle403Errors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);
            
            // Debug logging
            \Log::info('Handle403Errors: Response status = ' . $response->getStatusCode());
            \Log::info('Handle403Errors: X-Inertia header = ' . $request->header('X-Inertia'));
            
            // Check if response is 403 and request is Inertia
            if ($response->getStatusCode() === 403 && $request->header('X-Inertia')) {
                \Log::info('Handle403Errors: Rendering Unauthorized page');
                return Inertia::render('Unauthorized');
            }
            
            return $response;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            \Log::info('Handle403Errors: Caught AuthorizationException');
            if ($request->header('X-Inertia')) {
                \Log::info('Handle403Errors: Rendering Unauthorized page from AuthorizationException');
                return Inertia::render('Unauthorized');
            }
            throw $e;
        } catch (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e) {
            \Log::info('Handle403Errors: Caught AccessDeniedHttpException');
            if ($request->header('X-Inertia')) {
                \Log::info('Handle403Errors: Rendering Unauthorized page from AccessDeniedHttpException');
                return Inertia::render('Unauthorized');
            }
            throw $e;
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            \Log::info('Handle403Errors: Caught HttpException with status ' . $e->getStatusCode());
            if ($e->getStatusCode() === 403 && $request->header('X-Inertia')) {
                \Log::info('Handle403Errors: Rendering Unauthorized page from HttpException');
                return Inertia::render('Unauthorized');
            }
            throw $e;
        }
    }
} 