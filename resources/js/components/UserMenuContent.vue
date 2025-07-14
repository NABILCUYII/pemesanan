<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { LogOut, User as UserIcon } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.auth.user as User;


interface Props {
    user: User;
}

const handleLogout = () => {
    router.flushAll();
};

defineProps<Props>();
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
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
            <UserInfo :user="user" :show-email="true" />  
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem :as-child="true">
            <Link :href="route('profile.edit')" class="flex items-center">
                <UserIcon class="mr-2 h-4 w-4" />
                Edit Profile
            </Link>
        </DropdownMenuItem>
        <DropdownMenuSeparator />
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full" method="post" :href="route('logout')" @click="handleLogout" as="button">
                <LogOut class="mr-2 h-4 w-4" />
                Log out
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
</template>
