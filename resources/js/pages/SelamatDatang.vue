<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const user = usePage().props.auth.user;

const goToDashboard = () => {
    router.visit(route('dashboard'));
};

const goBack = () => {
    router.post(route('logout'));
};
</script>

<template>
    <Head title="Selamat Datang">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="min-h-screen bg-gradient-to-br from-[#F0F8FF] via-[#87CEEB] to-[#98FB98] flex items-center justify-center">
        <div class="text-center space-y-8 p-8 bg-white/20 backdrop-blur-lg rounded-2xl border border-white/30">
            <h1 class="text-4xl md:text-5xl font-bold text-[#2F4F4F]">
                Selamat Datang di Pemesanan
            </h1>
            <p class="text-xl text-[#2F4F4F]/80">
                Halo, {{ user.name }}! Anda telah berhasil login.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <Button 
                    @click="goToDashboard"
                    class="bg-gradient-to-r from-[#20B2AA] to-[#87CEEB] hover:from-[#1A9A94] hover:to-[#5F9EA0] text-white px-8 py-3 rounded-full shadow-lg transform hover:scale-105 transition duration-300"
                >
                    Masuk ke Dashboard
                </Button>
                <Button 
                    @click="goBack"
                    class="bg-gradient-to-r from-[#708090] to-[#B0C4DE] hover:from-[#5A6B7B] hover:to-[#9FB6CD] text-white px-8 py-3 rounded-full shadow-lg transform hover:scale-105 transition duration-300"
                >
                    Kembali
                </Button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>