import { router } from '@inertiajs/react';

import { Button } from '@/components/ui/button';

export function SimplePagination({ meta, links }) {
    const go = (url) => url && router.get(url, {}, { preserveScroll: true, preserveState: true });

    return (
        <div className="flex flex-col items-center justify-between gap-3 text-sm sm:flex-row">
            <p className="text-muted-foreground">
                {meta.total > 0
                    ? `Menampilkan ${meta.from}–${meta.to} dari ${meta.total} data`
                    : 'Tidak ada data'}
            </p>
            <div className="flex items-center gap-2">
                <Button variant="outline" size="sm" disabled={!links.prev} onClick={() => go(links.prev)}>
                    Sebelumnya
                </Button>
                <span className="text-muted-foreground">
                    Halaman {meta.current_page} dari {meta.last_page}
                </span>
                <Button variant="outline" size="sm" disabled={!links.next} onClick={() => go(links.next)}>
                    Berikutnya
                </Button>
            </div>
        </div>
    );
}
