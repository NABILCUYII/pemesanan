<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const isAuthenticated = computed(() => page.props.auth?.user);
</script>

<template>
    <Head title="Selamat Datang">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="min-h-screen bg-gradient-to-br from-[#F0F8FF] via-[#87CEEB] to-[#98FB98]">
        <!-- Animated Background -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -inset-[10px] opacity-50">
                <div class="absolute top-0 -left-4 w-72 h-72 bg-[#87CEEB] rounded-full mix-blend-multiply filter blur-xl animate-blob"></div>
                <div class="absolute top-0 -right-4 w-72 h-72 bg-[#98FB98] rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-2000"></div>
                <div class="absolute -bottom-8 left-20 w-72 h-72 bg-[#20B2AA] rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-4000"></div>
            </div>
        </div>

        <!-- Content -->
        <div class="relative min-h-screen flex flex-col items-center justify-center p-6 lg:p-8">
            <main class="w-full max-w-4xl">
                <div class="text-center mb-12">
                    <h1 class="text-5xl md:text-6xl font-bold text-[#2F4F4F] mb-6 animate-fade-in">
                        Selamat Datang di
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#20B2AA] to-[#98FB98]">
                            Pemesanan
                        </span>
                    </h1>
                    <p class="text-xl text-[#2F4F4F]/80 max-w-2xl mx-auto">
                        Sistem Peminjaman Barang Modern dan Terpercaya
                    </p>
                </div>

                <!-- Centered Navigation Buttons -->
                <div class="flex justify-center mb-12">
                    <nav class="flex items-center gap-6">
                        <!-- Show Login/Register buttons if not authenticated -->
                        <template v-if="!isAuthenticated">
                            <Link
                                :href="route('login')"
                                class="group relative inline-flex items-center justify-center px-8 py-3 overflow-hidden font-medium transition duration-300 ease-out rounded-full shadow-md bg-gradient-to-r from-[#20B2AA] to-[#87CEEB] text-white hover:from-[#1A9A94] hover:to-[#5F9EA0] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#20B2AA]"
                            >
                                <span class="absolute inset-0 flex items-center justify-center w-full h-full text-white duration-300 -translate-x-full bg-gradient-to-r from-[#87CEEB] to-[#20B2AA] group-hover:translate-x-0 ease">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                </span>
                                <span class="absolute flex items-center justify-center w-full h-full text-white transition-all duration-300 transform group-hover:translate-x-full ease">Login</span>
                                <span class="relative invisible">Login</span>
                            </Link>
                            <Link
                                :href="route('register')"
                                class="group relative inline-flex items-center justify-center px-8 py-3 overflow-hidden font-medium transition duration-300 ease-out rounded-full shadow-md bg-gradient-to-r from-[#87CEEB] to-[#20B2AA] text-white hover:from-[#5F9EA0] hover:to-[#1A9A94] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#87CEEB]"
                            >
                                <span class="absolute inset-0 flex items-center justify-center w-full h-full text-white duration-300 -translate-x-full bg-gradient-to-r from-[#20B2AA] to-[#87CEEB] group-hover:translate-x-0 ease">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                </span>
                                <span class="absolute flex items-center justify-center w-full h-full text-white transition-all duration-300 transform group-hover:translate-x-full ease">Sign In</span>
                                <span class="relative invisible">Sign In</span>
                            </Link>
                        </template>
                        
                        <!-- Show single "Masuk" button if authenticated -->
                        <template v-else>
                            <Link
                                :href="route('dashboard')"
                                class="group relative inline-flex items-center justify-center px-8 py-3 overflow-hidden font-medium transition duration-300 ease-out rounded-full shadow-md bg-gradient-to-r from-[#20B2AA] to-[#98FB98] text-white hover:from-[#1A9A94] hover:to-[#90EE90] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#20B2AA]"
                            >
                                <span class="absolute inset-0 flex items-center justify-center w-full h-full text-white duration-300 -translate-x-full bg-gradient-to-r from-[#98FB98] to-[#20B2AA] group-hover:translate-x-0 ease">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </span>
                                <span class="absolute flex items-center justify-center w-full h-full text-white transition-all duration-300 transform group-hover:translate-x-full ease">Masuk</span>
                                <span class="relative invisible">Masuk</span>
                            </Link>
                        </template>
                    </nav>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white/20 backdrop-blur-lg rounded-2xl p-6 border border-white/30 transform hover:scale-105 transition duration-300">
                        <div class="w-12 h-12 bg-white/30 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#2F4F4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-[#2F4F4F] mb-2">Peminjaman Cepat</h3>
                        <p class="text-[#2F4F4F]/70">Proses peminjaman yang mudah dan cepat dalam hitungan menit</p>
                    </div>

                    <div class="bg-white/20 backdrop-blur-lg rounded-2xl p-6 border border-white/30 transform hover:scale-105 transition duration-300">
                        <div class="w-12 h-12 bg-white/30 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#2F4F4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-[#2F4F4F] mb-2">Manajemen Barang</h3>
                        <p class="text-[#2F4F4F]/70">Kelola dan lacak semua barang dengan sistem yang terintegrasi</p>
                    </div>

                    <div class="bg-white/20 backdrop-blur-lg rounded-2xl p-6 border border-white/30 transform hover:scale-105 transition duration-300">
                        <div class="w-12 h-12 bg-white/30 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#2F4F4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-[#2F4F4F] mb-2">Notifikasi Real-time</h3>
                        <p class="text-[#2F4F4F]/70">Dapatkan pemberitahuan instan untuk setiap aktivitas peminjaman</p>
                    </div>
                </div>
            </main>
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

.animate-fade-in {
    animation: fadeIn 1s ease-out;
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