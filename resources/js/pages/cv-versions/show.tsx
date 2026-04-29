import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { EmptyState } from '@/components/empty-state';
import { ArrowLeft, Save, Loader2 } from 'lucide-react';
import { Link } from '@inertiajs/react';
import { index as cvVersionsIndex, show as cvVersionsShow } from '@/routes/cv-versions';

interface ProfileSection {
    id: string;
    type: string;
    type_label: string;
    title: string;
    organization: string | null;
    location: string | null;
    description: string | null;
    is_visible: boolean;
}

interface CvSectionOverride {
    id: string;
    profile_section_id: string;
    is_included: boolean;
    sort_order: number;
    override_title: string | null;
    override_description: string | null;
    profile_section?: ProfileSection;
}

interface CvVersion {
    id: string;
    name: string;
    target_role: string | null;
    target_industry: string | null;
    notes: string | null;
    overrides: { data: CvSectionOverride[] };
    sections: { data: ProfileSection[] };
}

interface CvVersionShowProps {
    cv_version: CvVersion;
}

export default function CvVersionShow({ cv_version }: CvVersionShowProps) {
    const [editingOverride, setEditingOverride] = useState<string | null>(null);

    const form = useForm({
        override_title: '',
        override_description: '',
        is_included: true,
    });

    const sections = cv_version.sections?.data ?? [];
    const overrides = cv_version.overrides?.data ?? [];

    const getOverride = (sectionId: string) => {
        return overrides.find((o) => o.profile_section_id === sectionId);
    };

    const handleEditOverride = (section: ProfileSection) => {
        const existing = getOverride(section.id);
        setEditingOverride(section.id);
        form.setData({
            override_title: existing?.override_title ?? section.title ?? '',
            override_description: existing?.override_description ?? section.description ?? '',
            is_included: existing?.is_included ?? true,
        });
    };

    const handleSaveOverride = (section: ProfileSection) => {
        router.post(`/cv-versions/${cv_version.id}/overrides`, {
            overrides: [
                {
                    profile_section_id: section.id,
                    override_title: form.data.override_title,
                    override_description: form.data.override_description,
                    is_included: form.data.is_included,
                },
            ],
        }, {
            onSuccess: () => setEditingOverride(null),
        });
    };

    return (
        <>
            <Head title={cv_version.name} />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="flex items-center gap-4">
                    <Link href={cvVersionsIndex()}>
                        <Button variant="ghost" size="icon">
                            <ArrowLeft className="h-4 w-4" />
                        </Button>
                    </Link>
                    <div>
                        <h1 className="text-2xl font-bold">{cv_version.name}</h1>
                        <p className="text-muted-foreground">
                            {cv_version.target_role ?? 'No target role'}
                        </p>
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    {/* Left Panel - Section List */}
                    <div className="space-y-4">
                        <Card>
                            <CardHeader>
                                <CardTitle>Sections</CardTitle>
                            </CardHeader>
                            <CardContent>
                                {sections.length === 0 ? (
                                    <EmptyState
                                        title="No sections"
                                        description="Add profile sections to include in this CV"
                                    />
                                ) : (
                                    <div className="space-y-3">
                                        {sections.map((section) => {
                                            const override = getOverride(section.id);
                                            const isEditing = editingOverride === section.id;

                                            return (
                                                <Card key={section.id}>
                                                    <CardContent className="pt-4">
                                                        <div className="flex items-start justify-between">
                                                            <div className="space-y-1 flex-1">
                                                                <div className="flex items-center gap-2">
                                                                    <Badge variant="outline">{section.type_label}</Badge>
                                                                    {override && !override.is_included && (
                                                                        <Badge variant="secondary">Excluded</Badge>
                                                                    )}
                                                                </div>
                                                                <h3 className="font-medium">
                                                                    {override?.override_title ?? section.title}
                                                                </h3>
                                                                {section.organization && (
                                                                    <p className="text-sm text-muted-foreground">
                                                                        {section.organization}
                                                                    </p>
                                                                )}
                                                            </div>
                                                            <Button
                                                                variant="ghost"
                                                                size="sm"
                                                                onClick={() => handleEditOverride(section)}
                                                            >
                                                                Edit
                                                            </Button>
                                                        </div>

                                                        {isEditing && (
                                                            <>
                                                                <Separator className="my-4" />
                                                                <div className="grid gap-4">
                                                                    <div className="flex items-center gap-2">
                                                                        <Switch
                                                                            checked={form.data.is_included}
                                                                            onCheckedChange={(v: boolean) => form.setData('is_included', v)}
                                                                        />
                                                                        <Label>Include in CV</Label>
                                                                    </div>
                                                                    <div>
                                                                        <Label htmlFor={`title-${section.id}`}>Title Override</Label>
                                                                        <Input
                                                                            id={`title-${section.id}`}
                                                                            value={form.data.override_title}
                                                                            onChange={(e) => form.setData('override_title', e.target.value)}
                                                                        />
                                                                    </div>
                                                                    <div>
                                                                        <Label htmlFor={`desc-${section.id}`}>Description Override</Label>
                                                                        <Textarea
                                                                            id={`desc-${section.id}`}
                                                                            value={form.data.override_description}
                                                                            onChange={(e) => form.setData('override_description', e.target.value)}
                                                                            rows={3}
                                                                        />
                                                                    </div>
                                                                    <div className="flex justify-end gap-2">
                                                                        <Button variant="outline" onClick={() => setEditingOverride(null)}>
                                                                            Cancel
                                                                        </Button>
                                                                        <Button
                                                                            onClick={() => handleSaveOverride(section)}
                                                                            disabled={form.processing}
                                                                        >
                                                                            {form.processing && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                                                                            <Save className="mr-1 h-4 w-4" />
                                                                            Save
                                                                        </Button>
                                                                    </div>
                                                                </div>
                                                            </>
                                                        )}
                                                    </CardContent>
                                                </Card>
                                            );
                                        })}
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    {/* Right Panel - Live Preview */}
                    <div className="space-y-4">
                        <Card>
                            <CardHeader>
                                <CardTitle>Live Preview</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="aspect-[210/297] w-full overflow-hidden rounded-lg border bg-white">
                                    <iframe
                                        src={`/cv-versions/${cv_version.id}/preview`}
                                        className="h-full w-full border-0"
                                        title="CV Preview"
                                    />
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </>
    );
}

CvVersionShow.layout = (page: { cv_version: { id: string } }) => ({
    breadcrumbs: [
        { title: 'My CVs', href: cvVersionsIndex() },
        { title: 'CV Details', href: cvVersionsShow({ cv: page.cv_version.id }) },
    ],
});
