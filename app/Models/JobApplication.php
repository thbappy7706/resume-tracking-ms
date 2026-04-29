<?php

namespace App\Models;

use App\Enums\ApplicationSource;
use App\Enums\ApplicationStatus;
use App\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['company_id', 'cv_version_id', 'role_title', 'job_url', 'source', 'salary_min', 'salary_max', 'currency', 'status', 'applied_at', 'responded_at', 'closed_at', 'deadline', 'excitement_level', 'notes'])]
#[ObservedBy(ModelObserver::class)]
class JobApplication extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'company_id' => 'string',
            'cv_version_id' => 'string',
            'source' => ApplicationSource::class,
            'status' => ApplicationStatus::class,
            'salary_min' => 'integer',
            'salary_max' => 'integer',
            'applied_at' => 'date',
            'responded_at' => 'date',
            'closed_at' => 'date',
            'deadline' => 'date',
            'excitement_level' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function cvVersion(): BelongsTo
    {
        return $this->belongsTo(CvVersion::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(StatusHistory::class);
    }

    public int $days_in_current_status {
        get => $this->statusHistories()
            ->latest('changed_at')
            ->value('changed_at')
            ? now()->diffInDays($this->statusHistories()->latest('changed_at')->value('changed_at'))
            : 0;
    }

    public bool $is_overdue {
        get => $this->deadline?->isPast() ?? false;
    }

    public ?string $salary_range_display {
        get {
            if ($this->salary_min && $this->salary_max) {
                return sprintf('%s %s–%s', $this->currency, $this->salary_min, $this->salary_max);
            }

            if ($this->salary_min) {
                return sprintf('%s %s', $this->currency, $this->salary_min);
            }

            if ($this->salary_max) {
                return sprintf('%s %s', $this->currency, $this->salary_max);
            }

            return null;
        }
    }

    #[Scope]
    public static function active(Builder $query): Builder
    {
        return $query->where('status', '!=', ApplicationStatus::Closed->value);
    }

    #[Scope]
    public static function byStatus(Builder $query, ApplicationStatus|string $status): Builder
    {
        return $query->where('status', $status instanceof ApplicationStatus ? $status->value : $status);
    }

    #[Scope]
    public static function appliedBetween(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('applied_at', [$from, $to]);
    }

    #[Scope]
    public static function withFullDetails(Builder $query): Builder
    {
        return $query->with(['company', 'cvVersion', 'interviews', 'statusHistories']);
    }

    #[Scope]
    public static function overdueDeadline(Builder $query): Builder
    {
        return $query->whereDate('deadline', '<', today())->where('status', '!=', ApplicationStatus::Closed->value);
    }
}
