<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HandleAllErrors
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
            
            // Handle different HTTP status codes for Inertia requests
            if ($request->header('X-Inertia') || $request->wantsJson()) {
                switch ($response->getStatusCode()) {
                    case 403:
                        return Inertia::render('Unauthorized')->toResponse($request);
                    case 404:
                        return Inertia::render('NotFound')->toResponse($request);
                    case 500:
                        return Inertia::render('ServerError')->toResponse($request);
                    default:
                        return $response;
                }
            }
            
            return $response;
        } catch (AuthorizationException $e) {
            if ($request->header('X-Inertia') || $request->wantsJson()) {
                return Inertia::render('Unauthorized')->toResponse($request);
            }
            throw $e;
        } catch (AccessDeniedHttpException $e) {
            if ($request->header('X-Inertia') || $request->wantsJson()) {
                return Inertia::render('Unauthorized')->toResponse($request);
            }
            throw $e;
        } catch (NotFoundHttpException $e) {
            if ($request->header('X-Inertia') || $request->wantsJson()) {
                return Inertia::render('NotFound')->toResponse($request);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->header('X-Inertia') || $request->wantsJson()) {
                return Inertia::render('ServerError')->toResponse($request);
            }
            throw $e;
        }
    }
} 