<template>
  <!-- Latar belakang dengan gradasi warna yang memberikan kesan peringatan namun tetap profesional -->
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-rose-50 to-orange-100 font-sans p-4">
    <div class="max-w-lg w-full mx-auto text-center bg-white p-8 rounded-2xl shadow-lg">
      
      <!-- Ikon Baru: Gembok atau Tanda Berhenti yang Relevan -->
      <div class="mb-6">
        <div class="w-28 h-28 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-5 border-4 border-white shadow-md">
          <!-- SVG Ikon yang merepresentasikan akses terkunci/dilarang -->
          <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
        </div>
      </div>
      
      <!-- Pesan Error -->
      <h1 class="text-5xl font-bold text-red-700 mb-2">403</h1>
      <h2 class="text-3xl font-semibold text-gray-800 mb-4">Akses Ditolak</h2>
      <p class="text-gray-600 mb-8 max-w-md mx-auto">
        Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator jika Anda merasa ini adalah sebuah kesalahan.
      </p>
      
      <!-- Kotak Peringatan -->
      <div class="mb-8 p-4 bg-red-50 border-l-4 border-red-400">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.21 3.03-1.742 3.03H4.42c-1.532 0-2.492-1.696-1.742-3.03l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1-8a1 1 0 00-1 1v3a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-red-700 text-left">
              Halaman ini hanya bisa diakses oleh admin
            </p>
          </div>
        </div>
      </div>
      
      <!-- Tombol Aksi -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <button 
          @click="goBack"
          class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Kembali
        </button>
        <button 
          @click="goHome"
          class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          Beranda
        </button>
      </div>

       <!-- Tombol Kontak Bantuan -->
       <div class="mt-4">
        <button 
          @click="requestAccess"
          class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center shadow-md hover:shadow-lg"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          Minta Akses
        </button>
      </div>
      
      <!-- Info Pengguna Saat Ini -->
      <div v-if="user" class="mt-8 pt-6 border-t border-gray-200">
        <div class="flex items-center space-x-2">
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          <p class="text-sm text-gray-500">
            Anda masuk sebagai: <strong class="text-gray-700">{{ user.name }}</strong> 
            <span class="text-gray-400">•</span>
          
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'

// Props untuk menerima data pengguna dari backend
interface Props {
  user?: {
    name: string
    role?: string
  }
}
defineProps<Props>()

// Fungsi untuk kembali ke halaman sebelumnya
const goBack = () => {
  if (window.history.length > 1) {
    window.history.back()
  } else {
    router.visit('/')
  }
}

// Fungsi untuk kembali ke halaman utama
const goHome = () => {
  router.visit('/')
}

// Fungsi untuk pindah ke halaman permintaan akses
const requestAccess = () => {
  // Anda bisa mengarahkan ke halaman form permintaan akses
  router.visit('/request-access')
}

// Fungsi untuk menghubungi support via email
const contactSupport = () => {
  const subject = encodeURIComponent('Permintaan Bantuan Akses Halaman')
  const body = encodeURIComponent(`
Halo Tim Support,

Saya ingin meminta bantuan terkait akses ke halaman yang dilarang.

Detail:
- URL yang diakses: ${window.location.href}
- Waktu: ${new Date().toLocaleString('id-ID')}

Mohon bantuannya untuk memeriksa izin akses akun saya.

Terima kasih.
  `)
  window.open(`mailto:support@puskesmas-anda.com?subject=${subject}&body=${body}`, '_blank')
}
</script>

<style scoped>
/* Menambahkan font yang lebih modern jika tersedia */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

body {
  font-family: 'Inter', sans-serif;
}
</style>
