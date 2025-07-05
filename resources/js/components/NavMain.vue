<script setup lang="ts">
import { SidebarMenu, SidebarMenuItem, SidebarMenuButton } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ChevronDown } from 'lucide-vue-next';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

interface Props {
    items: NavItem[];
}

defineProps<Props>();
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem v-for="item in items" :key="item.title">
            <!-- Dropdown Menu -->
            <template v-if="item.isDropdown">
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <SidebarMenuButton class="w-full justify-between">
                            <div class="flex items-center gap-2 text-[#1E293B] hover:text-[#2563EB] hover:bg-[#F1F5F9] transition-colors">
                                <component :is="item.icon" class="h-5 w-5" />
                                <span>{{ item.title }}</span>
                                <span v-if="item.badge && Number(item.badge) > 0" class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                    {{ item.badge }}
                                </span>
                            </div>
                            <ChevronDown class="h-4 w-4" />
                        </SidebarMenuButton>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent class="w-56" align="start">
                        <template v-for="dropdownItem in item.dropdownItems" :key="dropdownItem.title">
                            <DropdownMenuItem v-if="dropdownItem.href" as-child>
                                <Link 
                                    :href="dropdownItem.href" 
                                    class="flex items-center gap-2 w-full"
                                >
                                    <component :is="dropdownItem.icon" class="h-4 w-4" />
                                    <span>{{ dropdownItem.title }}</span>
                                </Link>
                            </DropdownMenuItem>
                        </template>
                    </DropdownMenuContent>
                </DropdownMenu>
            </template>
            
            <!-- Regular Menu Item -->
            <template v-else-if="item.href">
                <SidebarMenuButton :href="item.href" :active="route().current(item.href)" as-child>
                    <Link 
                        :href="item.href" 
                        class="flex items-center gap-2 text-[#1E293B] hover:text-[#2563EB] hover:bg-[#F1F5F9] transition-colors"
                    >
                        <component :is="item.icon" class="h-5 w-5" />
                        <span>{{ item.title }}</span>
                        <span v-if="item.badge && Number(item.badge) > 0" class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                            {{ item.badge }}
                        </span>
                    </Link>
                </SidebarMenuButton>
            </template>
        </SidebarMenuItem>
    </SidebarMenu>
</template>

