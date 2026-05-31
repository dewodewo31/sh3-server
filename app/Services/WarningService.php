<?php

namespace App\Services;

use App\Models\Participant;
use App\Models\ParticipantWarning;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WarningService
{
    /**
     * Issue warning to participant
     */
    public function issueWarning($participantId, $reason, $description = null)
    {
        return DB::transaction(function () use ($participantId, $reason, $description) {
            $participant = Participant::findOrFail($participantId);
            
            // Calculate new warning level
            $newWarningLevel = $participant->current_warning_level + 1;
            
            // Create warning record
            $warning = ParticipantWarning::create([
                'participant_id' => $participantId,
                'issued_by' => Auth::id(),
                'warning_level' => $newWarningLevel,
                'reason' => $reason,
                'description' => $description,
                'issued_at' => now(),
                'is_active' => true
            ]);
            
            // Apply sanction based on warning level
            $sanction = $this->applySanction($participant, $newWarningLevel, $reason);
            
            // Update participant warning info
            $participant->update([
                'warning_count' => $participant->warning_count + 1,
                'current_warning_level' => $newWarningLevel,
                'is_suspended' => $sanction['is_suspended'],
                'suspended_until' => $sanction['suspended_until'],
                'suspension_reason' => $sanction['suspension_reason']
            ]);
            
            return [
                'success' => true,
                'warning' => $warning,
                'sanction' => $sanction,
                'participant' => $participant
            ];
        });
    }
    
    /**
     * Apply sanction based on warning level
     */
    private function applySanction($participant, $warningLevel, $reason)
    {
        $result = [
            'is_suspended' => false,
            'suspended_until' => null,
            'suspension_reason' => $reason,
            'message' => ''
        ];
        
        switch ($warningLevel) {
            case 1:
                // Warning 1: Cannot join next 2 events
                $result['message'] = 'Peringatan level 1: Tidak bisa join 2 event berikutnya';
                $this->blockFutureEvents($participant->id, 2);
                break;
                
            case 2:
                // Warning 2: Cannot join next 5 events
                $result['message'] = 'Peringatan level 2: Tidak bisa join 5 event berikutnya';
                $this->blockFutureEvents($participant->id, 5);
                break;
                
            case 3:
                // Warning 3: Suspended indefinitely
                $result['is_suspended'] = true;
                $result['suspended_until'] = null; // Permanent suspension
                $result['message'] = 'Peringatan level 3: Akun dinonaktifkan permanen';
                break;
                
            default:
                if ($warningLevel > 3) {
                    $result['is_suspended'] = true;
                    $result['suspended_until'] = null;
                    $result['message'] = 'Akun dinonaktifkan permanen karena pelanggaran berat';
                }
                break;
        }
        
        return $result;
    }
    
    /**
     * Block participant from joining future events
     */
    private function blockFutureEvents($participantId, $numberOfEvents)
    {
        // This is handled by checking active warnings when joining
        // The logic is in canJoinEvent() method
    }
    
    /**
     * Get participant's active warnings
     */
    public function getActiveWarnings($participantId)
    {
        $participant = Participant::findOrFail($participantId);
        
        $warnings = $participant->activeWarnings()
            ->with('issuer')
            ->orderBy('warning_level', 'desc')
            ->get();
            
        $suspensionInfo = $participant->getSuspensionInfo();
        
        // Calculate remaining events to block
        $remainingBlockedEvents = $this->getRemainingBlockedEvents($participant);
        
        return [
            'warning_count' => $participant->warning_count,
            'current_level' => $participant->current_warning_level,
            'warnings' => $warnings,
            'suspension' => $suspensionInfo,
            'remaining_blocked_events' => $remainingBlockedEvents,
            'can_join' => $participant->canJoinEvent()
        ];
    }
    
    /**
     * Get remaining events participant cannot join
     */
    private function getRemainingBlockedEvents($participant)
    {
        if ($participant->current_warning_level == 0) {
            return 0;
        }
        
        // Count how many events participant has been blocked from
        $blockedEventsCount = Order::where('participant_id', $participant->id)
            ->where('status', 'blocked_by_warning')
            ->count();
            
        $totalBlockedRequired = $participant->current_warning_level == 1 ? 2 : 5;
        
        return max(0, $totalBlockedRequired - $blockedEventsCount);
    }
    
    /**
     * Remove warning (for admin)
     */
    public function removeWarning($warningId, $reason = null)
    {
        return DB::transaction(function () use ($warningId, $reason) {
            $warning = ParticipantWarning::findOrFail($warningId);
            $participant = $warning->participant;
            
            // Deactivate warning
            $warning->update([
                'is_active' => false,
                'expires_at' => now()
            ]);
            
            // Recalculate participant's warning level
            $activeWarnings = $participant->activeWarnings()->get();
            
            if ($activeWarnings->isEmpty()) {
                // No active warnings, remove suspension
                $participant->update([
                    'current_warning_level' => 0,
                    'is_suspended' => false,
                    'suspended_until' => null,
                    'suspension_reason' => null
                ]);
            } else {
                // Get highest warning level
                $highestLevel = $activeWarnings->max('warning_level');
                $participant->current_warning_level = $highestLevel;
                
                // Reapply sanction based on highest level
                if ($highestLevel >= 3) {
                    $participant->is_suspended = true;
                    $participant->suspended_until = null;
                } else {
                    $participant->is_suspended = false;
                    $participant->suspended_until = null;
                }
                
                $participant->save();
            }
            
            return [
                'success' => true,
                'message' => 'Warning berhasil dihapus',
                'participant' => $participant
            ];
        });
    }
    
    /**
     * Check if participant can join specific event
     */
    public function canJoinEvent($participantId, $eventId = null)
    {
        $participant = Participant::findOrFail($participantId);
        
        // Check suspension first
        $canJoin = $participant->canJoinEvent();
        if (!$canJoin['can_join']) {
            return $canJoin;
        }
        
        // Check warning levels for event blocking
        if ($participant->current_warning_level > 0) {
            $blockedCount = Order::where('participant_id', $participant->id)
                ->where('status', 'blocked_by_warning')
                ->count();
                
            $maxBlocked = $participant->current_warning_level == 1 ? 2 : 5;
            
            if ($blockedCount >= $maxBlocked) {
                return [
                    'can_join' => true,
                    'message' => 'Sanksi telah selesai, Anda sudah bisa join event kembali'
                ];
            }
            
            return [
                'can_join' => false,
                'reason' => "Anda mendapatkan peringatan level {$participant->current_warning_level}. Tidak bisa join " . 
                           ($maxBlocked - $blockedCount) . " event berikutnya.",
                'warning_level' => $participant->current_warning_level,
                'remaining_events' => $maxBlocked - $blockedCount
            ];
        }
        
        return ['can_join' => true];
    }
}