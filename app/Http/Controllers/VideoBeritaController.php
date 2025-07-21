<?php

namespace App\Http\Controllers;

use App\Models\VideoBerita;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class VideoBeritaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $videoBeritas = VideoBerita::orderBy('urutan', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return Inertia::render('VideoBerita/index', [
            'videoBeritas' => $videoBeritas,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('VideoBerita/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'video_url' => 'required|url',
            'thumbnail_url' => 'nullable|url',
            'sumber' => 'nullable|string|max:255',
            'tanggal_publish' => 'required|date',
            'is_active' => 'boolean',
            'urutan' => 'nullable|integer|min:0',
        ]);

        // Extract YouTube ID from URL
        $youtubeId = $this->extractYoutubeId($validated['video_url']);
        $embedUrl = $youtubeId ? "https://www.youtube.com/embed/{$youtubeId}" : null;
        $youtubeThumbnail = $youtubeId ? "https://img.youtube.com/vi/{$youtubeId}/maxresdefault.jpg" : null;

        VideoBerita::create([
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'video_url' => $validated['video_url'],
            'thumbnail_url' => $validated['thumbnail_url'],
            'sumber' => $validated['sumber'],
            'tanggal_publish' => $validated['tanggal_publish'],
            'is_active' => $validated['is_active'],
            'urutan' => $validated['urutan'],
            'youtube_id' => $youtubeId,
            'embed_url' => $embedUrl,
            'youtube_thumbnail' => $youtubeThumbnail,
        ]);

        return redirect()->route('video-berita.index')
            ->with('success', 'Video berita berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(VideoBerita $videoBerita)
    {
        return Inertia::render('VideoBerita/show', [
            'videoBerita' => $videoBerita,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VideoBerita $videoBerita)
    {
        return Inertia::render('VideoBerita/edit', [
            'videoBerita' => $videoBerita,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VideoBerita $videoBerita)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'video_url' => 'required|url',
            'thumbnail_url' => 'nullable|url',
            'sumber' => 'nullable|string|max:255',
            'tanggal_publish' => 'required|date',
            'is_active' => 'boolean',
            'urutan' => 'nullable|integer|min:0',
        ]);

        // Extract YouTube ID from URL
        $youtubeId = $this->extractYoutubeId($validated['video_url']);
        $embedUrl = $youtubeId ? "https://www.youtube.com/embed/{$youtubeId}" : null;
        $youtubeThumbnail = $youtubeId ? "https://img.youtube.com/vi/{$youtubeId}/maxresdefault.jpg" : null;

        $videoBerita->update([
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'video_url' => $validated['video_url'],
            'thumbnail_url' => $validated['thumbnail_url'],
            'sumber' => $validated['sumber'],
            'tanggal_publish' => $validated['tanggal_publish'],
            'is_active' => $validated['is_active'],
            'urutan' => $validated['urutan'],
            'youtube_id' => $youtubeId,
            'embed_url' => $embedUrl,
            'youtube_thumbnail' => $youtubeThumbnail,
        ]);

        return redirect()->route('video-berita.index')
            ->with('success', 'Video berita berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VideoBerita $videoBerita)
    {
        $videoBerita->delete();

        return redirect()->route('video-berita.index')
            ->with('success', 'Video berita berhasil dihapus!');
    }

    /**
     * Toggle active status of video
     */
    public function toggleStatus(VideoBerita $videoBerita)
    {
        $videoBerita->update([
            'is_active' => !$videoBerita->is_active
        ]);

        return back()->with('success', 
            $videoBerita->is_active 
                ? 'Video berita berhasil diaktifkan!' 
                : 'Video berita berhasil dinonaktifkan!'
        );
    }

    /**
     * Get active videos for dashboard
     */
    public function getActiveVideos()
    {
        $activeVideos = VideoBerita::where('is_active', true)
            ->orderBy('urutan', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return response()->json($activeVideos);
    }

    /**
     * Extract YouTube ID from various YouTube URL formats
     */
    private function extractYoutubeId($url)
    {
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/v\/([a-zA-Z0-9_-]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
