<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CvVersionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'target_role' => $this->target_role,
            'target_industry' => $this->target_industry,
            'notes' => $this->notes,
            'is_base' => $this->is_base,
            'last_exported_at' => $this->last_exported_at,
            'export_count' => $this->export_count,
            'template' => new CvTemplateResource($this->whenLoaded('cvTemplate')),
            'template_id' => $this->cv_template_id,
            'application_count' => $this->application_count,
            'sections' => ProfileSectionResource::collection($this->whenLoaded('resolvedSections')),
            'overrides' => CvSectionOverrideResource::collection($this->whenLoaded('sectionOverrides')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
