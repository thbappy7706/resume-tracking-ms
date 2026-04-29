import { cva, type VariantProps } from 'class-variance-authority';

const statusVariants = cva('inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', {
    variants: {
        variant: {
            saved: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
            applied: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            screening: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
            interviewing: 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
            offer: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            accepted: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
            rejected: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            closed: 'bg-slate-100 text-slate-800 dark:bg-slate-900 dark:text-slate-200',
        },
    },
    defaultVariants: {
        variant: 'saved',
    },
});

export interface StatusBadgeProps extends VariantProps<typeof statusVariants> {
    status: string;
    label?: string;
    className?: string;
}

export function StatusBadge({ status, label, className }: StatusBadgeProps) {
    const variant = (status?.toLowerCase() as VariantProps<typeof statusVariants>['variant']) ?? 'saved';
    const displayLabel = label ?? status?.charAt(0).toUpperCase() + status?.slice(1);

    return (
        <span className={statusVariants({ variant, className })}>
            {displayLabel}
        </span>
    );
}
