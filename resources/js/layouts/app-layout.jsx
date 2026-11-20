import { Head } from '@inertiajs/react';

import { FlashMessage } from '@/components/shared/flash-message';
import { Separator } from '@/components/ui/separator';
import { SidebarInset, SidebarProvider, SidebarTrigger } from '@/components/ui/sidebar';
import { TooltipProvider } from '@/components/ui/tooltip';
import { AppSidebar } from '@/layouts/app-sidebar';

export default function AppLayout({ title, children }) {
    return (
        <TooltipProvider delayDuration={200}>
            <SidebarProvider>
                <Head title={title} />
                <AppSidebar />
                <SidebarInset>
                    <header className="flex h-14 shrink-0 items-center gap-2 border-b px-4">
                        <SidebarTrigger className="-ml-1" />
                        <Separator orientation="vertical" className="mr-2 h-4" />
                        <span className="text-sm font-medium">{title}</span>
                    </header>
                    <main className="flex flex-1 flex-col gap-4 p-4 md:p-6">
                        <FlashMessage />
                        {children}
                    </main>
                </SidebarInset>
            </SidebarProvider>
        </TooltipProvider>
    );
}
