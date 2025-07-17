<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-amber-50 to-orange-100 font-sans p-4">
    <div class="max-w-lg w-full mx-auto text-center bg-white p-8 rounded-2xl shadow-lg">
      
      <!-- Ikon Kunci Terkunci -->
      <div class="mb-6">
        <div class="w-28 h-28 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-5 border-4 border-white shadow-md">
          <svg class="w-16 h-16 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
        </div>
      </div>
      
      <!-- Pesan Utama -->
      <h1 class="text-4xl font-bold text-amber-700 mb-2">403</h1>
      <h2 class="text-2xl font-semibold text-gray-800 mb-4">Mohon Maaf 🙏 Anda Belum Memiliki Izin Masuk</h2>
      <p class="text-gray-600 mb-6 max-w-md mx-auto">
        Maaf, akun Anda belum memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator untuk meminta akses.
      </p>
      
      <!-- Kotak Informasi -->
      <div class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-400 rounded-r-lg">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-amber-700 text-left">
              <strong>Status Akun:</strong> Menunggu persetujuan administrator
            </p>
          </div>
        </div>
      </div>
      
      <!-- Tombol Aksi -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
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
          class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          Beranda
        </button>
      </div>

      <!-- Tombol Minta Akses
       
      <div class="mb-4">
        <button 
          @click="requestAccess"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center shadow-md hover:shadow-lg"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          Minta Izin Akses
        </button>
      </div> -->

      <!-- Tombol Hubungi Admin -->
      <div class="mb-6">
        <button 
          @click="contactAdmin"
          class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
          </svg>
          Hubungi Administrator
        </button>
      </div>
      
      <!-- Info Pengguna Saat Ini -->
      <div v-if="user" class="mt-6 pt-4 border-t border-gray-200">
        <div class="flex items-center justify-center space-x-2">
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          <p class="text-sm text-gray-500">
            Anda masuk sebagai: <strong class="text-gray-700">{{ user.name }}</strong>
          </p>
        </div>
        <p class="text-xs text-gray-400 mt-1">
          Role: {{ user.role || 'User' }} • ID: {{ user.id }}
        </p>
      </div>

      <!-- Tips -->
      <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h4 class="text-sm font-semibold text-blue-800 mb-2">💡 Tips untuk Mendapatkan Akses:</h4>
        <ul class="text-xs text-blue-700 space-y-1 text-left">
          <li>• Pastikan Anda sudah terdaftar sebagai pengguna sistem</li>
          <li>• Hubungi administrator untuk meminta role yang sesuai</li>
          <li>• Tunggu persetujuan dari administrator</li>
          <li>• Periksa email Anda untuk notifikasi status akses</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'

// Props untuk menerima data pengguna dari backend
interface Props {
  user?: {
    id: number
    name: string
    role?: string
  }
}
const props = defineProps<Props>()

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

// Fungsi untuk menghubungi admin
const contactAdmin = () => {
  const subject = encodeURIComponent('Permintaan Izin Akses - Bantuan Diperlukan')
  const body = encodeURIComponent(`
Halo Administrator,

Saya ingin meminta bantuan untuk mendapatkan izin akses ke sistem.

Detail Pengguna:
- Nama: ${props.user?.name || 'Tidak diketahui'}
- Role saat ini: ${props.user?.role || 'User'}
- URL yang diakses: ${window.location.href}
- Waktu: ${new Date().toLocaleString('id-ID')}

Saya membutuhkan akses untuk:
[Silakan jelaskan alasan Anda membutuhkan akses]

Mohon bantuannya untuk memeriksa dan memberikan izin akses yang sesuai.

Terima kasih.
  `)
  window.open(`mailto:admin@puskesmas-anda.com?subject=${subject}&body=${body}`, '_blank')
}
</script>

<style scoped>
/* Menambahkan font yang lebih modern jika tersedia */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

body {
  font-family: 'Inter', sans-serif;
}
</style> 