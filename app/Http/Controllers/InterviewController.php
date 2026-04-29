<?php

namespace App\Http\Controllers;

use App\Http\Requests\InterviewStoreRequest;
use App\Http\Requests\InterviewUpdateRequest;
use App\Models\Interview;
use App\Models\JobApplication;

class InterviewController extends Controller
{
    public function store(JobApplication $application, InterviewStoreRequest $request)
    {
        $data = $request->validated();
        $data['job_application_id'] = $application->id;

        Interview::create($data);

        return back()->with('success', 'Interview added.');
    }

    public function update(JobApplication $application, Interview $interview, InterviewUpdateRequest $request)
    {
        $interview->update($request->validated());

        return back()->with('success', 'Interview updated.');
    }

    public function destroy(JobApplication $application, Interview $interview)
    {
        $interview->delete();

        return back()->with('success', 'Interview deleted.');
    }
}
