<?php

namespace App\Models;

use App\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['job_application_id', 'from_status', 'to_status', 'note', 'changed_at'])]
#[ObservedBy(ModelObserver::class)]
class StatusHistory extends Model
{
    use HasFactory, HasUlids;

    protected function casts(): array
    {
        return [
            'job_application_id' => 'string',
            'changed_at' => 'datetime',
        ];
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }
}
