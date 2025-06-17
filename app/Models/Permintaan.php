<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    protected $table = 'permintaan';

    protected $fillable = [
        'user_id',
        'barang_id',
        'jumlah',
        'keterangan',
        'status',
        'alasan_approval',
        'catatan_approval',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
} 