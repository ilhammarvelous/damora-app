import { Head } from '@inertiajs/react';
import { useState } from 'react';

import { PageHeader } from '@/components/shared/page-header';
import { SimplePagination } from '@/components/shared/simple-pagination';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { LocationFormDialog } from '@/features/locations/location-form-dialog';
import { LocationStatusDialog } from '@/features/locations/location-status-dialog';
import { LocationTable } from '@/features/locations/location-table';
import { locationRoutes } from '@/features/locations/routes';
import { useTableFilters } from '@/hooks/use-table-filters';
import AppLayout from '@/layouts/app-layout';
import { PlusIcon } from '@/lib/icons';

export default function LocationsIndex({ locations, filters, defaults }) {
    const [values, setFilter] = useTableFilters(locationRoutes.index, {
        search: filters.search ?? '',
        status: filters.status ?? 'semua',
    });

    const [form, setForm] = useState({ open: false, location: null });
    const [statusTarget, setStatusTarget] = useState(null);

    const hasFilter = values.search !== '' || values.status !== 'semua';
    const emptyMessage = hasFilter
        ? 'Tidak ada lokasi yang cocok dengan pencarian.'
        : 'Belum ada lokasi kantor. Klik "Tambah Kantor" untuk memulai.';

    return (
        <>
            <Head title="Lokasi Kantor" />

            <PageHeader
                title="Lokasi Kantor"
                description="Titik kantor yang dipakai untuk memeriksa lokasi absensi karyawan kantor."
                actions={
                    <Button onClick={() => setForm({ open: true, location: null })}>
                        <PlusIcon />
                        Tambah Kantor
                    </Button>
                }
            />

            <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <Input
                    value={values.search}
                    onChange={(e) => setFilter('search', e.target.value)}
                    placeholder="Cari nama atau alamat..."
                    className="sm:max-w-sm"
                    aria-label="Cari lokasi kantor"
                />
                <Tabs value={values.status} onValueChange={(value) => setFilter('status', value)}>
                    <TabsList>
                        <TabsTrigger value="semua">Semua</TabsTrigger>
                        <TabsTrigger value="aktif">Aktif</TabsTrigger>
                        <TabsTrigger value="nonaktif">Nonaktif</TabsTrigger>
                    </TabsList>
                </Tabs>
            </div>

            <LocationTable
                locations={locations.data}
                emptyMessage={emptyMessage}
                onEdit={(location) => setForm({ open: true, location })}
                onToggleStatus={setStatusTarget}
            />

            <SimplePagination meta={locations.meta} links={locations.links} />

            <LocationFormDialog
                open={form.open}
                onOpenChange={(open) => setForm((current) => ({ ...current, open }))}
                location={form.location}
                defaults={defaults}
            />

            <LocationStatusDialog
                location={statusTarget}
                open={statusTarget !== null}
                onOpenChange={(open) => !open && setStatusTarget(null)}
            />
        </>
    );
}

// Layout menetap: sidebar tidak dimuat ulang saat berpindah halaman
LocationsIndex.layout = (page) => <AppLayout title="Lokasi Kantor">{page}</AppLayout>;
