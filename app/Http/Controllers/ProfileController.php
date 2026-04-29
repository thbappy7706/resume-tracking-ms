<?php

namespace App\Http\Controllers;

use App\Enums\SectionType;
use App\Http\Resources\ProfileSectionResource;
use App\Http\Resources\TagResource;
use App\Models\ProfileSection;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $sections = ProfileSection::with('tags')
            ->ordered()
            ->get()
            ->groupBy('type');

        $sectionGroups = [];
        foreach (SectionType::cases() as $type) {
            $sectionGroups[$type->value] = ProfileSectionResource::collection(
                $sections->get($type->value, collect())
            );
        }

        return Inertia::render('profile', [
            'sections' => $sectionGroups,
            'tags' => TagResource::collection(Tag::all()),
            'section_types' => collect(SectionType::cases())->map(fn ($type) => [
                'value' => $type->value,
                'label' => $type->name,
            ]),
        ]);
    }
}
