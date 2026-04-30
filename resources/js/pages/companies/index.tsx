import { Head, Link, router } from '@inertiajs/react';
import type { ColumnDef } from '@tanstack/react-table';
import { Plus, Pencil, Trash2, Eye } from 'lucide-react';
import { useState } from 'react';
import { ConfirmDialog } from '@/components/confirm-dialog';
import { DataTable } from '@/components/data-table';
import { EmptyState } from '@/components/empty-state';
import { StatusBadge } from '@/components/status-badge';
import { Button } from '@/components/ui/button';
import { useFlashToast } from '@/hooks/use-flash-toast';
import { index as companiesIndex, create as companiesCreate, show as companiesShow, edit as companiesEdit, destroy as companiesDestroy } from '@/routes/companies';

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
    filters: Record<string, string>;
}

export default function CompaniesIndex({ companies: companiesProp }: CompaniesPageProps) {
    useFlashToast();

    const [deletingCompany, setDeletingCompany] = useState<Company | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);

    const handleDelete = () => {
        if (!deletingCompany) {
return;
}

        setIsDeleting(true);
        router.delete(companiesDestroy(deletingCompany).url, {
            onSuccess: () => {
                setDeletingCompany(null);
                setIsDeleting(false);
            },
            onError: () => setIsDeleting(false),
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
                    <Button asChild>
                        <Link href={companiesCreate().url}>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Company
                        </Link>
                    </Button>
                </div>

                {companyList.length === 0 ? (
                    <div className="rounded-lg border bg-card p-12">
                        <EmptyState
                            icon={Plus}
                            title="No companies"
                            description="Add companies you're applying to"
                            actionLabel="Add Company"
                            onAction={() => router.visit(companiesCreate().url)}
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
                                <Button variant="ghost" size="icon" asChild>
                                    <Link href={companiesShow(company).url}>
                                        <Eye className="h-4 w-4" />
                                    </Link>
                                </Button>
                                <Button variant="ghost" size="icon" asChild>
                                    <Link href={companiesEdit(company).url}>
                                        <Pencil className="h-4 w-4" />
                                    </Link>
                                </Button>
                                <Button variant="ghost" size="icon" onClick={() => setDeletingCompany(company)}>
                                    <Trash2 className="h-4 w-4 text-destructive" />
                                </Button>
                            </div>
                        )}
                    />
                )}



                {/* Delete Confirmation */}
                <ConfirmDialog
                    open={!!deletingCompany}
                    onOpenChange={(open) => !open && setDeletingCompany(null)}
                    title="Delete Company"
                    description={`Are you sure you want to delete "${deletingCompany?.name}"? This action cannot be undone.`}
                    onConfirm={handleDelete}
                    loading={isDeleting}
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
