<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Response as InertiaResponse;

class NoPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|InertiaResponse
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Jika user tidak login, redirect ke login
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Jika user memiliki role 'newUser' atau belum memiliki permission yang cukup
        if ($user->role && $user->role->role === 'newUser') {
            if ($request->header('X-Inertia')) {
                return Inertia::render('NoPermission', [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'role' => $user->role->role ?? 'newUser'
                    ]
                ]);
            }
            
            // Untuk non-Inertia requests, redirect ke halaman no-permission
            return redirect()->route('error.no-permission');
        }
        
        // Jika user memiliki role yang tidak diizinkan untuk halaman tertentu
        $requiredRole = $this->getRequiredRole($request);
        if ($requiredRole && (!$user->role || $user->role->role !== $requiredRole)) {
            if ($request->header('X-Inertia')) {
                return Inertia::render('NoPermission', [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'role' => $user->role->role ?? 'user'
                    ]
                ]);
            }
            
            return redirect()->route('error.no-permission');
        }
        
        return $next($request);
    }
    
    /**
     * Mendapatkan role yang diperlukan berdasarkan route
     */
    private function getRequiredRole(Request $request): ?string
    {
        $routeName = $request->route()->getName();
        
        // Mapping route ke role yang diperlukan
        $roleMapping = [
            'users.index' => 'admin',
            'users.create' => 'admin',
            'users.store' => 'admin',
            'users.edit' => 'admin',
            'users.update' => 'admin',
            'users.destroy' => 'admin',
            'laporan.index' => 'admin',
            'laporan.download' => 'admin',
            'laporan.download-permintaan' => 'admin',
            'laporan.download-peminjaman' => 'admin',
            'laporan.download-user' => 'admin',
            'laporan.download-excel' => 'admin',
            'laporan.download-permintaan-excel' => 'admin',
            'laporan.download-peminjaman-excel' => 'admin',
            'laporan.download-user-excel' => 'admin',
            'stok-log.index' => 'admin',
            'stok-log.show' => 'admin',
            'stok-log.barang' => 'admin',
            'video-berita.index' => 'admin',
            'video-berita.create' => 'admin',
            'video-berita.store' => 'admin',
            'video-berita.edit' => 'admin',
            'video-berita.update' => 'admin',
            'video-berita.destroy' => 'admin',
        ];
        
        return $roleMapping[$routeName] ?? null;
    }
} 