<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SelamatDatang;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class SelamatDatangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('SelamatDatang');
    }

}
