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
    FileText,
    TrendingUp,
    AlertTriangle,
    User
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed, ref, onMounted } from 'vue';

const page = usePage();
const user = page.props.auth.user;

const stokMenipisCount = ref(0);

onMounted(async () => {
    try {
        const res = await fetch('/api/barang/stok-menipis-count');
        const data = await res.json();
        stokMenipisCount.value = data.count;
    } catch (e) {
        stokMenipisCount.value = 0;
    }
});

const mainNavItems = computed<NavItem[]>(() => [
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
        title: 'Persetujuan',
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
        title: 'Barang Rusak',
        href: route('barang-rusak.index'),
        icon: AlertTriangle,
        adminOnly: true,
    },
    {
        title: 'Stok Barang',
        href: route('barang.stok'),
        icon: Package,
        adminOnly: true,
        badge: stokMenipisCount.value,
    },
    {
        title: 'Riwayat Stok',
        href: route('stok-log.index'),
        icon: TrendingUp,
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
]);

const filteredMainNavItems = computed(() => {
    return mainNavItems.value.filter(item => {
        if (!item.adminOnly) return true;
        return user?.role === 'admin';
    });
});

const footerNavItems: NavItem[] = [
    {
        title: 'Profil',
        href: route('profile.edit'),
        icon: User,
    },
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
            <NavMain :items="filteredMainNavItems" />
        </SidebarContent>

        <SidebarFooter class="border-t border-[#E2E8F0]">
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

