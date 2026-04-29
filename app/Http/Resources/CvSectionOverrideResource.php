<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CvSectionOverrideResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cv_version_id' => $this->cv_version_id,
            'profile_section_id' => $this->profile_section_id,
            'is_included' => $this->is_included,
            'sort_order' => $this->sort_order,
            'override_title' => $this->override_title,
            'override_description' => $this->override_description,
            'override_meta' => $this->override_meta,
            'profile_section' => new ProfileSectionResource($this->whenLoaded('profileSection')),
        ];
    }
}
