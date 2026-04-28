<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Participant extends Model
{
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
        
        if ($lastParticipant) {
            $lastId = (int) substr($lastParticipant->hash_id, strlen($prefix));
            $newId = str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newId = '000001';
        }
        
        return $prefix . $newId;
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
}