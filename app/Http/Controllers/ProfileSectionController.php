<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileSectionReorderRequest;
use App\Http\Requests\ProfileSectionStoreRequest;
use App\Http\Requests\ProfileSectionUpdateRequest;
use App\Models\ProfileSection;
use Illuminate\Support\Facades\DB;

class ProfileSectionController extends Controller
{
    public function store(ProfileSectionStoreRequest $request)
    {
        $data = $request->validated();

        $maxSortOrder = ProfileSection::where('type', $data['type'])->max('sort_order') ?? 0;
        $data['sort_order'] = $data['sort_order'] ?? ($maxSortOrder + 1);

        $section = ProfileSection::create($data);

        if ($request->has('tags')) {
            $section->tags()->sync($request->input('tags'));
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Profile section created.')]);

        return back();
    }

    public function update(ProfileSection $section, ProfileSectionUpdateRequest $request)
    {
        $data = $request->validated();
        $section->update($data);

        if ($request->has('tags')) {
            $section->tags()->sync($request->input('tags'));
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Profile section updated.')]);

        return back();
    }

    public function destroy(ProfileSection $section)
    {
        $section->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Profile section deleted.')]);

        return back();
    }

    public function reorder(ProfileSectionReorderRequest $request)
    {
        DB::transaction(function () use ($request): void {
            foreach ($request->input('sections') as $item) {
                ProfileSection::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Sections reordered.')]);

        return back();
    }
}
