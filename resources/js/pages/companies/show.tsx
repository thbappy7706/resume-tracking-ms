import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Pencil, ExternalLink } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import companies, { index as companiesIndex, edit as companiesEdit } from '@/routes/companies';

interface Company {
    id: string;
    name: string;
    slug: string;
    website: string | null;
    industry: string | null;
    size: string | null;
    location: string | null;
    notes: string | null;
    job_applications_count: number;
    created_at: string;
    updated_at: string;
}

interface ShowCompanyPageProps {
    company: Company;
}

export default function ShowCompany({ company }: ShowCompanyPageProps) {
    return (
        <>
            <Head title={company.name} />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Button variant="ghost" size="icon" asChild>
                            <Link href={companiesIndex().url}>
                                <ArrowLeft className="h-4 w-4" />
                            </Link>
                        </Button>
                        <div>
                            <h1 className="text-2xl font-bold">{company.name}</h1>
                            <p className="text-muted-foreground">
                                Company details
                            </p>
                        </div>
                    </div>
                    <Button asChild>
                        <Link href={companiesEdit(company).url}>
                            <Pencil className="mr-2 h-4 w-4" />
                            Edit Company
                        </Link>
                    </Button>
                </div>

                <div className="grid gap-6 md:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Basic Information</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">Name</label>
                                <p className="text-sm">{company.name}</p>
                            </div>
                            {company.website && (
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground">Website</label>
                                    <p className="text-sm">
                                        <a
                                            href={company.website}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="text-primary hover:underline inline-flex items-center gap-1"
                                        >
                                            {company.website}
                                            <ExternalLink className="h-3 w-3" />
                                        </a>
                                    </p>
                                </div>
                            )}
                            {company.industry && (
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground">Industry</label>
                                    <p className="text-sm">{company.industry}</p>
                                </div>
                            )}
                            {company.size && (
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground">Size</label>
                                    <p className="text-sm">
                                        <Badge variant="secondary">{company.size}</Badge>
                                    </p>
                                </div>
                            )}
                            {company.location && (
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground">Location</label>
                                    <p className="text-sm">{company.location}</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Applications</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">Total Applications</label>
                                <p className="text-2xl font-bold">{company.job_applications_count}</p>
                            </div>
                        </CardContent>
                    </Card>

                    {company.notes && (
                        <Card className="md:col-span-2">
                            <CardHeader>
                                <CardTitle>Notes</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="text-sm whitespace-pre-wrap">{company.notes}</p>
                            </CardContent>
                        </Card>
                    )}

                    <Card className="md:col-span-2">
                        <CardHeader>
                            <CardTitle>Timestamps</CardTitle>
                        </CardHeader>
                        <CardContent className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">Created</label>
                                <p className="text-sm">{new Date(company.created_at).toLocaleDateString()}</p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">Last Updated</label>
                                <p className="text-sm">{new Date(company.updated_at).toLocaleDateString()}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

ShowCompany.layout = {
    breadcrumbs: [
        { title: 'Companies', href: companiesIndex().url },
        { title: 'Show', href: companiesIndex().url }, // Will be updated by router
    ],
};