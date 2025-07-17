<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { User as UserIcon } from 'lucide-vue-next';
import { getInitials } from '@/composables/useInitials';
import { computed } from 'vue';
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar';

interface User {
    id: number;
    name: string;
    email: string;
    role: string | { role: string };
    photo?: string; // gunakan photo, bukan photo_url
}

interface Props {
    user: User;
}

const props = defineProps<Props>();
const user = props.user;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Users', href: route('users.index') },
    { title: 'Edit User', href: route('users.edit', props.user.id) },
];

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: typeof props.user.role === 'object' && props.user.role !== null ? props.user.role.role : (props.user.role || ''),
});

const submit = () => {
    form.put(route('users.update', props.user.id));
};

const goBack = () => {
    router.visit(route('users.index'));
};

// Ensure AvatarImage always receives a string (not undefined)
const avatarSrc = computed(() => user.photo ?? '');
const showAvatar = computed(() => !!user.photo && user.photo!== '');
</script>

<template>
    <Head title="Edit User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl py-10">
            <!-- FOTO PROFIL -->
            <div class="flex flex-col items-center">
                <template v-if="user?.photo">
                    <span class="inline-flex items-center justify-center w-56 h-56 rounded-full bg-gray-200 overflow-hidden shadow-lg border-2 border-gray-200 mb-8">
                        <img
                            :src="user.photo.startsWith('http') ? user.photo : `/storage/${user.photo}`"
                            alt="User avatar"
                            class="w-full h-full object-cover"
                        />
                    </span>
                </template>
                <template v-else>
                    <div class="w-56 h-56 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-semibold text-8xl shadow-lg border-2 border-gray-200 mb-8">
                        {{ user?.name?.charAt(0) || '?' }}
                    </div>
                </template>
            </div>
            <!-- FORM EDIT USER -->
            <form @submit.prevent="submit" class="space-y-8 bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <div>
                    <Label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Name</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1"
                        required
                        autocomplete="off"
                        placeholder="Enter user name"
                    />
                    <div v-if="form.errors.name" class="mt-1 text-xs text-red-600">
                        {{ form.errors.name }}
                    </div>
                </div>

                <div>
                    <Label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1"
                        required
                        autocomplete="off"
                        placeholder="Enter user email"
                    />
                    <div v-if="form.errors.email" class="mt-1 text-xs text-red-600">
                        {{ form.errors.email }}
                    </div>
                </div>

                <div class="flex flex-col">
                    <Label for="role" class="text-sm font-semibold text-gray-600 mb-1">
                        Role saat ini: {{ typeof form.role === 'string' && form.role ? form.role.charAt(0).toUpperCase() + form.role.slice(1) : '-' }}
                    </Label>
                    <select
                        id="role"
                        name="role"
                        v-model="form.role"
                        class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                        :required="!form.role"
                    >
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                        <option value="newUser">New User</option>
                    </select>
                    <div v-if="form.errors.role" class="mt-1 text-xs text-red-600">
                        {{ form.errors.role }}
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <Button
                        type="button"
                        @click="goBack"
                        class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 font-medium shadow-sm hover:bg-gray-50 transition"
                    >
                        Cancel
                    </Button>
                    <Button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-indigo-600 to-blue-500 py-2 px-6 text-sm font-semibold text-white shadow-md hover:from-indigo-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
                        :disabled="form.processing"
                    >
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        Update User
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
