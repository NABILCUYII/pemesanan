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

    <div class="min-h-screen bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center">
        <div class="text-center space-y-8 p-8 bg-white/10 backdrop-blur-lg rounded-2xl border border-white/20">
            <h1 class="text-4xl md:text-5xl font-bold text-white">
                Selamat Datang di Pemesanan
            </h1>
            <p class="text-xl text-white/80">
                Halo, {{ user.name }}! Anda telah berhasil login.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <Button 
                    @click="goToDashboard"
                    class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-8 py-3 rounded-full shadow-lg transform hover:scale-105 transition duration-300"
                >
                    Masuk ke Dashboard
                </Button>
                <Button 
                    @click="goBack"
                    class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white px-8 py-3 rounded-full shadow-lg transform hover:scale-105 transition duration-300"
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