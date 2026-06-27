<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrganizationHierarchy extends Model
{
    protected $table = 'organization_hierarchies';
    
    protected $fillable = [
        'year',
        'level',
        'level_name',
        'position_name',
        'position_code',
        'parent_id',
        'sort_order',
        'description',
        'responsibilities',
        'metadata',
        'is_active'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'year' => 'integer',
        'level' => 'integer',
        'sort_order' => 'integer'
    ];

    // Relations
    public function parent()
    {
        return $this->belongsTo(OrganizationHierarchy::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(OrganizationHierarchy::class, 'parent_id')->orderBy('sort_order');
    }

    public function holders()
    {
        return $this->hasMany(OrganizationPositionHolder::class, 'hierarchy_id')->orderBy('sort_order');
    }

    public function activeHolders()
    {
        return $this->hasMany(OrganizationPositionHolder::class, 'hierarchy_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    // Get all descendants
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    // Get path from root
    public function getPathAttribute()
    {
        $path = [$this->position_name];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->position_name);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $path);
    }

    // Get level label
    public function getLevelLabelAttribute()
    {
        $labels = [
            1 => 'Level 1 - Pengurus Inti',
            2 => 'Level 2 - Bidang',
            3 => 'Level 3 - Seksi',
            4 => 'Level 4 - Sub Seksi',
            5 => 'Level 5 - Staff',
        ];
        
        return $labels[$this->level] ?? 'Level ' . $this->level;
    }

    // Get position with level prefix
    public function getDisplayNameAttribute()
    {
        return $this->level_name ? "{$this->level_name} - {$this->position_name}" : $this->position_name;
    }

    // Scope by year
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    // Scope by level
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    // Scope active
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get root positions (no parent)
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
}