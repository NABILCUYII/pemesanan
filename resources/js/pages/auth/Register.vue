<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, User, Mail, Lock, UserPlus } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center p-4">
        <!-- Animated Background -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -inset-[10px] opacity-50">
                <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl animate-blob"></div>
                <div class="absolute top-0 -right-4 w-72 h-72 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-2000"></div>
                <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-4000"></div>
            </div>
        </div>

        <div class="w-full max-w-md">
            <Head title="Register" />

            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20 shadow-2xl">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-white mb-2">Create Account</h1>
                    <p class="text-white/80">Join us and start your journey</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <User class="h-5 w-5 text-white/60" />
                            </div>
                            <Input
                                id="name"
                                type="text"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="name"
                                v-model="form.name"
                                placeholder="Full name"
                                class="pl-10 bg-white/10 border-white/20 text-white placeholder:text-white/60 focus:border-white/40"
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Mail class="h-5 w-5 text-white/60" />
                            </div>
                            <Input
                                id="email"
                                type="email"
                                required
                                :tabindex="2"
                                autocomplete="email"
                                v-model="form.email"
                                placeholder="Email address"
                                class="pl-10 bg-white/10 border-white/20 text-white placeholder:text-white/60 focus:border-white/40"
                            />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Lock class="h-5 w-5 text-white/60" />
                            </div>
                            <Input
                                id="password"
                                type="password"
                                required
                                :tabindex="3"
                                autocomplete="new-password"
                                v-model="form.password"
                                placeholder="Password"
                                class="pl-10 bg-white/10 border-white/20 text-white placeholder:text-white/60 focus:border-white/40"
                            />
                            <InputError :message="form.errors.password" />
                        </div>

                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Lock class="h-5 w-5 text-white/60" />
                            </div>
                            <Input
                                id="password_confirmation"
                                type="password"
                                required
                                :tabindex="4"
                                autocomplete="new-password"
                                v-model="form.password_confirmation"
                                placeholder="Confirm password"
                                class="pl-10 bg-white/10 border-white/20 text-white placeholder:text-white/60 focus:border-white/40"
                            />
                            <InputError :message="form.errors.password_confirmation" />
                        </div>

                        <Button 
                            type="submit" 
                            class="w-full bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-semibold py-3 rounded-lg transition-all duration-300 transform hover:scale-[1.02] focus:scale-[0.98]"
                            :disabled="form.processing"
                        >
                            <UserPlus v-if="!form.processing" class="h-5 w-5 mr-2" />
                            <LoaderCircle v-else class="h-5 w-5 mr-2 animate-spin" />
                            {{ form.processing ? 'Creating account...' : 'Create account' }}
                        </Button>
                    </div>

                    <div class="text-center text-sm text-white/80">
                        Already have an account?
                        <TextLink :href="route('login')" class="text-white font-semibold hover:text-white/90 transition-colors" :tabindex="6">
                            Log in
                        </TextLink>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}
</style>
