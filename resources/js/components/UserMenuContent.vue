<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { LogOut, User as UserIcon } from 'lucide-vue-next';

interface Props {
    user: User;
}

const props = defineProps<Props>();

const handleLogout = () => {
    router.flushAll();
};

// Remove duplicate defineProps
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="props.user" :show-email="true" :key="props.user.photo_url" />  
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
