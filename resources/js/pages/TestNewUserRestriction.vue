<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
      <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">
        Test Pembatasan Akses User NewUser
      </h1>
      
      <!-- Info User -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Informasi User</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <p class="text-sm text-gray-600">ID</p>
            <p class="font-medium">{{ user.id }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Nama</p>
            <p class="font-medium">{{ user.name }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Role</p>
            <p class="font-medium">
              <span :class="isNewUser ? 'text-red-600' : 'text-green-600'">
                {{ user.role }}
              </span>
            </p>
          </div>
        </div>
        
        <!-- Status NewUser -->
        <div v-if="isNewUser" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <p class="text-red-700 font-medium">Status: User NewUser - Akses Terbatas</p>
          </div>
        </div>
        
        <div v-else class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-green-700 font-medium">Status: User Normal - Akses Penuh</p>
          </div>
        </div>
      </div>
      
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Routes yang Diizinkan -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Routes yang Diizinkan untuk NewUser
          </h3>
          <div class="space-y-2">
            <div v-for="route in allowedRoutes" :key="route" class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
              <span class="text-sm font-medium text-green-800">{{ route }}</span>
              <button 
                @click="testRoute(route)"
                class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition duration-200"
              >
                Test
              </button>
            </div>
          </div>
        </div>

        <!-- Routes yang Dibatasi -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            Routes yang Dibatasi untuk NewUser
          </h3>
          <div class="space-y-2">
            <div v-for="route in restrictedRoutes" :key="route" class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
              <span class="text-sm font-medium text-red-800">{{ route }}</span>
              <button 
                @click="testRoute(route)"
                class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition duration-200"
              >
                Test
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Instruksi Testing -->
      <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-4">Cara Testing</h3>
        <div class="text-blue-700 space-y-2">
          <p><strong>Untuk User NewUser:</strong></p>
          <ul class="list-disc list-inside space-y-1 ml-4">
            <li>Routes yang diizinkan akan berhasil diakses</li>
            <li>Routes yang dibatasi akan menampilkan halaman NoPermission</li>
            <li>Klik tombol "Test" untuk mencoba akses route</li>
          </ul>
          
          <p class="mt-4"><strong>Untuk User Normal (user/admin):</strong></p>
          <ul class="list-disc list-inside space-y-1 ml-4">
            <li>Semua routes bisa diakses normal</li>
            <li>Tidak ada pembatasan akses</li>
          </ul>
          
          <p class="mt-4"><strong>Tips:</strong></p>
          <ul class="list-disc list-inside space-y-1 ml-4">
            <li>Login dengan user yang berbeda untuk test berbagai role</li>
            <li>Perhatikan pesan error yang muncul</li>
            <li>Check browser console untuk debugging</li>
          </ul>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
        <button 
          @click="goToSelamatDatang"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          Selamat Datang
        </button>
        
        <button 
          @click="goToDashboard"
          class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          Dashboard
        </button>
        
        <button 
          @click="goToNoPermission"
          class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
          No Permission
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'

// Props
interface Props {
  user: {
    id: number
    name: string
    role?: string
  }
  isNewUser: boolean
  allowedRoutes: string[]
  restrictedRoutes: string[]
}

defineProps<Props>()

// Functions
const testRoute = (route: string) => {
  // Convert route name to URL
  const routeToUrl: Record<string, string> = {
    'selamat-datang.index': '/selamat-datang',
    'dashboard': '/dashboard',
    'users.index': '/users',
    'barang.index': '/barang',
    'peminjaman.index': '/peminjaman',
    'permintaan.index': '/permintaan',
    'inventaris.index': '/inventaris',
    'barang-rusak.index': '/barang-rusak',
    'laporan.index': '/laporan',
    'stok-log.index': '/stok-log',
    'video-berita.index': '/video-berita',
    'riwayat.index': '/riwayat',
    'tentang': '/tentang',
    'bantuan': '/bantuan',
    'notifications': '/notifications'
  }
  
  const url = routeToUrl[route]
  if (url) {
    router.visit(url)
  }
}

const goToSelamatDatang = () => {
  router.visit('/selamat-datang')
}

const goToDashboard = () => {
  router.visit('/dashboard')
}

const goToNoPermission = () => {
  router.visit('/test-no-permission')
}
</script> 