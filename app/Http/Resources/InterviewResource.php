<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InterviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type?->value,
            'type_label' => $this->type?->name,
            'round' => $this->round,
            'scheduled_at' => $this->scheduled_at,
            'duration_minutes' => $this->duration_minutes,
            'duration_display' => $this->duration_display,
            'location' => $this->location,
            'platform' => $this->platform,
            'interviewer_names' => $this->interviewer_names,
            'outcome' => $this->outcome?->value,
            'outcome_label' => $this->outcome?->name,
            'feedback' => $this->feedback,
            'prep_notes' => $this->prep_notes,
            'is_upcoming' => $this->is_upcoming,
            'job_application_id' => $this->job_application_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
