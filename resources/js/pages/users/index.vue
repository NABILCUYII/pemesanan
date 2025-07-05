<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { ref, computed } from 'vue';

interface User {
  id: number;
  name: string;
  email: string;
  role: {
    role: string;
  };
}

const props = defineProps<{
  users: User[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Users',
    href: route('users.index'),
  },
];

const searchQuery = ref('');

// Modal state
const showDeleteModal = ref(false);
const userToDelete = ref<User | null>(null);

const filteredUsers = computed(() => {
  if (!searchQuery.value.trim()) {
    return props.users;
  }
  
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
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex justify-between items-center">
        <h1 class="text-xl font-semibold text-gray-800">Users</h1>
        <Link
          :href="route('users.create')"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          Create User
        </Link>
      </div>

      <!-- Search Bar -->
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari nama atau email pengguna..."
          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
        />
      </div>

      <div class="relative flex-1 rounded-xl border border-gray-200 bg-white p-4">
        <!-- Scroll horizontal untuk tabel -->
        <div class="overflow-x-auto">
          <!-- Tabel untuk layar medium ke atas -->
          <table class="w-full min-w-[600px] hidden md:table">
            <thead>
              <tr class="border-b border-gray-200">
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Name</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Email</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Role</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="filteredUsers.length === 0">
                <td colspan="4" class="px-4 py-3 text-center text-gray-500">
                  {{ searchQuery ? 'Tidak ada hasil pencarian' : 'No users found' }}
                </td>
              </tr>
              <tr
                v-for="user in filteredUsers"
                :key="user.id"
                class="border-b border-gray-200 hover:bg-gray-50"
              >
                <td class="px-4 py-3 text-gray-700">{{ user.name }}</td>
                <td class="px-4 py-3 text-gray-700">{{ user.email }}</td>
                <td class="px-4 py-3">
                  <span
                    class="inline-flex items-center rounded-full bg-green-500 px-2.5 py-0.5 text-xs font-medium text-white"
                  >
                    {{ user.role?.role || 'No role' }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex gap-4">
                    <Link
                      :href="route('users.edit', user.id)"
                      class="text-sm text-blue-600 hover:text-blue-800"
                    >
                      Edit
                    </Link>
                    <button
                      @click="confirmDelete(user)"
                      class="text-sm text-red-600 hover:text-red-800"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Card list untuk layar kecil -->
          <div v-if="filteredUsers.length === 0" class="md:hidden text-center text-gray-500 py-8">
            {{ searchQuery ? 'Tidak ada hasil pencarian' : 'No users found' }}
          </div>

          <div class="space-y-4 md:hidden">
            <div
              v-for="user in filteredUsers"
              :key="user.id"
              class="border border-gray-200 rounded-lg p-4 shadow-sm bg-white"
            >
              <div class="flex justify-between items-center mb-2">
                <h2 class="text-lg font-semibold text-gray-800">{{ user.name }}</h2>
                <span
                  class="inline-flex items-center rounded-full bg-green-500 px-2 py-0.5 text-xs font-medium text-white"
                >
                  {{ user.role?.role || 'No role' }}
                </span>
              </div>
              <p class="text-gray-600 mb-4">{{ user.email }}</p>
              <div class="flex gap-6">
                <Link
                  :href="route('users.edit', user.id)"
                  class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center gap-1"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                  </svg>
                  Edit
                </Link>
                <button
                  @click="confirmDelete(user)"
                  class="text-red-600 hover:text-red-800 text-sm font-medium inline-flex items-center gap-1"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                  Delete
                </button>
              </div>
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
