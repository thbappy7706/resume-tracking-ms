<?php

namespace App\Http\Requests;

use App\Enums\CvLayout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CvTemplateUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'unique:cv_templates,slug,'.$this->template->id],
            'description' => ['nullable', 'string'],
            'layout' => ['sometimes', new Enum(CvLayout::class)],
            'config' => ['nullable', 'array'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }
}
