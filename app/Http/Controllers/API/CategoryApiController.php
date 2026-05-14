<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    /**
     * Get all categories
     */
    public function index()
    {
        $categories = Category::withCount('events')->get();

        return response()->json([
            'success' => true,
            'data' => $categories->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'events_count' => $category->events_count,
                    'created_at' => $category->created_at->toISOString(),
                ];
            })
        ]);
    }

    /**
     * Get single category
     */
    public function show($id)
    {
        $category = Category::withCount('events')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'events_count' => $category->events_count,
                'created_at' => $category->created_at->toISOString(),
            ]
        ]);
    }

    /**
     * Get events by category
     */
    public function events($id, Request $request)
    {
        $category = Category::findOrFail($id);
        
        $query = $category->events()->with(['category', 'creator']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'upcoming') {
                $query->where('start_date', '>', now());
            } elseif ($request->status == 'ongoing') {
                $query->where('start_date', '<=', now())->where('end_date', '>=', now());
            } elseif ($request->status == 'finished') {
                $query->where('end_date', '<', now());
            }
        }

        $events = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $events->items(),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'total' => $events->total(),
            ]
        ]);
    }
}