<?php

namespace App\Http\Requests;

use App\Enums\CompanySize;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CompanyUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'unique:companies,slug,'.$this->company->id],
            'website' => ['nullable', 'url'],
            'industry' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', new Enum(CompanySize::class)],
            'location' => ['nullable', 'string', 'max:255'],
            'logo_path' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
