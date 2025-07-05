<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $middleware = [
        \App\Http\Middleware\HandleInertiaRequests::class,
        \App\Http\Middleware\HandleAppearance::class,
        \App\Http\Middleware\AdminMiddleware::class,
        \App\Http\Middleware\Handle403Errors::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            // Web middleware group
        ],

        'api' => [
            // API middleware group
        ],
    ];

    /**
     * The application's route middleware aliases.
     *
     * Aliases may be used instead of class names to assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'user' => \App\Http\Middleware\UserMiddleware::class,
        'peminjaman' => \App\Http\Middleware\PeminjamanMiddleware::class,
        'permintaan' => \App\Http\Middleware\PermintaanMiddleware::class,
        'barang' => \App\Http\Middleware\BarangMiddleware::class,
        'barang-rusak' => \App\Http\Middleware\BarangRusakMiddleware::class,
        'stok-log' => \App\Http\Middleware\StokLogMiddleware::class,
        'laporan' => \App\Http\Middleware\LaporanMiddleware::class,
        'riwayat' => \App\Http\Middleware\RiwayatMiddleware::class,
        'settings' => \App\Http\Middleware\SettingsMiddleware::class,
        'appearance' => \App\Http\Middleware\HandleAppearance::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'handle.unauthorized' => \App\Http\Middleware\HandleUnauthorized::class,
        'handle.forbidden' => \App\Http\Middleware\HandleForbidden::class,
        'handle.all.errors' => \App\Http\Middleware\HandleAllErrors::class,
        'handle.403' => \App\Http\Middleware\Handle403Errors::class,
        'user' => \App\Http\Middleware\UserMiddleware::class,
        'peminjaman' => \App\Http\Middleware\PeminjamanMiddleware::class,
        'permintaan' => \App\Http\Middleware\PermintaanMiddleware::class,
        'barang' => \App\Http\Middleware\BarangMiddleware::class,
        'barang-rusak' => \App\Http\Middleware\BarangRusakMiddleware::class,
        'stok-log' => \App\Http\Middleware\StokLogMiddleware::class,
        'laporan' => \App\Http\Middleware\LaporanMiddleware::class,
        'riwayat' => \App\Http\Middleware\RiwayatMiddleware::class,

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        $this->middlewareGroups['web'] = [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\HandleAppearance::class,
            \App\Http\Middleware\AdminMiddleware::class,
        ];
        $this->middlewareGroups['api'] = [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\HandleAppearance::class,
            \App\Http\Middleware\AdminMiddleware::class,
        ];
        $this->middlewareGroups['admin'] = [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\HandleAppearance::class,
            \App\Http\Middleware\AdminMiddleware::class,
        ];
        $this->middlewareGroups['user'] = [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\HandleAppearance::class,
            \App\Http\Middleware\UserMiddleware::class,
        ];
        $this->middlewareGroups['peminjaman'] = [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\HandleAppearance::class,
            \App\Http\Middleware\PeminjamanMiddleware::class,
        ];
        $this->middlewareGroups['permintaan'] = [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\HandleAppearance::class,
            \App\Http\Middleware\PermintaanMiddleware::class,
        ];
        $this->middlewareGroups['barang'] = [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\HandleAppearance::class,
            \App\Http\Middleware\BarangMiddleware::class,
        ];
        $this->middlewareGroups['barang-rusak'] = [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\HandleAppearance::class,
            \App\Http\Middleware\BarangRusakMiddleware::class,
        ];
        $this->middlewareGroups['stok-log'] = [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\HandleAppearance::class,
            \App\Http\Middleware\StokLogMiddleware::class,
        ];
        $this->middlewareGroups['laporan'] = [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\HandleAppearance::class,
            \App\Http\Middleware\LaporanMiddleware::class,
        ];
        $this->middlewareGroups['riwayat'] = [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\HandleAppearance::class,
            \App\Http\Middleware\RiwayatMiddleware::class,
        ];
        $this->middlewareGroups['settings'] = [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\HandleAppearance::class,
            \App\Http\Middleware\SettingsMiddleware::class,
        ];
        $this->middlewareGroups['appearance'] = [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\HandleAppearance::class,
        ];
        $this->middlewareGroups['admin'] = [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\HandleAppearance::class,
            \App\Http\Middleware\AdminMiddleware::class,
        ];
        $this->middlewareGroups['user'] = [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\HandleAppearance::class,
            \App\Http\Middleware\UserMiddleware::class,
        ];
        
    }
} 