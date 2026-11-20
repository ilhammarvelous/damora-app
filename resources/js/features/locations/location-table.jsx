import { IconButton } from '@/components/shared/icon-button';
import { StatusBadge } from '@/components/shared/status-badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { PencilIcon, PowerIcon, PowerOffIcon } from '@/lib/icons';

export function LocationTable({ locations, emptyMessage, onEdit, onToggleStatus }) {
    return (
        <div className="rounded-lg border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Nama kantor</TableHead>
                        <TableHead className="hidden md:table-cell">Koordinat</TableHead>
                        <TableHead className="text-right">Radius</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead className="text-right">Aksi</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {locations.length === 0 ? (
                        <TableRow>
                            <TableCell colSpan={5} className="h-24 text-center text-muted-foreground">
                                {emptyMessage}
                            </TableCell>
                        </TableRow>
                    ) : (
                        locations.map((location) => (
                            <TableRow key={location.id}>
                                <TableCell>
                                    <div className="font-medium">{location.name}</div>
                                    {location.address && (
                                        <div className="max-w-xs truncate text-xs text-muted-foreground">
                                            {location.address}
                                        </div>
                                    )}
                                </TableCell>
                                <TableCell className="hidden font-mono text-xs md:table-cell">
                                    {location.latitude.toFixed(6)}, {location.longitude.toFixed(6)}
                                </TableCell>
                                <TableCell className="text-right tabular-nums">{location.radius_m} m</TableCell>
                                <TableCell>
                                    <StatusBadge active={location.is_active} />
                                </TableCell>
                                <TableCell>
                                    <div className="flex justify-end gap-1">
                                        <IconButton label="Ubah" icon={PencilIcon} onClick={() => onEdit(location)} />
                                        <IconButton
                                            label={location.is_active ? 'Nonaktifkan' : 'Aktifkan'}
                                            icon={location.is_active ? PowerOffIcon : PowerIcon}
                                            onClick={() => onToggleStatus(location)}
                                        />
                                    </div>
                                </TableCell>
                            </TableRow>
                        ))
                    )}
                </TableBody>
            </Table>
        </div>
    );
}
