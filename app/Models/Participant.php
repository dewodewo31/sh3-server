<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class Participant extends Authenticatable
{
    use HasApiTokens;
    
    protected $table = 'participants';
    
    protected $fillable = [
        'hash_id',
        'participant_type',  // Hanya satu kali
        'name',
        'email',
        'phone',
        'gender',
        'birthdate',
        'blood_type',
        'emergency_contact',
        'emergency_phone',
        'allergy_history',
        'identity_number',
        'identity_photo',
        'photo',
        'status',
        'warning_count',
        'current_warning_level',
        'suspended_until',
        'is_suspended',
        'suspension_reason',
        'last_login_at',
        'last_login_ip',
        'notes'
    ];

    protected $casts = [
        'birthdate' => 'date',
        'last_login_at' => 'datetime',
        'suspended_until' => 'datetime',
        'is_suspended' => 'boolean',
        'warning_count' => 'integer',
        'current_warning_level' => 'integer'
    ];

    protected $hidden = [
        'id'
    ];

    protected $attributes = [
        'participant_type' => 'non_member',
        'status' => 'active', // Tambahkan default status
        'warning_count' => 0,
        'current_warning_level' => 0,
        'is_suspended' => false
    ];

    protected static function booted()
    {
        static::creating(function ($participant) {
            if (empty($participant->hash_id)) {
                if ($participant->participant_type === 'member') {
                    $participant->hash_id = static::generateMemberHashId();
                } else {
                    $participant->hash_id = static::generateNonMemberHashId();
                }
            }
        });
    }

    /**
     * Generate unique hash ID for member (4 digits numeric)
     */
    public static function generateMemberHashId(): string
    {
        $lastMember = static::where('participant_type', 'member')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastMember && $lastMember->hash_id && is_numeric($lastMember->hash_id)) {
            $lastId = (int) $lastMember->hash_id;
            $newId = str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newId = '0001';
        }
        
        return $newId;
    }

    /**
     * Generate hash ID for non-member
     */
    public static function generateNonMemberHashId(): string
    {
        $prefix = 'NM';
        $lastNonMember = static::where('participant_type', 'non_member')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastNonMember && $lastNonMember->hash_id) {
            $lastNumber = (int) substr($lastNonMember->hash_id, 2);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Alias for generateMemberHashId (backward compatibility)
     */
    public static function generateHashId(): string
    {
        return static::generateMemberHashId();
    }

    // ==================== WARNING METHODS ====================
    
    /**
     * Check if participant can join events
     */
    public function canJoinEvent()
    {
        if ($this->is_suspended) {
            if ($this->suspended_until && now()->lt($this->suspended_until)) {
                return [
                    'can_join' => false,
                    'reason' => "Akun Anda sedang disuspensi hingga " . $this->suspended_until->format('d M Y H:i'),
                    'suspension_type' => 'temporary'
                ];
            } elseif (!$this->suspended_until) {
                return [
                    'can_join' => false,
                    'reason' => "Akun Anda telah dinonaktifkan permanen karena pelanggaran berat",
                    'suspension_type' => 'permanent'
                ];
            } else {
                // Auto reactivate if suspension period has passed
                $this->update([
                    'is_suspended' => false,
                    'suspended_until' => null,
                    'suspension_reason' => null
                ]);
                return ['can_join' => true];
            }
        }
        
        return ['can_join' => true];
    }

    /**
     * Get suspension info
     */
    public function getSuspensionInfo()
    {
        if (!$this->is_suspended) {
            return null;
        }

        if ($this->suspended_until && now()->lt($this->suspended_until)) {
            $remainingDays = now()->diffInDays($this->suspended_until, false);
            return [
                'is_suspended' => true,
                'type' => 'temporary',
                'reason' => $this->suspension_reason,
                'until' => $this->suspended_until,
                'remaining_days' => max(0, (int)$remainingDays),
                'warning_level' => $this->current_warning_level
            ];
        } elseif ($this->suspended_until && now()->gte($this->suspended_until)) {
            // Auto reactivate
            $this->update([
                'is_suspended' => false,
                'suspended_until' => null,
                'suspension_reason' => null
            ]);
            return null;
        } else {
            return [
                'is_suspended' => true,
                'type' => 'permanent',
                'reason' => $this->suspension_reason,
                'warning_level' => $this->current_warning_level
            ];
        }
    }

    /**
     * Get warning badge
     */
    public function getWarningBadgeAttribute()
    {
        if ($this->current_warning_level == 0) {
            return '<span class="badge badge-success">Aman</span>';
        } elseif ($this->current_warning_level == 1) {
            return '<span class="badge badge-warning">Warning 1</span>';
        } elseif ($this->current_warning_level == 2) {
            return '<span class="badge badge-danger">Warning 2</span>';
        } elseif ($this->current_warning_level >= 3) {
            return '<span class="badge badge-dark">Suspended</span>';
        }
        return '<span class="badge badge-secondary">Unknown</span>';
    }

    // ==================== RELATIONS ====================
    
    public function orders()
    {
        return $this->hasMany(Order::class, 'participant_id');
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Order::class);
    }
    
    public function warnings()
    {
        return $this->hasMany(ParticipantWarning::class);
    }

    public function activeWarnings()
    {
        return $this->hasMany(ParticipantWarning::class)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    // ==================== HELPER METHODS ====================
    
    public function getIdentityPhotoUrlAttribute()
    {
        return $this->identity_photo ? asset('storage/' . $this->identity_photo) : null;
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    public function isMember()
    {
        return $this->participant_type === 'member';
    }

    public function isNonMember()
    {
        return $this->participant_type === 'non_member';
    }
    
    public function isActive()
    {
        return $this->status === 'active';
    }
    
    public function activate()
    {
        $this->status = 'active';
        return $this->save();
    }

    public function deactivate()
    {
        $this->status = 'inactive';
        return $this->save();
    }

    public function upgradeToMember()
    {
        if ($this->participant_type === 'member') {
            return false;
        }
        
        $this->participant_type = 'member';
        $this->hash_id = static::generateMemberHashId();
        
        return $this->save();
    }

    // ==================== AUTHENTICATION METHODS ====================
    
    public function getAuthIdentifierName()
    {
        return 'hash_id';
    }

    public function getAuthIdentifier()
    {
        return $this->hash_id;
    }

    public function getAuthPassword()
    {
        return null;
    }
}