<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Models\Interview;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Summary cards
        $totalApplications = JobApplication::count();
        $activePipeline = JobApplication::where('status', '!=', ApplicationStatus::Closed->value)->count();
        $thisWeekActivity = JobApplication::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $appliedCount = JobApplication::where('status', ApplicationStatus::Applied->value)->count();
        $respondedCount = JobApplication::whereNotNull('responded_at')->count();
        $responseRate = $appliedCount > 0 ? round(($respondedCount / $appliedCount) * 100, 1) : 0;

        // Application funnel
        $funnel = JobApplication::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $funnelStages = [];
        foreach (ApplicationStatus::cases() as $status) {
            $funnelStages[] = [
                'status' => $status->value,
                'label' => $status->name,
                'count' => $funnel[$status->value] ?? 0,
            ];
        }

        // Recent applications
        $recentApplications = JobApplication::with(['company', 'cvVersion'])
            ->latest('applied_at')
            ->limit(10)
            ->get();

        // Upcoming interviews
        $upcomingInterviews = Interview::where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(3)
            ->get();

        // CV performance
        $cvPerformance = DB::table('cv_versions')
            ->leftJoin('job_applications', 'cv_versions.id', '=', 'job_applications.cv_version_id')
            ->whereNull('cv_versions.deleted_at')
            ->select(
                'cv_versions.id',
                'cv_versions.name',
                DB::raw('count(distinct job_applications.id) as applications'),
                DB::raw('count(distinct job_applications.responded_at) as responses')
            )
            ->groupBy('cv_versions.id', 'cv_versions.name')
            ->orderByDesc('applications')
            ->limit(3)
            ->get()
            ->map(function ($cv) {
                $cv->response_rate = $cv->applications > 0
                    ? round(($cv->responses / $cv->applications) * 100, 1)
                    : 0;

                return $cv;
            });

        return Inertia::render('dashboard', [
            'summary' => [
                'total_applications' => $totalApplications,
                'active_pipeline' => $activePipeline,
                'this_week_activity' => $thisWeekActivity,
                'response_rate' => $responseRate,
            ],
            'funnel' => $funnelStages,
            'recent_applications' => $recentApplications,
            'upcoming_interviews' => $upcomingInterviews,
            'cv_performance' => $cvPerformance,
        ]);
    }
}
