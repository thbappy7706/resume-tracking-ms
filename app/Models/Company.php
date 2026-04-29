<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'slug', 'website', 'industry', 'size', 'location', 'logo_path', 'notes'])]
#[ObservedBy(ModelObserver::class)]
class Company extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'size' => 'string',
        ];
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public int $active_applications_count {
        get => $this->jobApplications()->where('status', '!=', ApplicationStatus::Closed->value)->count();
    }
}
