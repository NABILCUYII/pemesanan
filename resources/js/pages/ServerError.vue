<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-orange-50 to-red-100">
    <div class="max-w-md w-full mx-4 text-center">
      <!-- Error Icon -->
      <div class="mb-8">
        <div class="w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
      </div>
      
      <!-- Error Message -->
      <h1 class="text-6xl font-bold text-gray-800 mb-4">500</h1>
      <h2 class="text-2xl font-semibold text-gray-700 mb-4">Kesalahan Server</h2>
      <p class="text-gray-600 mb-8">
        Maaf, terjadi kesalahan pada server kami. 
        Tim teknis telah diberitahu dan sedang mengatasi masalah ini.
      </p>
      
      <!-- Retry Button -->
      <div class="mb-8">
        <button 
          @click="retry"
          class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Coba Lagi
        </button>
      </div>
      
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
          @click="contactSupport"
          class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
          </svg>
          Laporkan Masalah
        </button>
      </div>
      
      <!-- Status Info -->
      <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center justify-center mb-2">
          <div class="w-2 h-2 bg-blue-600 rounded-full animate-pulse mr-2"></div>
          <span class="text-sm font-medium text-blue-800">Status Server</span>
        </div>
        <p class="text-sm text-blue-700">
          Tim teknis sedang bekerja untuk mengatasi masalah ini. 
          Silakan coba lagi dalam beberapa menit.
        </p>
      </div>
      
      <!-- Error ID (if available) -->
      <div v-if="errorId" class="mt-4 p-2 bg-gray-100 rounded text-xs text-gray-600">
        Error ID: {{ errorId }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const errorId = ref('ERR-' + Math.random().toString(36).substr(2, 9).toUpperCase())

const retry = () => {
  window.location.reload()
}

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

const contactSupport = () => {
  // You can customize this to open email or redirect to support page
  const subject = encodeURIComponent('Server Error - Bantuan Diperlukan')
  const body = encodeURIComponent(`Error ID: ${errorId.value}\n\nSaya mengalami error 500 pada aplikasi. Mohon bantuan untuk mengatasi masalah ini.`)
  window.open(`mailto:support@example.com?subject=${subject}&body=${body}`, '_blank')
}
</script> 