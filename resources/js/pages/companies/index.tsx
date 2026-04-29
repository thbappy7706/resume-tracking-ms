import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { ColumnDef } from '@tanstack/react-table';
import { DataTable } from '@/components/data-table';
import { ConfirmDialog } from '@/components/confirm-dialog';
import { StatusBadge } from '@/components/status-badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import { EmptyState } from '@/components/empty-state';
import { Plus, Pencil, Trash2, Loader2 } from 'lucide-react';
import { useFlashToast } from '@/hooks/use-flash-toast';
import companies, { index as companiesIndex, store as companiesStore, update as companiesUpdate, destroy as companiesDestroy } from '@/routes/companies';

interface Company {
    id: string;
    name: string;
    slug: string;
    website: string | null;
    industry: string | null;
    size: string | null;
    location: string | null;
    notes: string | null;
    active_applications_count: number;
    application_count?: number;
    created_at: string;
    updated_at: string;
}

interface CompaniesPageProps {
    companies: { data: Company[] };
    industries: string[];
    size_options: { value: string; label: string }[];
    filters: Record<string, string>;
}

export default function CompaniesIndex({ companies: companiesProp, industries, size_options }: CompaniesPageProps) {
    useFlashToast();

    const [isCreating, setIsCreating] = useState(false);
    const [editingCompany, setEditingCompany] = useState<Company | null>(null);
    const [deletingCompany, setDeletingCompany] = useState<Company | null>(null);

    const form = useForm({
        name: '',
        website: '',
        industry: '',
        size: '',
        location: '',
        notes: '',
    });

    const handleCreate = () => {
        form.post(companiesStore().url, {
            onSuccess: () => {
                setIsCreating(false);
                form.reset();
            },
        });
    };

    const handleUpdate = () => {
        if (!editingCompany) return;
        form.put(companiesUpdate(editingCompany).url, {
            onSuccess: () => {
                setEditingCompany(null);
                form.reset();
            },
        });
    };

    const handleDelete = () => {
        if (!deletingCompany) return;
        form.delete(companiesDestroy(deletingCompany).url, {
            onSuccess: () => setDeletingCompany(null),
        });
    };

    const openEdit = (company: Company) => {
        setEditingCompany(company);
        form.setData({
            name: company.name,
            website: company.website ?? '',
            industry: company.industry ?? '',
            size: company.size ?? '',
            location: company.location ?? '',
            notes: company.notes ?? '',
        });
    };

    const columns: ColumnDef<Company>[] = [
        {
            accessorKey: 'name',
            header: 'Name',
            cell: ({ row }) => <span className="font-medium">{row.getValue('name')}</span>,
        },
        {
            accessorKey: 'industry',
            header: 'Industry',
            cell: ({ row }) => row.getValue('industry') || '-',
        },
        {
            accessorKey: 'size',
            header: 'Size',
            cell: ({ row }) => {
                const size = row.getValue('size') as string;
                return size ? <StatusBadge status={size} label={size} /> : '-';
            },
        },
        {
            accessorKey: 'location',
            header: 'Location',
            cell: ({ row }) => row.getValue('location') || '-',
        },
        {
            accessorKey: 'active_applications_count',
            header: 'Active',
            cell: ({ row }) => (
                <span className="text-center">{row.getValue('active_applications_count')}</span>
            ),
        },
    ];

    const companyList = companiesProp?.data ?? [];

    return (
        <>
            <Head title="Companies" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">Companies</h1>
                        <p className="text-muted-foreground">
                            Manage companies you're applying to
                        </p>
                    </div>
                    <Dialog open={isCreating} onOpenChange={setIsCreating}>
                        <DialogTrigger asChild>
                            <Button>
                                <Plus className="mr-2 h-4 w-4" />
                                Add Company
                            </Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Add Company</DialogTitle>
                                <DialogDescription>
                                    Add a new company to track.
                                </DialogDescription>
                            </DialogHeader>
                            <div className="grid gap-4">
                                <div>
                                    <Label htmlFor="name">Company Name</Label>
                                    <Input
                                        id="name"
                                        value={form.data.name}
                                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => form.setData('name', e.target.value)}
                                    />
                                </div>
                                <div>
                                    <Label htmlFor="website">Website</Label>
                                    <Input id="website" type="url" value={form.data.website} onChange={(e) => form.setData('website', e.target.value)} />
                                </div>
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <Label htmlFor="industry">Industry</Label>
                                        <Input id="industry" value={form.data.industry} onChange={(e) => form.setData('industry', e.target.value)} />
                                    </div>
                                    <div>
                                        <Label htmlFor="size">Size</Label>
                                        <Select value={form.data.size} onValueChange={(v) => form.setData('size', v)}>
                                            <SelectTrigger id="size"><SelectValue placeholder="Select size" /></SelectTrigger>
                                            <SelectContent>
                                                {size_options.map((s) => (
                                                    <SelectItem key={s.value} value={s.value}>{s.label}</SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>
                                <div>
                                    <Label htmlFor="location">Location</Label>
                                    <Input id="location" value={form.data.location} onChange={(e) => form.setData('location', e.target.value)} />
                                </div>
                                <div>
                                    <Label htmlFor="notes">Notes</Label>
                                    <Input id="notes" value={form.data.notes} onChange={(e) => form.setData('notes', e.target.value)} />
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

                {companyList.length === 0 ? (
                    <div className="rounded-lg border bg-card p-12">
                        <EmptyState
                            icon={Plus}
                            title="No companies"
                            description="Add companies you're applying to"
                            actionLabel="Add Company"
                            onAction={() => setIsCreating(true)}
                        />
                    </div>
                ) : (
                    <DataTable
                        columns={columns}
                        data={companyList}
                        searchPlaceholder="Search companies..."
                        searchColumn="name"
                        actions={(company) => (
                            <div className="flex items-center gap-1">
                                <Button variant="ghost" size="icon" onClick={() => openEdit(company)}>
                                    <Pencil className="h-4 w-4" />
                                </Button>
                                <Button variant="ghost" size="icon" onClick={() => setDeletingCompany(company)}>
                                    <Trash2 className="h-4 w-4 text-destructive" />
                                </Button>
                            </div>
                        )}
                    />
                )}

                {/* Edit Dialog */}
                <Dialog open={!!editingCompany} onOpenChange={(open) => !open && setEditingCompany(null)}>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Edit Company</DialogTitle>
                        </DialogHeader>
                        <div className="grid gap-4">
                            <div>
                                <Label htmlFor="edit-name">Company Name</Label>
                                <Input id="edit-name" value={form.data.name} onChange={(e) => form.setData('name', e.target.value)} />
                            </div>
                            <div>
                                <Label htmlFor="edit-website">Website</Label>
                                <Input id="edit-website" type="url" value={form.data.website} onChange={(e) => form.setData('website', e.target.value)} />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <Label htmlFor="edit-industry">Industry</Label>
                                    <Input id="edit-industry" value={form.data.industry} onChange={(e) => form.setData('industry', e.target.value)} />
                                </div>
                                <div>
                                    <Label htmlFor="edit-size">Size</Label>
                                    <Select value={form.data.size} onValueChange={(v) => form.setData('size', v)}>
                                        <SelectTrigger id="edit-size"><SelectValue placeholder="Select size" /></SelectTrigger>
                                        <SelectContent>
                                            {size_options.map((s) => (
                                                <SelectItem key={s.value} value={s.value}>{s.label}</SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            <div>
                                <Label htmlFor="edit-location">Location</Label>
                                <Input id="edit-location" value={form.data.location} onChange={(e) => form.setData('location', e.target.value)} />
                            </div>
                            <div>
                                <Label htmlFor="edit-notes">Notes</Label>
                                <Input id="edit-notes" value={form.data.notes} onChange={(e) => form.setData('notes', e.target.value)} />
                            </div>
                        </div>
                        <DialogFooter>
                            <Button variant="outline" onClick={() => setEditingCompany(null)}>Cancel</Button>
                            <Button onClick={handleUpdate} disabled={form.processing}>
                                {form.processing && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                                Save
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>

                {/* Delete Confirmation */}
                <ConfirmDialog
                    open={!!deletingCompany}
                    onOpenChange={(open) => !open && setDeletingCompany(null)}
                    title="Delete Company"
                    description={`Are you sure you want to delete "${deletingCompany?.name}"? This action cannot be undone.`}
                    onConfirm={handleDelete}
                    loading={form.processing}
                />
            </div>
        </>
    );
}

CompaniesIndex.layout = {
    breadcrumbs: [
        { title: 'Companies', href: companiesIndex().url },
    ],
};
