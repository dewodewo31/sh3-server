<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParticipantController extends Controller
{
    /**
     * Display a listing of participants.
     */
    public function index()
    {
        $participants = Participant::latest()->paginate(15);
        
        $totalParticipants = Participant::count();
        $activeParticipants = Participant::where('status', 'active')->count();
        $inactiveParticipants = Participant::where('status', 'inactive')->count();
        $maleParticipants = Participant::where('gender', 'male')->count();
        $femaleParticipants = Participant::where('gender', 'female')->count();
        
        return view('participants.index', compact(
            'participants', 
            'totalParticipants',
            'activeParticipants',
            'inactiveParticipants',
            'maleParticipants',
            'femaleParticipants'
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:participants,email',
            'phone' => 'required|string|max:15',
            'gender' => 'required|in:male,female',
            'birthdate' => 'required|date|before:today',
            'status' => 'sometimes|in:active,inactive',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $participant = Participant::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
            'status' => $request->status ?? 'active',
            'notes' => $request->notes
        ]);

        return redirect()->route('participants.index')
            ->with('success', "Participant berhasil ditambahkan. Hash ID: {$participant->hash_id}");
    }

    /**
     * Display the specified participant.
     */
    public function show($id)
    {
        $participant = Participant::with(['orders' => function($query) {
            $query->with(['event', 'payment'])->latest();
        }])->findOrFail($id);
        
        // Order Statistics
        $totalOrders = $participant->orders()->count();
        $totalSpent = $participant->orders()->where('status', 'paid')->sum('total_price');
        $pendingOrders = $participant->orders()->where('status', 'pending')->count();
        $paidOrders = $participant->orders()->where('status', 'paid')->count();
        $freeOrders = $participant->orders()->where('status', 'free')->count();
        $cancelledOrders = $participant->orders()->where('status', 'cancelled')->count();
        
        // Event History (unique events the participant has joined)
        $eventIds = $participant->orders()->pluck('event_id')->unique();
        $eventsJoined = Event::whereIn('id', $eventIds)->with('category')->get();
        $totalEventsJoined = $eventsJoined->count();

        // Upcoming events
        $upcomingEvents = $eventsJoined->filter(function($event) {
            return $event->status === 'upcoming';
        });
        
        // Past events
        $pastEvents = $eventsJoined->filter(function($event) {
            return $event->status === 'finished';
        });
        
        // Ongoing events
        $ongoingEvents = $eventsJoined->filter(function($event) {
            return $event->status === 'ongoing';
        });
        
        // Monthly spending chart data
        $monthlySpending = $participant->orders()
            ->where('status', 'paid')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_price) as total')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();
        
        return view('participants.show', compact(
            'participant', 
            'totalOrders', 
            'totalSpent',
            'pendingOrders',
            'paidOrders', 
            'freeOrders',
            'cancelledOrders',
            'eventsJoined',
            'totalEventsJoined',
            'upcomingEvents',
            'pastEvents',
            'ongoingEvents',
            'monthlySpending'
        ));
    }

    /**
     * Show the form for editing the specified participant.
     */
    public function edit($id)
    {
        $participant = Participant::findOrFail($id);
        
        return view('participants.edit', compact('participant'));
    }

    /**
     * Update the specified participant in storage.
     */
    public function update(Request $request, $id)
    {
        $participant = Participant::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:participants,email,' . $participant->id,
            'phone' => 'required|string|max:15',
            'gender' => 'required|in:male,female',
            'birthdate' => 'required|date|before:today',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $participant->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return redirect()->route('participants.index')
            ->with('success', 'Participant berhasil diupdate');
    }

    /**
     * Remove the specified participant from storage.
     */
    public function destroy($id)
    {
        $participant = Participant::findOrFail($id);
        
        // Check if participant has orders
        if ($participant->orders()->count() > 0) {
            return redirect()->route('participants.index')
                ->with('error', 'Tidak dapat menghapus participant yang memiliki pesanan');
        }
        
        $participant->delete();

        return redirect()->route('participants.index')
            ->with('success', 'Participant berhasil dihapus');
    }
    
    /**
     * Generate new hash ID for participant
     */
    public function regenerateHashId($id)
    {
        $participant = Participant::findOrFail($id);
        
        $oldHashId = $participant->hash_id;
        $newHashId = Participant::generateHashId();
        
        $participant->update(['hash_id' => $newHashId]);
        
        return redirect()->route('participants.show', $participant)
            ->with('success', "Hash ID berhasil diganti dari {$oldHashId} menjadi {$newHashId}");
    }
    
    /**
     * Toggle participant status (active/inactive)
     */
    public function toggleStatus($id)
    {
        $participant = Participant::findOrFail($id);
        
        $newStatus = $participant->status === 'active' ? 'inactive' : 'active';
        $participant->update(['status' => $newStatus]);
        
        $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->route('participants.index')
            ->with('success', "Participant berhasil {$statusText}");
    }
    
    /**
     * Export participants to CSV
     */
    public function export()
    {
        $participants = Participant::all();
        
        $filename = 'participants_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        // Add CSV headers
        fputcsv($handle, [
            'Hash ID',
            'Name',
            'Email',
            'Phone',
            'Gender',
            'Birthdate',
            'Status',
            'Last Login',
            'Registered Date'
        ]);
        
        // Add data rows
        foreach ($participants as $participant) {
            fputcsv($handle, [
                $participant->hash_id,
                $participant->name,
                $participant->email,
                $participant->phone,
                $participant->gender,
                $participant->birthdate->format('Y-m-d'),
                $participant->status,
                $participant->last_login_at ? $participant->last_login_at->format('Y-m-d H:i') : '-',
                $participant->created_at->format('Y-m-d H:i')
            ]);
        }
        
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);
        
        return response($csvContent)
            ->withHeaders([
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
    }
    
    /**
     * Search participants
     */
    public function search(Request $request)
    {
        $query = Participant::query();
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('hash_id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('gender') && $request->gender != '') {
            $query->where('gender', $request->gender);
        }
        
        $participants = $query->latest()->paginate(15);
        
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
        $participant = Participant::with(['orders.event'])->findOrFail($id);
        
        $pdf = Pdf::loadView('exports.participant', compact('participant'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('participant_' . $participant->hash_id . '.pdf');
    }

    /**
     * Export all participants to PDF
     */
    public function exportAllPdf(Request $request)
    {
        $query = Participant::query();
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by gender
        if ($request->has('gender') && $request->gender != '') {
            $query->where('gender', $request->gender);
        }
        
        $participants = $query->latest()->get();
        $totalParticipants = $participants->count();
        $activeCount = $participants->where('status', 'active')->count();
        $inactiveCount = $participants->where('status', 'inactive')->count();
        $maleCount = $participants->where('gender', 'male')->count();
        $femaleCount = $participants->where('gender', 'female')->count();
        
        $pdf = Pdf::loadView('exports.participants-all', compact(
            'participants', 
            'totalParticipants',
            'activeCount',
            'inactiveCount',
            'maleCount',
            'femaleCount'
        ));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('participants_' . date('Y-m-d') . '.pdf');
    }
}

