import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

import { Alert, AlertDescription } from '@/components/ui/alert';

export function FlashMessage({ duration = 5000 }) {
    const { flash } = usePage().props;
    const [message, setMessage] = useState(null);

    useEffect(() => {
        const next = flash?.error
            ? { type: 'error', text: flash.error }
            : flash?.success
              ? { type: 'success', text: flash.success }
              : null;

        setMessage(next);
        if (!next) return;

        const timer = setTimeout(() => setMessage(null), duration);
        return () => clearTimeout(timer);
    }, [flash]);

    if (!message) return null;

    return (
        <Alert variant={message.type === 'error' ? 'destructive' : 'default'} role="status">
            <AlertDescription>{message.text}</AlertDescription>
        </Alert>
    );
}
