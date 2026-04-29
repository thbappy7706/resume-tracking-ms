<?php

namespace App\Models;

use App\Enums\CvLayout;
use App\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'layout', 'config', 'thumbnail_path', 'is_default'])]
#[ObservedBy(ModelObserver::class)]
class CvTemplate extends Model
{
    use HasFactory, HasUlids;

    protected function casts(): array
    {
        return [
            'layout' => CvLayout::class,
            'config' => 'array',
            'is_default' => 'boolean',
        ];
    }

    public function cvVersions(): HasMany
    {
        return $this->hasMany(CvVersion::class);
    }
}
