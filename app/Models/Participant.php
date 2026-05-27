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
        'participant_type',  // member / non_member
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
        'last_login_at',
        'last_login_ip',
        'notes'
    ];

    protected $casts = [
        'birthdate' => 'date',
        'last_login_at' => 'datetime'
    ];

    protected $hidden = [
        'id'
    ];

    protected $attributes = [
        'participant_type' => 'non_member' // Default: non_member
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
     * Example: 0001, 0002, 0003...
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
     * Example: NM01, NM02, NM03...
     */
    public static function generateNonMemberHashId(): string
    {
        $prefix = 'NM';
        $lastNonMember = static::where('participant_type', 'non_member')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastNonMember && $lastNonMember->hash_id) {
            // Extract number from NM01 -> 1
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

    /**
     * Get identity photo URL
     */
    public function getIdentityPhotoUrlAttribute()
    {
        return $this->identity_photo ? asset('storage/' . $this->identity_photo) : null;
    }

    /**
     * Get photo URL
     */
    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    /**
     * Check if participant is member
     */
    public function isMember()
    {
        return $this->participant_type === 'member';
    }

    /**
     * Check if participant is non-member
     */
    public function isNonMember()
    {
        return $this->participant_type === 'non_member';
    }

    /**
     * Get orders for this participant
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'participant_id');
    }

    /**
     * Get payments for this participant
     */
    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Order::class);
    }
    
    /**
     * Check if participant is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
    
    /**
     * Activate participant account
     */
    public function activate()
    {
        $this->status = 'active';
        return $this->save();
    }

    /**
     * Deactivate participant account
     */
    public function deactivate()
    {
        $this->status = 'inactive';
        return $this->save();
    }

    /**
     * Upgrade non-member to member
     */
    public function upgradeToMember()
    {
        if ($this->participant_type === 'member') {
            return false;
        }
        
        $this->participant_type = 'member';
        $this->hash_id = static::generateMemberHashId();
        
        return $this->save();
    }

    /**
     * Get the name of the unique identifier for the user.
     */
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