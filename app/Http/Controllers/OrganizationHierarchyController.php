<?php

namespace App\Http\Controllers;

use App\Models\OrganizationHierarchy;
use App\Models\OrganizationPositionHolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class OrganizationHierarchyController extends Controller
{
    /**
     * Display a listing of hierarchies.
     */
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        $hierarchies = OrganizationHierarchy::with(['parent', 'holders'])
            ->byYear($year)
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get();
        
        // Get available years
        $years = OrganizationHierarchy::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        
        // Build tree structure
        $tree = $this->buildTree($hierarchies);
        
        // Get levels for filter
        $levels = OrganizationHierarchy::select('level', 'level_name')
            ->distinct()
            ->orderBy('level')
            ->get();
        
        $stats = [
            'total_positions' => $hierarchies->count(),
            'total_holders' => OrganizationPositionHolder::whereHas('hierarchy', function($q) use ($year) {
                $q->where('year', $year);
            })->count(),
            'total_levels' => $hierarchies->pluck('level')->unique()->count(),
        ];
        
        return view('organization.index', compact(
            'hierarchies', 
            'tree', 
            'years', 
            'year', 
            'levels',
            'stats'
        ));
    }

    /**
     * Show form for creating a new hierarchy.
     */
    public function create()
    {
        $years = range(date('Y') - 5, date('Y') + 1);
        $levels = [
            1 => 'Level 1 - Pengurus Inti',
            2 => 'Level 2 - Bidang',
            3 => 'Level 3 - Seksi',
            4 => 'Level 4 - Sub Seksi',
            5 => 'Level 5 - Staff',
        ];
        
        $parents = OrganizationHierarchy::where('year', date('Y'))
            ->orderBy('level')
            ->orderBy('position_name')
            ->get();
        
        return view('organization.create', compact('years', 'levels', 'parents'));
    }

    /**
     * Store a newly created hierarchy.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|integer|min:2000',
            'level' => 'required|integer|min:1|max:5',
            'position_name' => 'required|string|max:255',
            'level_name' => 'nullable|string|max:255',
            'position_code' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:organization_hierarchies,id',
            'description' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            // Holder fields
            'holder_name' => 'nullable|string|max:255',
            'holder_nickname' => 'nullable|string|max:255',
            'holder_email' => 'nullable|email|max:255',
            'holder_phone' => 'nullable|string|max:15',
            'holder_member_since' => 'nullable|integer|min:2000',
            'holder_period_start' => 'nullable|integer|min:2000',
            'holder_period_end' => 'nullable|integer|min:2000',
            'holder_bio' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $hierarchy = OrganizationHierarchy::create([
            'year' => $request->year,
            'level' => $request->level,
            'level_name' => $request->level_name,
            'position_name' => $request->position_name,
            'position_code' => $request->position_code,
            'parent_id' => $request->parent_id,
            'sort_order' => $request->sort_order ?? 0,
            'description' => $request->description,
            'responsibilities' => $request->responsibilities,
            'is_active' => $request->has('is_active'),
        ]);

        // Create holder if provided
        if ($request->filled('holder_name')) {
            OrganizationPositionHolder::create([
                'hierarchy_id' => $hierarchy->id,
                'name' => $request->holder_name,
                'nickname' => $request->holder_nickname,
                'email' => $request->holder_email,
                'phone' => $request->holder_phone,
                'member_since' => $request->holder_member_since,
                'period_start' => $request->holder_period_start,
                'period_end' => $request->holder_period_end,
                'bio' => $request->holder_bio,
                'is_active' => true,
            ]);
        }

        return redirect()->route('organization.index', ['year' => $request->year])
            ->with('success', 'Jabatan berhasil ditambahkan');
    }

    /**
     * Display the specified hierarchy.
     */
    public function show($id)
    {
        $hierarchy = OrganizationHierarchy::with(['parent', 'children', 'holders'])
            ->findOrFail($id);
        
        return view('organization.show', compact('hierarchy'));
    }

    /**
     * Show form for editing the specified hierarchy.
     */
    public function edit($id)
    {
        $hierarchy = OrganizationHierarchy::with(['holders'])->findOrFail($id);
        
        $years = range(date('Y') - 5, date('Y') + 1);
        $levels = [
            1 => 'Level 1 - Pengurus Inti',
            2 => 'Level 2 - Bidang',
            3 => 'Level 3 - Seksi',
            4 => 'Level 4 - Sub Seksi',
            5 => 'Level 5 - Staff',
        ];
        
        $parents = OrganizationHierarchy::where('year', $hierarchy->year)
            ->where('id', '!=', $id)
            ->orderBy('level')
            ->orderBy('position_name')
            ->get();
        
        $holder = $hierarchy->holders->first();
        
        return view('organization.edit', compact('hierarchy', 'years', 'levels', 'parents', 'holder'));
    }

    /**
     * Update the specified hierarchy.
     */
    public function update(Request $request, $id)
    {
        $hierarchy = OrganizationHierarchy::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'year' => 'required|integer|min:2000',
            'level' => 'required|integer|min:1|max:5',
            'position_name' => 'required|string|max:255',
            'level_name' => 'nullable|string|max:255',
            'position_code' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:organization_hierarchies,id',
            'description' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            // Holder fields
            'holder_name' => 'nullable|string|max:255',
            'holder_nickname' => 'nullable|string|max:255',
            'holder_email' => 'nullable|email|max:255',
            'holder_phone' => 'nullable|string|max:15',
            'holder_member_since' => 'nullable|integer|min:2000',
            'holder_period_start' => 'nullable|integer|min:2000',
            'holder_period_end' => 'nullable|integer|min:2000',
            'holder_bio' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $hierarchy->update([
            'year' => $request->year,
            'level' => $request->level,
            'level_name' => $request->level_name,
            'position_name' => $request->position_name,
            'position_code' => $request->position_code,
            'parent_id' => $request->parent_id,
            'sort_order' => $request->sort_order ?? 0,
            'description' => $request->description,
            'responsibilities' => $request->responsibilities,
            'is_active' => $request->has('is_active'),
        ]);

        // Update or create holder
        if ($request->filled('holder_name')) {
            $holder = $hierarchy->holders->first();
            if ($holder) {
                $holder->update([
                    'name' => $request->holder_name,
                    'nickname' => $request->holder_nickname,
                    'email' => $request->holder_email,
                    'phone' => $request->holder_phone,
                    'member_since' => $request->holder_member_since,
                    'period_start' => $request->holder_period_start,
                    'period_end' => $request->holder_period_end,
                    'bio' => $request->holder_bio,
                ]);
            } else {
                OrganizationPositionHolder::create([
                    'hierarchy_id' => $hierarchy->id,
                    'name' => $request->holder_name,
                    'nickname' => $request->holder_nickname,
                    'email' => $request->holder_email,
                    'phone' => $request->holder_phone,
                    'member_since' => $request->holder_member_since,
                    'period_start' => $request->holder_period_start,
                    'period_end' => $request->holder_period_end,
                    'bio' => $request->holder_bio,
                    'is_active' => true,
                ]);
            }
        } else {
            // Remove holder if exists and name is empty
            $hierarchy->holders()->delete();
        }

        return redirect()->route('organization.index', ['year' => $hierarchy->year])
            ->with('success', 'Jabatan berhasil diupdate');
    }

    /**
     * Duplicate the specified hierarchy.
     */
    public function duplicate($id)
    {
        $source = OrganizationHierarchy::with(['holders'])->findOrFail($id);
        
        // Create duplicate
        $duplicate = $source->replicate();
        $duplicate->position_name = $source->position_name . ' (Copy)';
        $duplicate->created_at = now();
        $duplicate->updated_at = now();
        $duplicate->save();
        
        // Duplicate holders
        foreach ($source->holders as $holder) {
            $newHolder = $holder->replicate();
            $newHolder->hierarchy_id = $duplicate->id;
            $newHolder->created_at = now();
            $newHolder->updated_at = now();
            $newHolder->save();
        }
        
        return redirect()->route('organization.index', ['year' => $source->year])
            ->with('success', 'Jabatan berhasil diduplikasi');
    }

    /**
     * Duplicate entire year structure.
     */
    public function duplicateYear(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source_year' => 'required|integer|min:2000',
            'target_year' => 'required|integer|min:2000|different:source_year',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $sourceYear = $request->source_year;
        $targetYear = $request->target_year;
        
        // Check if target year already has data
        $existingCount = OrganizationHierarchy::where('year', $targetYear)->count();
        if ($existingCount > 0) {
            return redirect()->back()
                ->with('error', "Tahun {$targetYear} sudah memiliki data. Silahkan hapus terlebih dahulu atau pilih tahun lain.");
        }

        // Get all hierarchies from source year
        $hierarchies = OrganizationHierarchy::with(['holders'])
            ->where('year', $sourceYear)
            ->get();

        if ($hierarchies->isEmpty()) {
            return redirect()->back()
                ->with('error', "Tidak ada data untuk tahun {$sourceYear}");
        }

        // Map old IDs to new IDs for parent-child relationship
        $idMap = [];
        
        // Create new hierarchies
        foreach ($hierarchies as $hierarchy) {
            $newHierarchy = $hierarchy->replicate();
            $newHierarchy->year = $targetYear;
            $newHierarchy->position_name = $hierarchy->position_name;
            $newHierarchy->created_at = now();
            $newHierarchy->updated_at = now();
            $newHierarchy->save();
            
            // Store mapping
            $idMap[$hierarchy->id] = $newHierarchy->id;
        }

        // Update parent_id relationships
        foreach ($hierarchies as $hierarchy) {
            if ($hierarchy->parent_id && isset($idMap[$hierarchy->parent_id])) {
                $newHierarchy = OrganizationHierarchy::find($idMap[$hierarchy->id]);
                $newHierarchy->parent_id = $idMap[$hierarchy->parent_id];
                $newHierarchy->save();
            }
        }

        // Duplicate holders
        foreach ($hierarchies as $hierarchy) {
            if (isset($idMap[$hierarchy->id])) {
                foreach ($hierarchy->holders as $holder) {
                    $newHolder = $holder->replicate();
                    $newHolder->hierarchy_id = $idMap[$hierarchy->id];
                    $newHolder->created_at = now();
                    $newHolder->updated_at = now();
                    $newHolder->save();
                }
            }
        }

        $count = $hierarchies->count();
        
        return redirect()->route('organization.index', ['year' => $targetYear])
            ->with('success', "Berhasil menduplikasi {$count} jabatan dari tahun {$sourceYear} ke {$targetYear}");
    }

    /**
     * Remove the specified hierarchy.
     */
    public function destroy($id)
    {
        $hierarchy = OrganizationHierarchy::findOrFail($id);
        $year = $hierarchy->year;
        
        // Check if has children
        if ($hierarchy->children()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus jabatan yang memiliki anak. Hapus anak terlebih dahulu.');
        }
        
        // Delete holders first
        $hierarchy->holders()->delete();
        $hierarchy->delete();

        return redirect()->route('organization.index', ['year' => $year])
            ->with('success', 'Jabatan berhasil dihapus');
    }

    /**
     * Build tree structure from flat collection.
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