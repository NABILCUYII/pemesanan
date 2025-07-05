<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="text-center">
      <!-- Loading Spinner -->
      <div class="relative mb-8">
        <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mx-auto"></div>
        <div class="absolute inset-0 flex items-center justify-center">
          <div class="w-8 h-8 bg-blue-600 rounded-full animate-pulse"></div>
        </div>
      </div>
      
      <!-- Loading Text -->
      <h2 class="text-2xl font-semibold text-gray-800 mb-2">Memuat...</h2>
      <p class="text-gray-600">Mohon tunggu sebentar</p>
      
      <!-- Loading Dots -->
      <div class="flex justify-center mt-4 space-x-1">
        <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce"></div>
        <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
        <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

// Optional: Auto redirect after 3 seconds
onMounted(() => {
  setTimeout(() => {
    // You can redirect to a specific page or just let the parent handle navigation
    // router.visit('/dashboard')
  }, 3000)
})
</script>

<style scoped>
@keyframes bounce {
  0%, 80%, 100% {
    transform: scale(0);
  }
  40% {
    transform: scale(1);
  }
}

.animate-bounce {
  animation: bounce 1.4s infinite ease-in-out both;
}
</style> 