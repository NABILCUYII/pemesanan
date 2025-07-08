<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

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

const goBack = () => {
    router.visit(route('users.index'));
};
</script>

<template>
    <Head title="Edit User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-lg px-4 py-10">
            <div class="mb-10 flex flex-col items-center">
                <div class="rounded-full bg-indigo-100 p-4 mb-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 15.232a6 6 0 11-8.485-8.485 6 6 0 018.485 8.485z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L21 20"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Edit User</h2>
                <p class="text-gray-500 text-sm text-center">Perbarui data user dengan mudah dan cepat.</p>
            </div>
            <form @submit.prevent="submit" class="rounded-xl bg-white shadow-lg p-8 space-y-9 border border-gray-100">
                <div class="space-y-2">
                    <label for="name" class="text-base font-semibold text-gray-700">Nama Lengkap</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        placeholder="Masukkan nama lengkap"
                        class="mt-2 block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition"
                        required
                        autocomplete="off"
                    />
                    <transition name="fade">
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                    </transition>
                </div>

                <div class="space-y-2">
                    <label for="email" class="text-base font-semibold text-gray-700">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        placeholder="Masukkan email"
                        class="mt-2 block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition"
                        required
                        autocomplete="off"
                    />
                    <transition name="fade">
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</p>
                    </transition>
                </div>


                <div class="space-y-2">
                    <label class="text-base font-semibold text-gray-700">Role</label>
                    <div class="flex gap-8 mt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input

                                type="radio"
                                id="admin"
                                value="admin"
                                v-model="form.role"
                                class="accent-indigo-600"
                            />
                            <span class="text-gray-700">Admin</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input

                                type="radio"
                                id="user"
                                value="user"
                                v-model="form.role"
                                class="accent-indigo-600"
                            />
                            <span class="text-gray-700">User</span>
                        </label>
                    </div>
                    <transition name="fade">
                        <p v-if="form.errors.role" class="mt-1 text-sm text-red-500">{{ form.errors.role }}</p>
                    </transition>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-4">
                    <button
                        type="button"
                        @click="goBack"
                        class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Kembali
                    </button>
                    <button
                        type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-6 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        :disabled="form.processing"
                    >
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

