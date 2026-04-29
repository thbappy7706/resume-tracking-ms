<?php

namespace App\Http\Controllers;

use App\Http\Requests\CvTemplateStoreRequest;
use App\Http\Requests\CvTemplateUpdateRequest;
use App\Http\Resources\CvTemplateResource;
use App\Models\CvTemplate;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CvTemplateController extends Controller
{
    public function index()
    {
        $templates = CvTemplate::withCount('cvVersions')->get();

        return Inertia::render('templates/index', [
            'templates' => CvTemplateResource::collection($templates),
        ]);
    }

    public function store(CvTemplateStoreRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $template = CvTemplate::create($data);

        return back()->with('success', 'Template created.');
    }

    public function update(CvTemplate $template, CvTemplateUpdateRequest $request)
    {
        $data = $request->validated();

        if (isset($data['name']) && ! isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $template->update($data);

        return back()->with('success', 'Template updated.');
    }
}
