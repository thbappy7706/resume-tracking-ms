import { Head, Link } from '@inertiajs/react';
import { Briefcase, TrendingUp, Calendar, Star, Clock, FileText } from 'lucide-react';
import { EmptyState } from '@/components/empty-state';
import { StarRating } from '@/components/star-rating';
import { StatusBadge } from '@/components/status-badge';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';

interface Summary {
    total_applications: number;
    active_pipeline: number;
    this_week_activity: number;
    response_rate: number;
}

interface FunnelStage {
    status: string;
    label: string;
    count: number;
}

interface RecentApplication {
    id: string;
    role_title: string;
    status: string;
    status_label: string;
    applied_at: string;
    company?: { name: string };
    cv_version?: { name: string };
}

interface UpcomingInterview {
    id: string;
    type: string;
    type_label: string;
    scheduled_at: string;
    job_application?: { role_title: string };
}

interface CvPerformance {
    id: string;
    name: string;
    applications: number;
    responses: number;
    response_rate: number;
}

interface DashboardProps {
    summary: Summary;
    funnel: FunnelStage[];
    recent_applications: RecentApplication[];
    upcoming_interviews: UpcomingInterview[];
    cv_performance: CvPerformance[];
}

const maxFunnelCount = (funnel: FunnelStage[]) => {
    return Math.max(...funnel.map((s) => s.count), 1);
};

export default function Dashboard({
    summary,
    funnel,
    recent_applications,
    upcoming_interviews,
    cv_performance,
}: DashboardProps) {
    const funnelMax = maxFunnelCount(funnel);

    return (
        <>
            <Head title="Dashboard" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div>
                    <h1 className="text-2xl font-bold">Dashboard</h1>
                    <p className="text-muted-foreground">
                        Overview of your job search progress
                    </p>
                </div>

                {/* Summary Cards */}
                <div className="grid gap-4 md:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Total Applications
                            </CardTitle>
                            <Briefcase className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {summary.total_applications}
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Active Pipeline
                            </CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {summary.active_pipeline}
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                This Week
                            </CardTitle>
                            <Calendar className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {summary.this_week_activity}
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Response Rate
                            </CardTitle>
                            <Star className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {summary.response_rate}%
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid gap-6 md:grid-cols-2">
                    {/* Application Funnel */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Application Funnel</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {funnel.length === 0 || funnel.every((s) => s.count === 0) ? (
                                <EmptyState
                                    title="No applications yet"
                                    description="Start tracking your job applications to see your funnel here"
                                />
                            ) : (
                                <div className="space-y-3">
                                    {funnel.map((stage) => (
                                        <div key={stage.status} className="space-y-1">
                                            <div className="flex items-center justify-between text-sm">
                                                <span className="font-medium">{stage.label}</span>
                                                <span className="text-muted-foreground">
                                                    {stage.count}
                                                </span>
                                            </div>
                                            <div className="h-2 w-full overflow-hidden rounded-full bg-muted">
                                                <div
                                                    className="h-full rounded-full bg-primary transition-all"
                                                    style={{
                                                        width: `${(stage.count / funnelMax) * 100}%`,
                                                    }}
                                                />
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Upcoming Interviews */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Upcoming Interviews</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {upcoming_interviews.length === 0 ? (
                                <EmptyState
                                    icon={Clock}
                                    title="No upcoming interviews"
                                    description="Interviews will appear here when scheduled"
                                />
                            ) : (
                                <div className="space-y-4">
                                    {upcoming_interviews.map((interview) => (
                                        <div
                                            key={interview.id}
                                            className="flex items-center justify-between rounded-lg border p-3"
                                        >
                                            <div>
                                                <p className="font-medium">
                                                    {interview.job_application?.role_title ?? 'Unknown Role'}
                                                </p>
                                                <p className="text-sm text-muted-foreground">
                                                    {interview.type_label} Interview
                                                </p>
                                            </div>
                                            <Badge variant="outline">
                                                {new Date(interview.scheduled_at).toLocaleDateString()}
                                            </Badge>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>

                <div className="grid gap-6 md:grid-cols-2">
                    {/* Recent Applications */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Recent Applications</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {recent_applications.length === 0 ? (
                                <EmptyState
                                    icon={FileText}
                                    title="No applications yet"
                                    description="Start adding job applications to track your progress"
                                    actionLabel="Add Application"
                                />
                            ) : (
                                <div className="space-y-3">
                                    {recent_applications.map((app) => (
                                        <div
                                            key={app.id}
                                            className="flex items-center justify-between rounded-lg border p-3"
                                        >
                                            <div>
                                                <p className="font-medium">{app.role_title}</p>
                                                <p className="text-sm text-muted-foreground">
                                                    {app.company?.name ?? 'Unknown Company'}
                                                </p>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <StatusBadge
                                                    status={app.status}
                                                    label={app.status_label}
                                                />
                                                <span className="text-xs text-muted-foreground">
                                                    {app.applied_at
                                                        ? new Date(app.applied_at).toLocaleDateString()
                                                        : ''}
                                                </span>
                                            </div>
                                        </div>
                                    ))}
                                    <Link
                                        href="/applications"
                                        className="mt-2 block text-center text-sm text-primary hover:underline"
                                    >
                                        View all applications →
                                    </Link>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* CV Performance */}
                    <Card>
                        <CardHeader>
                            <CardTitle>CV Performance</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {cv_performance.length === 0 ? (
                                <EmptyState
                                    icon={FileText}
                                    title="No CV data"
                                    description="Create CV versions and link them to applications to track performance"
                                />
                            ) : (
                                <div className="space-y-3">
                                    {cv_performance.map((cv) => (
                                        <div
                                            key={cv.id}
                                            className="flex items-center justify-between rounded-lg border p-3"
                                        >
                                            <div>
                                                <p className="font-medium">{cv.name}</p>
                                                <p className="text-sm text-muted-foreground">
                                                    {cv.applications} applications, {cv.responses} responses
                                                </p>
                                            </div>
                                            <div className="text-right">
                                                <p className="text-lg font-bold">{cv.response_rate}%</p>
                                                <p className="text-xs text-muted-foreground">
                                                    response rate
                                                </p>
                                            </div>
                                        </div>
                                    ))}
                                    <Link
                                        href="/cv-versions"
                                        className="mt-2 block text-center text-sm text-primary hover:underline"
                                    >
                                        Manage CVs →
                                    </Link>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
