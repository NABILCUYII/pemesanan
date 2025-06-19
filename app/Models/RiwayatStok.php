<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatStok extends Model
{
    protected $table = 'riwayat_stok';
    
    protected $fillable = [
        'barang_id',
        'jumlah',
        'tipe',
        'keterangan'
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the barang that owns the riwayat stok.
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * Scope a query to only include records from the last month.
     */
    public function scopeLastMonth($query)
    {
        return $query->where('created_at', '>=', now()->subMonth());
    }

    /**
     * Scope a query to only include records of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('tipe', $type);
    }
} 