<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Eye, EyeOff } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Password settings',
        href: '/settings/password',
    },
];

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const toggleCurrentPasswordVisibility = () => {
    showCurrentPassword.value = !showCurrentPassword.value;
};

const toggleNewPasswordVisibility = () => {
    showNewPassword.value = !showNewPassword.value;
};

const toggleConfirmPasswordVisibility = () => {
    showConfirmPassword.value = !showConfirmPassword.value;
};

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: (errors: any) => {
            if (errors.password) {
                form.reset('password', 'password_confirmation');
                if (passwordInput.value instanceof HTMLInputElement) {
                    passwordInput.value.focus();
                }
            }

            if (errors.current_password) {
                form.reset('current_password');
                if (currentPasswordInput.value instanceof HTMLInputElement) {
                    currentPasswordInput.value.focus();
                }
            }
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Password settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Update password" description="Ensure your account is using a long, random password to stay secure" />

                <form @submit.prevent="updatePassword" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="current_password">Current password</Label>
                        <div class="relative">
                            <Input
                                id="current_password"
                                ref="currentPasswordInput"
                                v-model="form.current_password"
                                :type="showCurrentPassword ? 'text' : 'password'"
                                class="mt-1 block w-full pr-12"
                                autocomplete="current-password"
                                placeholder="Current password"
                            />
                            <button
                                type="button"
                                @click="toggleCurrentPasswordVisibility"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <Eye v-if="!showCurrentPassword" class="h-5 w-5" />
                                <EyeOff v-else class="h-5 w-5" />
                            </button>
                        </div>
                        <InputError :message="form.errors.current_password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">New password</Label>
                        <div class="relative">
                            <Input
                                id="password"
                                ref="passwordInput"
                                v-model="form.password"
                                :type="showNewPassword ? 'text' : 'password'"
                                class="mt-1 block w-full pr-12"
                                autocomplete="new-password"
                                placeholder="New password"
                            />
                            <button
                                type="button"
                                @click="toggleNewPasswordVisibility"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <Eye v-if="!showNewPassword" class="h-5 w-5" />
                                <EyeOff v-else class="h-5 w-5" />
                            </button>
                        </div>
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation">Confirm new password</Label>
                        <div class="relative">
                            <Input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                class="mt-1 block w-full pr-12"
                                autocomplete="new-password"
                                placeholder="Confirm new password"
                            />
                            <button
                                type="button"
                                @click="toggleConfirmPasswordVisibility"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <Eye v-if="!showConfirmPassword" class="h-5 w-5" />
                                <EyeOff v-else class="h-5 w-5" />
                            </button>
                        </div>
                        <InputError :message="form.errors.password_confirmation" />
                    </div>

                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Updating...' : 'Update password' }}
                    </Button>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>

