<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Mail, Lock, LogIn, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-[#F0F8FF] via-[#87CEEB] to-[#98FB98] flex items-center justify-center p-4">
        <!-- Animated Background -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -inset-[10px] opacity-50">
                <div class="absolute top-0 -left-4 w-72 h-72 bg-[#87CEEB] rounded-full mix-blend-multiply filter blur-xl animate-blob"></div>
                <div class="absolute top-0 -right-4 w-72 h-72 bg-[#98FB98] rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-2000"></div>
                <div class="absolute -bottom-8 left-20 w-72 h-72 bg-[#20B2AA] rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-4000"></div>
            </div>
        </div>

        <div class="w-full max-w-md">
            <Head title="Log in" />

            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20 shadow-2xl">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-[#2F4F4F] mb-2">Selamat Datang!</h1>
                    <p class="text-[#2F4F4F]/80">Masuk ke akun Anda untuk melanjutkan</p>
                </div>

                <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600 bg-green-100 p-3 rounded-lg border border-green-200">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Mail class="h-5 w-5 text-[#2F4F4F]/60" />
                            </div>
                            <Input
                                id="email"
                                type="email"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="email"
                                v-model="form.email"
                                placeholder="Alamat email"
                                class="pl-10 bg-white/20 border-[#2F4F4F]/20 text-[#2F4F4F] placeholder:text-[#2F4F4F]/60 focus:border-[#20B2AA]/40 focus:ring-[#20B2AA]/20"
                            />
                            <InputError :message="form.errors.email" class="text-orange-600" />
                        </div>

                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Lock class="h-5 w-5 text-[#2F4F4F]/60" />
                            </div>
                            <Input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                :tabindex="2"
                                autocomplete="current-password"
                                v-model="form.password"
                                placeholder="Kata sandi"
                                class="pl-10 pr-12 bg-white/20 border-[#2F4F4F]/20 text-[#2F4F4F] placeholder:text-[#2F4F4F]/60 focus:border-[#20B2AA]/40 focus:ring-[#20B2AA]/20"
                            />
                            <button
                                type="button"
                                @click="togglePasswordVisibility"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#2F4F4F]/60 hover:text-[#2F4F4F] transition-colors"
                                :tabindex="3"
                            >
                                <Eye v-if="!showPassword" class="h-5 w-5" />
                                <EyeOff v-else class="h-5 w-5" />
                            </button>
                            <InputError :message="form.errors.password" class="text-orange-600" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <Label for="remember" class="flex items-center space-x-3 text-[#2F4F4F]/80">
                            <Checkbox id="remember" v-model="form.remember" :tabindex="4" class="border-[#2F4F4F]/40" />
                            <span>Ingat saya</span>
                        </Label>
                        <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm text-[#2F4F4F]/80 hover:text-[#20B2AA]" :tabindex="6">
                            Lupa kata sandi?
                        </TextLink>
                    </div>

                    <Button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-[#20B2AA] to-[#87CEEB] hover:from-[#1A9A94] hover:to-[#5F9EA0] text-white font-semibold py-3 rounded-lg transition-all duration-300 transform hover:scale-[1.02] focus:scale-[0.98]" 
                        :tabindex="5" 
                        :disabled="form.processing"
                    >
                        <LogIn v-if="!form.processing" class="h-5 w-5 mr-2" />
                        <LoaderCircle v-else class="h-5 w-5 mr-2 animate-spin" />
                        {{ form.processing ? 'Memproses...' : 'Masuk' }}
                    </Button>

                    <div class="text-center text-sm text-[#2F4F4F]/80">
                        Belum punya akun?
                        <TextLink :href="route('register')" class="text-[#20B2AA] hover:text-[#1A9A94] font-semibold transition-colors" :tabindex="7">Daftar</TextLink>
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
