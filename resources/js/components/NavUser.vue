<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { type User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ChevronsUpDown, User as UserIcon } from 'lucide-vue-next';
import UserMenuContent from './UserMenuContent.vue';


const page = usePage();
const user = page.props.auth.user as User;


interface Props {
    user: User;
}

const { isMobile, state } = useSidebar();

defineProps<Props>();
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton
                        size="lg"
                        class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground flex items-center gap-2"
                    >
                        <!-- User photo profile like in UserMenuContent -->
                        <template v-if="user?.photo">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-200 overflow-hidden">
                                <img
                                    :src="user.photo.startsWith('http') ? user.photo : `/storage/${user.photo}`"
                                    alt="User avatar"
                                    class="w-full h-full object-cover"
                                    @error="(e) => { e.target.style.display = 'none'; e.target.parentNode.innerHTML = `<span class='text-gray-600 font-semibold'>${user?.name?.charAt(0) || '?'}</span>` }"
                                />
                            </span>
                        </template>
                        <template v-else>
                            <div class="w-9 h-9 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-semibold">
                                {{ user?.name?.charAt(0) || '?' }}
                            </div>
                        </template>
            
                        <span class="truncate max-w-[7rem] text-sm font-medium">{{ user.name }}</span>
                        <ChevronsUpDown class="ml-auto size-4" />
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    class="min-w-56 rounded-lg"
                    :side="isMobile ? 'bottom' : state === 'collapsed' ? 'left' : 'bottom'"
                    align="end"
                    :side-offset="4"
                >
                    <UserMenuContent :user="user" />
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
