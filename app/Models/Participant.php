<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Participant extends Authenticatable  // ← Ubah dari Model ke Authenticatable
{
    use HasApiTokens;  // ← Tambahkan trait ini
    
    protected $table = 'participants';
    
    protected $fillable = [
                'hash_id',
        'name',
        'email',
        'phone',
        'gender',
        'birthdate',
        'blood_type',           // ← Tambahan
        'emergency_contact',    // ← Tambahan
        'emergency_phone',      // ← Tambahan
        'allergy_history',      // ← Tambahan
        'identity_number',      // ← Tambahan
        'identity_photo',       // ← Tambahan
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

    // Sembunyikan ID dari response API
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
     * Format: SH3ID + 6 digit number (example: SH3ID000001)
     */
    public static function generateHashId(): string
    {
        $prefix = 'SH3ID';
        $lastParticipant = static::orderBy('id', 'desc')->first();
        
        if ($lastParticipant && $lastParticipant->hash_id) {
            $lastId = (int) substr($lastParticipant->hash_id, strlen($prefix));
            $newId = str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newId = '000001';
        }
        
        return $prefix . $newId;
    }

    /**
     * Generate random hash ID (alternatif)
     */
    public static function generateRandomHashId(): string
    {
        $prefix = 'SH3ID';
        
        do {
            $random = Str::upper(Str::random(8));
            $hashId = $prefix . $random;
        } while (self::where('hash_id', $hashId)->exists());
        
        return $hashId;
    }

    /**
     * Find participant by hash ID
     */
    public static function findByHashId($hashId)
    {
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
    /**
     * Get identity photo URL
     */
    public function getIdentityPhotoUrlAttribute()
    {
        return $this->identity_photo ? asset('storage/' . $this->identity_photo) : null;
    }

    /**
     * Get blood type label
     */
    public function getBloodTypeLabelAttribute()
    {
        $labels = [
            'A' => 'A',
            'B' => 'B',
            'AB' => 'AB',
            'O' => 'O'
        ];
        return $labels[$this->blood_type] ?? '-';
    }
}