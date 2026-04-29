<?php

namespace App\Http\Controllers;

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
            'filters' => $request->only(['search', 'industry', 'sort_by', 'sort_direction']),
        ]);
    }

    public function store(CompanyStoreRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        Company::create($data);

        return back()->with('success', 'Company created.');
    }

    public function update(Company $company, CompanyUpdateRequest $request)
    {
        $data = $request->validated();

        if (isset($data['name']) && ! isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $company->update($data);

        return back()->with('success', 'Company updated.');
    }
}
