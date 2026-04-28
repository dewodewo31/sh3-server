<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventGalleryRequest;
use App\Http\Requests\UpdateEventGalleryRequest;
use App\Models\EventGallery;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventGalleryController extends Controller
{
    public function index()
    {
        $galleries = EventGallery::with('event')->latest()->get();

        return view('galleries.index', compact('galleries'));
    }

    public function create()
    {
        // Ambil semua events untuk dropdown
        $events = Event::all();
        
        return view('galleries.create', compact('events'));
    }

    public function store(StoreEventGalleryRequest $request)
    {
        $data = $request->validated();
        
        $imagePaths = [];
        
        // Handle multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('galleries', 'public');
                $imagePaths[] = $path;
            }
        }
        
        $data['image'] = $imagePaths; // Simpan sebagai array
        $data['uploaded_by'] = Auth::id();

        EventGallery::create($data);

        return redirect()->route('galleries.index')
            ->with('success', 'Gallery berhasil ditambahkan');
    }

    public function edit(EventGallery $eventGallery)
    {
        // Ambil semua events untuk dropdown
        $events = Event::all();
        
        return view('galleries.edit', compact('eventGallery', 'events'));
    }

    public function update(UpdateEventGalleryRequest $request, EventGallery $eventGallery)
    {
        $data = $request->validated();

        if ($request->hasFile('images')) {
            // Hapus semua gambar lama
            $oldImages = $eventGallery->image;
            if (is_array($oldImages)) {
                foreach ($oldImages as $oldImage) {
                    if ($oldImage) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }
            } elseif ($oldImages) {
                Storage::disk('public')->delete($oldImages);
            }
            
            // Upload gambar baru
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('galleries', 'public');
                $imagePaths[] = $path;
            }
            
            $data['image'] = $imagePaths;
        }

        $eventGallery->update($data);

        return redirect()->route('galleries.index')
            ->with('success', 'Gallery berhasil diupdate');
    }

    public function show(EventGallery $eventGallery)
    {
        $eventGallery->load(['event', 'uploader']);
        $images = is_array($eventGallery->image) ? $eventGallery->image : json_decode($eventGallery->image, true);
        
        return view('galleries.show', compact('eventGallery', 'images'));
    }

    public function destroy(EventGallery $gallery)
    {
        $images = $gallery->image;

        foreach ($images ?? [] as $image) {
            if ($image && Storage::disk('public')->exists($image)) {
                Storage::disk('public')->delete($image);
            }
        }

        $gallery->delete();

        return redirect()->route('galleries.index')
            ->with('success', 'Gallery berhasil dihapus');
    }
    
    // Optional: Method untuk menghapus image tertentu saja
    public function deleteImage(EventGallery $eventGallery, $imageIndex)
    {
        $images = $eventGallery->image;
        
        if (isset($images[$imageIndex])) {
            // Hapus file
            Storage::disk('public')->delete($images[$imageIndex]);
            
            // Hapus dari array
            unset($images[$imageIndex]);
            
            // Reindex array
            $images = array_values($images);
            
            // Update database
            $eventGallery->update(['image' => $images]);
            
            return redirect()->back()
                ->with('success', 'Image berhasil dihapus');
        }
        
        return redirect()->back()
            ->with('error', 'Image tidak ditemukan');
    }
}