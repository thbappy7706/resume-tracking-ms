<?php

namespace App\Http\Controllers;

use App\Http\Requests\CvVersionDuplicateRequest;
use App\Http\Requests\CvVersionExportPdfRequest;
use App\Http\Requests\CvVersionStoreRequest;
use App\Http\Requests\CvVersionUpdateRequest;
use App\Http\Resources\CvVersionResource;
use App\Models\CvVersion;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CvVersionController extends Controller
{
    public function index()
    {
        $cvVersions = CvVersion::with('cvTemplate')
            ->withCount('jobApplications')
            ->latest()
            ->get();

        return Inertia::render('cv-versions/index', [
            'cv_versions' => CvVersionResource::collection($cvVersions),
        ]);
    }

    public function store(CvVersionStoreRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']).'-'.Str::ulid();

        $cvVersion = CvVersion::create($data);

        return back()->with('success', 'CV version created.');
    }

    public function show(CvVersion $cv)
    {
        $cv->load(['cvTemplate', 'sectionOverrides.profileSection', 'resolvedSections.tags']);

        return Inertia::render('cv-versions/show', [
            'cv_version' => new CvVersionResource($cv),
        ]);
    }

    public function update(CvVersion $cv, CvVersionUpdateRequest $request)
    {
        $data = $request->validated();

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']).'-'.Str::ulid();
        }

        $cv->update($data);

        return back()->with('success', 'CV version updated.');
    }

    public function destroy(CvVersion $cv)
    {
        $cv->delete();

        return back()->with('success', 'CV version deleted.');
    }

    public function duplicate(CvVersion $cv, CvVersionDuplicateRequest $request)
    {
        $newCv = $cv->replicate();
        $newCv->name = $request->input('name');
        $newCv->slug = Str::slug($request->input('name')).'-'.Str::ulid();
        $newCv->is_base = false;
        $newCv->save();

        // Duplicate section overrides
        $cv->sectionOverrides()->each(function ($override) use ($newCv): void {
            $newOverride = $override->replicate();
            $newOverride->cv_version_id = $newCv->id;
            $newOverride->save();
        });

        return back()->with('success', 'CV version duplicated.');
    }

    public function exportPdf(CvVersion $cv, CvVersionExportPdfRequest $request)
    {
        // TODO: Implement PDF export
        return back()->with('info', 'PDF export coming soon.');
    }
}
