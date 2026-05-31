<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantWarning extends Model
{
    protected $table = 'participant_warnings';
    
    protected $fillable = [
        'participant_id',
        'issued_by',
        'warning_level',
        'reason',
        'description',
        'issued_at',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    // Relations
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    // Get warning badge
    public function getWarningLevelBadgeAttribute()
    {
        return match($this->warning_level) {
            1 => '<span class="badge badge-warning">Warning 1</span>',
            2 => '<span class="badge badge-danger">Warning 2</span>',
            3 => '<span class="badge badge-dark">Warning 3 - Suspended</span>',
            default => '<span class="badge badge-secondary">Unknown</span>',
        };
    }

    // Get sanction info
    public function getSanctionTextAttribute()
    {
        return match($this->warning_level) {
            1 => 'Tidak bisa join 2 event berikutnya',
            2 => 'Tidak bisa join 5 event berikutnya',
            3 => 'Akun dinonaktifkan (permanen sampai ada tindakan lanjut)',
            default => 'Unknown sanction',
        };
    }
}