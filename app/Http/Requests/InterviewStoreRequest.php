<?php

namespace App\Http\Requests;

use App\Enums\InterviewOutcome;
use App\Enums\InterviewType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class InterviewStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(InterviewType::class)],
            'round' => ['nullable', 'integer', 'min:1'],
            'scheduled_at' => ['required', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'location' => ['nullable', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'max:255'],
            'interviewer_names' => ['nullable', 'array'],
            'interviewer_names.*' => ['string'],
            'outcome' => ['nullable', new Enum(InterviewOutcome::class)],
            'feedback' => ['nullable', 'string'],
            'prep_notes' => ['nullable', 'string'],
        ];
    }
}
