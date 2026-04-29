<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'sort_by' => ['nullable', 'string', 'in:name,industry,size'],
            'sort_direction' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }
}
