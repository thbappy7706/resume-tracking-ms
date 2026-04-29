<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CvVersionExportPdfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'format' => ['nullable', 'string', 'in:a4,letter,legal'],
        ];
    }
}
