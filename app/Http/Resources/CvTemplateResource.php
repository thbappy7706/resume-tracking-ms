<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CvTemplateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'layout' => $this->layout->value,
            'layout_label' => $this->layout->name,
            'config' => $this->config,
            'thumbnail_path' => $this->thumbnail_path,
            'is_default' => $this->is_default,
            'cv_count' => $this->whenCounted('cvVersions'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
