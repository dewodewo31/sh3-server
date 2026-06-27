<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganisationHierarchyTreeResource extends JsonResource
{
    /**
     * Transform the resource into an array (Tree Structure).
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'year' => $this->year,
            'level' => $this->level,
            'level_name' => $this->level_name,
            'position_name' => $this->position_name,
            'position_code' => $this->position_code,
            'parent_id' => $this->parent_id,
            'sort_order' => $this->sort_order,
            'description' => $this->description,
            'responsibilities' => $this->responsibilities,
            'is_active' => $this->is_active,
            'level_label' => $this->level_label,
            'display_name' => $this->display_name,
            'path' => $this->path,
            'holders' => $this->when($this->holders, function() {
                return OrganisationHolderResource::collection($this->holders);
            }),
            'children' => $this->when($this->children_list, function() {
                return OrganisationHierarchyTreeResource::collection($this->children_list);
            }),
            'children_count' => $this->children ? $this->children->count() : 0,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}