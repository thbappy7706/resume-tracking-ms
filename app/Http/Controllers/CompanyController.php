<?php

namespace App\Http\Controllers;

use App\Enums\CompanySize;
use App\Http\Requests\CompanyIndexRequest;
use App\Http\Requests\CompanyStoreRequest;
use App\Http\Requests\CompanyUpdateRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index(CompanyIndexRequest $request)
    {
        $query = Company::query()->withCount('jobApplications');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->filled('industry')) {
            $query->where('industry', $request->industry);
        }

        $sortBy = $request->input('sort_by', 'name');
        $sortDirection = $request->input('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $companies = $query->paginate(20);

        $industries = Company::whereNotNull('industry')
            ->distinct()
            ->pluck('industry')
            ->sort()
            ->values();

        return Inertia::render('companies/index', [
            'companies' => CompanyResource::collection($companies),
            'industries' => $industries,
            'size_options' => collect(CompanySize::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->name]),
            'filters' => $request->only(['search', 'industry', 'sort_by', 'sort_direction']),
        ]);
    }

    public function create()
    {
        return Inertia::render('companies/create', [
            'size_options' => collect(CompanySize::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->name]),
        ]);
    }

    public function edit(Company $company)
    {
        return Inertia::render('companies/edit', [
            'company' => new CompanyResource($company),
            'size_options' => collect(CompanySize::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->name]),
        ]);
    }

    public function show(Company $company)
    {
        return Inertia::render('companies/show', [
            'company' => new CompanyResource($company->loadCount('jobApplications')),
        ]);
    }

    public function store(CompanyStoreRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        Company::create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Company created.')]);

        return back();
    }

    public function update(Company $company, CompanyUpdateRequest $request)
    {
        $data = $request->validated();

        if (isset($data['name']) && ! isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $company->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Company updated.')]);

        return back();
    }

    public function destroy(Company $company)
    {
        $company->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Company deleted.')]);

        return back();
    }
}
