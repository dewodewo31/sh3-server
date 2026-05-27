<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SponsorController extends Controller
{
    /**
     * Display a listing of sponsors.
     */
    public function index(Request $request)
    {
        $query = Sponsor::query();
        
        if ($request->has('tier') && $request->tier != '') {
            $query->where('tier', $request->tier);
        }
        
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $sponsors = $query->orderBy('sort_order')->latest()->paginate(15);
        
        $totalSponsors = Sponsor::count();
        $activeSponsors = Sponsor::where('is_active', true)->count();
        $tiers = ['platinum', 'gold', 'silver', 'bronze', 'partner'];
        
        return view('sponsors.index', compact('sponsors', 'totalSponsors', 'activeSponsors', 'tiers'));
    }

    /**
     * Show form for creating new sponsor.
     */
    public function create()
    {
        $events = Event::all();
        $tiers = ['platinum', 'gold', 'silver', 'bronze', 'partner'];
        
        return view('sponsors.create', compact('events', 'tiers'));
    }

    /**
     * Store a newly created sponsor.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:sponsors,name',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'tier' => 'required|in:platinum,gold,silver,bronze,partner',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
            'event_ids' => 'nullable|array',
            'event_ids.*' => 'exists:events,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('sponsors', 'public');
        }
        
        $data['slug'] = Str::slug($request->name);
        $data['sort_order'] = $request->sort_order ?? 0;
        $data['is_active'] = $request->has('is_active');
        
        $sponsor = Sponsor::create($data);
        
        // Attach to events
        if ($request->has('event_ids')) {
            $sponsor->events()->attach($request->event_ids);
        }
        
        return redirect()->route('sponsors.index')
            ->with('success', 'Sponsor berhasil ditambahkan');
    }

    /**
     * Display sponsor details.
     */
    public function show(Sponsor $sponsor)
    {
        $sponsor->load('events');
        
        return view('sponsors.show', compact('sponsor'));
    }

    /**
     * Show form for editing sponsor.
     */
    public function edit(Sponsor $sponsor)
    {
        $events = Event::all();
        $tiers = ['platinum', 'gold', 'silver', 'bronze', 'partner'];
        $selectedEvents = $sponsor->events->pluck('id')->toArray();
        
        return view('sponsors.edit', compact('sponsor', 'events', 'tiers', 'selectedEvents'));
    }

    /**
     * Update sponsor.
     */
    public function update(Request $request, Sponsor $sponsor)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:sponsors,name,' . $sponsor->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'tier' => 'required|in:platinum,gold,silver,bronze,partner',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
            'event_ids' => 'nullable|array',
            'event_ids.*' => 'exists:events,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($sponsor->logo) {
                Storage::disk('public')->delete($sponsor->logo);
            }
            $data['logo'] = $request->file('logo')->store('sponsors', 'public');
        }
        
        $data['is_active'] = $request->has('is_active');
        
        $sponsor->update($data);
        
        // Sync events
        $sponsor->events()->sync($request->event_ids ?? []);
        
        return redirect()->route('sponsors.index')
            ->with('success', 'Sponsor berhasil diupdate');
    }

    /**
     * Delete sponsor.
     */
    public function destroy(Sponsor $sponsor)
    {
        if ($sponsor->logo) {
            Storage::disk('public')->delete($sponsor->logo);
        }
        
        $sponsor->delete();
        
        return redirect()->route('sponsors.index')
            ->with('success', 'Sponsor berhasil dihapus');
    }
    
    /**
     * Toggle sponsor active status.
     */
    public function toggleStatus(Sponsor $sponsor)
    {
        $sponsor->is_active = !$sponsor->is_active;
        $sponsor->save();
        
        $status = $sponsor->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()
            ->with('success', "Sponsor berhasil {$status}");
    }
}