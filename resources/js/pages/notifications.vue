<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
// import { echo } from '@/echo';

interface NewUserNotification {
  id: number;
  name: string;
  email: string;
  created_at: string;
}

const notifications = ref<NewUserNotification[]>([]);
const loading = ref(true);

async function fetchNewUserNotifications() {
  loading.value = true;
  try {
    const res = await fetch('/api/new-users');
    const data = await res.json();
    notifications.value = data;
  } catch (e) {
    notifications.value = [];
  }
  loading.value = false;
}

function handleRefresh() {
  fetchNewUserNotifications();
}

async function acceptUser(userId: number) {
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const res = await fetch(`/api/new-users/${userId}/accept`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token || '',
        'Content-Type': 'application/json',
        // ...other headers
      },
    });
    if (res.ok) {
      // Hapus user dari daftar notifikasi
      notifications.value = notifications.value.filter(n => n.id !== userId);
    }
  } catch (e) {
    // Optional: tampilkan error
    alert('Gagal meng-accept user.');
  }
}

onMounted(() => {
  fetchNewUserNotifications();
  // echo.channel('admin-notifications')
  //   .listen('NewUserRegistered', (e: any) => {
  //     notifications.value.unshift(e.user);
  //   });
});

onUnmounted(() => {
  // echo.leave('admin-notifications');
});

// Helper to format date
function formatDate(dateStr: string) {
  const date = new Date(dateStr);
  return date.toLocaleString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}
</script>

<template>
  <Head title="Notifikasi User Baru" />
  <AppLayout>
    <div class="max-w-2xl py-10 px-4">
      <div class="flex items-center justify-start mb-8">
        <div class="flex items-center gap-3">
          <svg class="w-9 h-9 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          <div>
            <h1 class="text-3xl font-extrabold text-gray-800 leading-tight">Notifikasi User Baru</h1>
            <p class="text-gray-500 text-sm mt-1">Daftar user yang baru saja mendaftar ke aplikasi.</p>
          </div>
        </div>
        <button
          @click="handleRefresh"
          :disabled="loading"
          class="ml-4 inline-flex items-center justify-center w-11 h-11 bg-white border border-blue-200 text-blue-600 rounded-full shadow hover:bg-blue-50 transition disabled:opacity-50 disabled:cursor-not-allowed"
          aria-label="Refresh"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582M20 20v-5h-.581M5.42 19A9 9 0 0021 12.082M18.36 6.64A9 9 0 003 11.918" />
          </svg>
        </button>
      </div>
      <div v-if="loading" class="flex flex-col items-start justify-center py-16">
        <svg class="animate-spin h-8 w-8 text-blue-500 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        <span class="text-gray-500 text-lg">Memuat notifikasi...</span>
      </div>
      <div v-else>
        <div v-if="notifications.length === 0" class="flex flex-col items-start justify-center py-16">
          <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          <span class="text-gray-400 text-lg">Belum ada user baru yang mendaftar.</span>
        </div>
        <ul v-else class="space-y-6">
          <transition-group name="fade" tag="div">
            <li
              v-for="notif in notifications"
              :key="notif.id"
              class="relative bg-gradient-to-br from-blue-50 via-white to-blue-100 p-6 rounded-2xl shadow-lg border border-blue-100 hover:shadow-xl transition group"
            >
              <div class="flex items-center gap-4 mb-2">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 text-xl font-bold shadow-inner group-hover:scale-105 transition">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2">
                    <span class="font-semibold text-lg text-gray-800 group-hover:text-blue-700 transition">{{ notif.name }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-blue-500 text-white rounded-full ml-2">
                      User Baru
                    </span>
                  </div>
                  <div class="text-gray-500 text-sm mt-0.5 flex items-center gap-1">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16 17v1a3 3 0 11-6 0v-1m6 0H9m6 0v-1a6 6 0 10-12 0v1m12 0v-1a6 6 0 10-12 0v1" />
                    </svg>
                    <span>{{ notif.email }}</span>
                  </div>
                </div>
                <div class="flex flex-col items-end">
                  <span class="text-xs text-gray-400 font-mono">{{ formatDate(notif.created_at) }}</span>
                  <button
                    class="mt-3 px-4 py-1.5 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition text-sm font-semibold"
                    @click="acceptUser(notif.id)"
                  >
                    Accept
                  </button>
                </div>
              </div>
            </li>
          </transition-group>
        </ul>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: all 0.3s cubic-bezier(.4,0,.2,1);
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>