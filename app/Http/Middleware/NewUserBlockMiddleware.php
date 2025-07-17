<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Response as InertiaResponse;

class NewUserBlockMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|InertiaResponse
    {
        $user = $request->user();
        $currentRoute = $request->route() ? $request->route()->getName() : null;

        // Only block if user is 'newUser'
        if ($user && $user->role && $user->role->role === 'newUser') {
            // Routes yang diizinkan untuk newUser
            $allowedRoutes = [
                'selamat-datang.index',
                'logout',
            ];

            // Jika route tidak diizinkan, redirect ke selamat-datang
            if (!in_array($currentRoute, $allowedRoutes)) {
                // Untuk Inertia requests, render halaman NoPermission
                if ($request->header('X-Inertia')) {
                    return \Inertia\Inertia::render('NoPermission', [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'role' => $user->role->role ?? 'User'
                        ]
                    ]);
                }
                
                // Untuk non-Inertia requests, redirect ke selamat-datang
                return redirect()->route('selamat-datang.index');
            }
        }

        return $next($request);
    }
} 