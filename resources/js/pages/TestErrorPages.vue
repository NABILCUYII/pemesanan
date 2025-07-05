<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
      <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">
        Test Halaman Error dan Loading
      </h1>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Test Loading Page -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Loading Page</h3>
          <p class="text-gray-600 mb-4">
            Halaman loading penuh dengan animasi spinner
          </p>
          <button 
            @click="testLoading"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200"
          >
            Test Loading
          </button>
        </div>

        <!-- Test 404 Page -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">404 Not Found</h3>
          <p class="text-gray-600 mb-4">
            Halaman tidak ditemukan
          </p>
          <button 
            @click="test404"
            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200"
          >
            Test 404
          </button>
        </div>

        <!-- Test 403 Page -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">403 Unauthorized</h3>
          <p class="text-gray-600 mb-4">
            Akses ditolak
          </p>
          <button 
            @click="test403"
            class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200"
          >
            Test 403
          </button>
        </div>

        <!-- Test Forbidden Page -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">403 Forbidden</h3>
          <p class="text-gray-600 mb-4">
            Akses dilarang
          </p>
          <button 
            @click="testForbidden"
            class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200"
          >
            Test Forbidden
          </button>
        </div>

        <!-- Test 500 Page -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">500 Server Error</h3>
          <p class="text-gray-600 mb-4">
            Kesalahan server
          </p>
          <button 
            @click="test500"
            class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200"
          >
            Test 500
          </button>
        </div>

        <!-- Test Loading Spinner -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Loading Spinner</h3>
          <p class="text-gray-600 mb-4">
            Komponen loading spinner
          </p>
          <button 
            @click="showSpinner = true"
            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200"
          >
            Show Spinner
          </button>
        </div>

        <!-- Test Non-existent Route -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Test Route 404</h3>
          <p class="text-gray-600 mb-4">
            Akses route yang tidak ada
          </p>
          <button 
            @click="testRoute404"
            class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200"
          >
            Test Route 404
          </button>
        </div>
      </div>

      <!-- Instructions -->
      <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-4">Cara Penggunaan</h3>
        <ul class="text-blue-700 space-y-2">
          <li>• <strong>Loading:</strong> Untuk menampilkan halaman loading penuh</li>
          <li>• <strong>404:</strong> Untuk halaman yang tidak ditemukan</li>
          <li>• <strong>403:</strong> Untuk akses yang ditolak</li>
          <li>• <strong>500:</strong> Untuk kesalahan server</li>
          <li>• <strong>Spinner:</strong> Komponen loading yang dapat digunakan di mana saja</li>
        </ul>
      </div>
    </div>

    <!-- Loading Spinner Component -->
    <LoadingSpinner 
      :show="showSpinner" 
      :fullscreen="true"
      text="Memuat data..."
      @close="showSpinner = false"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

const showSpinner = ref(false)

const testLoading = () => {
  router.visit('/loading')
}

const test404 = () => {
  router.visit('/404')
}

const test403 = () => {
  router.visit('/403')
}

const testForbidden = () => {
  router.visit('/forbidden')
}

const test500 = () => {
  router.visit('/500')
}

const testRoute404 = () => {
  router.visit('/route-yang-tidak-ada')
}

// Auto hide spinner after 3 seconds
const hideSpinner = () => {
  setTimeout(() => {
    showSpinner.value = false
  }, 3000)
}

// Watch for spinner changes
watch(showSpinner, (newValue) => {
  if (newValue) {
    hideSpinner()
  }
})
</script> 