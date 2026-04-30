<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationSource;
use App\Enums\ApplicationStatus;
use App\Http\Requests\JobApplicationIndexRequest;
use App\Http\Requests\JobApplicationStoreRequest;
use App\Http\Requests\JobApplicationUpdateRequest;
use App\Http\Requests\JobApplicationUpdateStatusRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CvVersionResource;
use App\Http\Resources\JobApplicationResource;
use App\Models\Company;
use App\Models\CvVersion;
use App\Models\JobApplication;
use App\Models\StatusHistory;
use Inertia\Inertia;

class JobApplicationController extends Controller
{
    public function index(JobApplicationIndexRequest $request)
    {
        $query = JobApplication::query()
            ->with(['company', 'cvVersion', 'interviews'])
            ->withCount('interviews');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->filled('cv_version_id')) {
            $query->where('cv_version_id', $request->cv_version_id);
        }
        if ($request->filled('date_from')) {
            $query->where('applied_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('applied_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search): void {
                $q->where('role_title', 'like', "%{$search}%")
                    ->orWhereHas('company', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'applied_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        if ($sortBy === 'company.name') {
            $query->join('companies', 'job_applications.company_id', '=', 'companies.id')
                ->orderBy('companies.name', $sortDirection)
                ->select('job_applications.*');
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        $applications = $query->latest('applied_at')->get();

        $companies = Company::orderBy('name')->get(['id', 'name']);
        $cvVersions = CvVersion::orderBy('name')->get(['id', 'name']);

        return Inertia::render('applications/index', [
            'applications' => JobApplicationResource::collection($applications),
            'companies' => CompanyResource::collection($companies),
            'cv_versions' => CvVersionResource::collection($cvVersions),
            'filters' => $request->only(['status', 'source', 'company_id', 'cv_version_id', 'date_from', 'date_to', 'search', 'sort_by', 'sort_direction', 'view']),
            'status_options' => collect(ApplicationStatus::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->name]),
            'source_options' => collect(ApplicationSource::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->name]),
        ]);
    }

    public function store(JobApplicationStoreRequest $request)
    {
        $data = $request->validated();

        if (empty($data['applied_at'])) {
            $data['applied_at'] = now();
        }

        $application = JobApplication::create($data);

        // Create initial status history
        StatusHistory::create([
            'job_application_id' => $application->id,
            'from_status' => null,
            'to_status' => $application->status->value,
            'changed_at' => now(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Application added.')]);

        return back();
    }

    public function show(JobApplication $application)
    {
        $application->load(['company', 'cvVersion', 'interviews', 'statusHistories']);

        return Inertia::render('applications/show', [
            'application' => new JobApplicationResource($application),
        ]);
    }

    public function update(JobApplication $application, JobApplicationUpdateRequest $request)
    {
        $application->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Application updated.')]);

        return back();
    }

    public function destroy(JobApplication $application)
    {
        $application->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Application deleted.')]);

        return back();
    }

    public function updateStatus(JobApplication $application, JobApplicationUpdateStatusRequest $request)
    {
        $oldStatus = $application->status;
        $newStatus = $request->status;

        $application->update([
            'status' => $newStatus,
            'responded_at' => $application->responded_at ?? ($oldStatus !== $newStatus ? now() : null),
        ]);

        StatusHistory::create([
            'job_application_id' => $application->id,
            'from_status' => $oldStatus->value,
            'to_status' => $newStatus->value,
            'note' => $request->input('notes'),
            'changed_at' => now(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Application status updated.')]);

        return back();
    }
}
