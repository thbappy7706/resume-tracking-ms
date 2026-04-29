<?php

namespace App\Http\Requests;

use App\Enums\ApplicationSource;
use App\Enums\ApplicationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class JobApplicationUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'string', 'exists:companies,id'],
            'cv_version_id' => ['nullable', 'string', 'exists:cv_versions,id'],
            'role_title' => ['sometimes', 'string', 'max:255'],
            'job_url' => ['nullable', 'url'],
            'source' => ['sometimes', new Enum(ApplicationSource::class)],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0', 'gte:salary_min'],
            'currency' => ['nullable', 'string', 'max:3'],
            'status' => ['sometimes', new Enum(ApplicationStatus::class)],
            'applied_at' => ['nullable', 'date'],
            'responded_at' => ['nullable', 'date'],
            'closed_at' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date'],
            'excitement_level' => ['nullable', 'integer', 'min:1', 'max:5'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
