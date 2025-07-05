<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'user_id',
        'barang_id',
        'jumlah',
        'keterangan',
        'tanggal_peminjaman',
        'tanggal_pengembalian',
        'due_date',
        'status',
        'alasan_approval',
        'catatan_approval',
        'approved_by',
        'approved_at',
        'kondisi_barang',
        'catatan_pengembalian',
        'returned_by',
        'returned_at',
    ];

    /**
     * Get the user that owns the Peminjaman
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the barang that owns the Peminjaman
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * Get the user who processed the return
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    /**
     * Get the user who approved the peminjaman
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    protected $casts = [
        'tanggal_peminjaman' => 'date',
        'tanggal_pengembalian' => 'date',
        'due_date' => 'date',
        'approved_at' => 'datetime',
        'returned_at' => 'datetime',
    ];
}
