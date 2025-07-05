<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
      <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">
        Test 403 Error Handling
      </h1>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Test Basic 403 -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic 403 Error</h3>
          <p class="text-gray-600 mb-4">
            Test menggunakan abort(403)
          </p>
          <button 
            @click="testBasic403"
            class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200"
          >
            Test Basic 403
          </button>
        </div>

        <!-- Test Authorization Exception -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Authorization Exception</h3>
          <p class="text-gray-600 mb-4">
            Test menggunakan AuthorizationException
          </p>
          <button 
            @click="testAuthException"
            class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200"
          >
            Test Auth Exception
          </button>
        </div>

        <!-- Test HTTP Exception -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">HTTP Exception</h3>
          <p class="text-gray-600 mb-4">
            Test menggunakan AccessDeniedHttpException
          </p>
          <button 
            @click="testHttpException"
            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200"
          >
            Test HTTP Exception
          </button>
        </div>

        <!-- Test Direct Route -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Direct Route Access</h3>
          <p class="text-gray-600 mb-4">
            Akses langsung ke route test
          </p>
          <a 
            href="/test-403"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center"
          >
            Go to /test-403
          </a>
        </div>
      </div>

      <!-- Status Info -->
      <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-4">Status Testing</h3>
        <div class="space-y-2 text-sm text-blue-700">
          <p>✅ Middleware Handle403Errors terdaftar secara global</p>
          <p>✅ Exception handler untuk 403 error sudah dikonfigurasi</p>
          <p>✅ Test routes sudah dibuat</p>
          <p>✅ Cache sudah di-clear</p>
        </div>
      </div>

      <!-- Instructions -->
      <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-yellow-800 mb-4">Cara Testing</h3>
        <ol class="text-sm text-yellow-700 space-y-1 list-decimal list-inside">
          <li>Klik salah satu tombol test di atas</li>
          <li>Jika berhasil, akan muncul halaman Unauthorized.vue yang modern</li>
          <li>Jika masih muncul halaman Laravel default, ada masalah dengan middleware</li>
          <li>Periksa browser console untuk error JavaScript</li>
          <li>Periksa Laravel logs untuk error PHP</li>
        </ol>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'

const testBasic403 = () => {
  router.visit('/test-403')
}

const testAuthException = () => {
  router.visit('/test-403-auth')
}

const testHttpException = () => {
  router.visit('/test-403-http')
}
</script> 