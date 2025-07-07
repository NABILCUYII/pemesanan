<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Inventaris extends Model
{
    protected $table = 'inventaris';

    protected $fillable = [
        'user_id',
        'barang_id',
        'nomor_inventaris',
        'jumlah',
        'keterangan',
        'tanggal_peminjaman',
        'tanggal_pengembalian',
        'due_date',
        'status',
        'kondisi_barang',
        'catatan_pengembalian',
        'lokasi_peminjaman',
        'alasan_approval',
        'catatan_approval',
        'approved_by',
        'approved_at',
        'returned_by',
        'returned_at',
        'started_at',
        'started_by',
    ];

    protected $casts = [
        'tanggal_peminjaman' => 'date',
        'tanggal_pengembalian' => 'date',
        'due_date' => 'date',
        'approved_at' => 'datetime',
        'returned_at' => 'datetime',
        'started_at' => 'datetime',
    ];

    /**
     * Get the user that owns the Inventaris
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the barang that owns the Inventaris
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * Get the user who approved the inventaris
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who processed the return
     */
    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    /**
     * Get the user who started the process
     */
    public function startedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    /**
     * Generate unique nomor inventaris
     */
    public static function generateNomorInventaris(): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');
        
        // Get the last inventaris number for this month
        $lastInventaris = self::where('nomor_inventaris', 'like', "{$prefix}{$year}{$month}%")
            ->orderBy('nomor_inventaris', 'desc')
            ->first();
        
        if ($lastInventaris) {
            $lastNumber = (int) substr($lastInventaris->nomor_inventaris, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if inventaris is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status !== 'returned' && 
               $this->status !== 'rejected' && 
               $this->due_date < Carbon::today();
    }

    /**
     * Get status text in Indonesian
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'in_progress' => 'Proses Peminjaman',
            'rejected' => 'Ditolak',
            'returned' => 'Dikembalikan',
            'overdue' => 'Terlambat',
            'maintenance' => 'Maintenance',
            default => $this->status
        };
    }

    /**
     * Get kondisi barang text in Indonesian
     */
    public function getKondisiBarangTextAttribute(): string
    {
        return match($this->kondisi_barang) {
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            'hilang' => 'Hilang',
            default => '-'
        };
    }

    /**
     * Scope for overdue items
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::today())
                    ->whereNotIn('status', ['returned', 'rejected']);
    }

    /**
     * Scope for active items (not returned or rejected)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['returned', 'rejected']);
    }
} 