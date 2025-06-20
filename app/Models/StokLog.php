<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokLog extends Model
{
    use HasFactory;

    protected $table = 'stok_logs';

    protected $fillable = [
        'barang_id',
        'user_id',
        'jenis',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'keterangan',
        'referensi'
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'stok_sebelum' => 'integer',
        'stok_sesudah' => 'integer',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getJenisLabelAttribute()
    {
        return $this->jenis === 'masuk' ? 'Stok Masuk' : 'Stok Keluar';
    }

    public function getJenisColorAttribute()
    {
        return $this->jenis === 'masuk' ? 'text-green-600' : 'text-red-600';
    }

    public function getJenisBadgeAttribute()
    {
        return $this->jenis === 'masuk' ? 'default' : 'destructive';
    }
}
