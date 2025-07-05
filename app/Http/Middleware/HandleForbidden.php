<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class HandleForbidden
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
            
            // Jika response adalah 403, render halaman forbidden
            if ($response->getStatusCode() === 403) {
                return Inertia::render('Forbidden', [
                    'user' => auth()->user() ? [
                        'name' => auth()->user()->name,
                        'role' => auth()->user()->role ?? 'User'
                    ] : null
                ]);
            }
            
            return $response;
        } catch (\Exception $e) {
            // Jika ada exception yang berhubungan dengan forbidden access
            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return Inertia::render('Forbidden', [
                    'user' => auth()->user() ? [
                        'name' => auth()->user()->name,
                        'role' => auth()->user()->role ?? 'User'
                    ] : null
                ]);
            }
            
            // Re-throw exception untuk error handling lainnya
            throw $e;
        }
    }
} 