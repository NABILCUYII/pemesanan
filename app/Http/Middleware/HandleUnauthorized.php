<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class HandleUnauthorized
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
            
            // Jika response adalah 403, render halaman unauthorized
            if ($response->getStatusCode() === 403) {
                return Inertia::render('Unauthorized');
            }
            
            return $response;
        } catch (\Exception $e) {
            // Jika ada exception yang berhubungan dengan authorization
            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return Inertia::render('Unauthorized');
            }
            
            // Re-throw exception untuk error handling lainnya
            throw $e;
        }
    }
} 