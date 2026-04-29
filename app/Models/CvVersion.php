<?php

namespace App\Models;

use App\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

#[Fillable(['cv_template_id', 'name', 'slug', 'target_role', 'target_industry', 'notes', 'is_base', 'last_exported_at', 'export_count'])]
#[ObservedBy(ModelObserver::class)]
class CvVersion extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'cv_template_id' => 'string',
            'is_base' => 'boolean',
            'last_exported_at' => 'datetime',
            'export_count' => 'integer',
        ];
    }

    public function cvTemplate(): BelongsTo
    {
        return $this->belongsTo(CvTemplate::class);
    }

    public function sectionOverrides(): HasMany
    {
        return $this->hasMany(CvSectionOverride::class);
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function resolvedSections(): Collection
    {
        return ProfileSection::query()
            ->select('profile_sections.*', 'cv_section_overrides.is_included as override_is_included', 'cv_section_overrides.sort_order as override_sort_order', 'cv_section_overrides.override_title', 'cv_section_overrides.override_description', 'cv_section_overrides.override_meta')
            ->leftJoin('cv_section_overrides', function (JoinClause $join): void {
                $join->on('profile_sections.id', '=', 'cv_section_overrides.profile_section_id')
                    ->where('cv_section_overrides.cv_version_id', $this->id);
            })
            ->orderByRaw('coalesce(cv_section_overrides.sort_order, profile_sections.sort_order) asc')
            ->get()
            ->map(function (ProfileSection $section): ProfileSection {
                $section->forceFill([
                    'title' => $section->override_title ?? $section->title,
                    'description' => $section->override_description ?? $section->description,
                    'meta' => $section->override_meta ?? $section->meta,
                ]);

                return $section;
            })
            ->filter(fn (ProfileSection $section): bool => $section->override_is_included ?? true)
            ->values();
    }

    public int $application_count {
        get => $this->job_applications_count ?? 0;
    }

    public string $preview_url {
        get => route('cv-versions.preview', $this);
    }
}
