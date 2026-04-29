<?php

namespace App\Models;

use App\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\AsEncryptedArrayObject;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['cv_version_id', 'profile_section_id', 'is_included', 'sort_order', 'override_title', 'override_description', 'override_meta'])]
#[ObservedBy(ModelObserver::class)]
class CvSectionOverride extends Model
{
    use HasFactory, HasUlids;

    protected function casts(): array
    {
        return [
            'cv_version_id' => 'string',
            'profile_section_id' => 'string',
            'is_included' => 'boolean',
            'sort_order' => 'integer',
            'override_meta' => AsEncryptedArrayObject::class,
        ];
    }

    public function cvVersion(): BelongsTo
    {
        return $this->belongsTo(CvVersion::class);
    }

    public function profileSection(): BelongsTo
    {
        return $this->belongsTo(ProfileSection::class);
    }
}
