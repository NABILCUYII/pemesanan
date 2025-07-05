<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-red-50 to-pink-100">
    <div class="max-w-md w-full mx-4 text-center">
      <!-- Error Icon -->
      <div class="mb-8">
        <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
          </svg>
        </div>
      </div>
      
      <!-- Error Message -->
      <h1 class="text-4xl font-bold text-gray-800 mb-4">403</h1>
      <h2 class="text-2xl font-semibold text-gray-700 mb-4">Akses Ditolak</h2>
      <p class="text-gray-600 mb-8">
        Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. 
        Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
      </p>
      
      <!-- Action Buttons -->
      <div class="space-y-4">
        <button 
          @click="goBack"
          class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Kembali
        </button>
        
        <button 
          @click="goHome"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          Beranda
        </button>
        
        <button 
          @click="contactAdmin"
          class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
          </svg>
          Hubungi Admin
        </button>
      </div>
      
      <!-- Additional Info -->
      <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <p class="text-sm text-yellow-800">
          <strong>Tips:</strong> Pastikan Anda sudah login dengan akun yang memiliki hak akses yang sesuai.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'

const goBack = () => {
  if (window.history.length > 1) {
    window.history.back()
  } else {
    router.visit('/')
  }
}

const goHome = () => {
  router.visit('/')
}

const contactAdmin = () => {
  // You can customize this to open email or redirect to contact page
  window.open('mailto:admin@example.com?subject=Akses Ditolak - Bantuan Diperlukan', '_blank')
}
</script> 