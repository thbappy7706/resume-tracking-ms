<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileSectionReorderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sections' => ['required', 'array'],
            'sections.*.id' => ['required', 'string', 'exists:profile_sections,id'],
            'sections.*.sort_order' => ['required', 'integer', 'min:0'],
        ];
    }
}
