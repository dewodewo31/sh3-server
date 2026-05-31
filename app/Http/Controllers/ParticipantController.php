<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Services\ParticipantService\ParticipantServiceInterface;
use App\Services\WarningService;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    protected $participantService;
    protected $warningService; // Tambahkan property

    public function __construct(
        ParticipantServiceInterface $participantService,
        WarningService $warningService // Tambahkan injection
    ) {
        $this->participantService = $participantService;
        $this->warningService = $warningService; // Initialize
    }
    /**
     * Display a listing of participants.
     */
    public function index(Request $request)
    {
        $query = Participant::query();
        
        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('hash_id', 'like', "%{$search}%");
            });
        }
        
        // Type filter (member/non_member)
        if ($request->has('type') && $request->type != '') {
            $query->where('participant_type', $request->type);
        }
        
        // Status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Gender filter
        if ($request->has('gender') && $request->gender != '') {
            $query->where('gender', $request->gender);
        }
        
        // Warning level filter
        if ($request->has('warning_level') && $request->warning_level != '') {
            if ($request->warning_level == '0') {
                $query->where('current_warning_level', 0);
            } else {
                $query->where('current_warning_level', $request->warning_level);
            }
        }
        
        // Sort
        switch ($request->get('sort', 'latest')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->latest();
        }
        
        $participants = $query->paginate(15);
        
        // Stats for dashboard
        $totalParticipants = Participant::count();
        $activeParticipants = Participant::where('status', 'active')->count();
        $inactiveParticipants = Participant::where('status', 'inactive')->count();
        $maleParticipants = Participant::where('gender', 'male')->count();
        $femaleParticipants = Participant::where('gender', 'female')->count();
        $memberCount = Participant::where('participant_type', 'member')->count();
        $nonMemberCount = Participant::where('participant_type', 'non_member')->count();

        return view('participants.index', compact(
            'participants', 
            'totalParticipants', 
            'activeParticipants', 
            'inactiveParticipants',
            'maleParticipants',
            'femaleParticipants',
            'memberCount',
            'nonMemberCount'
        ));
    }

    /**
     * Show the form for creating a new participant.
     */
    public function create()
    {
        return view('participants.create');
    }

    /**
     * Store a newly created participant in storage.
     */
    public function store(Request $request)
    {
        try {
            $participant = $this->participantService->createParticipant($request->all());

            return redirect()
                ->route('participants.index')
                ->with('success', "Participant berhasil ditambahkan. Hash ID: {$participant->hash_id}");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified participant.
     */
    public function show($id)
    {
        $data = $this->participantService->getParticipantWithDetails($id);

        return view('participants.show', $data);
    }

    /**
     * Show the form for editing the specified participant.
     */
    public function edit($id)
    {
        $participant = $this->participantService->getParticipantById($id);

        return view('participants.edit', compact('participant'));
    }

    /**
     * Update the specified participant in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $this->participantService->updateParticipant($id, $request->all());

            return redirect()->route('participants.index')->with('success', 'Participant berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified participant from storage.
     */
    public function destroy($id)
    {
        try {
            $this->participantService->deleteParticipant($id);

            return redirect()->route('participants.index')->with('success', 'Participant berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('participants.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Generate new hash ID for participant
     */
    public function regenerateHashId($id)
    {
        $result = $this->participantService->regenerateHashId($id);

        return redirect()
            ->route('participants.show', $id)
            ->with('success', "Hash ID berhasil diganti dari {$result['old_hash_id']} menjadi {$result['new_hash_id']}");
    }

    /**
     * Toggle participant status (active/inactive)
     */
    public function toggleStatus($id)
    {
        $result = $this->participantService->toggleStatus($id);

        return redirect()
            ->route('participants.index')
            ->with('success', "Participant berhasil {$result['message']}");
    }

    /**
     * Export participants to CSV
     */
    public function export()
    {
        $result = $this->participantService->exportParticipantsToCsv();

        return response($result['content'])->withHeaders($result['headers']);
    }

    /**
     * Search participants
     */
    public function search(Request $request)
    {
        $participants = $this->participantService->searchParticipants($request);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $participants,
            ]);
        }

        return view('participants.index', compact('participants'));
    }

    /**
     * Export participant to PDF
     */
    public function exportPdf($id)
    {
        return $this->participantService->exportSingleParticipantToPdf($id);
    }

    /**
     * Export all participants to PDF
     */
    public function exportAllPdf(Request $request)
    {
        return $this->participantService->exportParticipantsToPdf($request);
    }

     /**
     * Upgrade non-member to member
     */
    public function upgradeToMember($id)
    {
        $participant = Participant::findOrFail($id);  // ← Gunakan App\Models\Participant
        
        if ($participant->participant_type === 'member') {
            return redirect()->route('participants.index')
                ->with('error', 'Participant sudah menjadi member');
        }
        
        // Generate new hash ID for member using model method
        $newHashId = Participant::generateMemberHashId();  // ← Panggil method dari model
        
        $participant->update([
            'participant_type' => 'member',
            'hash_id' => $newHashId
        ]);
        
        return redirect()->route('participants.index')
            ->with('success', "Participant berhasil di-upgrade menjadi member dengan Hash ID: {$newHashId}");
    }
    /**
     * Issue warning to participant
     */
    public function issueWarning(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $result = $this->warningService->issueWarning(
                $id,
                $request->reason,
                $request->description
            );

            return redirect()
                ->route('participants.show', $id)
                ->with('success', "Warning berhasil diberikan: " . $result['sanction']['message']);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal memberikan warning: ' . $e->getMessage());
        }
    }

    /**
     * Get participant warnings
     */
    public function getWarnings($id)
    {
        $warnings = $this->warningService->getActiveWarnings($id);
        
        if (request()->ajax()) {
            return response()->json($warnings);
        }
        
        return view('participants.warnings', compact('warnings'));
    }

    /**
     * Remove warning from participant
     */
    public function removeWarning($participantId, $warningId)
    {
        try {
            $result = $this->warningService->removeWarning($warningId);
            
            return redirect()
                ->route('participants.show', $participantId)
                ->with('success', 'Warning berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus warning: ' . $e->getMessage());
        }
    }

    /**
     * Check if participant can join event (for API/AJAX)
     */
    public function checkCanJoinEvent(Request $request, $id)
    {
        $result = $this->warningService->canJoinEvent($id, $request->event_id);
        
        return response()->json($result);
    }

}
