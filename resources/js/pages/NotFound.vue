<template>
  <!-- Latar belakang dengan gradasi warna hijau-cyan yang menenangkan -->
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-50 to-cyan-100 font-sans p-4">
    <div class="max-w-lg w-full mx-auto text-center bg-white p-8 rounded-2xl shadow-lg">
      
      <!-- Ganti ikon dengan logo dari link image address -->
      <div class="mb-6">
        <div class="w-28 h-28 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-5 border-4 border-white shadow-md overflow-hidden">
          <img 
            src="https://puskesmasmuncan.lomboktengahkab.go.id/uploads/images/Logo_Puskesmas_Tanpa_Background.png" 
            alt="Logo Puskesmas" 
            class="w-20 h-20 object-contain"
          />
        </div>
      </div>
      
      <!-- Pesan Error -->
      <h1 class="text-6xl font-bold text-emerald-700 mb-2">404</h1>
      <h2 class="text-3xl font-semibold text-gray-800 mb-4">Halaman Tidak Ditemukan</h2>
      <p class="text-gray-600 mb-8 max-w-md mx-auto">
        Maaf, halaman yang Anda cari tidak ada atau mungkin telah dipindahkan. Mari kami bantu Anda kembali ke jalur yang benar.
      </p>
      
  
      
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
          class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center shadow-md hover:shadow-lg"
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
          @click="contactSupport"
          class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          Hubungi Bantuan
        </button>
      </div>
      
      <!-- Link Cepat -->
      <div class="mt-8 pt-6 border-t border-gray-200">
        <h3 class="text-md font-semibold text-gray-700 mb-3">Mungkin Anda mencari:</h3>
        <div class="flex flex-wrap justify-center gap-x-4 gap-y-2 text-sm">
          <a href="/dashboard" class="text-emerald-600 hover:text-emerald-800 hover:underline">Dashboard</a>
          <a href="/peminjaman" class="text-emerald-600 hover:text-emerald-800 hover:underline">Peminjaman</a>
          <a href="/permintaan" class="text-emerald-600 hover:text-emerald-800 hover:underline">Permintaan</a>
          <a href="/artikel-kesehatan" class="text-emerald-600 hover:text-emerald-800 hover:underline">Artikel Kesehatan</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

// State untuk input pencarian
const searchQuery = ref('')

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

// Fungsi untuk melakukan pencarian (logika bisa ditambahkan di sini)
const search = () => {
  if (searchQuery.value.trim()) {
    // Implementasikan logika pencarian Anda di sini
    // Contoh: router.visit(`/search?q=${searchQuery.value}`)
    console.log('Mencari:', searchQuery.value)
  }
}

// Fungsi untuk menghubungi support via email
const contactSupport = () => {
  // Arahkan ke halaman kontak atau buka email client
  window.open('mailto:support@puskesmas-anda.com?subject=Bantuan Halaman Tidak Ditemukan', '_blank')
}
</script>

<style scoped>
/* Menambahkan font yang lebih modern jika tersedia */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

body {
  font-family: 'Inter', sans-serif;
}
</style>
