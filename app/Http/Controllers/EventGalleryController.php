<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventGalleryRequest;
use App\Http\Requests\UpdateEventGalleryRequest;
use App\Models\EventGallery;
use App\Models\Event;
use App\Services\GalleryService\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EventGalleryController extends Controller
{
    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    public function index()
    {
        $galleries = EventGallery::with('event')->latest()->get();

        return view('galleries.index', compact('galleries'));
    }

    public function create()
    {
        $events = Event::all();
        
        return view('galleries.create', compact('events'));
    }

    /**
     * Store new gallery with file upload OR Google Drive links
     */
    public function store(StoreEventGalleryRequest $request)
    {
        $data = $request->validated();
        
        $imagePaths = [];
        
        // Handle file upload (existing method)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('galleries', 'public');
                $imagePaths[] = $path;
            }
        }
        
        // Handle Google Drive links
        if ($request->has('google_drive_links') && !empty($request->google_drive_links)) {
            $links = explode("\n", $request->google_drive_links);
            $links = array_filter(array_map('trim', $links));
            
            foreach ($links as $link) {
                $fileId = $this->googleDriveService->extractFileId($link);
                if ($fileId) {
                    $result = $this->googleDriveService->downloadFile($fileId);
                    if ($result['success']) {
                        $imagePaths[] = $result['path'];
                    }
                }
            }
        }
        
        if (empty($imagePaths)) {
            return redirect()->back()
                ->with('error', 'Tidak ada gambar yang berhasil diupload')
                ->withInput();
        }
        
        $data['image'] = $imagePaths;
        $data['uploaded_by'] = Auth::id();

        EventGallery::create($data);

        return redirect()->route('galleries.index')
            ->with('success', 'Gallery berhasil ditambahkan');
    }

    public function edit(EventGallery $eventGallery)
    {
        $events = Event::all();
        
        return view('galleries.edit', compact('eventGallery', 'events'));
    }

    public function update(UpdateEventGalleryRequest $request, EventGallery $eventGallery)
    {
        $data = $request->validated();
        $newImages = [];

        // Handle new file uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('galleries', 'public');
                $newImages[] = $path;
            }
        }
        
        // Handle Google Drive links
        if ($request->has('google_drive_links') && !empty($request->google_drive_links)) {
            $links = explode("\n", $request->google_drive_links);
            $links = array_filter(array_map('trim', $links));
            
            foreach ($links as $link) {
                $fileId = $this->googleDriveService->extractFileId($link);
                if ($fileId) {
                    $result = $this->googleDriveService->downloadFile($fileId);
                    if ($result['success']) {
                        $newImages[] = $result['path'];
                    }
                }
            }
        }
        
        // If there are new images, merge with existing
        if (!empty($newImages)) {
            $existingImages = $eventGallery->image ?? [];
            if (!is_array($existingImages)) {
                $existingImages = json_decode($existingImages, true) ?? [];
            }
            $eventGallery->image = array_merge($existingImages, $newImages);
        }
        
        $eventGallery->save();

        return redirect()->route('galleries.index')
            ->with('success', 'Gallery berhasil diupdate');
    }

    public function show(EventGallery $eventGallery)
    {
        $eventGallery->load(['event', 'uploader']);
        $images = is_array($eventGallery->image) ? $eventGallery->image : json_decode($eventGallery->image, true);
        
        return view('galleries.show', compact('eventGallery', 'images'));
    }

    /**
     * Remove the specified gallery from storage.
     */
    public function destroy(EventGallery $eventGallery)  // ← Ganti parameter name
    {
        try {
            // Hapus semua gambar dari storage
            $images = $eventGallery->image;
            
            if (is_array($images)) {
                foreach ($images as $image) {
                    if ($image && Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                }
            } elseif ($images && is_string($images)) {
                // Handle jika gambar berupa string JSON
                $decodedImages = json_decode($images, true);
                if (is_array($decodedImages)) {
                    foreach ($decodedImages as $image) {
                        if ($image && Storage::disk('public')->exists($image)) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                } elseif ($images && Storage::disk('public')->exists($images)) {
                    Storage::disk('public')->delete($images);
                }
            }
            
            // Hapus record dari database
            $deleted = $eventGallery->delete();
            
            if (!$deleted) {
                return redirect()->route('galleries.index')
                    ->with('error', 'Gagal menghapus gallery');
            }
            
            return redirect()->route('galleries.index')
                ->with('success', 'Gallery berhasil dihapus');
                
        } catch (\Exception $e) {
            \Log::error('Error deleting gallery: ' . $e->getMessage());
            return redirect()->route('galleries.index')
                ->with('error', 'Gagal menghapus gallery: ' . $e->getMessage());
        }
    }
    
    public function deleteImage(EventGallery $eventGallery, $imageIndex)
    {
        $images = $eventGallery->image;
        
        if (isset($images[$imageIndex])) {
            Storage::disk('public')->delete($images[$imageIndex]);
            unset($images[$imageIndex]);
            $images = array_values($images);
            $eventGallery->update(['image' => $images]);
            
            return redirect()->back()
                ->with('success', 'Image berhasil dihapus');
        }
        
        return redirect()->back()
            ->with('error', 'Image tidak ditemukan');
    }
}