<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'website' => $this->website,
            'industry' => $this->industry,
            'size' => $this->size,
            'location' => $this->location,
            'logo_path' => $this->logo_path,
            'notes' => $this->notes,
            'active_applications_count' => $this->active_applications_count,
            'application_count' => $this->whenCounted('jobApplications'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
