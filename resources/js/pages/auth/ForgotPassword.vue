<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Mail } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
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
            <Head title="Forgot password" />

            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20 shadow-2xl">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-[#2F4F4F] mb-2">Lupa Password?</h1>
                    <p class="text-[#2F4F4F]/80">Masukkan email Anda untuk menerima link reset password</p>
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
                                name="email"
                                required
                                autofocus
                                autocomplete="off"
                                v-model="form.email"
                                placeholder="Masukkan email Anda"
                                class="pl-10 bg-white/10 border-white/20 text-[#2F4F4F] placeholder:text-[#2F4F4F]/60 focus:border-white/40"
                            />
                            <InputError :message="form.errors.email" />
                        </div>
                    </div>

                    <div class="my-6">
                        <Button 
                            class="w-full bg-gradient-to-r from-[#20B2AA] to-[#87CEEB] hover:from-[#1A9A94] hover:to-[#5F9EA0] text-white font-semibold py-3 rounded-lg transition-all duration-300 transform hover:scale-[1.02] focus:scale-[0.98]" 
                            :disabled="form.processing"
                        >
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                            {{ form.processing ? 'Mengirim...' : 'Kirim Link Reset' }}
                        </Button>
                    </div>
                </form>

                <div class="text-center text-sm">
                    <span class="text-[#2F4F4F]/80">Ingat password Anda?</span>
                    <TextLink 
                        :href="route('login')" 
                        class="ml-1 text-[#2F4F4F] font-semibold hover:text-[#2F4F4F]/90 transition-colors"
                    >
                        Kembali ke login
                    </TextLink>
                </div>
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
