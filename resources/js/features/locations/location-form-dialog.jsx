import { useForm } from '@inertiajs/react';
import { lazy, Suspense, useEffect, useState } from 'react';

import { FormField } from '@/components/shared/form-field';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Skeleton } from '@/components/ui/skeleton';
import { Textarea } from '@/components/ui/textarea';
import { locationRoutes } from '@/features/locations/routes';
import { LocateIcon, SpinnerIcon } from '@/lib/icons';

// Peta dimuat saat dialog dibuka, bukan saat halaman dimuat
const MapPicker = lazy(() => import('@/components/shared/map-picker'));

// Harus sama dengan batas di LocationRequest (backend)
const MIN_RADIUS_M = 10;
const MAX_RADIUS_M = 1000;

const blankForm = (defaults) => ({
    name: '',
    address: '',
    latitude: '',
    longitude: '',
    radius_m: defaults?.radius_m ?? 50,
});

const formFrom = (location) => ({
    name: location.name,
    address: location.address ?? '',
    latitude: location.latitude,
    longitude: location.longitude,
    radius_m: location.radius_m,
});

export function LocationFormDialog({ open, onOpenChange, location = null, defaults }) {
    const isEdit = location !== null;
    const form = useForm(blankForm(defaults));
    const [locating, setLocating] = useState(false);
    const [locateError, setLocateError] = useState('');

    // Setiap dialog dibuka: isi ulang data dan bersihkan galat sebelumnya
    useEffect(() => {
        if (!open) return;

        form.clearErrors();
        form.setData(isEdit ? formFrom(location) : blankForm(defaults));
        setLocateError('');
    }, [open, location?.id]);

    const submit = (event) => {
        event.preventDefault();

        const options = { preserveScroll: true, onSuccess: () => onOpenChange(false) };

        if (isEdit) {
            form.put(locationRoutes.update(location.id), options);
        } else {
            form.post(locationRoutes.store, options);
        }
    };

    const setCoordinates = ({ latitude, longitude }) => form.setData((data) => ({ ...data, latitude, longitude }));

    const handleUseDevicePosition = () => {
        if (!navigator.geolocation) {
            setLocateError('Peramban ini tidak mendukung penentuan lokasi.');
            return;
        }

        setLocating(true);
        setLocateError('');

        navigator.geolocation.getCurrentPosition(
            ({ coords }) => {
                setCoordinates({
                    latitude: coords.latitude.toFixed(6),
                    longitude: coords.longitude.toFixed(6),
                });
                setLocating(false);
            },
            () => {
                setLocateError('Lokasi tidak dapat dibaca. Pastikan izin lokasi diaktifkan.');
                setLocating(false);
            },
            { enableHighAccuracy: true, timeout: 10000 },
        );
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            {/* Header & footer tetap terlihat; hanya bagian isi yang bisa di-scroll */}
            <DialogContent className="flex max-h-[90vh] flex-col gap-0 p-0 sm:max-w-4xl">
                <DialogHeader className="border-b px-6 py-4">
                    <DialogTitle>{isEdit ? 'Ubah lokasi kantor' : 'Tambah lokasi kantor'}</DialogTitle>
                    <DialogDescription>
                        Titik ini dipakai untuk memeriksa apakah karyawan berada di area kantor saat absen.
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={submit} className="flex min-h-0 flex-1 flex-col">
                    <div className="grid flex-1 gap-6 overflow-y-auto px-6 py-5 md:grid-cols-2 md:gap-8">
                        {/* Kolom kiri: data kantor dan koordinat */}
                        <div className="grid content-start gap-4">
                            <FormField id="name" label="Nama kantor" error={form.errors.name}>
                                <Input
                                    id="name"
                                    value={form.data.name}
                                    onChange={(e) => form.setData('name', e.target.value)}
                                    placeholder="Contoh: Kantor Pusat Surabaya"
                                    autoFocus
                                />
                            </FormField>

                            <FormField id="address" label="Alamat" error={form.errors.address}>
                                <Textarea
                                    id="address"
                                    value={form.data.address}
                                    onChange={(e) => form.setData('address', e.target.value)}
                                    rows={3}
                                />
                            </FormField>

                            <FormField
                                id="radius_m"
                                label="Radius absen (meter)"
                                error={form.errors.radius_m}
                                hint={`Antara ${MIN_RADIUS_M} dan ${MAX_RADIUS_M} meter dari titik kantor.`}
                            >
                                <Input
                                    id="radius_m"
                                    type="number"
                                    min={MIN_RADIUS_M}
                                    max={MAX_RADIUS_M}
                                    value={form.data.radius_m}
                                    onChange={(e) => form.setData('radius_m', e.target.value)}
                                />
                            </FormField>

                            <div className="grid gap-4 sm:grid-cols-2">
                                <FormField id="latitude" label="Lintang (latitude)" error={form.errors.latitude}>
                                    <Input
                                        id="latitude"
                                        type="number"
                                        step="any"
                                        inputMode="decimal"
                                        value={form.data.latitude}
                                        onChange={(e) => form.setData('latitude', e.target.value)}
                                        placeholder="-7.257472"
                                    />
                                </FormField>
                                <FormField id="longitude" label="Bujur (longitude)" error={form.errors.longitude}>
                                    <Input
                                        id="longitude"
                                        type="number"
                                        step="any"
                                        inputMode="decimal"
                                        value={form.data.longitude}
                                        onChange={(e) => form.setData('longitude', e.target.value)}
                                        placeholder="112.752088"
                                    />
                                </FormField>
                            </div>
                        </div>

                        {/* Kolom kanan: peta */}
                        <div className="grid content-start gap-2">
                            <div className="flex flex-wrap items-center justify-between gap-2">
                                <p className="text-sm font-medium">Pilih lokasi di peta</p>
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    onClick={handleUseDevicePosition}
                                    disabled={locating}
                                >
                                    {locating ? <SpinnerIcon className="animate-spin" /> : <LocateIcon />}
                                    Gunakan lokasi saya
                                </Button>
                            </div>

                            {locateError && <p className="text-sm text-destructive">{locateError}</p>}

                            <Suspense fallback={<Skeleton className="h-72 w-full rounded-lg md:h-96" />}>
                                <MapPicker
                                    latitude={form.data.latitude}
                                    longitude={form.data.longitude}
                                    radius={form.data.radius_m}
                                    onChange={setCoordinates}
                                />
                            </Suspense>

                            <p className="text-sm text-muted-foreground">
                                Klik peta atau geser pin untuk memilih titik. Koordinat terisi otomatis dan juga
                                bisa diketik.
                            </p>
                        </div>
                    </div>

                    <DialogFooter className="border-t px-6 py-4">
                        <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
                            Batal
                        </Button>
                        <Button type="submit" disabled={form.processing}>
                            {form.processing && <SpinnerIcon className="animate-spin" />}
                            Simpan Kantor
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
