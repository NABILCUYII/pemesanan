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
    Info,
    BarChart,
    Check,
    History,
    FileText,
    TrendingUp,
    AlertTriangle,
    ChevronDown
} from 'lucide-vue-next';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import AppLogo from './AppLogo.vue';
import { computed, ref, onMounted, onUnmounted } from 'vue';

const page = usePage();
const user = page.props.auth.user;

const stokMenipisCount = ref(0);
const pendingApprovalCount = ref(0);
const pendingInventarisCount = ref(0);
let refreshInterval: number | null = null;

const fetchCounts = async () => {
    try {
        const res = await fetch('/api/barang/stok-menipis-count');
        const data = await res.json();
        stokMenipisCount.value = data.count;
    } catch (e) {
        stokMenipisCount.value = 0;
    }

    try {
        const res = await fetch('/api/permintaan/pending-count');
        const data = await res.json();
        pendingApprovalCount.value = data.count;
    } catch (e) {
        pendingApprovalCount.value = 0;
    }

    // Only fetch inventaris pending count for admin
    if (user?.role === 'admin') {
        try {
            const res = await fetch('/api/inventaris/pending-count');
            const data = await res.json();
            pendingInventarisCount.value = data.count;
        } catch (e) {
            pendingInventarisCount.value = 0;
        }
    } else {
        pendingInventarisCount.value = 0;
    }
};

onMounted(async () => {
    await fetchCounts();
    
    // Refresh counts every 30 seconds
    refreshInterval = setInterval(fetchCounts, 30000);
});

onUnmounted(() => {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});

const mainNavItems = computed(() => [
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
        title: 'Inventaris',
        href: route('inventaris.index'),
        icon: Package,
        badge: user?.role === 'admin' ? pendingInventarisCount.value : undefined,
    },

    {
        title: 'Persetujuan',
        href: route('permintaan.approval'),
        icon: Check,
        adminOnly: true,
    },

    {
        title: 'Barang',
        icon: Package,
       
        isDropdown: true,
        dropdownItems: [
            {
                title: 'Daftar Semua Barang',
                href: route('barang.index'),
                icon: Package,
                
            },
            {
                title: 'Aset',
                href: route('barang.aset'),
                icon: Package,
              
            },
            {
                title: 'Barang Permintaan',
                href: route('barang.permintaan'),
                icon: Package,
               
            },

            {
        title: 'Barang Rusak',
        href: route('barang-rusak.index'),
        icon: AlertTriangle,
        adminOnly: true,
    },
        ],
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

    {
        title: 'Riwayat',
        href: route('riwayat.index'),
        icon: History,
    },
]);

const filteredMainNavItems = computed(() => {
    return mainNavItems.value.map(item => {
        // Create a new object to avoid mutating the original
        const newItem = { ...item };
        
        // Update badge values reactively
        if (item.title === 'Stok Barang') {
            newItem.badge = stokMenipisCount.value;
        } else if (item.title === 'Persetujuan') {
            newItem.badge = pendingApprovalCount.value;
        } else if (item.title === 'Inventaris') {
            newItem.badge = user?.role === 'admin' ? pendingInventarisCount.value : undefined;
        }
        
        return newItem;
    }).filter(item => {
        if (!item.adminOnly) return true;
        return user?.role === 'admin';
    });
});

const footerNavItems: NavItem[] = [
    {
        title: 'Tentang',
        href: route('tentang'),
        icon: Info,
    },
    {
        title: 'Bantuan',
        href: route('bantuan'),
        icon: HelpCircle,
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
            <NavUser :user="user" />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
