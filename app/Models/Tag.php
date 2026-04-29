<?php

namespace App\Models;

use App\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug', 'color', 'type'])]
#[ObservedBy(ModelObserver::class)]
class Tag extends Model
{
    use HasFactory, HasUlids;

    protected function casts(): array
    {
        return [];
    }

    public function profileSections(): BelongsToMany
    {
        return $this->belongsToMany(ProfileSection::class);
    }
}
