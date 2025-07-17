<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Membatasi akses user dengan role 'newUser' hanya ke halaman selamat-datang
     */
    protected function checkNewUserBlock($currentRoute = null)
    {
        $user = Auth::user();
        $currentRoute = $currentRoute ?? (request()->route() ? request()->route()->getName() : null);
        if ($user && $user->role && $user->role->role === 'newUser') {
            if ($currentRoute !== 'selamat-datang.index' && $currentRoute !== 'logout') {
                return redirect()->route('selamat-datang.index');
            }
        }
        return null;
    }
}
