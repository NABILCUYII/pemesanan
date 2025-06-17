<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanBarang extends Model
{
    protected $table = 'permintaan_barang';
    
    protected $fillable = [
        'user_id', 
        'barang_id', 
        'jumlah', 
        'alasan', 
        'tanggal_permintaan', 
        'status'
    ];

    protected $casts = [
        'tanggal_permintaan' => 'date',
        'jumlah' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
