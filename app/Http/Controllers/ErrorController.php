<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ErrorController extends Controller
{
    /**
     * Menampilkan halaman 404 Not Found
     */
    public function notFound()
    {
        return Inertia::render('NotFound');
    }

    /**
     * Menampilkan halaman 403 Unauthorized
     */
    public function unauthorized()
    {
        return Inertia::render('Unauthorized');
    }

    /**
     * Menampilkan halaman 403 Forbidden
     */
    public function forbidden()
    {
        $user = auth()->user();
        return Inertia::render('Forbidden', [
            'user' => $user ? [
                'name' => $user->name,
                'role' => $user->role ?? 'User'
            ] : null
        ]);
    }

    /**
     * Menampilkan halaman 500 Server Error
     */
    public function serverError()
    {
        return Inertia::render('ServerError');
    }

    /**
     * Menampilkan halaman loading
     */
    public function loading()
    {
        return Inertia::render('Loading');
    }

    /**
     * Menampilkan halaman No Permission - Anda belum memiliki izin masuk
     */
    public function noPermission()
    {
        $user = auth()->user();
        return Inertia::render('NoPermission', [
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role ?? 'User'
            ] : null
        ]);
    }
} 