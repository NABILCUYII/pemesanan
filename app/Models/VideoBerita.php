<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoBerita extends Model
{
    use HasFactory;

    protected $table = 'video_beritas';

    protected $fillable = [
        'judul',
        'deskripsi',
        'video_url',
        'thumbnail_url',
        'sumber',
        'tanggal_publish',
        'is_active',
        'urutan'
    ];

    protected $casts = [
        'tanggal_publish' => 'date',
        'is_active' => 'boolean',
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
        return $query->orderBy('urutan', 'asc')->orderBy('tanggal_publish', 'desc');
    }

    /**
     * Get video ID from YouTube URL
     */
    public function getYoutubeIdAttribute()
    {
        $url = $this->video_url;
        
        // Handle different YouTube URL formats
        if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/youtu\.be\/([^?]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/youtube\.com\/embed\/([^?]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Get Google Drive file ID from URL
     */
    public function getGoogleDriveIdAttribute()
    {
        $url = $this->video_url;
        
        // Handle Google Drive sharing URLs
        if (preg_match('/drive\.google\.com\/file\/d\/([^\/]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/drive\.google\.com\/open\?id=([^&]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/docs\.google\.com\/uc\?id=([^&]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Get video type (youtube, googledrive, or other)
     */
    public function getVideoTypeAttribute()
    {
        $url = $this->video_url;
        
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            return 'youtube';
        }
        
        if (strpos($url, 'drive.google.com') !== false || strpos($url, 'docs.google.com') !== false) {
            return 'googledrive';
        }
        
        return 'other';
    }

    /**
     * Get embed URL for video
     */
    public function getEmbedUrlAttribute()
    {
        $videoType = $this->video_type;
        
        if ($videoType === 'youtube') {
            $videoId = $this->youtube_id;
            return $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
        }
        
        if ($videoType === 'googledrive') {
            $fileId = $this->google_drive_id;
            return $fileId ? "https://drive.google.com/file/d/{$fileId}/preview" : null;
        }
        
        return null;
    }

    /**
     * Get thumbnail URL for video
     */
    public function getYoutubeThumbnailAttribute()
    {
        $videoType = $this->video_type;
        
        if ($videoType === 'youtube') {
            $videoId = $this->youtube_id;
            return $videoId ? "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg" : null;
        }
        
        if ($videoType === 'googledrive') {
            // Google Drive doesn't provide direct thumbnail URLs, so we'll use a default
            return '/images/google-drive-thumbnail.jpg';
        }
        
        return null;
    }

    /**
     * Get video source name for display
     */
    public function getVideoSourceNameAttribute()
    {
        $videoType = $this->video_type;
        
        switch ($videoType) {
            case 'youtube':
                return 'YouTube';
            case 'googledrive':
                return 'Google Drive';
            default:
                return 'Video';
        }
    }
}
