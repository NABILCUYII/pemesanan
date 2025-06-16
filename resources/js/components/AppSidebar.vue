<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { 
    LayoutGrid, 
    Package, 
    ShoppingCart, 
    ClipboardList, 
    Users, 
    Settings, 
    HelpCircle,
    BarChart
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
        icon: LayoutGrid,
    },
    {
        title: 'Pemesanan',
        href: '#',
        icon: ShoppingCart,
    },
    {
        title: 'Barang',
        href: '#',
        icon: Package,
    },
    {
        title: 'Riwayat',
        href: '#',
        icon: ClipboardList,
    },
    {
        title: 'Pengguna',
        href: route('users.index'),
        icon: Users,
    },
    {
        title: 'Laporan',
        href: '#',
        icon: BarChart,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Bantuan',
        href: '#',
        icon: HelpCircle,
    },
    {
        title: 'Pengaturan',
        href: '#',
        icon: Settings,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" class="bg-white border-r border-[#E2E8F0]">
        <SidebarHeader class="border-b border-[#E2E8F0]">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')" class="text-[#2563EB]">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter class="border-t border-[#E2E8F0]">
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
