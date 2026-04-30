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

        Inertia::flash('toast', ['type' => 'success', 'message' => __('CV version created.')]);

        return back();
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

        Inertia::flash('toast', ['type' => 'success', 'message' => __('CV version updated.')]);

        return back();
    }

    public function destroy(CvVersion $cv)
    {
        $cv->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('CV version deleted.')]);

        return back();
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

        Inertia::flash('toast', ['type' => 'success', 'message' => __('CV version duplicated.')]);

        return back();
    }

    public function exportPdf(CvVersion $cv, CvVersionExportPdfRequest $request)
    {
        // TODO: Implement PDF export
        return back()->with('info', 'PDF export coming soon.');
    }

    public function preview(CvVersion $cv)
    {
        $cv->load('resolvedSections');

        return view('cv-templates.preview', [
            'cvVersion' => $cv,
        ]);
    }
}
