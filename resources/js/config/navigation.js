import { DashboardIcon, MapPinIcon } from '@/lib/icons';

export const navigation = [
    {
        label: 'Utama',
        items: [{ title: 'Dashboard', href: '/', icon: DashboardIcon }],
    },
    {
        label: 'Pengaturan',
        ownerOnly: true,
        items: [{ title: 'Lokasi Kantor', href: '/lokasi-kantor', icon: MapPinIcon }],
    },
];
