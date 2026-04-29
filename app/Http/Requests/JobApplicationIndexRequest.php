<?php

namespace App\Http\Requests;

use App\Enums\ApplicationSource;
use App\Enums\ApplicationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class JobApplicationIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', new Enum(ApplicationStatus::class)],
            'source' => ['nullable', new Enum(ApplicationSource::class)],
            'company_id' => ['nullable', 'string', 'exists:companies,id'],
            'cv_version_id' => ['nullable', 'string', 'exists:cv_versions,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'search' => ['nullable', 'string', 'max:255'],
            'sort_by' => ['nullable', 'string', 'in:role_title,applied_at,status,company.name'],
            'sort_direction' => ['nullable', 'string', 'in:asc,desc'],
            'view' => ['nullable', 'string', 'in:kanban,table'],
        ];
    }
}
