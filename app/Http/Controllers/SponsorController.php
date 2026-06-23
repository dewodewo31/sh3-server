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
        $query = Sponsor::with('events');

        // Filter by year
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Filter by tier
        if ($request->filled('tier')) {
            $query->where('tier', $request->tier);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $sponsors = $query->orderBy('sort_order')->paginate(15);

        // Stats
        $totalSponsors = Sponsor::count();
        $activeSponsors = Sponsor::where('is_active', true)->count();
        $currentYear = date('Y');
        $currentYearSponsors = Sponsor::where('year', $currentYear)->count();

        // Available years for filter dropdown
        $availableYears = Sponsor::whereNotNull('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('sponsors.index', compact(
            'sponsors', 
            'totalSponsors', 
            'activeSponsors', 
            'currentYearSponsors',
            'availableYears'
        ));
    }

    /**
     * Show form for creating new sponsor.
     */
    public function create()
    {
        $events = Event::all();
        $tiers = Sponsor::getAvailableTiers();
        
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
            'year' => 'nullable|string|max:4',
            'tier' => 'required|in:platinum,gold,silver,bronze,partner',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
            'event_sponsors' => 'nullable|array',
            'event_sponsors.*.event_id' => 'required|exists:events,id',
            'event_sponsors.*.tier' => 'required|in:platinum,gold,silver,bronze,partner',
            'event_sponsors.*.contribution_amount' => 'nullable|numeric',
            'event_sponsors.*.benefits' => 'nullable|string'
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
        
        // Attach to events with specific tier data
        if ($request->has('event_sponsors')) {
            foreach ($request->event_sponsors as $eventSponsor) {
                $sponsor->events()->attach($eventSponsor['event_id'], [
                    'tier' => $eventSponsor['tier'],
                    'contribution_amount' => $eventSponsor['contribution_amount'] ?? null,
                    'benefits' => $eventSponsor['benefits'] ?? null,
                    'sort_order' => $eventSponsor['sort_order'] ?? 0
                ]);
            }
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
        $tiers = Sponsor::getAvailableTiers();
        
        // Get existing event-sponsor relationships with pivot data
        $eventSponsors = $sponsor->events->map(function($event) {
            return [
                'event_id' => $event->id,
                'event_name' => $event->title,
                'tier' => $event->pivot->tier,
                'contribution_amount' => $event->pivot->contribution_amount,
                'benefits' => $event->pivot->benefits,
                'sort_order' => $event->pivot->sort_order
            ];
        })->toArray();
        
        return view('sponsors.edit', compact('sponsor', 'events', 'tiers', 'eventSponsors'));
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
            'year' => 'nullable|string|max:4',
            'tier' => 'required|in:platinum,gold,silver,bronze,partner',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
            'event_sponsors' => 'nullable|array',
            'event_sponsors.*.event_id' => 'required|exists:events,id',
            'event_sponsors.*.tier' => 'required|in:platinum,gold,silver,bronze,partner',
            'event_sponsors.*.contribution_amount' => 'nullable|numeric',
            'event_sponsors.*.benefits' => 'nullable|string'
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
        
        // Sync events with pivot data
        $syncData = [];
        if ($request->has('event_sponsors')) {
            foreach ($request->event_sponsors as $eventSponsor) {
                $syncData[$eventSponsor['event_id']] = [
                    'tier' => $eventSponsor['tier'],
                    'contribution_amount' => $eventSponsor['contribution_amount'] ?? null,
                    'benefits' => $eventSponsor['benefits'] ?? null,
                    'sort_order' => $eventSponsor['sort_order'] ?? 0
                ];
            }
        }
        
        $sponsor->events()->sync($syncData);
        
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
    
    /**
     * Get event specific sponsor data via AJAX
     */
    public function getEventSponsorData(Sponsor $sponsor, Event $event)
    {
        $data = $sponsor->getEventSponsorData($event->id);
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
}