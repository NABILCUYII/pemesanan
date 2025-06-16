<script setup lang="ts">
import { SidebarMenu, SidebarMenuItem, SidebarMenuButton } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';

interface Props {
    items: NavItem[];
}

defineProps<Props>();
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem v-for="item in items" :key="item.href">
            <SidebarMenuButton :href="item.href" :active="route().current(item.href)" as-child>
                <Link 
                    :href="item.href" 
                    class="flex items-center gap-2 text-[#1E293B] hover:text-[#2563EB] hover:bg-[#F1F5F9] transition-colors"
                >
                    <component :is="item.icon" class="h-5 w-5" />
                    <span>{{ item.title }}</span>
                </Link>
            </SidebarMenuButton>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
