<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PenggunaBaruBlockMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Jika user adalah pengguna baru, hanya izinkan akses ke halaman selamat datang
        if ($user && $user->role && ($user->role->role ?? '') === 'penggunaBARU') {
            if (!$request->routeIs('selamat-datang.index')) {
                return redirect()->route('selamat-datang.index');
            }
        }

        return $next($request);
    }
}