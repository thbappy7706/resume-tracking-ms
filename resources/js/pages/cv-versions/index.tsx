import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import { Badge } from '@/components/ui/badge';
import { EmptyState } from '@/components/empty-state';
import { Plus, FileText, Copy, Trash2, Eye, Loader2 } from 'lucide-react';
import { useFlashToast } from '@/hooks/use-flash-toast';
import { ConfirmDialog } from '@/components/confirm-dialog';
import { index as cvVersionsIndex, store as cvVersionsStore, destroy as cvVersionsDestroy } from '@/routes/cv-versions';

interface CvTemplate {
    id: string;
    name: string;
    slug: string;
}

interface CvVersion {
    id: string;
    name: string;
    slug: string;
    target_role: string | null;
    target_industry: string | null;
    export_count: number;
    last_exported_at: string | null;
    template: CvTemplate;
    application_count: number;
}

interface CvVersionsPageProps {
    cv_versions: { data: CvVersion[] };
}

export default function CvVersionsIndex({ cv_versions }: CvVersionsPageProps) {
    useFlashToast();
    const [isCreating, setIsCreating] = useState(false);
    const [duplicateName, setDuplicateName] = useState('');
    const [duplicatingId, setDuplicatingId] = useState<string | null>(null);
    const [deletingCv, setDeletingCv] = useState<CvVersion | null>(null);

    const form = useForm({
        name: '',
        cv_template_id: '',
        target_role: '',
        target_industry: '',
    });

    const duplicateForm = useForm({ name: '' });

    const handleCreate = () => {
        form.post(cvVersionsStore().url, {
            onSuccess: () => {
                setIsCreating(false);
                form.reset();
            },
        });
    };

    const handleDuplicate = (cv: CvVersion) => {
        duplicateForm.post(`/cv-versions/${cv.id}/duplicate`, {
            onStart: () => setDuplicatingId(cv.id),
            onFinish: () => setDuplicatingId(null),
        });
    };

    const handleDelete = () => {
        if (!deletingCv) return;
        form.delete(cvVersionsDestroy(deletingCv).url, {
            onSuccess: () => setDeletingCv(null),
        });
    };

    const versions = cv_versions?.data ?? [];

    return (
        <>
            <Head title="My CVs" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">My CVs</h1>
                        <p className="text-muted-foreground">
                            Manage your CV versions and templates
                        </p>
                    </div>
                    <Dialog open={isCreating} onOpenChange={setIsCreating}>
                        <DialogTrigger asChild>
                            <Button>
                                <Plus className="mr-2 h-4 w-4" />
                                Create CV
                            </Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Create New CV</DialogTitle>
                                <DialogDescription>
                                    Create a new CV version with a template and target role.
                                </DialogDescription>
                            </DialogHeader>
                            <div className="grid gap-4">
                                <div>
                                    <Label htmlFor="name">Name</Label>
                                    <Input
                                        id="name"
                                        value={form.data.name}
                                        onChange={(e) => form.setData('name', e.target.value)}
                                        placeholder="e.g., Senior Developer CV"
                                    />
                                </div>
                                <div>
                                    <Label htmlFor="template">Template</Label>
                                    <Select
                                        value={form.data.cv_template_id}
                                        onValueChange={(v) => form.setData('cv_template_id', v)}
                                    >
                                        <SelectTrigger id="template">
                                            <SelectValue placeholder="Select a template" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="default">Default Template</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <Label htmlFor="target_role">Target Role</Label>
                                    <Input
                                        id="target_role"
                                        value={form.data.target_role}
                                        onChange={(e) => form.setData('target_role', e.target.value)}
                                        placeholder="e.g., Senior Frontend Developer"
                                    />
                                </div>
                                <div>
                                    <Label htmlFor="target_industry">Target Industry</Label>
                                    <Input
                                        id="target_industry"
                                        value={form.data.target_industry}
                                        onChange={(e) => form.setData('target_industry', e.target.value)}
                                        placeholder="e.g., Technology"
                                    />
                                </div>
                            </div>
                            <DialogFooter>
                                <Button variant="outline" onClick={() => setIsCreating(false)}>
                                    Cancel
                                </Button>
                                <Button onClick={handleCreate} disabled={form.processing}>
                                    {form.processing && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                                    Create
                                </Button>
                            </DialogFooter>
                        </DialogContent>
                    </Dialog>
                </div>

                {versions.length === 0 ? (
                    <Card>
                        <CardContent className="py-12">
                            <EmptyState
                                icon={FileText}
                                title="No CV versions yet"
                                description="Create your first CV version to start applying for jobs"
                                actionLabel="Create CV"
                                onAction={() => setIsCreating(true)}
                            />
                        </CardContent>
                    </Card>
                ) : (
                    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {versions.map((cv) => (
                            <Card key={cv.id} className="flex flex-col">
                                <CardHeader>
                                    <CardTitle className="text-lg">{cv.name}</CardTitle>
                                    <CardDescription>
                                        {cv.target_role ?? 'No target role'}
                                    </CardDescription>
                                </CardHeader>
                                <CardContent className="flex-1 space-y-2">
                                    <div className="flex flex-wrap gap-1">
                                        {cv.target_role && (
                                            <Badge variant="secondary">{cv.target_role}</Badge>
                                        )}
                                        {cv.application_count > 0 && (
                                            <Badge variant="outline">
                                                {cv.application_count} applications
                                            </Badge>
                                        )}
                                    </div>
                                    <p className="text-xs text-muted-foreground">
                                        Template: {cv.template?.name ?? 'Default'}
                                    </p>
                                    {cv.last_exported_at && (
                                        <p className="text-xs text-muted-foreground">
                                            Last exported: {new Date(cv.last_exported_at).toLocaleDateString()}
                                        </p>
                                    )}
                                </CardContent>
                                <CardFooter className="flex justify-between gap-2">
                                    <Link href={`/cv-versions/${cv.id}`} className="flex-1">
                                        <Button variant="outline" size="sm" className="w-full">
                                            <Eye className="mr-1 h-4 w-4" />
                                            View
                                        </Button>
                                    </Link>
                                    <Dialog>
                                        <DialogTrigger asChild>
                                            <Button variant="outline" size="sm">
                                                <Copy className="h-4 w-4" />
                                            </Button>
                                        </DialogTrigger>
                                        <DialogContent>
                                            <DialogHeader>
                                                <DialogTitle>Duplicate CV</DialogTitle>
                                            </DialogHeader>
                                            <div className="grid gap-4">
                                                <div>
                                                    <Label htmlFor="dup-name">New Name</Label>
                                                    <Input
                                                        id="dup-name"
                                                        defaultValue={`${cv.name} (Copy)`}
                                                        onChange={(e) => duplicateForm.setData('name', e.target.value)}
                                                    />
                                                </div>
                                            </div>
                                            <DialogFooter>
                                                <Button
                                                    onClick={() => handleDuplicate(cv)}
                                                    disabled={duplicateForm.processing || duplicatingId === cv.id}
                                                >
                                                    {duplicateForm.processing && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                                                    Duplicate
                                                </Button>
                                            </DialogFooter>
                                        </DialogContent>
                                    </Dialog>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        onClick={() => setDeletingCv(cv)}
                                    >
                                        <Trash2 className="h-4 w-4 text-destructive" />
                                    </Button>
                                </CardFooter>
                            </Card>
                        ))}
                    </div>
                )}

                <ConfirmDialog
                    open={!!deletingCv}
                    onOpenChange={(open) => !open && setDeletingCv(null)}
                    title="Delete CV Version"
                    description={`Are you sure you want to delete "${deletingCv?.name}"? This action cannot be undone.`}
                    onConfirm={handleDelete}
                    loading={form.processing}
                />
            </div>
        </>
    );
}

CvVersionsIndex.layout = {
    breadcrumbs: [
        { title: 'My CVs', href: cvVersionsIndex() },
    ],
};
