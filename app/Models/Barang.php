<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Barang extends Model
{
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'deskripsi',
        'kategori',
        'stok',
        'status',
        'satuan',
        'lokasi',
    ];

    protected $table = 'barang';

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function stokLogs()
    {
        return $this->hasMany(StokLog::class);
    }

    public function addStokLog($jenis, $jumlah, $keterangan = null, $referensi = null, $user_id = null)
    {
        $stokSebelum = $this->stok;
        
        if ($jenis === 'masuk') {
            $this->stok += $jumlah;
        } else {
            $this->stok -= $jumlah;
        }
        
        // Ensure stok is not negative
        if ($this->stok < 0) {
            $this->stok = 0;
        }
        
        $this->save();

        return $this->stokLogs()->create([
            'user_id' => $user_id ?? Auth::id(),
            'jenis' => $jenis,
            'jumlah' => $jumlah,
            'stok_sebelum' => $stokSebelum,
            'stok_sesudah' => $this->stok,
            'keterangan' => $keterangan,
            'referensi' => $referensi
        ]);
    }

    // Scope untuk stok menipis
    public function scopeStokMenipis($query, $threshold = 5)
    {
        return $query->where('stok', '<=', $threshold);
    }
}
