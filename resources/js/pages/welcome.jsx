import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { House } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';

export default function Welcome() {
    return (
        <div className="p-8">
            <h1 className="mb-4 text-3xl font-semibold">DAMORA HR</h1>
            <Button>Tombol uji ada ya guys</Button>
            <Card className="mt-4 p-4">
                <p className="text-xl">Ini adalah contoh penggunaan komponen Card.</p>
            </Card>
            <House className="mt-4 h-6 w-6 text-blue-500" />
        </div>
    );
}

Welcome.layout = (page) => <AppLayout title="Dashboard">{page}</AppLayout>;
