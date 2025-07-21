<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoBerita extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'deskripsi',
        'video_url',
        'thumbnail_url',
        'sumber',
        'tanggal_publish',
        'is_active',
        'urutan',
        'youtube_id',
        'embed_url',
        'youtube_thumbnail',
    ];

    protected $casts = [
        'tanggal_publish' => 'date',
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    /**
     * Scope untuk video yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk mengurutkan berdasarkan urutan
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Get formatted tanggal publish
     */
    public function getFormattedTanggalPublishAttribute()
    {
        return $this->tanggal_publish->format('d F Y');
    }

    /**
     * Get YouTube embed URL
     */
    public function getEmbedUrlAttribute($value)
    {
        if ($value) {
            return $value;
        }

        if ($this->youtube_id) {
            return "https://www.youtube.com/embed/{$this->youtube_id}";
        }

        return null;
    }

    /**
     * Get YouTube thumbnail URL
     */
    public function getYoutubeThumbnailAttribute($value)
    {
        if ($value) {
            return $value;
        }

        if ($this->youtube_id) {
            return "https://img.youtube.com/vi/{$this->youtube_id}/maxresdefault.jpg";
        }

        return null;
    }

    /**
     * Get thumbnail URL (custom or YouTube)
     */
    public function getThumbnailUrlAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return $this->youtube_thumbnail;
    }
}
