<script setup lang="ts">
import { ref } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

const user = usePage().props.auth.user;

const showLogoutModal = ref(false);

const goToDashboard = () => {
    router.visit(route('dashboard'));
};

const goBack = () => {
    router.post(route('logout'));
};

const openLogoutModal = () => {
    showLogoutModal.value = true;
};

const closeLogoutModal = () => {
    showLogoutModal.value = false;
};

const confirmLogout = () => {
    closeLogoutModal();
    goBack();
};
</script>

<template>
    <Head title="Selamat Datang Di Pemesanan Barang">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-sky-100 via-blue-200 to-green-100">
        <div class="w-full max-w-3xl mx-auto p-6 sm:p-10 bg-white/30 backdrop-blur-xl rounded-3xl shadow-xl border border-white/40 animate-fade-in transition-all duration-700">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <!-- SVG/Ilustrasi -->
                <div class="w-full md:w-1/2 text-center">
                    <img src="https://png.pngtree.com/png-clipart/20230812/original/pngtree-people-greeting-gesture-character-welcome-picture-image_7872226.png" alt="Welcome" class="w-64 mx-auto" />
                </div>

                <!-- Konten Teks dan Tombol -->
                <div class="w-full md:w-1/2 text-center md:text-left space-y-6">
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-800 leading-tight">
                        Selamat Datang Di Pemesanan Barang 👋
                    </h1>
                    <p class="text-lg text-gray-700">
                        Halo, <span class="font-semibold text-primary">{{ user.name }}</span>! <br />
                        Anda berhasil login ke Pemesanan.
                    </p>

                    <!-- Untuk user yang belum di-acc -->
                    <p v-if="user.role === 'newUser'" class="bg-yellow-100 border-l-4 border-yellow-400 text-yellow-800 p-4 rounded-lg text-base font-medium">
                        <span class="block mb-1">⏳ <b>Menunggu Persetujuan Admin</b></span>
                        Maaf, Anda belum memiliki izin masuk. Silakan menunggu konfirmasi dari admin.
                    </p>

                    <!-- Untuk user dan admin -->
                    <div v-if="user.role === 'user' || user.role === 'admin'" class="space-y-4">
                        <div class="flex items-center gap-2 justify-center md:justify-start">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                                :class="user.role === 'admin' ? 'bg-blue-600 text-white' : 'bg-green-500 text-white'">
                                <svg v-if="user.role === 'admin'" class="w-5 h-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm0 2c-2.67 0-8 1.337-8 4v3h16v-3c0-2.663-5.33-4-8-4z"/>
                                </svg>
                                <svg v-else class="w-5 h-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ user.role === 'admin' ? 'Admin' : 'User' }}
                            </span>
                            <span class="text-base text-gray-700">
                                Selamat, Anda sudah memiliki izin sebagai <span class="font-semibold text-primary">{{ user.role }}</span>
                            </span>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start mt-2">
                            <Button
                                @click="goToDashboard"
                                class="bg-gradient-to-r from-teal-500 to-blue-400 hover:from-teal-600 hover:to-blue-500 text-white px-6 py-3 rounded-full shadow-lg transform hover:scale-105 transition duration-300 font-semibold flex items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6h6v6m-6 0h6m-6 0v2a2 2 0 002 2h2a2 2 0 002-2v-2"/>
                                </svg>
                                Masuk ke Dashboard
                            </Button>
                            <Button
                                @click="router.visit(route('home'))"
                                class="bg-gradient-to-r from-gray-500 to-gray-300 hover:from-gray-600 hover:to-gray-400 text-white px-6 py-3 rounded-full shadow-lg transform hover:scale-105 transition duration-300 font-semibold flex items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Kembali 
                            </Button>
                            <Button
                                @click="openLogoutModal"
                                class="bg-gradient-to-r from-red-500 to-pink-400 hover:from-red-600 hover:to-pink-500 text-white px-6 py-3 rounded-full shadow-lg transform hover:scale-105 transition duration-300 font-semibold flex items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M19.5 12h-9m0 0l3-3m-3 3l3 3" />
                                </svg>
                                Logout
                            </Button>
                        </div>
                    </div>

                    <!-- Tombol untuk newUser -->
                    <div v-else-if="user.role === 'newUser'" class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start mt-2">
                        <Button
                            @click="router.visit(route('home'))"
                            class="bg-gradient-to-r from-gray-500 to-gray-300 hover:from-gray-600 hover:to-gray-400 text-white px-6 py-3 rounded-full shadow-lg transform hover:scale-105 transition duration-300 font-semibold flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Kembali 
                        </Button>
                        <Button
                            @click="openLogoutModal"
                            class="bg-gradient-to-r from-red-500 to-pink-400 hover:from-red-600 hover:to-pink-500 text-white px-6 py-3 rounded-full shadow-lg transform hover:scale-105 transition duration-300 font-semibold flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M19.5 12h-9m0 0l3-3m-3 3l3 3" />
                            </svg>
                            Logout
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Logout -->
    <transition name="fade">
        <div v-if="showLogoutModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full text-center relative animate-fade-in">
                <svg class="mx-auto mb-4 w-14 h-14 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <h2 class="text-2xl font-bold mb-2 text-gray-800">Konfirmasi Logout</h2>
                <p class="text-gray-600 mb-6">Apakah Anda yakin ingin logout dari sistem?</p>
                <div class="flex gap-4 justify-center">
                    <Button
                        @click="confirmLogout"
                        class="bg-gradient-to-r from-red-500 to-pink-400 hover:from-red-600 hover:to-pink-500 text-white px-6 py-2 rounded-full shadow font-semibold"
                    >
                        Ya, Logout
                    </Button>
                    <Button
                        @click="closeLogoutModal"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-full shadow font-semibold"
                    >
                        Batal
                    </Button>
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.98);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out;
}

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.2s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
