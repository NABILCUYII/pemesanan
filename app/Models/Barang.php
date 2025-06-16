<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'deskripsi',
        'kategori',
        'stok',
    ];

    protected $table = 'barang';

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }
}
