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
        'name',
        'email',
        'phone',
        'gender',
        'birthdate',
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
     * Generate unique hash ID
     * Format: 4 digit number (example: 0001, 0002, 0010, 0100, 1000, 9999)
     */
    public static function generateHashId(): string
    {
        $lastParticipant = static::orderBy('id', 'desc')->first();
        
        if ($lastParticipant && $lastParticipant->hash_id) {
            $lastId = (int) $lastParticipant->hash_id;
            $newId = str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newId = '0001';
        }
        
        return $newId;
    }

    /**
     * Generate random hash ID (alternatif)
     */
    public static function generateRandomHashId(): string
    {
        do {
            $random = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('hash_id', $random)->exists());
        
        return $random;
    }

    /**
     * Find participant by hash ID
     */
    public static function findByHashId($hashId)
    {
        // Ensure hash_id is 4 digits (pad with zeros if needed)
        $hashId = str_pad($hashId, 4, '0', STR_PAD_LEFT);
        return static::where('hash_id', $hashId)->first();
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
     * Get the name of the unique identifier for the user.
     * Required by Authenticatable
     */
    public function getAuthIdentifierName()
    {
        return 'hash_id';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->hash_id;
    }

    /**
     * Get the password for the user.
     * Participants don't have password
     */
    public function getAuthPassword()
    {
        return null;
    }

    /**
     * Get the token value for the "remember me" session.
     */
    public function getRememberToken()
    {
        return null;
    }

    /**
     * Set the token value for the "remember me" session.
     */
    public function setRememberToken($value)
    {
        // Not implemented
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName()
    {
        return '';
    }
}