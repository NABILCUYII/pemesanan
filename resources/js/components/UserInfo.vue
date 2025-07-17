<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';
import { computed } from 'vue';
import { User as UserIcon } from 'lucide-vue-next';

interface Props {
    user: User;
    showEmail?: boolean;
}

const props = withDefaults(defineProps<Props>(), { 
    showEmail: false,
});

const { getInitials } = useInitials();

// Compute whether we should show the avatar image
const showAvatar = computed(() => props.user.photo_url && props.user.photo_url !== '');
</script>

<template>
    <Link :href="route('profile.edit')" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
        <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
            <AvatarImage v-if="showAvatar" :src="user.photo_url!" :alt="user.name" />
            <AvatarFallback class="rounded-lg text-black dark:text-white flex items-center justify-center">
                <UserIcon class="w-4 h-4" />
                <span v-if="!showAvatar" class="ml-1">{{ getInitials(user.name) }}</span>
            </AvatarFallback>
        </Avatar>

        <div class="grid flex-1 text-left text-sm leading-tight">
            <span class="truncate font-medium">{{ user.name }}</span>
            <span v-if="showEmail" class="truncate text-xs text-muted-foreground">{{ user.email }}</span>
        </div>
    </Link>
</template>

