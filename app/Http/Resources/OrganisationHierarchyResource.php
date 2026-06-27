<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganisationHierarchyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
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
            'metadata' => $this->metadata,
            'is_active' => $this->is_active,
            'level_label' => $this->level_label,
            'display_name' => $this->display_name,
            'path' => $this->path,
            'parent' => $this->when($this->parent, function() {
                return new OrganisationHierarchyResource($this->parent);
            }),
            'children' => $this->when($this->children, function() {
                return OrganisationHierarchyResource::collection($this->children);
            }),
            'children_count' => $this->children ? $this->children->count() : 0,
            'holders' => $this->when($this->holders, function() {
                return OrganisationHolderResource::collection($this->holders);
            }),
            'holders_count' => $this->holders ? $this->holders->count() : 0,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}