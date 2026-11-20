import { Badge } from '@/components/ui/badge';

export function StatusBadge({ active }) {
    return <Badge variant={active ? 'default' : 'secondary'}>{active ? 'Aktif' : 'Nonaktif'}</Badge>;
}
