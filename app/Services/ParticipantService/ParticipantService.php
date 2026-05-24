<?php

namespace App\Services\ParticipantService;

use App\Models\Participant;
use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

class ParticipantService implements ParticipantServiceInterface
{
    protected $participant;

    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
    }

    /**
     * Get participants with pagination
     */
    public function getParticipants(Request $request): LengthAwarePaginator
    {
        $query = Participant::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('hash_id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        return $query->latest()->paginate($request->per_page ?? 15);
    }

    /**
     * Get participant statistics
     */
    public function getParticipantStats(): array
    {
        return [
            'totalParticipants' => Participant::count(),
            'activeParticipants' => Participant::where('status', 'active')->count(),
            'inactiveParticipants' => Participant::where('status', 'inactive')->count(),
            'maleParticipants' => Participant::where('gender', 'male')->count(),
            'femaleParticipants' => Participant::where('gender', 'female')->count(),
        ];
    }

    /**
     * Get participant by ID
     */
    public function getParticipantById($id)
    {
        return Participant::findOrFail($id);
    }

    /**
     * Get participant with details (orders, events, etc.)
     */
    public function getParticipantWithDetails($id)
    {
        $participant = Participant::with(['orders' => function($query) {
            $query->with(['event', 'payment'])->latest();
        }])->findOrFail($id);
        
        // Order Statistics
        $orderStats = [
            'totalOrders' => $participant->orders()->count(),
            'totalSpent' => $participant->orders()->where('status', 'paid')->sum('total_price'),
            'pendingOrders' => $participant->orders()->where('status', 'pending')->count(),
            'paidOrders' => $participant->orders()->where('status', 'paid')->count(),
            'freeOrders' => $participant->orders()->where('status', 'free')->count(),
            'cancelledOrders' => $participant->orders()->where('status', 'cancelled')->count(),
        ];
        
        // Event History
        $eventIds = $participant->orders()->pluck('event_id')->unique();
        $eventsJoined = Event::whereIn('id', $eventIds)->with('category')->get();
        
        $eventStats = [
            'eventsJoined' => $eventsJoined,
            'totalEventsJoined' => $eventsJoined->count(),
            'upcomingEvents' => $eventsJoined->filter(fn($e) => $e->status === 'upcoming'),
            'pastEvents' => $eventsJoined->filter(fn($e) => $e->status === 'finished'),
            'ongoingEvents' => $eventsJoined->filter(fn($e) => $e->status === 'ongoing'),
        ];
        
        // Monthly spending chart
        $monthlySpending = $participant->orders()
            ->where('status', 'paid')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_price) as total')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();
        
        return array_merge(
            ['participant' => $participant],
            $orderStats,
            $eventStats,
            ['monthlySpending' => $monthlySpending]
        );
    }

    /**
     * Create new participant
     */
    public function createParticipant(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:participants,email',
            'phone' => 'required|string|max:15',
            'gender' => 'required|in:male,female',
            'birthdate' => 'required|date|before:today',
            'status' => 'sometimes|in:active,inactive',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        return Participant::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'gender' => $data['gender'],
            'birthdate' => $data['birthdate'],
            'status' => $data['status'] ?? 'active',
            'notes' => $data['notes'] ?? null
        ]);
    }

    /**
     * Update participant
     */
    public function updateParticipant($id, array $data)
    {
        $participant = Participant::findOrFail($id);
        
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:participants,email,' . $participant->id,
            'phone' => 'required|string|max:15',
            'gender' => 'required|in:male,female',
            'birthdate' => 'required|date|before:today',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $participant->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'gender' => $data['gender'],
            'birthdate' => $data['birthdate'],
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null
        ]);
        
        return $participant;
    }

    /**
     * Delete participant
     */
    public function deleteParticipant($id)
    {
        $participant = Participant::findOrFail($id);
        
        // Check if participant has orders
        if ($participant->orders()->count() > 0) {
            throw new \Exception('Tidak dapat menghapus participant yang memiliki pesanan');
        }
        
        return $participant->delete();
    }

    /**
     * Regenerate hash ID
     */
    public function regenerateHashId($id)
    {
        $participant = Participant::findOrFail($id);
        
        $oldHashId = $participant->hash_id;
        $newHashId = Participant::generateHashId();
        
        $participant->update(['hash_id' => $newHashId]);
        
        return [
            'old_hash_id' => $oldHashId,
            'new_hash_id' => $newHashId
        ];
    }

    /**
     * Toggle participant status
     */
    public function toggleStatus($id)
    {
        $participant = Participant::findOrFail($id);
        
        $newStatus = $participant->status === 'active' ? 'inactive' : 'active';
        $participant->update(['status' => $newStatus]);
        
        return [
            'status' => $newStatus,
            'message' => $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan'
        ];
    }

    /**
     * Export participants to CSV
     */
    public function exportParticipantsToCsv()
    {
        $participants = Participant::all();
        
        $filename = 'participants_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        // CSV Headers
        fputcsv($handle, [
            'Hash ID', 'Name', 'Email', 'Phone', 'Gender', 
            'Birthdate', 'Status', 'Last Login', 'Registered Date'
        ]);
        
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
        
        return [
            'content' => $csvContent,
            'filename' => $filename,
            'headers' => [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        ];
    }

    /**
     * Export all participants to PDF
     */
    public function exportParticipantsToPdf(Request $request)
    {
        $query = Participant::query();
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        
        $participants = $query->latest()->get();
        
        $stats = [
            'totalParticipants' => $participants->count(),
            'activeCount' => $participants->where('status', 'active')->count(),
            'inactiveCount' => $participants->where('status', 'inactive')->count(),
            'maleCount' => $participants->where('gender', 'male')->count(),
            'femaleCount' => $participants->where('gender', 'female')->count(),
        ];
        
        $pdf = Pdf::loadView('exports.participants-all', array_merge(compact('participants'), $stats));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('participants_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export single participant to PDF
     */
    public function exportSingleParticipantToPdf($id)
    {
        $participant = $this->getParticipantWithDetails($id);
        
        $pdf = Pdf::loadView('exports.participant', $participant);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('participant_' . $participant['participant']->hash_id . '.pdf');
    }

    /**
     * Search participants
     */
    public function searchParticipants(Request $request): LengthAwarePaginator
    {
        $query = Participant::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('hash_id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        
        return $query->latest()->paginate($request->per_page ?? 15);
    }
}