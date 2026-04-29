<?php

namespace App\Models;

use App\Enums\SectionType;
use App\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsEncryptedArrayObject;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['type', 'title', 'organization', 'location', 'start_date', 'end_date', 'is_current', 'description', 'meta', 'sort_order', 'is_visible'])]
#[ObservedBy(ModelObserver::class)]
class ProfileSection extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'type' => SectionType::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
            'meta' => AsEncryptedArrayObject::class,
            'sort_order' => 'integer',
            'is_visible' => 'boolean',
        ];
    }

    public string $date_range {
        get => $this->start_date
            ? $this->start_date->format('M Y').' – '.($this->is_current ? 'Present' : ($this->end_date?->format('M Y') ?? ''))
            : '';
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function overrides(): HasMany
    {
        return $this->hasMany(CvSectionOverride::class);
    }

    #[Scope]
    public static function byType(Builder $query, SectionType|string $type): Builder
    {
        return $query->where('type', $type instanceof SectionType ? $type->value : $type);
    }

    #[Scope]
    public static function visible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    #[Scope]
    public static function ordered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}
