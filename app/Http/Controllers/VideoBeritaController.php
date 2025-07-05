<?php

namespace App\Http\Controllers;

use App\Models\VideoBerita;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VideoBeritaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }

        $videoBeritas = VideoBerita::ordered()->get()->map(function ($video) {
            return [
                'id' => $video->id,
                'judul' => $video->judul,
                'deskripsi' => $video->deskripsi,
                'video_url' => $video->video_url,
                'thumbnail_url' => $video->thumbnail_url,
                'sumber' => $video->sumber,
                'tanggal_publish' => $video->tanggal_publish,
                'is_active' => $video->is_active,
                'urutan' => $video->urutan,
                'youtube_id' => $video->youtube_id,
                'google_drive_id' => $video->google_drive_id,
                'video_type' => $video->video_type,
                'embed_url' => $video->embed_url,
                'youtube_thumbnail' => $video->youtube_thumbnail,
                'video_source_name' => $video->video_source_name,
            ];
        });
        
        return Inertia::render('VideoBerita/index', [
            'videoBeritas' => $videoBeritas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }

        return Inertia::render('VideoBerita/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'video_url' => 'required|url',
            'thumbnail_url' => 'nullable|url',
            'sumber' => 'nullable|string|max:255',
            'tanggal_publish' => 'required|date',
            'is_active' => 'boolean',
            'urutan' => 'integer|min:0'
        ]);

        VideoBerita::create($request->all());

        return redirect()->route('video-berita.index')
            ->with('success', 'Video berita berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(VideoBerita $videoBerita)
    {
        $videoData = [
            'id' => $videoBerita->id,
            'judul' => $videoBerita->judul,
            'deskripsi' => $videoBerita->deskripsi,
            'video_url' => $videoBerita->video_url,
            'thumbnail_url' => $videoBerita->thumbnail_url,
            'sumber' => $videoBerita->sumber,
            'tanggal_publish' => $videoBerita->tanggal_publish,
            'is_active' => $videoBerita->is_active,
            'urutan' => $videoBerita->urutan,
            'youtube_id' => $videoBerita->youtube_id,
            'google_drive_id' => $videoBerita->google_drive_id,
            'video_type' => $videoBerita->video_type,
            'embed_url' => $videoBerita->embed_url,
            'youtube_thumbnail' => $videoBerita->youtube_thumbnail,
            'video_source_name' => $videoBerita->video_source_name,
        ];
        
        return Inertia::render('VideoBerita/show', [
            'videoBerita' => $videoData
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VideoBerita $videoBerita)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }

        $videoData = [
            'id' => $videoBerita->id,
            'judul' => $videoBerita->judul,
            'deskripsi' => $videoBerita->deskripsi,
            'video_url' => $videoBerita->video_url,
            'thumbnail_url' => $videoBerita->thumbnail_url,
            'sumber' => $videoBerita->sumber,
            'tanggal_publish' => $videoBerita->tanggal_publish,
            'is_active' => $videoBerita->is_active,
            'urutan' => $videoBerita->urutan,
            'youtube_id' => $videoBerita->youtube_id,
            'google_drive_id' => $videoBerita->google_drive_id,
            'video_type' => $videoBerita->video_type,
            'embed_url' => $videoBerita->embed_url,
            'youtube_thumbnail' => $videoBerita->youtube_thumbnail,
            'video_source_name' => $videoBerita->video_source_name,
        ];

        return Inertia::render('VideoBerita/edit', [
            'videoBerita' => $videoData
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VideoBerita $videoBerita)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'video_url' => 'required|url',
            'thumbnail_url' => 'nullable|url',
            'sumber' => 'nullable|string|max:255',
            'tanggal_publish' => 'required|date',
            'is_active' => 'boolean',
            'urutan' => 'integer|min:0'
        ]);

        $videoBerita->update($request->all());

        return redirect()->route('video-berita.index')
            ->with('success', 'Video berita berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VideoBerita $videoBerita)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }

        $videoBerita->delete();

        return redirect()->route('video-berita.index')
            ->with('success', 'Video berita berhasil dihapus');
    }

    /**
     * Get active video berita for dashboard
     */
    public function getActiveVideos()
    {
        $videos = VideoBerita::active()->ordered()->take(3)->get()->map(function ($video) {
            return [
                'id' => $video->id,
                'judul' => $video->judul,
                'deskripsi' => $video->deskripsi,
                'video_url' => $video->video_url,
                'thumbnail_url' => $video->thumbnail_url,
                'sumber' => $video->sumber,
                'tanggal_publish' => $video->tanggal_publish,
                'is_active' => $video->is_active,
                'urutan' => $video->urutan,
                'youtube_id' => $video->youtube_id,
                'google_drive_id' => $video->google_drive_id,
                'video_type' => $video->video_type,
                'embed_url' => $video->embed_url,
                'youtube_thumbnail' => $video->youtube_thumbnail,
                'video_source_name' => $video->video_source_name,
            ];
        });
        
        return response()->json($videos);
    }
}
