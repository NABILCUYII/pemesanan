﻿<script setup lang="ts">
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
  // Tambahkan field tambahan jika ingin menambah kolom baru
  // created_at?: string;
  // updated_at?: string;
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

const destroy = (id: number) => {
  if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
    router.delete(route('users.destroy', id));
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
                <div class="w-11 h-11 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
                  {{ user.name.charAt(0).toUpperCase() }}
                </div>
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
                    @click="destroy(user.id)"
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
                @click="destroy(user.id)"
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
  </AppLayout>
</template>
