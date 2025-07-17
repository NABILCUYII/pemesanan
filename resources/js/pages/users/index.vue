<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
// Hapus import echo
// import { echo } from '@/echo';

interface User {
  id: number;
  name: string;
  email: string;
  photo?: string;
  role?: { role: string };
}

interface Meta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

interface Props {
  users: User[];
  meta: Meta;
  query: {
    page?: number;
    per_page?: number;
    q?: string;
  };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Users', href: route('users.index') },
];

const searchQuery = ref(props.query.q || '');
const showDeleteModal = ref(false);
const userToDelete = ref<User | null>(null);

// Notifikasi state
const showNotification = ref(false);
const notificationCount = ref(
  props.users.filter(user => user.role && user.role.role === 'newUser').length
); // jumlah user dengan role newUser
const newUserNotification = ref<{ name: string; email: string } | null>(null);

function openNotification() {
  showNotification.value = !showNotification.value;
}

// Pagination states
const currentPage = ref(props.meta.current_page || 1);
const perPage = ref(props.meta.per_page || 10);

const totalPages = computed(() => props.meta.last_page || 1);

// Local users state for client-side pagination
const allUsers = ref<User[]>(props.users);

// If you want to keep server-side search, update allUsers when props.users changes
watch(
  () => props.users,
  (val) => {
    allUsers.value = val;
  }
);

const paginatedUsers = computed(() => {
  // If using server-side search, just return allUsers (which is props.users)
  // If you want client-side search, filter here
  return allUsers.value;
});

// Debounce for search
let searchTimeout: ReturnType<typeof setTimeout> | null = null;
watch(searchQuery, (val) => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    goToPage(1, true);
  }, 400);
});

// Watch perPage
watch(perPage, (val) => {
  goToPage(1, true);
});

// Update currentPage when props.meta.current_page changes (for Inertia navigation)
watch(
  () => props.meta.current_page,
  (val) => {
    currentPage.value = val;
  }
);

const goToPage = (page: number, replace = false) => {
  // Clamp page
  if (page < 1) page = 1;
  if (page > totalPages.value) page = totalPages.value;
  // Update currentPage immediately for UI responsiveness
  currentPage.value = page;
  router.get(
    route('users.index'),
    {
      page,
      per_page: perPage.value,
      q: searchQuery.value || undefined,
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace,
      only: ['users', 'meta'], // Only update users and meta, not full reload
    }
  );
};

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

// Slide pagination logic
const slideRange = 2;
const slidePages = computed(() => {
  const pages = [];
  let start = Math.max(1, currentPage.value - slideRange);
  let end = Math.min(totalPages.value, currentPage.value + slideRange);

  // Adjust if at the start or end
  if (currentPage.value <= slideRange) {
    end = Math.min(totalPages.value, 1 + slideRange * 2);
  }
  if (currentPage.value + slideRange > totalPages.value) {
    start = Math.max(1, totalPages.value - slideRange * 2);
  }

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  return pages;
});

// Hapus listener echo.channel('users')
// onMounted(() => {
//   echo.channel('users')
//     .listen('UserUpdated', (e: any) => {
//       goToPage(currentPage.value, true);
//     });
// });

onUnmounted(() => {
//   echo.leave('users');
});
</script>

<template>
  <Head title="Users" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- Layout lebih ke kiri: hilangkan mx-auto, gunakan margin kiri -->
    <div class="max-w-7xl py-8 px-4 ml-0 md:ml-8">
      <!-- Notifikasi User Baru -->
      <transition name="fade">
        <div
          v-if="newUserNotification"
          class="fixed top-6 right-6 z-50 bg-green-100 border border-green-300 text-green-800 px-6 py-4 rounded-xl shadow-lg flex items-center gap-3 animate-fade-in"
          style="min-width: 280px;"
        >
          <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0-1.104.896-2 2-2s2 .896 2 2-.896 2-2 2-2-.896-2-2zm0 0V7m0 4v4m0 0h4m-4 0H8" />
          </svg>
          <div>
            <div class="font-semibold">User Baru Ditambahkan!</div>
            <div class="text-sm">
              <span class="font-bold">{{ newUserNotification.name }}</span>
              <span v-if="newUserNotification.email">({{ newUserNotification.email }})</span>
            </div>
          </div>
          <button
            class="ml-auto text-green-700 hover:text-green-900 transition"
            @click="newUserNotification = null"
            aria-label="Tutup notifikasi"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </transition>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 mb-1">Daftar Pengguna</h1>
          <p class="text-gray-500 text-sm">Kelola data user aplikasi dengan mudah dan nyaman.</p>
        </div>
        <div class="flex items-center gap-2">
          <!-- Tombol Notifikasi diarahkan ke halaman notifikasi.vue -->
          <Link
            :href="route('notifications')"
            class="relative inline-flex items-center justify-center w-11 h-11 rounded-lg bg-white border border-gray-200 shadow hover:bg-blue-50 transition focus:outline-none"
            aria-label="Notifikasi"
          >
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span v-if="notificationCount > 0" class="absolute top-1 right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
              {{ notificationCount }}
            </span>
          </Link>
          <!-- Tombol Tambah User -->
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
        <div class="relative max-w-md ml-0">
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

      <!-- Pagination Controls (top) -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
        <div>
          <label class="text-sm text-gray-600 mr-2">Tampilkan</label>
          <select v-model.number="perPage" class="border rounded px-2 py-1 text-sm">
            <option :value="5">5</option>
            <option :value="10">10</option>
            <option :value="20">20</option>
            <option :value="50">50</option>
          </select>
          <span class="text-sm text-gray-600 ml-2">per halaman</span>
        </div>
        <div v-if="totalPages > 1" class="flex items-center gap-2 justify-end">
          <!-- Prev Button -->
          <button
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-2 py-1 rounded border text-sm"
            :class="currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white hover:bg-gray-50 text-gray-700'"
            tabindex="0"
            aria-label="Halaman sebelumnya"
          >
            &laquo; Prev
          </button>
          <!-- First page and dots -->
          <template v-if="slidePages[0] > 1">
            <button
              @click="goToPage(1)"
              :disabled="currentPage === 1"
              class="px-2 py-1 rounded border text-sm bg-white hover:bg-gray-50 text-gray-700"
              tabindex="0"
              aria-label="Halaman 1"
            >1</button>
            <span class="text-gray-400 px-1">...</span>
          </template>
          <!-- Numbered page buttons -->
          <button
            v-for="page in slidePages"
            :key="page"
            @click="goToPage(page)"
            :class="[
              'px-2 py-1 rounded border text-sm',
              page === currentPage ? 'bg-blue-600 text-white border-blue-600 cursor-default' : 'bg-white hover:bg-gray-50 text-gray-700 cursor-pointer'
            ]"
            :disabled="page === currentPage"
            :aria-current="page === currentPage ? 'page' : undefined"
            tabindex="0"
            :aria-label="`Halaman ${page}`"
          >
            {{ page }}
          </button>
          <!-- Last page and dots -->
          <template v-if="slidePages[slidePages.length-1] < totalPages">
            <span class="text-gray-400 px-1">...</span>
            <button
              @click="goToPage(totalPages)"
              :disabled="currentPage === totalPages"
              class="px-2 py-1 rounded border text-sm bg-white hover:bg-gray-50 text-gray-700"
              tabindex="0"
              :aria-label="`Halaman ${totalPages}`"
            >{{ totalPages }}</button>
          </template>
          <!-- Next Button -->
          <button
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="px-2 py-1 rounded border text-sm"
            :class="currentPage === totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white hover:bg-gray-50 text-gray-700'"
            tabindex="0"
            aria-label="Halaman berikutnya"
          >
            Next &raquo;
          </button>
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
            <tr v-if="paginatedUsers.length === 0">
              <td colspan="5" class="px-8 py-6 text-center text-gray-400 text-base">
                <span v-if="searchQuery">Tidak ada hasil pencarian</span>
                <span v-else>Tidak ada user ditemukan</span>
              </td>
            </tr>
            <tr
              v-for="(user, idx) in paginatedUsers"
              :key="user.id"
              class="border-b last:border-b-0 border-gray-100 hover:bg-blue-50/40 transition"
            >
              <td class="px-8 py-4 text-gray-500 text-sm">{{ (props.meta.per_page * (props.meta.current_page - 1)) + idx + 1 }}</td>
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
        <div v-if="paginatedUsers.length === 0" class="md:hidden text-center text-gray-400 py-10 text-base">
          <span v-if="searchQuery">Tidak ada hasil pencarian</span>
          <span v-else>Tidak ada user ditemukan</span>
        </div>

        <div class="space-y-5 md:hidden p-4">
          <div
            v-for="user in paginatedUsers"
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

      <!-- Pagination Controls (bottom) -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between mt-4 gap-2">
        <div>
          <label class="text-sm text-gray-600 mr-2">Tampilkan</label>
          <select v-model.number="perPage" class="border rounded px-2 py-1 text-sm">
            <option :value="5">5</option>
            <option :value="10">10</option>
            <option :value="20">20</option>
            <option :value="50">50</option>
          </select>
          <span class="text-sm text-gray-600 ml-2">per halaman</span>
        </div>
        <div v-if="totalPages > 1" class="flex items-center gap-2 justify-end">
          <!-- Prev Button -->
          <button
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-2 py-1 rounded border text-sm"
            :class="currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white hover:bg-gray-50 text-gray-700'"
            tabindex="0"
            aria-label="Halaman sebelumnya"
          >
            &laquo; Prev
          </button>
          <!-- First page and dots -->
          <template v-if="slidePages[0] > 1">
            <button
              @click="goToPage(1)"
              :disabled="currentPage === 1"
              class="px-2 py-1 rounded border text-sm bg-white hover:bg-gray-50 text-gray-700"
              tabindex="0"
              aria-label="Halaman 1"
            >1</button>
            <span class="text-gray-400 px-1">...</span>
          </template>
          <!-- Numbered page buttons -->
          <button
            v-for="page in slidePages"
            :key="page"
            @click="goToPage(page)"
            :class="[
              'px-2 py-1 rounded border text-sm',
              page === currentPage ? 'bg-blue-600 text-white border-blue-600 cursor-default' : 'bg-white hover:bg-gray-50 text-gray-700 cursor-pointer'
            ]"
            :disabled="page === currentPage"
            :aria-current="page === currentPage ? 'page' : undefined"
            tabindex="0"
            :aria-label="`Halaman ${page}`"
          >
            {{ page }}
          </button>
          <!-- Last page and dots -->
          <template v-if="slidePages[slidePages.length-1] < totalPages">
            <span class="text-gray-400 px-1">...</span>
            <button
              @click="goToPage(totalPages)"
              :disabled="currentPage === totalPages"
              class="px-2 py-1 rounded border text-sm bg-white hover:bg-gray-50 text-gray-700"
              tabindex="0"
              :aria-label="`Halaman ${totalPages}`"
            >{{ totalPages }}</button>
          </template>
          <!-- Next Button -->
          <button
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="px-2 py-1 rounded border text-sm"
            :class="currentPage === totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white hover:bg-gray-50 text-gray-700'"
            tabindex="0"
            aria-label="Halaman berikutnya"
          >
            Next &raquo;
          </button>
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
