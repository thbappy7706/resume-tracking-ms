<?php

namespace App\Http\Controllers;

use App\Http\Requests\InterviewStoreRequest;
use App\Http\Requests\InterviewUpdateRequest;
use App\Models\Interview;
use App\Models\JobApplication;
use Inertia\Inertia;

class InterviewController extends Controller
{
    public function store(JobApplication $application, InterviewStoreRequest $request)
    {
        $data = $request->validated();
        $data['job_application_id'] = $application->id;

        Interview::create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Interview added.')]);

        return back();
    }

    public function update(JobApplication $application, Interview $interview, InterviewUpdateRequest $request)
    {
        $interview->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Interview updated.')]);

        return back();
    }

    public function destroy(JobApplication $application, Interview $interview)
    {
        $interview->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Interview deleted.')]);

        return back();
    }
}
