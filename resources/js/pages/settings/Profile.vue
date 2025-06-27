<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { useInitials } from '@/composables/useInitials';
import { type BreadcrumbItem, type User } from '@/types';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = usePage();
const user = page.props.auth.user as User;
const { getInitials } = useInitials();

const photoInput = ref<HTMLInputElement>();

const form = useForm({
    name: user.name,
    email: user.email,
    photo: null as File | null,
});

const selectPhoto = () => {
    photoInput.value?.click();
};

const updatePhotoPreview = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.photo = target.files[0];
    }
};

const submit = () => {
    if (form.photo) {
        // Use POST for file uploads
        form.post(route('profile.update.post'), {
            preserveScroll: true,
            onSuccess: () => {
                form.photo = null;
                if (photoInput.value) {
                    photoInput.value.value = '';
                }
            },
        });
    } else {
        // Use PATCH for regular updates
        form.patch(route('profile.update'), {
            preserveScroll: true,
        });
    }
};

const getPhotoUrl = (photoPath: string) => {
    return `/storage/${photoPath}`;
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name, email address, and profile photo" />

                <!-- Profile Photo Section -->
                <div class="flex items-center space-x-6">
                    <div class="flex flex-col items-center space-y-4">
                        <Avatar class="h-24 w-24">
                            <AvatarImage 
                                v-if="user.photo" 
                                :src="getPhotoUrl(user.photo)" 
                                :alt="user.name" 
                            />
                            <AvatarFallback class="text-lg font-semibold">
                                {{ getInitials(user.name) }}
                            </AvatarFallback>
                        </Avatar>
                        
                        <div class="flex flex-col space-y-2">
                            <Button 
                                type="button" 
                                variant="outline" 
                                size="sm"
                                @click="selectPhoto"
                                :disabled="form.processing"
                            >
                                {{ user.photo ? 'Change Photo' : 'Add Photo' }}
                            </Button>
                            
                            <input
                                ref="photoInput"
                                type="file"
                                class="hidden"
                                accept="image/*"
                                @change="updatePhotoPreview"
                            />
                        </div>
                    </div>

                    <div class="flex-1">
                        <div v-if="form.photo" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-sm text-green-700">
                                Photo selected: {{ form.photo.name }}
                            </p>
                        </div>
                        
                        <InputError class="mt-2" :message="form.errors.photo" />
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" placeholder="Full name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>

