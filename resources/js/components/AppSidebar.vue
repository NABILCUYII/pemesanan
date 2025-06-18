<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { 
    LayoutGrid, 
    Package, 
    ShoppingCart, 
    ClipboardList, 
    Users, 
    Settings, 
    HelpCircle,
    BarChart,
    Check,
    History,
    FileText
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

const page = usePage();
const user = page.props.auth.user;

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
        icon: LayoutGrid,
    },
    {
        title: 'Permintaan',
        href: route('permintaan.index'),
        icon: ShoppingCart,
    },
    {
        title: 'Peminjaman',
        href: route('peminjaman.index'),
        icon: ClipboardList,
    },
    {
        title: 'Riwayat',
        href: route('riwayat.index'),
        icon: History,
    },
    {
        title: 'Approval',
        href: route('permintaan.approval'),
        icon: Check,
        adminOnly: true,
    },
    {
        title: 'Barang',
        href: route('barang.index'),
        icon: Package,
        adminOnly: true,
    },
    {
        title: 'Stok Barang',
        href: route('barang.stok'),
        icon: Package,
        adminOnly: true,
    },
    {
        title: 'Pengguna',
        href: route('users.index'),
        icon: Users,
        adminOnly: true,
    },
    {
        title: 'Laporan',
        href: route('laporan.index'),
        icon: FileText,
        adminOnly: true,
    },
];

const filteredMainNavItems = computed(() => {
    return mainNavItems.filter(item => {
        if (!item.adminOnly) return true;
        return user?.role === 'admin';
    });
});

const footerNavItems: NavItem[] = [
    {
        title: 'Bantuan',
        href: '#',
        icon: HelpCircle,
    },
    {
        title: 'Pengaturan',
        href: route('profile.edit'),
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
            <NavMain :items="filteredMainNavItems" />
        </SidebarContent>

        <SidebarFooter class="border-t border-[#E2E8F0]">
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

