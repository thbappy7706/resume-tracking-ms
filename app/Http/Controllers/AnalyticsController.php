<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationSource;
use App\Enums\ApplicationStatus;
use App\Http\Requests\AnalyticsIndexRequest;
use App\Models\Interview;
use App\Models\JobApplication;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    public function index(AnalyticsIndexRequest $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(90)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        return Inertia::render('analytics/index', [
            'date_range' => [
                'from' => $dateFrom,
                'to' => $dateTo,
            ],
            'metrics' => $this->getMetrics($dateFrom, $dateTo),
        ]);
    }

    protected function getMetrics(string $dateFrom, string $dateTo): array
    {
        return [
            'application_funnel' => $this->getApplicationFunnel($dateFrom, $dateTo),
            'applications_over_time' => $this->getApplicationsOverTime($dateFrom, $dateTo),
            'response_rate' => $this->getResponseRate($dateFrom, $dateTo),
            'avg_days_to_response' => $this->getAvgDaysToResponse($dateFrom, $dateTo),
            'top_companies_by_industry' => $this->getTopCompaniesByIndustry($dateFrom, $dateTo),
            'source_breakdown' => $this->getSourceBreakdown($dateFrom, $dateTo),
            'cv_performance' => $this->getCvPerformance($dateFrom, $dateTo),
            'interview_outcomes' => $this->getInterviewOutcomes($dateFrom, $dateTo),
            'excitement_vs_outcome' => $this->getExcitementVsOutcome($dateFrom, $dateTo),
            'weekly_activity' => $this->getWeeklyActivity($dateFrom, $dateTo),
        ];
    }

    protected function getApplicationFunnel(string $dateFrom, string $dateTo): array
    {
        $counts = JobApplication::whereBetween('applied_at', [$dateFrom, $dateTo])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $stages = [];
        foreach (ApplicationStatus::cases() as $status) {
            $stages[] = [
                'status' => $status->value,
                'label' => $status->name,
                'count' => $counts[$status->value] ?? 0,
            ];
        }

        return $stages;
    }

    protected function getApplicationsOverTime(string $dateFrom, string $dateTo): array
    {
        return JobApplication::whereBetween('applied_at', [$dateFrom, $dateTo])
            ->select(DB::raw('DATE(applied_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => $row->date,
                'count' => $row->count,
            ])
            ->toArray();
    }

    protected function getResponseRate(string $dateFrom, string $dateTo): float
    {
        $total = JobApplication::whereBetween('applied_at', [$dateFrom, $dateTo])->count();
        $responded = JobApplication::whereBetween('applied_at', [$dateFrom, $dateTo])
            ->whereNotNull('responded_at')
            ->count();

        return $total > 0 ? round(($responded / $total) * 100, 1) : 0;
    }

    protected function getAvgDaysToResponse(string $dateFrom, string $dateTo): ?float
    {
        $applications = JobApplication::whereBetween('applied_at', [$dateFrom, $dateTo])
            ->whereNotNull('responded_at')
            ->whereNotNull('applied_at')
            ->get(['applied_at', 'responded_at']);

        if ($applications->isEmpty()) {
            return null;
        }

        $totalDays = $applications->sum(function ($app) {
            return $app->applied_at->diffInDays($app->responded_at);
        });

        return round($totalDays / $applications->count(), 1);
    }

    protected function getTopCompaniesByIndustry(string $dateFrom, string $dateTo): array
    {
        return JobApplication::whereBetween('applied_at', [$dateFrom, $dateTo])
            ->join('companies', 'job_applications.company_id', '=', 'companies.id')
            ->select('companies.industry', DB::raw('count(*) as count'))
            ->whereNotNull('companies.industry')
            ->groupBy('companies.industry')
            ->orderByDesc('count')
            ->get()
            ->map(fn ($row) => [
                'industry' => $row->industry,
                'count' => $row->count,
            ])
            ->toArray();
    }

    protected function getSourceBreakdown(string $dateFrom, string $dateTo): array
    {
        $counts = JobApplication::whereBetween('applied_at', [$dateFrom, $dateTo])
            ->select('source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->pluck('count', 'source')
            ->toArray();

        $sources = [];
        foreach (ApplicationSource::cases() as $source) {
            $sources[] = [
                'source' => $source->value,
                'label' => $source->name,
                'count' => $counts[$source->value] ?? 0,
            ];
        }

        return $sources;
    }

    protected function getCvPerformance(string $dateFrom, string $dateTo): array
    {
        return DB::table('cv_versions')
            ->leftJoin('job_applications', function ($join) use ($dateFrom, $dateTo): void {
                $join->on('cv_versions.id', '=', 'job_applications.cv_version_id')
                    ->whereBetween('job_applications.applied_at', [$dateFrom, $dateTo])
                    ->whereNull('job_applications.deleted_at');
            })
            ->whereNull('cv_versions.deleted_at')
            ->select(
                'cv_versions.id',
                'cv_versions.name',
                DB::raw('count(distinct job_applications.id) as applications'),
                DB::raw('count(distinct job_applications.responded_at) as responses')
            )
            ->groupBy('cv_versions.id', 'cv_versions.name')
            ->orderByDesc('applications')
            ->get()
            ->map(function ($cv) {
                $cv->response_rate = $cv->applications > 0
                    ? round(($cv->responses / $cv->applications) * 100, 1)
                    : 0;

                return $cv;
            })
            ->toArray();
    }

    protected function getInterviewOutcomes(string $dateFrom, string $dateTo): array
    {
        return Interview::whereBetween('scheduled_at', [$dateFrom, $dateTo])
            ->select('outcome', DB::raw('count(*) as count'))
            ->groupBy('outcome')
            ->get()
            ->map(fn ($row) => [
                'outcome' => $row->outcome?->value,
                'label' => $row->outcome?->name ?? 'pending',
                'count' => $row->count,
            ])
            ->toArray();
    }

    protected function getExcitementVsOutcome(string $dateFrom, string $dateTo): array
    {
        return JobApplication::whereBetween('applied_at', [$dateFrom, $dateTo])
            ->whereNotNull('excitement_level')
            ->whereIn('status', [ApplicationStatus::Offer->value, ApplicationStatus::Rejected->value])
            ->select('status', DB::raw('AVG(excitement_level) as avg_excitement'))
            ->groupBy('status')
            ->get()
            ->map(fn ($row) => [
                'status' => $row->status,
                'avg_excitement' => round((float) $row->avg_excitement, 2),
            ])
            ->toArray();
    }

    protected function getWeeklyActivity(string $dateFrom, string $dateTo): array
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            $weekExpr = "TO_CHAR(applied_at, 'IYYY-IW')";
            $dayExpr = 'EXTRACT(DOW FROM applied_at) + 1';
        } elseif ($driver === 'sqlite') {
            $weekExpr = "strftime('%Y-%W', applied_at)";
            $dayExpr = "CAST(strftime('%w', applied_at) + 1 AS INTEGER)";
        } else {
            $weekExpr = 'YEARWEEK(applied_at, 1)';
            $dayExpr = 'DAYOFWEEK(applied_at)';
        }

        return JobApplication::whereBetween('applied_at', [$dateFrom, $dateTo])
            ->select(
                DB::raw("{$weekExpr} as week"),
                DB::raw("{$dayExpr} as day_of_week"),
                DB::raw('count(*) as count')
            )
            ->groupBy('week', 'day_of_week')
            ->orderBy('week')
            ->orderBy('day_of_week')
            ->get()
            ->map(fn ($row) => [
                'week' => $row->week,
                'day_of_week' => (int) $row->day_of_week,
                'count' => $row->count,
            ])
            ->toArray();
    }
}
