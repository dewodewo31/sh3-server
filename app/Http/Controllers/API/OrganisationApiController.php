<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrganisationHierarchyResource;
use App\Http\Resources\OrganisationHierarchyTreeResource;
use App\Http\Resources\OrganizationHierarchyTreeResource;
use App\Http\Resources\OrganisationHolderResource;
use App\Models\OrganizationHierarchy;
use App\Models\OrganizationPositionHolder;
use Illuminate\Http\Request;

class OrganisationApiController extends Controller
{
    /**
     * Get all hierarchies with pagination.
     * GET /api/v1/organisations
     */
    public function index(Request $request)
    {
        $query = OrganizationHierarchy::with(['parent', 'holders']);

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        // Filter by level
        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
        }

        // Filter by active
        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        // Search by position name
        if ($request->has('search') && $request->search) {
            $query->where('position_name', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $hierarchies = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Organisation hierarchies retrieved successfully',
            'data' => OrganisationHierarchyResource::collection($hierarchies),
            'meta' => [
                'current_page' => $hierarchies->currentPage(),
                'last_page' => $hierarchies->lastPage(),
                'per_page' => $hierarchies->perPage(),
                'total' => $hierarchies->total(),
            ],
            'filters' => [
                'year' => $request->year,
                'level' => $request->level,
                'is_active' => $request->is_active,
                'search' => $request->search,
            ]
        ]);
    }

    /**
     * Get hierarchy tree structure.
     * GET /api/v1/organisations/tree
     */
    public function tree(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $hierarchies = OrganizationHierarchy::with(['holders'])
            ->where('year', $year)
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get();

        // Build tree
        $tree = $this->buildTree($hierarchies);

        // Get available years
        $years = OrganizationHierarchy::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Get levels for filter
        $levels = OrganizationHierarchy::select('level', 'level_name')
            ->where('year', $year)
            ->distinct()
            ->orderBy('level')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Organisation tree retrieved successfully',
            'data' => [
                'year' => $year,
                'available_years' => $years,
                'levels' => $levels,
                'tree' => OrganisationHierarchyTreeResource::collection($tree),
                'stats' => [
                    'total_positions' => $hierarchies->count(),
                    'total_holders' => OrganizationPositionHolder::whereHas('hierarchy', function($q) use ($year) {
                        $q->where('year', $year);
                    })->count(),
                    'total_levels' => $hierarchies->pluck('level')->unique()->count(),
                ]
            ]
        ]);
    }

    /**
     * Get single hierarchy detail.
     * GET /api/v1/organisations/{id}
     */
    public function show($id)
    {
        $hierarchy = OrganizationHierarchy::with(['parent', 'children', 'holders'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Organisation hierarchy retrieved successfully',
            'data' => new OrganisationHierarchyResource($hierarchy)
        ]);
    }

    /**
     * Get hierarchy by year.
     * GET /api/v1/organisations/year/{year}
     */
    public function getByYear($year)
    {
        $hierarchies = OrganizationHierarchy::with(['parent', 'holders'])
            ->where('year', $year)
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'message' => "Organisation hierarchies for year {$year} retrieved successfully",
            'data' => OrganisationHierarchyResource::collection($hierarchies),
            'stats' => [
                'total' => $hierarchies->count(),
                'levels' => $hierarchies->pluck('level')->unique()->count(),
            ]
        ]);
    }

    /**
     * Get holders by hierarchy.
     * GET /api/v1/organisations/{id}/holders
     */
    public function getHolders($id)
    {
        $hierarchy = OrganizationHierarchy::with(['holders'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Holders retrieved successfully',
            'data' => [
                'hierarchy' => [
                    'id' => $hierarchy->id,
                    'position_name' => $hierarchy->position_name,
                ],
                'holders' => OrganisationHolderResource::collection($hierarchy->holders),
            ]
        ]);
    }

    /**
     * Get available years.
     * GET /api/v1/organisations/years
     */
    public function getYears()
    {
        $years = OrganizationHierarchy::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        return response()->json([
            'success' => true,
            'message' => 'Available years retrieved successfully',
            'data' => $years
        ]);
    }

    /**
     * Get stats.
     * GET /api/v1/organisations/stats
     */
    public function getStats(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $stats = [
            'total_positions' => OrganizationHierarchy::where('year', $year)->count(),
            'total_holders' => OrganizationPositionHolder::whereHas('hierarchy', function($q) use ($year) {
                $q->where('year', $year);
            })->count(),
            'total_levels' => OrganizationHierarchy::where('year', $year)->pluck('level')->unique()->count(),
            'active_positions' => OrganizationHierarchy::where('year', $year)->where('is_active', true)->count(),
            'inactive_positions' => OrganizationHierarchy::where('year', $year)->where('is_active', false)->count(),
            'active_holders' => OrganizationPositionHolder::whereHas('hierarchy', function($q) use ($year) {
                $q->where('year', $year);
            })->where('is_active', true)->count(),
        ];

        // Get level distribution
        $levelDistribution = OrganizationHierarchy::where('year', $year)
            ->select('level', 'level_name')
            ->withCount('holders')
            ->orderBy('level')
            ->get()
            ->map(function($item) {
                return [
                    'level' => $item->level,
                    'level_name' => $item->level_name ?? 'Level ' . $item->level,
                    'positions_count' => OrganizationHierarchy::where('year', $item->year ?? date('Y'))->where('level', $item->level)->count(),
                    'holders_count' => $item->holders_count,
                ];
            });

        $stats['level_distribution'] = $levelDistribution;

        return response()->json([
            'success' => true,
            'message' => 'Statistics retrieved successfully',
            'data' => $stats
        ]);
    }

    /**
     * Search hierarchies.
     * GET /api/v1/organisations/search
     */
    public function search(Request $request)
    {
        $query = OrganizationHierarchy::with(['parent', 'holders']);

        if ($request->has('q') && $request->q) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('position_name', 'like', '%' . $search . '%')
                  ->orWhere('position_code', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('level_name', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $results = $query->limit(20)->get();

        return response()->json([
            'success' => true,
            'message' => 'Search results retrieved successfully',
            'data' => OrganisationHierarchyResource::collection($results),
            'search_term' => $request->q,
            'total' => $results->count()
        ]);
    }

    /**
     * Get hierarchy levels.
     * GET /api/v1/organisations/levels
     */
    public function getLevels(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $levels = OrganizationHierarchy::select('level', 'level_name')
            ->where('year', $year)
            ->distinct()
            ->orderBy('level')
            ->get()
            ->map(function($item) {
                return [
                    'level' => $item->level,
                    'level_name' => $item->level_name ?? 'Level ' . $item->level,
                    'label' => $item->level_name ? "Level {$item->level} - {$item->level_name}" : "Level {$item->level}",
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Levels retrieved successfully',
            'data' => $levels
        ]);
    }

    /**
     * Get hierarchy by level.
     * GET /api/v1/organisations/level/{level}
     */
    public function getByLevel($level, Request $request)
    {
        $year = $request->get('year', date('Y'));

        $hierarchies = OrganizationHierarchy::with(['parent', 'holders'])
            ->where('year', $year)
            ->where('level', $level)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'message' => "Organisation hierarchies for level {$level} retrieved successfully",
            'data' => OrganisationHierarchyResource::collection($hierarchies),
            'stats' => [
                'total' => $hierarchies->count(),
                'level' => $level,
                'year' => $year,
            ]
        ]);
    }

    /**
     * Get holder detail.
     * GET /api/v1/organisations/holders/{id}
     */
    public function getHolder($id)
    {
        $holder = OrganizationPositionHolder::with(['hierarchy'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Holder retrieved successfully',
            'data' => new OrganisationHolderResource($holder)
        ]);
    }

    /**
     * Build tree structure.
     */
    private function buildTree($hierarchies, $parentId = null)
    {
        $result = [];

        foreach ($hierarchies as $hierarchy) {
            if ($hierarchy->parent_id == $parentId) {
                $children = $this->buildTree($hierarchies, $hierarchy->id);
                $hierarchy->children_list = $children;
                $result[] = $hierarchy;
            }
        }

        return $result;
    }
}