<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Transition } from 'vue';
import { User, Camera, Mail, Phone, MapPin } from 'lucide-vue-next';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const form = useForm({
    name: '',
    email: '',
    phone: '',
    address: '',
    bio: '',
    avatar: null as File | null,
});

// Computed property untuk preview avatar
const avatarPreview = computed(() => {
    if (form.avatar) {
        return URL.createObjectURL(form.avatar);
    }
    return null;
});

const updateProfile = () => {
    form.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success
        },
        onError: (errors: any) => {
            // Handle errors
        },
    });
};

const handleAvatarChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.avatar = target.files[0];
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="max-w-4xl mx-auto space-y-6">
                <!-- Profile Information Card -->
                <Card class="shadow-lg border-0 bg-gradient-to-br from-white to-gray-50/50">
                    <CardHeader class="pb-4">
                        <CardTitle class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <User class="w-6 h-6 text-blue-600" />
                            </div>
                            Profile Information
                        </CardTitle>
                        <CardDescription class="text-gray-600">
                            Update your account's profile information and email address
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="space-y-6">
                        <form @submit.prevent="updateProfile" class="space-y-6" enctype="multipart/form-data">
                            <!-- Avatar Section -->
                            <div class="flex items-center space-x-6">
                                <div class="relative">
                                    <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                        <img 
                                            v-if="avatarPreview" 
                                            :src="avatarPreview" 
                                            alt="Profile preview" 
                                            class="w-full h-full object-cover"
                                        />
                                        <User v-else class="w-12 h-12 text-gray-400" />
                                    </div>
                                    <label for="avatar" class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer hover:bg-blue-700 transition-colors">
                                        <Camera class="w-4 h-4" />
                                    </label>
                                    <input
                                        id="avatar"
                                        name="avatar"
                                        type="file"
                                        accept="image/*"
                                        class="hidden"
                                        @change="handleAvatarChange"
                                    />
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Profile Photo</h3>
                                    <p class="text-sm text-gray-500">Upload a new profile photo (JPG, PNG, GIF up to 2MB)</p>
                                </div>
                            </div>

                            <!-- Name Field -->
                            <div class="grid gap-2">
                                <Label for="name" class="text-sm font-medium text-gray-700 flex items-center gap-2">
                                    <User class="w-4 h-4" />
                                    Full Name
                                </Label>
                                <Input
                                    id="name"
                                    name="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Enter your full name"
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <!-- Email Field -->
                            <div class="grid gap-2">
                                <Label for="email" class="text-sm font-medium text-gray-700 flex items-center gap-2">
                                    <Mail class="w-4 h-4" />
                                    Email Address
                                </Label>
                                <Input
                                    id="email"
                                    name="email"
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    placeholder="Enter your email address"
                                />
                                <InputError :message="form.errors.email" />
                            </div>

                            <!-- Phone Field -->
                            <div class="grid gap-2">
                                <Label for="phone" class="text-sm font-medium text-gray-700 flex items-center gap-2">
                                    <Phone class="w-4 h-4" />
                                    Phone Number
                                </Label>
                                <Input
                                    id="phone"
                                    name="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    class="mt-1 block w-full"
                                    placeholder="Enter your phone number"
                                />
                                <InputError :message="form.errors.phone" />
                            </div>

                            <!-- Address Field -->
                            <div class="grid gap-2">
                                <Label for="address" class="text-sm font-medium text-gray-700 flex items-center gap-2">
                                    <MapPin class="w-4 h-4" />
                                    Address
                                </Label>
                                <Textarea
                                    id="address"
                                    name="address"
                                    v-model="form.address"
                                    class="mt-1 block w-full"
                                    placeholder="Enter your address"
                                    :rows="3"
                                />
                                <InputError :message="form.errors.address" />
                            </div>

                            <!-- Bio Field -->
                            <div class="grid gap-2">
                                <Label for="bio" class="text-sm font-medium text-gray-700">
                                    Bio
                                </Label>
                                <Textarea
                                    id="bio"
                                    name="bio"
                                    v-model="form.bio"
                                    class="mt-1 block w-full"
                                    placeholder="Tell us about yourself..."
                                    :rows="4"
                                />
                                <InputError :message="form.errors.bio" />
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center gap-4 pt-4">
                                <Button 
                                    :disabled="form.processing" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium"
                                >
                                    Update Profile
                                </Button>

                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <p v-show="form.recentlySuccessful" class="text-sm text-green-600 font-medium">Profile updated successfully!</p>
                                </Transition>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
