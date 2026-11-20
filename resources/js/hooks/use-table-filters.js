import { router } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';

const EMPTY_VALUES = ['', null, undefined, 'semua'];

export function useTableFilters(url, initial, { delay = 350 } = {}) {
    const [values, setValues] = useState(initial);
    const isFirstRender = useRef(true);

    useEffect(() => {
        // Lewati render pertama agar tidak memuat ulang halaman yang baru dibuka
        if (isFirstRender.current) {
            isFirstRender.current = false;
            return;
        }

        const timer = setTimeout(() => {
            const params = Object.fromEntries(
                Object.entries(values).filter(([, value]) => !EMPTY_VALUES.includes(value)),
            );

            router.get(url, params, { preserveState: true, preserveScroll: true, replace: true });
        }, delay);

        return () => clearTimeout(timer);
    }, [values]);

    const setFilter = (key, value) => setValues((current) => ({ ...current, [key]: value }));

    return [values, setFilter];
}
