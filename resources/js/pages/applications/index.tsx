import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetDescription } from '@/components/ui/sheet';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Textarea } from '@/components/ui/textarea';
import { StatusBadge } from '@/components/status-badge';
import { StarRating } from '@/components/star-rating';
import { EmptyState } from '@/components/empty-state';
import { Plus, ExternalLink, Loader2, LayoutGrid, List, Trash2 } from 'lucide-react';
import { useFlashToast } from '@/hooks/use-flash-toast';
import { ConfirmDialog } from '@/components/confirm-dialog';
import applicationsRoutes, { index as applicationsIndex, store as applicationsStore, destroy as applicationsDestroy } from '@/routes/applications';

interface Company {
    id: string;
    name: string;
    industry: string | null;
}

interface CvVersion {
    id: string;
    name: string;
}

interface Interview {
    id: string;
    type: string;
    type_label: string;
    scheduled_at: string;
}

interface JobApplication {
    id: string;
    role_title: string;
    job_url: string | null;
    source: string;
    source_label: string;
    status: string;
    status_label: string;
    applied_at: string | null;
    excitement_level: number | null;
    notes: string | null;
    company?: Company;
    cv_version?: CvVersion;
    interviews?: Interview[];
    interview_count?: number;
}

interface ApplicationsPageProps {
    applications: { data: JobApplication[] };
    companies: { data: Company[] };
    cv_versions: { data: CvVersion[] };
    filters: Record<string, string>;
    status_options: { value: string; label: string }[];
    source_options: { value: string; label: string }[];
}

const statusOrder = ['saved', 'applied', 'screening', 'interviewing', 'offer', 'accepted', 'rejected', 'closed'];

export default function ApplicationsIndex({
    applications,
    companies,
    cv_versions,
    filters,
    status_options,
    source_options,
}: ApplicationsPageProps) {
    useFlashToast();
    const [view, setView] = useState<'kanban' | 'table'>(filters.view === 'table' ? 'table' : 'kanban');
    const [isCreating, setIsCreating] = useState(false);
    const [selectedApp, setSelectedApp] = useState<JobApplication | null>(null);
    const [deletingApp, setDeletingApp] = useState<JobApplication | null>(null);

    const form = useForm({
        company_id: '',
        role_title: '',
        source: 'other',
        status: 'saved',
        job_url: '',
        excitement_level: 3,
        notes: '',
        applied_at: '',
        cv_version_id: '',
    });

    const filterForm = useForm({
        status: filters.status ?? '',
        source: filters.source ?? '',
        search: filters.search ?? '',
    });

    const handleCreate = () => {
        form.post(applicationsStore().url, {
            onSuccess: () => {
                setIsCreating(false);
                form.reset();
            },
        });
    };

    const handleDelete = () => {
        if (!deletingApp) return;
        form.delete(applicationsDestroy(deletingApp).url, {
            onSuccess: () => setDeletingApp(null),
        });
    };

    const handleStatusChange = (app: JobApplication, newStatus: string) => {
        router.patch(`/applications/${app.id}/status`, { status: newStatus });
    };

    const handleFilter = () => {
        router.get(applicationsIndex().url, {
            ...filterForm.data,
            view,
        }, { preserveState: true, preserveScroll: true });
    };

    const apps = applications?.data ?? [];
    const companyList = companies?.data ?? [];
    const cvList = cv_versions?.data ?? [];

    const groupedByStatus = statusOrder.map((status) => ({
        status,
        label: status_options.find((s) => s.value === status)?.label ?? status,
        applications: apps.filter((a) => a.status === status),
    }));

    return (
        <>
            <Head title="Applications" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">Applications</h1>
                        <p className="text-muted-foreground">
                            Track your job applications
                        </p>
                    </div>
                    <div className="flex items-center gap-2">
                        <div className="flex border rounded-md">
                            <Button
                                variant={view === 'kanban' ? 'default' : 'ghost'}
                                size="sm"
                                onClick={() => { setView('kanban'); handleFilter(); }}
                                className="rounded-r-none"
                            >
                                <LayoutGrid className="h-4 w-4" />
                            </Button>
                            <Button
                                variant={view === 'table' ? 'default' : 'ghost'}
                                size="sm"
                                onClick={() => { setView('table'); handleFilter(); }}
                                className="rounded-l-none"
                            >
                                <List className="h-4 w-4" />
                            </Button>
                        </div>
                        <Dialog open={isCreating} onOpenChange={setIsCreating}>
                            <DialogTrigger asChild>
                                <Button>
                                    <Plus className="mr-2 h-4 w-4" />
                                    Add Application
                                </Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogHeader>
                                    <DialogTitle>Add Application</DialogTitle>
                                    <DialogDescription>
                                        Track a new job application.
                                    </DialogDescription>
                                </DialogHeader>
                                <div className="grid gap-4">
                                    <div>
                                        <Label htmlFor="company">Company</Label>
                                        <Select
                                            value={form.data.company_id}
                                            onValueChange={(v: string) => form.setData('company_id', v)}
                                        >
                                            <SelectTrigger id="company">
                                                <SelectValue placeholder="Select company" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {companyList.map((c) => (
                                                    <SelectItem key={c.id} value={c.id}>{c.name}</SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div>
                                        <Label htmlFor="role">Role Title</Label>
                                        <Input
                                            id="role"
                                            value={form.data.role_title}
                                            onChange={(e: React.ChangeEvent<HTMLInputElement>) => form.setData('role_title', e.target.value)}
                                        />
                                    </div>
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <Label htmlFor="source">Source</Label>
                                            <Select
                                                value={form.data.source}
                                                onValueChange={(v: string) => form.setData('source', v)}
                                            >
                                                <SelectTrigger id="source">
                                                    <SelectValue />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {source_options.map((s) => (
                                                        <SelectItem key={s.value} value={s.value}>{s.label}</SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                        </div>
                                        <div>
                                            <Label htmlFor="status">Status</Label>
                                            <Select
                                                value={form.data.status}
                                                onValueChange={(v: string) => form.setData('status', v)}
                                            >
                                                <SelectTrigger id="status">
                                                    <SelectValue />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {status_options.map((s) => (
                                                        <SelectItem key={s.value} value={s.value}>{s.label}</SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                        </div>
                                    </div>
                                    <div>
                                        <Label htmlFor="excitement">Excitement</Label>
                                        <StarRating
                                            value={form.data.excitement_level}
                                            onChange={(v: number) => form.setData('excitement_level', v)}
                                            size="lg"
                                        />
                                    </div>
                                    <div>
                                        <Label htmlFor="notes">Notes</Label>
                                        <Textarea
                                            id="notes"
                                            value={form.data.notes}
                                            onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => form.setData('notes', e.target.value)}
                                        />
                                    </div>
                                </div>
                                <DialogFooter>
                                    <Button variant="outline" onClick={() => setIsCreating(false)}>Cancel</Button>
                                    <Button onClick={handleCreate} disabled={form.processing}>
                                        {form.processing && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                                        Add
                                    </Button>
                                </DialogFooter>
                            </DialogContent>
                        </Dialog>
                    </div>
                </div>

                {/* Filters */}
                <Card>
                    <CardContent className="pt-4">
                        <div className="flex gap-4">
                            <div className="flex-1">
                                <Input
                                    placeholder="Search..."
                                    value={filterForm.data.search}
                                    onChange={(e: React.ChangeEvent<HTMLInputElement>) => filterForm.setData('search', e.target.value)}
                                />
                            </div>
                            <Select
                                value={filterForm.data.status || 'all'}
                                onValueChange={(v: string) => filterForm.setData('status', v === 'all' ? '' : v)}
                            >
                                <SelectTrigger className="w-[180px]">
                                    <SelectValue placeholder="Status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All</SelectItem>
                                    {status_options.map((s) => (
                                        <SelectItem key={s.value} value={s.value}>{s.label}</SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <Button onClick={handleFilter}>Filter</Button>
                        </div>
                    </CardContent>
                </Card>

                {view === 'kanban' ? (
                    <div className="flex gap-4 overflow-x-auto pb-4">
                        {groupedByStatus.map((group) => (
                            <div key={group.status} className="min-w-[280px] flex-1">
                                <div className="mb-2 flex items-center justify-between">
                                    <StatusBadge status={group.status} label={group.label} />
                                    <Badge variant="outline">{group.applications.length}</Badge>
                                </div>
                                <div className="space-y-2">
                                    {group.applications.map((app) => (
                                        <Card
                                            key={app.id}
                                            className="cursor-pointer hover:shadow-md transition-shadow"
                                            onClick={() => setSelectedApp(app)}
                                        >
                                            <CardContent className="pt-4">
                                                <h3 className="font-medium">{app.role_title}</h3>
                                                <p className="text-sm text-muted-foreground">
                                                    {app.company?.name ?? 'Unknown'}
                                                </p>
                                                <div className="mt-2 flex items-center justify-between">
                                                    {app.excitement_level && (
                                                        <StarRating value={app.excitement_level} readonly size="sm" />
                                                    )}
                                                    <span className="text-xs text-muted-foreground">
                                                        {app.applied_at ? new Date(app.applied_at).toLocaleDateString() : ''}
                                                    </span>
                                                </div>
                                            </CardContent>
                                        </Card>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <Card>
                        <CardContent className="p-0">
                            <table className="w-full">
                                <thead>
                                    <tr className="border-b">
                                        <th className="text-left p-4 font-medium">Role</th>
                                        <th className="text-left p-4 font-medium">Company</th>
                                        <th className="text-left p-4 font-medium">Status</th>
                                        <th className="text-left p-4 font-medium">Source</th>
                                        <th className="text-left p-4 font-medium">Applied</th>
                                        <th className="text-left p-4 font-medium">Excitement</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {apps.map((app) => (
                                        <tr
                                            key={app.id}
                                            className="border-b cursor-pointer hover:bg-muted/50"
                                            onClick={() => setSelectedApp(app)}
                                        >
                                            <td className="p-4 font-medium">{app.role_title}</td>
                                            <td className="p-4">{app.company?.name ?? '-'}</td>
                                            <td className="p-4">
                                                <StatusBadge status={app.status} label={app.status_label} />
                                            </td>
                                            <td className="p-4 text-sm">{app.source_label}</td>
                                            <td className="p-4 text-sm">
                                                {app.applied_at ? new Date(app.applied_at).toLocaleDateString() : '-'}
                                            </td>
                                            <td className="p-4">
                                                {app.excitement_level && (
                                                    <StarRating value={app.excitement_level} readonly size="sm" />
                                                )}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </CardContent>
                    </Card>
                )}

                {/* Application Detail Sheet */}
                <Sheet open={!!selectedApp} onOpenChange={(open: boolean) => !open && setSelectedApp(null)}>
                    <SheetContent className="w-full sm:max-w-xl">
                        {selectedApp && (
                            <>
                                <SheetHeader>
                                    <SheetTitle>{selectedApp.role_title}</SheetTitle>
                                    <SheetDescription>
                                        {selectedApp.company?.name ?? 'Unknown Company'}
                                    </SheetDescription>
                                </SheetHeader>
                                <Tabs defaultValue="overview" className="mt-6">
                                    <TabsList>
                                        <TabsTrigger value="overview">Overview</TabsTrigger>
                                        <TabsTrigger value="interviews">Interviews</TabsTrigger>
                                        <TabsTrigger value="notes">Notes</TabsTrigger>
                                    </TabsList>
                                    <TabsContent value="overview" className="space-y-4 mt-4">
                                        <div className="grid gap-4">
                                            <div>
                                                <Label>Status</Label>
                                                <Select
                                                    value={selectedApp.status}
                                                    onValueChange={(v: string) => handleStatusChange(selectedApp, v)}
                                                >
                                                    <SelectTrigger>
                                                        <SelectValue />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {status_options.map((s) => (
                                                            <SelectItem key={s.value} value={s.value}>{s.label}</SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                            <div>
                                                <Label>Excitement</Label>
                                                <StarRating value={selectedApp.excitement_level ?? 0} readonly size="lg" />
                                            </div>
                                            {selectedApp.job_url && (
                                                <div>
                                                    <Label>Job URL</Label>
                                                    <div className="flex items-center gap-2 mt-1">
                                                        <a href={selectedApp.job_url} target="_blank" rel="noopener noreferrer" className="text-sm text-primary hover:underline flex items-center gap-1">
                                                            {selectedApp.job_url}
                                                            <ExternalLink className="h-3 w-3" />
                                                        </a>
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    </TabsContent>
                                    <TabsContent value="interviews" className="mt-4">
                                        {selectedApp.interviews && selectedApp.interviews.length > 0 ? (
                                            <div className="space-y-3">
                                                {selectedApp.interviews.map((interview) => (
                                                    <Card key={interview.id}>
                                                        <CardContent className="pt-4">
                                                            <div className="flex items-center justify-between">
                                                                <div>
                                                                    <p className="font-medium">{interview.type_label} Interview</p>
                                                                    <p className="text-sm text-muted-foreground">
                                                                        {new Date(interview.scheduled_at).toLocaleString()}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </CardContent>
                                                    </Card>
                                                ))}
                                            </div>
                                        ) : (
                                            <EmptyState title="No interviews" description="Interviews will appear here" />
                                        )}
                                    </TabsContent>
                                    <TabsContent value="notes" className="mt-4">
                                        {selectedApp.notes ? (
                                            <p className="text-sm whitespace-pre-wrap">{selectedApp.notes}</p>
                                        ) : (
                                            <EmptyState title="No notes" description="Add notes to this application" />
                                        )}
                                    </TabsContent>
                                </Tabs>
                                <div className="mt-6 pt-6 border-t">
                                    <Button
                                        variant="destructive"
                                        className="w-full"
                                        onClick={() => setDeletingApp(selectedApp)}
                                    >
                                        <Trash2 className="mr-2 h-4 w-4" />
                                        Delete Application
                                    </Button>
                                </div>
                            </>
                        )}
                    </SheetContent>
                </Sheet>

                <ConfirmDialog
                    open={!!deletingApp}
                    onOpenChange={(open) => !open && setDeletingApp(null)}
                    title="Delete Application"
                    description="Are you sure you want to delete this application? This action cannot be undone."
                    onConfirm={handleDelete}
                    loading={form.processing}
                />
            </div>
        </>
    );
}

ApplicationsIndex.layout = {
    breadcrumbs: [
        { title: 'Applications', href: applicationsIndex().url },
    ],
};