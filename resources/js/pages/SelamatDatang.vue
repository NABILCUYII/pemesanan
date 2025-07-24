<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

// Ganti path di bawah sesuai lokasi file PNG Anda di public atau assets
const welcomeImage = '/images/WELCOME_BOX.png';

const user = usePage().props.auth.user;

const showModal = ref(false);

const isPenggunaBaru = user?.role === 'penggunaBARU';

const goToDashboard = () => {
    if (isPenggunaBaru) {
        showModal.value = true;
    } else {
        router.visit(route('dashboard'));
    }
};

const confirmGoToDashboard = () => {
    showModal.value = false;
    router.visit(route('dashboard'));
};

const cancelModal = () => {
    showModal.value = false;
};


const goToWelcome = () => {
    router.visit(route('welcome'));
};
</script>

<template>
    <Head title="Selamat Datang">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="min-h-screen bg-gradient-to-br from-[#F0F8FF] via-[#87CEEB] to-[#98FB98] flex items-center justify-center">
        <div class="text-center space-y-8 p-8 bg-white/20 backdrop-blur-lg rounded-2xl border border-white/30">
            <!-- Tambahkan gambar PNG besar di atas judul -->
            <img 
                src="/images/WELCOME_BOX.png" 
                alt="Welcome" 
                class="mx-auto mb-6 w-40 h-40 md:w-56 md:h-56 object-contain drop-shadow-lg"
            />
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
                    @click="goToWelcome"
                    class="bg-gradient-to-r from-[#708090] to-[#B0C4DE] hover:from-[#5A6B7B] hover:to-[#9FB6CD] text-white px-8 py-3 rounded-full shadow-lg transform hover:scale-105 transition duration-300"
                >
                    Kembali
                </Button>
            </div>
        </div>
    </div>

    <!-- Modal khusus penggunaBARU -->
    <div 
        v-if="showModal" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
    >
        <div class="bg-white rounded-xl shadow-xl p-8 max-w-md w-full text-center space-y-6">
            <!-- Gambar permintaan maaf besar -->
            <img 
                src="/images/SORRY_BOX.png" 
                alt="Permintaan Maaf" 
                class="mx-auto mb-6 w-32 h-32 md:w-40 md:h-40 object-contain drop-shadow-lg"
            />
            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-2">MOHON MAAF ANDA BELUM MEMILKI IZIN MASUK</h2>
            <p class="text-[#2F4F4F]/80">
                Silahkan hubungi administrator untuk mendapatkan izin akses.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center mt-6">
                <Button 
                    @click="confirmGoToDashboard"
                    class="bg-gradient-to-r from-[#20B2AA] to-[#87CEEB] hover:from-[#1A9A94] hover:to-[#5F9EA0] text-white px-8 py-3 rounded-full shadow-lg transform hover:scale-105 transition duration-300"
                >
                    Ya, Lanjutkan
                </Button>
                <Button 
                    @click="cancelModal"
                    class="bg-gradient-to-r from-[#708090] to-[#B0C4DE] hover:from-[#5A6B7B] hover:to-[#9FB6CD] text-white px-8 py-3 rounded-full shadow-lg transform hover:scale-105 transition duration-300"
                >
                    Batal
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