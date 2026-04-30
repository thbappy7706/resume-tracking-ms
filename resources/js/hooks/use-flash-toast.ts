import { router } from '@inertiajs/react';
import { useEffect, useRef } from 'react';
import { toast } from 'sonner';

export function useFlashToast(): void {
    const handlerRef = useRef<((event: Event) => void) | null>(null);

    useEffect(() => {
        if (handlerRef.current) {
            return;
        }

        handlerRef.current = (event: Event) => {
            const detail = (event as CustomEvent).detail;

            if (detail?.flash?.success) {
                toast.success(detail.flash.success);
            }

            if (detail?.flash?.error) {
                toast.error(detail.flash.error);
            }

            if (detail?.flash?.info) {
                toast.info(detail.flash.info);
            }

            if (detail?.flash?.warning) {
                toast.warning(detail.flash.warning);
            }
        };

        router.on('flash', handlerRef.current);

        return () => {
            handlerRef.current = null;
        };
    }, []);
}
