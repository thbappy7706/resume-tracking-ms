import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { EmptyState } from '@/components/empty-state';
import { BarChart, Bar, LineChart, Line, PieChart, Pie, Cell, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend } from 'recharts';
import { TrendingUp, Briefcase, Clock, Award, Calendar } from 'lucide-react';
import { index as analyticsIndex } from '@/routes/analytics';

interface MetricData {
    application_funnel: { status: string; label: string; count: number }[];
    applications_over_time: { date: string; count: number }[];
    response_rate: number;
    avg_days_to_response: number | null;
    top_companies_by_industry: { industry: string; count: number }[];
    source_breakdown: { source: string; label: string; count: number }[];
    cv_performance: { id: string; name: string; applications: number; responses: number; response_rate: number }[];
    interview_outcomes: { outcome: string; label: string; count: number }[];
    excitement_vs_outcome: { status: string; avg_excitement: number }[];
    weekly_activity: { week: string; day_of_week: number; count: number }[];
}

interface AnalyticsPageProps {
    metrics: MetricData;
    date_range: { from: string; to: string };
}

const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8', '#82CA9D'];

export default function AnalyticsIndex({ metrics, date_range }: AnalyticsPageProps) {
    const [dateFrom, setDateFrom] = useState(date_range.from);
    const [dateTo, setDateTo] = useState(date_range.to);

    const handleFilter = () => {
        router.get('/analytics', { date_from: dateFrom, date_to: dateTo }, { preserveState: true });
    };

    const totalApplications = metrics.application_funnel.reduce((sum, s) => sum + s.count, 0);
    const offersReceived = metrics.application_funnel.find((s) => s.status === 'offer')?.count ?? 0;

    return (
        <>
            <Head title="Analytics" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">Analytics</h1>
                        <p className="text-muted-foreground">
                            Insights into your job search performance
                        </p>
                    </div>
                    <div className="flex items-center gap-4">
                        <div className="flex items-center gap-2">
                            <Label htmlFor="date-from">From</Label>
                            <Input
                                id="date-from"
                                type="date"
                                value={dateFrom}
                                onChange={(e) => setDateFrom(e.target.value)}
                                className="w-[160px]"
                            />
                        </div>
                        <div className="flex items-center gap-2">
                            <Label htmlFor="date-to">To</Label>
                            <Input
                                id="date-to"
                                type="date"
                                value={dateTo}
                                onChange={(e) => setDateTo(e.target.value)}
                                className="w-[160px]"
                            />
                        </div>
                        <Button onClick={handleFilter}>Apply</Button>
                    </div>
                </div>

                {/* Metric Cards */}
                <div className="grid gap-4 md:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Applications</CardTitle>
                            <Briefcase className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{totalApplications}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Response Rate</CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{metrics.response_rate}%</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Avg Days to Response</CardTitle>
                            <Clock className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {metrics.avg_days_to_response ?? '-'}
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Offers Received</CardTitle>
                            <Award className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{offersReceived}</div>
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
                            {metrics.application_funnel.length === 0 ? (
                                <EmptyState title="No data" description="Application funnel will appear here" />
                            ) : (
                                <ResponsiveContainer width="100%" height={300}>
                                    <BarChart data={metrics.application_funnel}>
                                        <CartesianGrid strokeDasharray="3 3" />
                                        <XAxis dataKey="label" />
                                        <YAxis />
                                        <Tooltip />
                                        <Bar dataKey="count" fill="#0088FE" />
                                    </BarChart>
                                </ResponsiveContainer>
                            )}
                        </CardContent>
                    </Card>

                    {/* Applications Over Time */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Applications Over Time</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {metrics.applications_over_time.length === 0 ? (
                                <EmptyState title="No data" description="Applications over time will appear here" />
                            ) : (
                                <ResponsiveContainer width="100%" height={300}>
                                    <LineChart data={metrics.applications_over_time}>
                                        <CartesianGrid strokeDasharray="3 3" />
                                        <XAxis dataKey="date" />
                                        <YAxis />
                                        <Tooltip />
                                        <Line type="monotone" dataKey="count" stroke="#00C49F" strokeWidth={2} />
                                    </LineChart>
                                </ResponsiveContainer>
                            )}
                        </CardContent>
                    </Card>

                    {/* Source Breakdown */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Source Breakdown</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {metrics.source_breakdown.length === 0 ? (
                                <EmptyState title="No data" description="Source breakdown will appear here" />
                            ) : (
                                <ResponsiveContainer width="100%" height={300}>
                                    <PieChart>
                                        <Pie
                                            data={metrics.source_breakdown}
                                            dataKey="count"
                                            nameKey="label"
                                            cx="50%"
                                            cy="50%"
                                            outerRadius={100}
                                            label
                                        >
                                            {metrics.source_breakdown.map((_, index) => (
                                                <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                                            ))}
                                        </Pie>
                                        <Tooltip />
                                        <Legend />
                                    </PieChart>
                                </ResponsiveContainer>
                            )}
                        </CardContent>
                    </Card>

                    {/* Interview Outcomes */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Interview Outcomes</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {metrics.interview_outcomes.length === 0 ? (
                                <EmptyState title="No data" description="Interview outcomes will appear here" />
                            ) : (
                                <ResponsiveContainer width="100%" height={300}>
                                    <BarChart data={metrics.interview_outcomes}>
                                        <CartesianGrid strokeDasharray="3 3" />
                                        <XAxis dataKey="label" />
                                        <YAxis />
                                        <Tooltip />
                                        <Bar dataKey="count" fill="#FFBB28" />
                                    </BarChart>
                                </ResponsiveContainer>
                            )}
                        </CardContent>
                    </Card>

                    {/* Industry Breakdown */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Applications by Industry</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {metrics.top_companies_by_industry.length === 0 ? (
                                <EmptyState title="No data" description="Industry breakdown will appear here" />
                            ) : (
                                <ResponsiveContainer width="100%" height={300}>
                                    <BarChart data={metrics.top_companies_by_industry} layout="vertical">
                                        <CartesianGrid strokeDasharray="3 3" />
                                        <XAxis type="number" />
                                        <YAxis type="category" dataKey="industry" width={120} />
                                        <Tooltip />
                                        <Bar dataKey="count" fill="#8884D8" />
                                    </BarChart>
                                </ResponsiveContainer>
                            )}
                        </CardContent>
                    </Card>

                    {/* CV Performance Table */}
                    <Card>
                        <CardHeader>
                            <CardTitle>CV Performance</CardTitle>
                            <CardDescription>Response rate by CV version</CardDescription>
                        </CardHeader>
                        <CardContent>
                            {metrics.cv_performance.length === 0 ? (
                                <EmptyState title="No data" description="CV performance will appear here" />
                            ) : (
                                <table className="w-full">
                                    <thead>
                                        <tr className="border-b">
                                            <th className="text-left p-2 font-medium">CV Name</th>
                                            <th className="text-center p-2 font-medium">Applications</th>
                                            <th className="text-center p-2 font-medium">Responses</th>
                                            <th className="text-center p-2 font-medium">Response Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {metrics.cv_performance.map((cv) => (
                                            <tr key={cv.id} className="border-b">
                                                <td className="p-2">{cv.name}</td>
                                                <td className="text-center p-2">{cv.applications}</td>
                                                <td className="text-center p-2">{cv.responses}</td>
                                                <td className="text-center p-2">
                                                    <Badge variant={cv.response_rate > 50 ? 'default' : 'secondary'}>
                                                        {cv.response_rate}%
                                                    </Badge>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

AnalyticsIndex.layout = {
    breadcrumbs: [
        { title: 'Analytics', href: analyticsIndex() },
    ],
};
