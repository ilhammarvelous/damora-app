import { router } from '@inertiajs/react';
import { useState } from 'react';

import { ConfirmDialog } from '@/components/shared/confirm-dialog';
import { locationRoutes } from '@/features/locations/routes';

export function LocationStatusDialog({ location, open, onOpenChange }) {
    const [processing, setProcessing] = useState(false);

    if (!location) return null;

    const activating = !location.is_active;

    const confirm = () =>
        router.patch(
            locationRoutes.status(location.id),
            { is_active: activating },
            {
                preserveScroll: true,
                onStart: () => setProcessing(true),
                onFinish: () => setProcessing(false),
                onSuccess: () => onOpenChange(false),
            },
        );

    return (
        <ConfirmDialog
            open={open}
            onOpenChange={onOpenChange}
            processing={processing}
            destructive={!activating}
            title={activating ? 'Aktifkan lokasi kantor?' : 'Nonaktifkan lokasi kantor?'}
            description={
                activating
                    ? `"${location.name}" akan bisa dipakai lagi untuk absensi karyawan kantor.`
                    : `"${location.name}" tidak akan bisa dipakai untuk absensi. Riwayat absensi yang sudah tercatat tetap aman dan lokasi bisa diaktifkan kembali kapan saja.`
            }
            confirmLabel={activating ? 'Ya, aktifkan' : 'Ya, nonaktifkan'}
            onConfirm={confirm}
        />
    );
}
