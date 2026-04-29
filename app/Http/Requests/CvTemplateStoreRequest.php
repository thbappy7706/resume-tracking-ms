<?php

namespace App\Http\Requests;

use App\Enums\CvLayout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CvTemplateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:cv_templates,slug'],
            'description' => ['nullable', 'string'],
            'layout' => ['required', new Enum(CvLayout::class)],
            'config' => ['nullable', 'array'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }
}
