import { Head, useForm, usePoll } from '@inertiajs/react';
import { useState } from 'react';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import { EmptyState } from '@/components/empty-state';
import { Plus, Pencil, Trash2, Loader2 } from 'lucide-react';
import { useFlashToast } from '@/hooks/use-flash-toast';
import { ConfirmDialog } from '@/components/confirm-dialog';
import profileRoutes, { sections, index as profileIndex } from '@/routes/profile';

interface Tag {
    id: string;
    name: string;
    slug: string;
}

interface ProfileSection {
    id: string;
    type: string;
    type_label: string;
    title: string;
    organization: string | null;
    location: string | null;
    start_date: string | null;
    end_date: string | null;
    is_current: boolean;
    date_range: string;
    description: string | null;
    meta: Record<string, unknown> | null;
    sort_order: number;
    is_visible: boolean;
    tags: Tag[];
}

interface ProfilePageProps {
    sections: Record<string, { data: ProfileSection[] }>;
    tags: { data: Tag[] };
    section_types: { value: string; label: string }[];
}

export default function ProfilePage({ sections, tags, section_types }: ProfilePageProps) {
    const [editingId, setEditingId] = useState<string | null>(null);
    const [isCreating, setIsCreating] = useState(false);
    const [deletingSection, setDeletingSection] = useState<ProfileSection | null>(null);

    usePoll(30000, { only: ['sections'] });
    useFlashToast();

    const form = useForm({
        id: '',
        type: 'experience',
        title: '',
        organization: '',
        location: '',
        start_date: '',
        end_date: '',
        is_current: false,
        description: '',
        is_visible: true,
        tags: [] as string[],
    });

    const handleEdit = (section: ProfileSection) => {
        setEditingId(section.id);
        form.setData({
            id: section.id,
            type: section.type,
            title: section.title ?? '',
            organization: section.organization ?? '',
            location: section.location ?? '',
            start_date: section.start_date ?? '',
            end_date: section.end_date ?? '',
            is_current: section.is_current,
            description: section.description ?? '',
            is_visible: section.is_visible,
            tags: section.tags.map((t) => t.id),
        });
    };

    const handleCancel = () => {
        setEditingId(null);
        form.reset();
    };

    const handleSave = (section?: ProfileSection) => {
        if (section) {
            form.put(sections.update(section).url, {
                onSuccess: () => {
                    setEditingId(null);
                    form.reset();
                },
            });
        } else {
            form.post(sections.store().url, {
                onSuccess: () => {
                    setIsCreating(false);
                    form.reset();
                },
            });
        }
    };

    const handleDelete = () => {
        if (!deletingSection) return;
        form.delete(sections.destroy(deletingSection).url);
    };

    const handleToggleVisibility = (section: ProfileSection) => {
        const currentData = { ...form.data };
        form.setData('is_visible', !section.is_visible);
        form.patch(`/profile/sections/${section.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                form.setData(currentData);
            },
        });
    };

    const allTags = tags?.data ?? [];

    const SectionForm = ({ section }: { section?: ProfileSection }) => (
        <div className="grid gap-4">
            <div className="grid grid-cols-2 gap-4">
                <div>
                    <Label htmlFor="type">Type</Label>
                    <Select
                        value={form.data.type}
                        onValueChange={(v: string) => form.setData('type', v)}
                    >
                        <SelectTrigger id="type">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            {section_types.map((t) => (
                                <SelectItem key={t.value} value={t.value}>
                                    {t.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>
                <div>
                    <Label htmlFor="title">Title</Label>
                    <Input
                        id="title"
                        value={form.data.title}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => form.setData('title', e.target.value)}
                    />
                </div>
            </div>
            <div className="grid grid-cols-2 gap-4">
                <div>
                    <Label htmlFor="organization">Organization</Label>
                    <Input
                        id="organization"
                        value={form.data.organization}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => form.setData('organization', e.target.value)}
                    />
                </div>
                <div>
                    <Label htmlFor="location">Location</Label>
                    <Input
                        id="location"
                        value={form.data.location}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => form.setData('location', e.target.value)}
                    />
                </div>
            </div>
            <div className="grid grid-cols-2 gap-4">
                <div>
                    <Label htmlFor="start_date">Start Date</Label>
                    <Input
                        id="start_date"
                        type="date"
                        value={form.data.start_date}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => form.setData('start_date', e.target.value)}
                    />
                </div>
                <div>
                    <Label htmlFor="end_date">End Date</Label>
                    <Input
                        id="end_date"
                        type="date"
                        value={form.data.end_date}
                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => form.setData('end_date', e.target.value)}
                        disabled={form.data.is_current}
                    />
                </div>
            </div>
            <div className="flex items-center gap-4">
                <div className="flex items-center gap-2">
                    <Switch
                        checked={form.data.is_current}
                        onCheckedChange={(v: boolean) => form.setData('is_current', v)}
                    />
                    <Label>Current</Label>
                </div>
                <div className="flex items-center gap-2">
                    <Switch
                        checked={form.data.is_visible}
                        onCheckedChange={(v: boolean) => form.setData('is_visible', v)}
                    />
                    <Label>Visible</Label>
                </div>
            </div>
            <div>
                <Label htmlFor="description">Description</Label>
                <Textarea
                    id="description"
                    value={form.data.description}
                    onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => form.setData('description', e.target.value)}
                    rows={3}
                />
            </div>
        </div>
    );

    return (
        <>
            <Head title="Profile" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">Profile</h1>
                        <p className="text-muted-foreground">
                            Manage your professional information
                        </p>
                    </div>
                    <Dialog open={isCreating} onOpenChange={setIsCreating}>
                        <DialogTrigger asChild>
                            <Button>
                                <Plus className="mr-2 h-4 w-4" />
                                Add Section
                            </Button>
                        </DialogTrigger>
                        <DialogContent className="max-w-2xl">
                            <DialogHeader>
                                <DialogTitle>Add Profile Section</DialogTitle>
                                <DialogDescription>
                                    Add a new experience, education, skill, or summary section.
                                </DialogDescription>
                            </DialogHeader>
                            <SectionForm />
                            <DialogFooter>
                                <Button variant="outline" onClick={() => setIsCreating(false)}>
                                    Cancel
                                </Button>
                                <Button
                                    onClick={() => handleSave()}
                                    disabled={form.processing}
                                >
                                    {form.processing && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                                    Create
                                </Button>
                            </DialogFooter>
                        </DialogContent>
                    </Dialog>
                </div>

                <Tabs defaultValue="experience" className="space-y-4">
                    <TabsList>
                        {section_types.map((t) => (
                            <TabsTrigger key={t.value} value={t.value}>
                                {t.label}
                            </TabsTrigger>
                        ))}
                    </TabsList>

                    {section_types.map((type) => {
                        const typeSections = sections[type.value]?.data ?? [];

                        return (
                            <TabsContent key={type.value} value={type.value}>
                                {typeSections.length === 0 ? (
                                    <Card>
                                        <CardContent className="py-8">
                                            <EmptyState
                                                title={`No ${type.label.toLowerCase()} sections`}
                                                description={`Add your ${type.label.toLowerCase()} to build your profile`}
                                                actionLabel={`Add ${type.label}`}
                                                onAction={() => {
                                                    form.setData('type', type.value);
                                                    setIsCreating(true);
                                                }}
                                            />
                                        </CardContent>
                                    </Card>
                                ) : (
                                    <div className="space-y-3">
                                        {typeSections.map((section) => (
                                            <Card key={section.id}>
                                                {editingId === section.id ? (
                                                    <CardContent className="pt-6">
                                                        <SectionForm section={section} />
                                                        <div className="flex justify-end gap-2 mt-4">
                                                            <Button variant="outline" onClick={handleCancel}>
                                                                Cancel
                                                            </Button>
                                                            <Button
                                                                onClick={() => handleSave(section)}
                                                                disabled={form.processing}
                                                            >
                                                                {form.processing && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                                                                Save
                                                            </Button>
                                                        </div>
                                                    </CardContent>
                                                ) : (
                                                    <CardContent className="pt-6">
                                                        <div className="flex items-start justify-between">
                                                            <div className="space-y-1">
                                                                <div className="flex items-center gap-2">
                                                                    <h3 className="font-semibold">{section.title}</h3>
                                                                    {!section.is_visible && (
                                                                        <Badge variant="secondary">Hidden</Badge>
                                                                    )}
                                                                </div>
                                                                {section.organization && (
                                                                    <p className="text-sm text-muted-foreground">
                                                                        {section.organization}
                                                                        {section.location && ` · ${section.location}`}
                                                                    </p>
                                                                )}
                                                                {section.date_range && (
                                                                    <p className="text-xs text-muted-foreground">
                                                                        {section.date_range}
                                                                    </p>
                                                                )}
                                                                {section.description && (
                                                                    <p className="mt-2 text-sm">{section.description}</p>
                                                                )}
                                                                {section.tags.length > 0 && (
                                                                    <div className="flex gap-1 pt-1">
                                                                        {section.tags.map((tag) => (
                                                                            <Badge key={tag.id} variant="outline" className="text-xs">
                                                                                {tag.name}
                                                                            </Badge>
                                                                        ))}
                                                                    </div>
                                                                )}
                                                            </div>
                                                            <div className="flex items-center gap-1">
                                                                <Button
                                                                    variant="ghost"
                                                                    size="icon"
                                                                    onClick={() => handleToggleVisibility(section)}
                                                                >
                                                                    {section.is_visible ? (
                                                                        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                        </svg>
                                                                    ) : (
                                                                        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                                        </svg>
                                                                    )}
                                                                </Button>
                                                                <Button
                                                                    variant="ghost"
                                                                    size="icon"
                                                                    onClick={() => handleEdit(section)}
                                                                >
                                                                    <Pencil className="h-4 w-4" />
                                                                </Button>
                                                                <Button
                                                                    variant="ghost"
                                                                    size="icon"
                                                                    onClick={() => setDeletingSection(section)}
                                                                >
                                                                    <Trash2 className="h-4 w-4 text-destructive" />
                                                                </Button>
                                                            </div>
                                                        </div>
                                                    </CardContent>
                                                )}
                                            </Card>
                                        ))}
                                    </div>
                                )}
                            </TabsContent>
                        );
                    })}
                </Tabs>

                <ConfirmDialog
                    open={!!deletingSection}
                    onOpenChange={(open) => !open && setDeletingSection(null)}
                    title="Delete Profile Section"
                    description={`Are you sure you want to delete this ${deletingSection?.type_label || 'section'}? This action cannot be undone.`}
                    onConfirm={handleDelete}
                    loading={form.processing}
                />
            </div>
        </>
    );
}

ProfilePage.layout = {
    breadcrumbs: [
        { title: 'Profile', href: profileIndex() },
    ],
};
