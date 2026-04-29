<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CvVersionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'cv_template_id' => ['sometimes', 'string', 'exists:cv_templates,id'],
            'target_role' => ['nullable', 'string', 'max:255'],
            'target_industry' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'section_ids' => ['nullable', 'array'],
            'section_ids.*' => ['string', 'exists:profile_sections,id'],
            'section_order' => ['nullable', 'array'],
            'section_order.*' => ['string', 'exists:profile_sections,id'],
        ];
    }
}
