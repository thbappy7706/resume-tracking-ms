<?php

namespace App\Models;

use App\Enums\InterviewOutcome;
use App\Enums\InterviewType;
use App\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['job_application_id', 'round', 'type', 'scheduled_at', 'duration_minutes', 'interviewer_names', 'platform', 'outcome', 'feedback', 'prep_notes'])]
#[ObservedBy(ModelObserver::class)]
class Interview extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'job_application_id' => 'string',
            'round' => 'integer',
            'type' => InterviewType::class,
            'scheduled_at' => 'datetime',
            'duration_minutes' => 'integer',
            'outcome' => InterviewOutcome::class,
        ];
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public bool $is_upcoming {
        get => $this->scheduled_at?->isFuture() ?? false;
    }

    public string $duration_display {
        get => $this->duration_minutes ? sprintf('%s min', $this->duration_minutes) : 'TBD';
    }
}
