<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CvSectionOverrideSyncRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'overrides' => ['required', 'array'],
            'overrides.*.profile_section_id' => ['required', 'string', 'exists:profile_sections,id'],
            'overrides.*.is_included' => ['nullable', 'boolean'],
            'overrides.*.sort_order' => ['nullable', 'integer'],
            'overrides.*.override_title' => ['nullable', 'string', 'max:255'],
            'overrides.*.override_description' => ['nullable', 'string'],
            'overrides.*.override_meta' => ['nullable', 'array'],
        ];
    }
}
