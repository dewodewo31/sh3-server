<?php

namespace App\Http\Controllers;

use App\Services\ParticipantService\ParticipantServiceInterface;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    protected $participantService;

    public function __construct(ParticipantServiceInterface $participantService)
    {
        $this->participantService = $participantService;
    }

    /**
     * Display a listing of participants.
     */
    public function index(Request $request)
    {
        $participants = $this->participantService->getParticipants($request);
        $stats = $this->participantService->getParticipantStats();
        
        return view('participants.index', array_merge(
            compact('participants'),
            $stats
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
            
            return redirect()->route('participants.index')
                ->with('success', "Participant berhasil ditambahkan. Hash ID: {$participant->hash_id}");
        } catch (\Exception $e) {
            return redirect()->back()
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
            
            return redirect()->route('participants.index')
                ->with('success', 'Participant berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()
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
            
            return redirect()->route('participants.index')
                ->with('success', 'Participant berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('participants.index')
                ->with('error', $e->getMessage());
        }
    }
    
    /**
     * Generate new hash ID for participant
     */
    public function regenerateHashId($id)
    {
        $result = $this->participantService->regenerateHashId($id);
        
        return redirect()->route('participants.show', $id)
            ->with('success', "Hash ID berhasil diganti dari {$result['old_hash_id']} menjadi {$result['new_hash_id']}");
    }
    
    /**
     * Toggle participant status (active/inactive)
     */
    public function toggleStatus($id)
    {
        $result = $this->participantService->toggleStatus($id);
        
        return redirect()->route('participants.index')
            ->with('success', "Participant berhasil {$result['message']}");
    }
    
    /**
     * Export participants to CSV
     */
    public function export()
    {
        $result = $this->participantService->exportParticipantsToCsv();
        
        return response($result['content'])
            ->withHeaders($result['headers']);
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
                'data' => $participants
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
}