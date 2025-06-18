<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelamatDatang extends Model
{
    protected $table = 'selamat_datang';
    
    protected $fillable = [
        'title',
        'content'
    ];
}
