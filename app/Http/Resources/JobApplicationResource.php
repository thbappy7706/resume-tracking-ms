<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'role_title' => $this->role_title,
            'job_url' => $this->job_url,
            'source' => $this->source->value,
            'source_label' => $this->source->name,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'currency' => $this->currency,
            'salary_range_display' => $this->salary_range_display,
            'status' => $this->status->value,
            'status_label' => $this->status->name,
            'applied_at' => $this->applied_at?->format('Y-m-d'),
            'responded_at' => $this->responded_at?->format('Y-m-d'),
            'closed_at' => $this->closed_at?->format('Y-m-d'),
            'deadline' => $this->deadline?->format('Y-m-d'),
            'excitement_level' => $this->excitement_level,
            'notes' => $this->notes,
            'days_in_current_status' => $this->days_in_current_status,
            'is_overdue' => $this->is_overdue,
            'company' => new CompanyResource($this->whenLoaded('company')),
            'cv_version' => new CvVersionResource($this->whenLoaded('cvVersion')),
            'interviews' => InterviewResource::collection($this->whenLoaded('interviews')),
            'status_histories' => StatusHistoryResource::collection($this->whenLoaded('statusHistories')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
