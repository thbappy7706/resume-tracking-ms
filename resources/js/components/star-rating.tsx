import { Star } from 'lucide-react';

interface StarRatingProps {
    value: number;
    max?: number;
    onChange?: (value: number) => void;
    readonly?: boolean;
    size?: 'sm' | 'md' | 'lg';
}

const sizeClasses = {
    sm: 'h-3 w-3',
    md: 'h-4 w-4',
    lg: 'h-5 w-5',
};

export function StarRating({ value, max = 5, onChange, readonly = false, size = 'md' }: StarRatingProps) {
    const handleClick = (index: number) => {
        if (!readonly && onChange) {
            onChange(index);
        }
    };

    return (
        <div className="flex items-center gap-0.5">
            {Array.from({ length: max }, (_, i) => i + 1).map((index) => (
                <button
                    key={index}
                    type="button"
                    disabled={readonly}
                    onClick={() => handleClick(index)}
                    className={`${readonly ? 'cursor-default' : 'cursor-pointer'} text-amber-400 hover:text-amber-500 disabled:opacity-100`}
                >
                    <Star
                        className={sizeClasses[size]}
                        fill={index <= value ? 'currentColor' : 'none'}
                    />
                </button>
            ))}
        </div>
    );
}
