import { Link, usePage } from '@inertiajs/react';

import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarRail,
} from '@/components/ui/sidebar';
import { navigation } from '@/config/navigation';
import { BuildingIcon } from '@/lib/icons';

export function AppSidebar(props) {
    const { url, props: pageProps } = usePage();
    const user = pageProps.auth.user;

    // Buang query string (?search=...) agar menu tetap dikenali aktif
    const path = url.split('?')[0];
    const isActive = (href) => (href === '/' ? path === '/' : path === href || path.startsWith(`${href}/`));

    const groups = navigation.filter((group) => !group.ownerOnly || user.is_owner);

    return (
        <Sidebar collapsible="icon" {...props}>
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/">
                                <div className="flex aspect-square size-8 items-center justify-center rounded-lg bg-sidebar-primary text-sidebar-primary-foreground">
                                    <BuildingIcon className="size-4" />
                                </div>
                                <div className="grid flex-1 text-left text-sm leading-tight">
                                    <span className="truncate font-semibold">DAMORA HR</span>
                                    <span className="truncate text-xs text-sidebar-foreground/70">
                                        {user.company.name}
                                    </span>
                                </div>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                {groups.map((group) => (
                    <SidebarGroup key={group.label}>
                        <SidebarGroupLabel>{group.label}</SidebarGroupLabel>
                        <SidebarGroupContent>
                            <SidebarMenu>
                                {group.items.map((item) => (
                                    <SidebarMenuItem key={item.href}>
                                        <SidebarMenuButton asChild isActive={isActive(item.href)} tooltip={item.title}>
                                            <Link href={item.href}>
                                                <item.icon />
                                                <span>{item.title}</span>
                                            </Link>
                                        </SidebarMenuButton>
                                    </SidebarMenuItem>
                                ))}
                            </SidebarMenu>
                        </SidebarGroupContent>
                    </SidebarGroup>
                ))}
            </SidebarContent>

            <SidebarFooter>
                <div className="px-2 py-1.5 text-xs group-data-[collapsible=icon]:hidden">
                    <p className="truncate font-medium">{user.email}</p>
                    <p className="text-sidebar-foreground/70">{user.is_owner ? 'Owner' : 'Karyawan'}</p>
                </div>
            </SidebarFooter>
            <SidebarRail />
        </Sidebar>
    );
}
