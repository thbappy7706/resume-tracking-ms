import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Loader2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useFlashToast } from '@/hooks/use-flash-toast';
import companies, { index as companiesIndex, show as companiesShow, update as companiesUpdate } from '@/routes/companies';

interface Company {
    id: string;
    name: string;
    slug: string;
    website: string | null;
    industry: string | null;
    size: string | null;
    location: string | null;
    notes: string | null;
    created_at: string;
    updated_at: string;
}

interface EditCompanyPageProps {
    company: Company;
    size_options: { value: string; label: string }[];
}

export default function EditCompany({ company, size_options }: EditCompanyPageProps) {
    useFlashToast();

    const form = useForm({
        name: company.name,
        website: company.website ?? '',
        industry: company.industry ?? '',
        size: company.size ?? '',
        location: company.location ?? '',
        notes: company.notes ?? '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        companiesUpdate(company).put(form.data);
    };

    return (
        <>
            <Head title={`Edit ${company.name}`} />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Button variant="ghost" size="icon" asChild>
                        <Link href={companiesShow(company).url}>
                            <ArrowLeft className="h-4 w-4" />
                        </Link>
                    </Button>
                    <div>
                        <h1 className="text-2xl font-bold">Edit Company</h1>
                        <p className="text-muted-foreground">
                            Update company information
                        </p>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="max-w-2xl space-y-6">
                    <div>
                        <Label htmlFor="name">Company Name *</Label>
                        <Input
                            id="name"
                            value={form.data.name}
                            onChange={(e) => form.setData('name', e.target.value)}
                            required
                        />
                        {form.errors.name && <p className="text-sm text-destructive mt-1">{form.errors.name}</p>}
                    </div>

                    <div>
                        <Label htmlFor="website">Website</Label>
                        <Input
                            id="website"
                            type="url"
                            value={form.data.website}
                            onChange={(e) => form.setData('website', e.target.value)}
                        />
                        {form.errors.website && <p className="text-sm text-destructive mt-1">{form.errors.website}</p>}
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <Label htmlFor="industry">Industry</Label>
                            <Input
                                id="industry"
                                value={form.data.industry}
                                onChange={(e) => form.setData('industry', e.target.value)}
                            />
                            {form.errors.industry && <p className="text-sm text-destructive mt-1">{form.errors.industry}</p>}
                        </div>
                        <div>
                            <Label htmlFor="size">Size</Label>
                            <Select value={form.data.size} onValueChange={(v) => form.setData('size', v)}>
                                <SelectTrigger id="size">
                                    <SelectValue placeholder="Select size" />
                                </SelectTrigger>
                                <SelectContent>
                                    {size_options.map((s) => (
                                        <SelectItem key={s.value} value={s.value}>
                                            {s.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {form.errors.size && <p className="text-sm text-destructive mt-1">{form.errors.size}</p>}
                        </div>
                    </div>

                    <div>
                        <Label htmlFor="location">Location</Label>
                        <Input
                            id="location"
                            value={form.data.location}
                            onChange={(e) => form.setData('location', e.target.value)}
                        />
                        {form.errors.location && <p className="text-sm text-destructive mt-1">{form.errors.location}</p>}
                    </div>

                    <div>
                        <Label htmlFor="notes">Notes</Label>
                        <Input
                            id="notes"
                            value={form.data.notes}
                            onChange={(e) => form.setData('notes', e.target.value)}
                        />
                        {form.errors.notes && <p className="text-sm text-destructive mt-1">{form.errors.notes}</p>}
                    </div>

                    <div className="flex gap-4">
                        <Button type="submit" disabled={form.processing}>
                            {form.processing && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                            Update Company
                        </Button>
                        <Button variant="outline" asChild>
                            <Link href={companiesShow(company).url}>Cancel</Link>
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}

EditCompany.layout = {
    breadcrumbs: [
        { title: 'Companies', href: companiesIndex().url },
        { title: 'Show', href: companiesShow(company).url }, // Will be updated
        { title: 'Edit', href: companiesIndex().url }, // Will be updated
    ],
};