<?php

namespace App\Http\Controllers;

use App\Models\Merchandise;
use App\Models\MerchandiseOrder;
use App\Models\Event; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MerchandiseController extends Controller
{
    /**
     * Display a listing of merchandise.
     */
    public function index(Request $request)
    {
        $query = Merchandise::query();

        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $merchandise = $query->latest()->paginate(15);

        $stats = [
            'total' => Merchandise::count(),
            'active' => Merchandise::where('is_active', true)->count(),
            'low_stock' => Merchandise::where('stock', '<', 10)->where('stock', '>', 0)->count(),
            'out_of_stock' => Merchandise::where('stock', 0)->count(),
        ];

        $categories = ['clothing', 'accessories', 'collectibles', 'others'];

        return view('merchandise.index', compact('merchandise', 'stats', 'categories'));
    }

    /**
     * Show form for creating new merchandise.
     */
    public function create()
    {
        $categories = ['clothing', 'accessories', 'collectibles', 'others'];
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $colors = ['Red', 'Blue', 'Black', 'White', 'Gray', 'Green', 'Yellow'];

        // Ambil data events untuk ditampilkan di form
        $events = Event::orderBy('start_date', 'desc')->get();

        return view('merchandise.create', compact('categories', 'sizes', 'colors', 'events'));
    }

    /**
     * Store a newly created merchandise.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:merchandise,name',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|in:clothing,accessories,collectibles,others',
            'sizes' => 'nullable|array',
            'colors' => 'nullable|array',
            'is_active' => 'boolean',
            'event_id' => 'nullable|exists:events,id',
            'event_discount_price' => 'nullable|numeric|min:0', // Tambahkan
            'event_stock' => 'nullable|integer|min:0', // Tambahkan
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except('_token', 'event_id', 'event_discount_price', 'event_stock');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('merchandise', 'public');
        }

        $data['is_active'] = $request->has('is_active');
        $data['sold_count'] = 0;

        $merchandise = Merchandise::create($data);

        // If event is selected, attach with special price and stock
        if ($request->filled('event_id')) {
            $pivotData = ['is_available' => true];

            if ($request->filled('event_discount_price')) {
                $pivotData['discount_price'] = $request->event_discount_price;
            }

            if ($request->filled('event_stock')) {
                $pivotData['event_stock'] = $request->event_stock;
            }

            $merchandise->events()->attach($request->event_id, $pivotData);
        }

        return redirect()->route('merchandise.index')->with('success', 'Merchandise berhasil ditambahkan');
    }

    /**
     * Display merchandise details.
     */
    public function show(Merchandise $merchandise)
    {
        $merchandise->load('orders.participant', 'events'); // Load events juga

        $totalOrders = $merchandise->orders()->count();
        $totalRevenue = $merchandise->orders()->sum('total_price');

        return view('merchandise.show', compact('merchandise', 'totalOrders', 'totalRevenue'));
    }

    /**
     * Show form for editing merchandise.
     */
    public function edit(Merchandise $merchandise)
    {
        $categories = ['clothing', 'accessories', 'collectibles', 'others'];
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $colors = ['Red', 'Blue', 'Black', 'White', 'Gray', 'Green', 'Yellow'];

        // Ambil data events
        $events = Event::orderBy('start_date', 'desc')->get();

        // Load existing event relations
        $merchandise->load('events');

        return view('merchandise.edit', compact('merchandise', 'categories', 'sizes', 'colors', 'events'));
    }

    /**
     * Update merchandise.
     */
    public function update(Request $request, Merchandise $merchandise)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:merchandise,name,' . $merchandise->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|in:clothing,accessories,collectibles,others',
            'sizes' => 'nullable|array',
            'colors' => 'nullable|array',
            'is_active' => 'boolean',
            'event_id' => 'nullable|exists:events,id',
            'event_discount_price' => 'nullable|numeric|min:0', // Tambahkan ini
            'event_stock' => 'nullable|integer|min:0', // Tambahkan ini
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except('_token', '_method', 'event_id', 'event_discount_price', 'event_stock');

        if ($request->hasFile('image')) {
            if ($merchandise->image) {
                Storage::disk('public')->delete($merchandise->image);
            }
            $data['image'] = $request->file('image')->store('merchandise', 'public');
        }

        $data['is_active'] = $request->has('is_active');

        $merchandise->update($data);

        // Update event relation with pivot data
        if ($request->filled('event_id')) {
            $pivotData = [
                'is_available' => true,
            ];

            // Set discount price if provided
            if ($request->filled('event_discount_price')) {
                $pivotData['discount_price'] = $request->event_discount_price;
            }

            // Set event stock if provided
            if ($request->filled('event_stock')) {
                $pivotData['event_stock'] = $request->event_stock;
            }

            $merchandise->events()->sync([$request->event_id => $pivotData]);
        } else {
            $merchandise->events()->detach();
        }

        return redirect()->route('merchandise.index')->with('success', 'Merchandise berhasil diupdate');
    }

    /**
     * Delete merchandise.
     */
    public function destroy(Merchandise $merchandise)
    {
        if ($merchandise->image) {
            Storage::disk('public')->delete($merchandise->image);
        }

        // Detach from all events
        $merchandise->events()->detach();

        $merchandise->delete();

        return redirect()->route('merchandise.index')->with('success', 'Merchandise berhasil dihapus');
    }

    /**
     * Update stock.
     */
    public function updateStock(Request $request, Merchandise $merchandise)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $merchandise->update(['stock' => $request->stock]);

        return redirect()->back()->with('success', 'Stock berhasil diupdate');
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(Merchandise $merchandise)
    {
        $merchandise->is_active = !$merchandise->is_active;
        $merchandise->save();

        $status = $merchandise->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->back()
            ->with('success', "Merchandise berhasil {$status}");
    }
}
