<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
        role: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: route('users.index'),
    },
    {
        title: 'Edit User',
        href: route('users.edit', props.user.id),
    },
];

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: props.user.role,
});

const submit = () => {
    form.put(route('users.update', props.user.id));
};
</script>

<template>
    <Head title="Edit User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl">
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium">Name</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    />
                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                        {{ form.errors.name }}
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    />
                    <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                        {{ form.errors.email }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Role</label>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input
                                id="role-admin"
                                v-model="form.role"
                                type="radio"
                                value="admin"
                                class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <label for="role-admin" class="ml-2 block text-sm text-gray-900">Admin</label>
                        </div>
                        <div class="flex items-center">
                            <input
                                id="role-user"
                                v-model="form.role"
                                type="radio"
                                value="user"
                                class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <label for="role-user" class="ml-2 block text-sm text-gray-900">User</label>
                        </div>
                    </div>
                    <div v-if="form.errors.role" class="mt-1 text-sm text-red-600">
                        {{ form.errors.role }}
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        :disabled="form.processing"
                    >
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

