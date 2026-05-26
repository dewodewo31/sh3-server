<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class NonMemberParticipant extends Authenticatable
{
    use HasApiTokens;
    
    protected $table = 'non_member_participants';
    
    protected $fillable = [
        'hash_id',
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

    protected static function booted()
    {
        static::creating(function ($participant) {
            if (!$participant->hash_id) {
                $participant->hash_id = static::generateHashId();
            }
        });
    }

    /**
     * Generate unique hash ID (4 digits)
     */
    public static function generateHashId(): string
    {
        $prefix = 'NM'; // Non-Member prefix
        $lastParticipant = static::orderBy('id', 'desc')->first();
        
        if ($lastParticipant && $lastParticipant->hash_id) {
            $lastId = (int) str_replace($prefix, '', $lastParticipant->hash_id);
            $newId = $prefix . str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newId = $prefix . '000001';
        }
        
        return $newId;
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
     * Check if participant is active
     */
    public function isActive()
    {
        return $this->status === 'active';
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