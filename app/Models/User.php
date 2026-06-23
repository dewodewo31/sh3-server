<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ==================== RELATIONS ====================
    
    /**
     * Events created by this user (sebagai organizer/creator)
     */
    public function eventsCreated()
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    /**
     * Galleries uploaded by this user
     */
    public function uploadedGalleries()
    {
        return $this->hasMany(EventGallery::class, 'uploaded_by');
    }

    /**
     * Sponsors managed by this user
     */
    public function sponsors()
    {
        return $this->hasMany(Sponsor::class, 'created_by');
    }

    /**
     * Merchandise managed by this user
     */
    public function merchandises()
    {
        return $this->hasMany(Merchandise::class, 'created_by');
    }

    // ==================== ROLE METHODS ====================
    
    /**
     * Check if user has specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin (any admin type)
     */
    public function isAdmin()
    {
        $adminRoles = ['admin', 'admin_full_access', 'admin_laman', 'admin_member', 'admin_bnh'];
        return in_array($this->role, $adminRoles);
    }

    /**
     * Check if user is organizer
     */
    public function isOrganizer()
    {
        return $this->role === 'organizer';
    }

    /**
     * Check if user is bendahara
     */
    public function isBendahara()
    {
        return $this->role === 'bendahara';
    }

    /**
     * Check if user is sponsor
     */
    public function isSponsor()
    {
        return $this->role === 'sponsor';
    }

    /**
     * Check if user is merchandise
     */
    public function isMerchandise()
    {
        return $this->role === 'merchandise';
    }

    /**
     * Check if user is participant
     */
    public function isParticipant()
    {
        return $this->role === 'participant';
    }

    // ==================== HELPERS ====================
    
    /**
     * Get role badge HTML
     */
    public function getRoleBadgeAttribute()
    {
        $badges = [
            'admin' => ['bg' => 'bg-purple-500/20', 'text' => 'text-purple-300', 'label' => 'Admin'],
            'admin_full_access' => ['bg' => 'bg-purple-600/20', 'text' => 'text-purple-400', 'label' => 'Admin Full'],
            'admin_laman' => ['bg' => 'bg-purple-400/20', 'text' => 'text-purple-300', 'label' => 'Admin Laman'],
            'admin_member' => ['bg' => 'bg-indigo-500/20', 'text' => 'text-indigo-300', 'label' => 'Admin Member'],
            'admin_bnh' => ['bg' => 'bg-pink-500/20', 'text' => 'text-pink-300', 'label' => 'Admin BNH'],
            'organizer' => ['bg' => 'bg-yellow-500/20', 'text' => 'text-yellow-300', 'label' => 'Organizer'],
            'bendahara' => ['bg' => 'bg-green-500/20', 'text' => 'text-green-300', 'label' => 'Bendahara'],
            'sponsor' => ['bg' => 'bg-blue-500/20', 'text' => 'text-blue-300', 'label' => 'Sponsor'],
            'merchandise' => ['bg' => 'bg-orange-500/20', 'text' => 'text-orange-300', 'label' => 'Merchandise'],
            'participant' => ['bg' => 'bg-gray-500/20', 'text' => 'text-gray-300', 'label' => 'Participant'],
        ];
        
        $badge = $badges[$this->role] ?? $badges['participant'];
        
        return sprintf(
            '<span class="px-2 py-1 %s %s rounded-full text-xs">%s</span>',
            $badge['bg'],
            $badge['text'],
            $badge['label']
        );
    }

    /**
     * Get role label
     */
    public function getRoleLabelAttribute()
    {
        $labels = [
            'admin' => 'Admin',
            'admin_full_access' => 'Admin Full Access',
            'admin_laman' => 'Admin Laman',
            'admin_member' => 'Admin Member',
            'admin_bnh' => 'Admin BNH',
            'organizer' => 'Organizer',
            'bendahara' => 'Bendahara',
            'sponsor' => 'Sponsor',
            'merchandise' => 'Merchandise',
            'participant' => 'Participant',
        ];
        
        return $labels[$this->role] ?? 'Participant';
    }

    /**
     * Get role color
     */
    public function getRoleColorAttribute()
    {
        $colors = [
            'admin' => 'purple',
            'admin_full_access' => 'purple',
            'admin_laman' => 'purple',
            'admin_member' => 'indigo',
            'admin_bnh' => 'pink',
            'organizer' => 'yellow',
            'bendahara' => 'green',
            'sponsor' => 'blue',
            'merchandise' => 'orange',
            'participant' => 'gray',
        ];
        
        return $colors[$this->role] ?? 'gray';
    }
}