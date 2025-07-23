<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface User {
  id: number;
  name: string;
  email: string;
  photo?: string;
  role?: { role: string };
}

interface Props {
  users: User[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Users', href: route('users.index') },
];

const searchQuery = ref('');
const showDeleteModal = ref(false);
const userToDelete = ref<User | null>(null);

const filteredUsers = computed(() => {
  const query = searchQuery.value.toLowerCase().trim();
  return props.users.filter(user => 
    user.name.toLowerCase().includes(query) ||
    user.email.toLowerCase().includes(query)
  );
});

const confirmDelete = (user: User) => {
  userToDelete.value = user;
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  userToDelete.value = null;
};

const destroy = () => {
  if (userToDelete.value) {
    router.delete(route('users.destroy', userToDelete.value.id), {
      onFinish: () => {
        closeDeleteModal();
      }
    });
  }
};
</script>

<template>
  <Head title="Users" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto max-w-7xl py-8 px-4">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 mb-1">Daftar Pengguna</h1>
          <p class="text-gray-500 text-sm">Kelola data user aplikasi dengan mudah dan nyaman.</p>
        </div>
        <div class="flex items-center gap-2">
          <!-- Notifikasi Button to NEWuser -->
          <Link
            :href="route('users.NEWuser')"
            class="relative inline-flex items-center px-4 py-2.5 bg-yellow-100 text-yellow-800 rounded-lg shadow font-semibold hover:bg-yellow-200 transition"
            style="min-width: 0;"
          >
            <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
            <span class="text-sm font-bold uppercase tracking-wide">NEWuser</span>
            <span class="absolute -top-1 -right-1 flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
            </span>
          </Link>
          <Link
            :href="route('users.create')"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah User
          </Link>
        </div>
      </div>

      <!-- Search Bar -->
      <div class="mb-6">
        <div class="relative max-w-md mx-auto">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari nama atau email pengguna..."
            class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg shadow-sm leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
          />
        </div>
      </div>

      <div class="relative rounded-2xl border border-gray-100 bg-white shadow-lg overflow-x-auto">
        <!-- Tabel untuk layar medium ke atas -->
        <table class="w-full min-w-[950px] hidden md:table">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-8 py-4 text-left text-sm font-semibold text-gray-600">#</th>
              <th class="px-8 py-4 text-left text-sm font-semibold text-gray-600">Nama</th>
              <th class="px-8 py-4 text-left text-sm font-semibold text-gray-600">Email</th>
              <th class="px-8 py-4 text-left text-sm font-semibold text-gray-600">Role</th>
              <th class="px-8 py-4 text-left text-sm font-semibold text-gray-600">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredUsers.length === 0">
              <td colspan="5" class="px-8 py-6 text-center text-gray-400 text-base">
                <span v-if="searchQuery">Tidak ada hasil pencarian</span>
                <span v-else>Tidak ada user ditemukan</span>
              </td>
            </tr>
            <tr
              v-for="(user, idx) in filteredUsers"
              :key="user.id"
              class="border-b last:border-b-0 border-gray-100 hover:bg-blue-50/40 transition"
            >
              <td class="px-8 py-4 text-gray-500 text-sm">{{ idx + 1 }}</td>
              <td class="px-8 py-4 text-gray-800 font-medium flex items-center gap-3">
                 <!-- User photo profile like in UserMenuContent -->
                 <template v-if="user && user.photo">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-200 overflow-hidden">
                        <img
                            :src="user.photo.startsWith?.('http') ? user.photo : `/storage/${user.photo}`"
                            alt="User avatar"
                            class="w-full h-full object-cover"
                            @error="e => {
                                const target = e.target as HTMLImageElement | null;
                                if (target) {
                                    target.style.display = 'none';
                                    const parent = target.parentElement as HTMLElement | null;
                                    if (parent) {
                                        parent.innerHTML = `<span class='text-gray-600 font-semibold'>${user?.name?.charAt(0) || '?'}</span>`;
                                    }
                                }
                            }"
                        />
                    </span>
                 </template>
                        <template v-else>
                            <div class="w-9 h-9 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-semibold">
                                {{ user?.name?.charAt(0) || '?' }}
                            </div>
                        </template>
               <span class="text-base">{{ user.name }}</span>
              </td>
              <td class="px-8 py-4 text-gray-700 text-base">{{ user.email }}</td>
              <td class="px-8 py-4">
                <span
                  class="inline-flex items-center rounded-full px-4 py-1 text-sm font-semibold"
                  :class="user.role?.role === 'admin' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-700'"
                >
                  {{ user.role?.role || 'No role' }}
                </span>
              </td>
              <td class="px-8 py-4">
                <div class="flex gap-3">
                  <Link
                    :href="route('users.edit', user.id)"
                    class="inline-flex items-center gap-1 px-4 py-2 text-base font-medium rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6-6 3 3-6 6H9v-3z"/>
                    </svg>
                    Edit
                  </Link>
                  <button
                    @click="confirmDelete(user)"
                    class="inline-flex items-center gap-1 px-4 py-2 text-base font-medium rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Card list untuk layar kecil -->
        <div v-if="filteredUsers.length === 0" class="md:hidden text-center text-gray-400 py-10 text-base">
          <span v-if="searchQuery">Tidak ada hasil pencarian</span>
          <span v-else>Tidak ada user ditemukan</span>
        </div>

        <div class="space-y-5 md:hidden p-4">
          <div
            v-for="user in filteredUsers"
            :key="user.id"
            class="border border-gray-100 rounded-xl p-6 shadow bg-white flex flex-col gap-3"
          >
            <div class="flex items-center gap-4 mb-2">
              <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
                {{ user.name.charAt(0).toUpperCase() }}
              </div>
              <div>
                <h2 class="text-lg font-semibold text-gray-800">{{ user.name }}</h2>
                <span
                  class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold"
                  :class="user.role?.role === 'admin' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-700'"
                >
                  {{ user.role?.role || 'No role' }}
                </span>
              </div>
            </div>
            <p class="text-gray-600 text-base mb-2">{{ user.email }}</p>
            <div class="flex gap-4">
              <Link
                :href="route('users.edit', user.id)"
                class="inline-flex items-center gap-1 px-4 py-2 text-base font-medium rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6-6 3 3-6 6H9v-3z"/>
                </svg>
                Edit
              </Link>
              <button
                @click="confirmDelete(user)"
                class="inline-flex items-center gap-1 px-4 py-2 text-base font-medium rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Hapus
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Konfirmasi Hapus User -->
    <transition name="fade">
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 z-50 flex items-center justify-center"
      >
        <!-- Backdrop, semi-transparent and blurred, but not covering with color -->
        <div
          class="absolute inset-0 bg-black/20 backdrop-blur-sm"
          @click="closeDeleteModal"
        ></div>
        <!-- Modal Card -->
        <div
          class="relative z-10 max-w-md w-full mx-4 rounded-2xl shadow-2xl bg-gradient-to-br from-green-200 via-white to-teal-100 border border-green-300 p-8 animate-fade-in"
        >
          <!-- Close Button -->
          <button
            @click="closeDeleteModal"
            class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 transition"
            aria-label="Tutup"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
          <div class="flex flex-col items-center text-center">
            <div class="bg-red-100 rounded-full p-3 mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
              </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Konfirmasi Hapus User</h2>
            <p class="text-gray-700 mb-6">
              Apakah Anda yakin ingin menghapus user
              <span class="font-bold text-red-600">{{ userToDelete?.name }}</span>
              ({{ userToDelete?.email }})?
              <br />
              <span class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</span>
            </p>
            <div class="flex w-full justify-center gap-4">
              <button
                @click="closeDeleteModal"
                class="px-5 py-2 rounded-lg bg-gray-100 text-gray-700 font-medium hover:bg-gray-200 transition"
              >
                Batal
              </button>
              <button
                @click="destroy"
                class="px-5 py-2 rounded-lg bg-gradient-to-r from-red-500 to-pink-500 text-white font-semibold shadow hover:from-red-600 hover:to-pink-600 transition"
              >
                Hapus
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </AppLayout>
</template>
