<?php

namespace App\Http\Controllers;

use App\Http\Requests\CvSectionOverrideSyncRequest;
use App\Models\CvSectionOverride;
use App\Models\CvVersion;
use Illuminate\Support\Facades\DB;

class CvSectionOverrideController extends Controller
{
    public function sync(CvVersion $cv, CvSectionOverrideSyncRequest $request)
    {
        DB::transaction(function () use ($cv, $request): void {
            // Delete existing overrides for this CV version
            CvSectionOverride::where('cv_version_id', $cv->id)->delete();

            // Create new overrides
            foreach ($request->input('overrides') as $overrideData) {
                CvSectionOverride::create([
                    'cv_version_id' => $cv->id,
                    'profile_section_id' => $overrideData['profile_section_id'],
                    'is_included' => $overrideData['is_included'] ?? true,
                    'sort_order' => $overrideData['sort_order'] ?? 0,
                    'override_title' => $overrideData['override_title'] ?? null,
                    'override_description' => $overrideData['override_description'] ?? null,
                    'override_meta' => $overrideData['override_meta'] ?? null,
                ]);
            }
        });

        return back()->with('success', 'CV section overrides synced.');
    }
}
