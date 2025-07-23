<template>
  <AppLayout>
    <!-- Main Content -->
    <div class="flex-1">
      <div class="max-w-2xl mx-auto mt-10 p-6 bg-white rounded-lg shadow">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Notifikasi Pengguna Baru</h1>
        <div class="mb-4">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari nama atau email pengguna..."
            class="block w-full px-4 py-2 border border-gray-200 rounded-lg"
          />
        </div>
        <div v-if="filteredUsers.length === 0" class="text-gray-500 text-center py-8">
          Tidak ada notifikasi pengguna baru.
        </div>
        <ul v-else>
          <li
            v-for="user in filteredUsers"
            :key="user.id"
            class="flex items-center justify-between p-4 mb-3 bg-blue-50 rounded border border-blue-100"
          >
            <div>
              <div class="font-semibold text-blue-700">
                {{ user.name }}
                <span
                  v-if="(typeof user.role === 'string' ? user.role : user.role?.role) === 'penggunaBARU'"
                  class="ml-2 px-2 py-0.5 text-xs bg-yellow-200 text-yellow-800 rounded"
                  >Pengguna Baru</span
                >
              </div>
              <div class="text-sm text-gray-500">
                Email: {{ user.email }}
              </div>
              <div class="text-xs text-gray-400">
                Terdaftar pada: {{ formatDate(user.created_at) }}
              </div>
              <div class="text-xs mt-1">
                <span :class="'inline-block px-2 py-0.5 rounded ' + statusBadge(user.status || undefined)">
                  {{ user.status === 'active' ? 'Aktif' : user.status === 'rejected' ? 'Ditolak' : 'Menunggu Persetujuan' }}
                </span>
              </div>
            </div>
            <div>
              <button
                @click="openApproveModal(user)"
                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition mr-2"
                >Setujui</button
              >
              <button
                @click="openRejectModal(user)"
                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition"
                >Tolak</button
              >
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Modal Setujui -->
    <div
      v-if="showApproveModal"
      class="fixed inset-0 z-50 flex items-center justify-center modal-blur-bg"
    >
      <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6 relative">
        <button
          class="absolute top-2 right-2 text-gray-400 hover:text-gray-600"
          @click="closeApproveModal"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <div class="mb-4">
          <h2 class="text-lg font-bold text-gray-800 mb-2">Konfirmasi Persetujuan</h2>
          <p class="text-gray-600">
            Apakah Anda yakin ingin <span class="font-semibold text-green-600">menyetujui</span> user <span class="font-semibold">{{ approveUserData?.name }}</span>?
          </p>
        </div>
        <div class="flex justify-end gap-2 mt-6">
          <button
            @click="closeApproveModal"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition"
          >Batal</button>
          <button
            @click="confirmApproveUser"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition"
          >Setujui</button>
        </div>
      </div>
    </div>

    <!-- Modal Tolak -->
    <div
      v-if="showRejectModal"
      class="fixed inset-0 z-50 flex items-center justify-center modal-blur-bg"
    >
      <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6 relative">
        <button
          class="absolute top-2 right-2 text-gray-400 hover:text-gray-600"
          @click="closeRejectModal"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <div class="mb-4">
          <h2 class="text-lg font-bold text-gray-800 mb-2">Konfirmasi Penolakan</h2>
          <p class="text-gray-600">
            Apakah Anda yakin ingin <span class="font-semibold text-red-600">menolak</span> user <span class="font-semibold">{{ rejectUserData?.name }}</span>?
          <!-- INSERT_YOUR_CODE -->
          <!--
            Teks tambahan sesuai permintaan:
            -->
          <br>
          <span class="italic text-xs text-gray-400">Dengan ini Anda akan menolak permintaan pengguna untuk bergabung.</span>
          </p>
        </div>
        <div class="flex justify-end gap-2 mt-6">
          <button
            @click="closeRejectModal"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition"
          >Batal</button>
          <button
            @click="confirmRejectUser"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
          >Tolak</button>
        </div>
      </div>
    </div>

    <!-- Modal Berhasil Disetujui -->
    <div
      v-if="showApprovedSuccessModal"
      class="fixed inset-0 z-50 flex items-center justify-center modal-blur-bg"
    >
      <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6 relative">
        <button
          class="absolute top-2 right-2 text-gray-400 hover:text-gray-600"
          @click="closeApprovedSuccessModal"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <div class="flex flex-col items-center text-center">
          <svg class="w-14 h-14 text-green-500 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="#dcfce7"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4" stroke="#22c55e"/>
          </svg>
          <h2 class="text-lg font-bold text-green-700 mb-2">Berhasil Disetujui!</h2>
          <p class="text-gray-600 mb-4">
            User <span class="font-semibold">{{ approvedUserName }}</span> telah disetujui.
          </p>
          <button
            @click="closeApprovedSuccessModal"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition"
          >Tutup</button>
        </div>
      </div>
    </div>

    <!-- Modal Berhasil Ditolak -->
    <div
      v-if="showRejectedSuccessModal"
      class="fixed inset-0 z-50 flex items-center justify-center modal-blur-bg"
    >
      <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6 relative">
        <button
          class="absolute top-2 right-2 text-gray-400 hover:text-gray-600"
          @click="closeRejectedSuccessModal"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <div class="flex flex-col items-center text-center">
          <svg class="w-14 h-14 text-red-500 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="#fee2e2"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9l-6 6M9 9l6 6" stroke="#ef4444"/>
          </svg>
          <h2 class="text-lg font-bold text-red-700 mb-2">Berhasil Ditolak!</h2>
          <p class="text-gray-600 mb-4">
            User <span class="font-semibold">{{ rejectedUserName }}</span> telah ditolak.
          </p>
          <button
            @click="closeRejectedSuccessModal"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
          >Tutup</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';

interface User {
  id: number;
  name: string;
  email: string;
  role?: { role: string } | string | null;
  status?: string | null;
  created_at?: string;
}

const props = defineProps<{ users: User[] }>();
const searchQuery = ref('');

const filteredUsers = computed(() => {
  const query = searchQuery.value.toLowerCase().trim();
  return props.users.filter(user =>
    user.name.toLowerCase().includes(query) ||
    user.email.toLowerCase().includes(query)
  );
});

// Modal state
const showApproveModal = ref(false);
const approveUserData = ref<User | null>(null);

// Modal untuk "Tolak"
const showRejectModal = ref(false);
const rejectUserData = ref<User | null>(null);

// Modal untuk "Berhasil Disetujui!"
const showApprovedSuccessModal = ref(false);
const approvedUserName = ref<string>('');

// Modal untuk "Berhasil Ditolak!"
const showRejectedSuccessModal = ref(false);
const rejectedUserName = ref<string>('');

function openApproveModal(user: User) {
  approveUserData.value = user;
  showApproveModal.value = true;
}
function closeApproveModal() {
  showApproveModal.value = false;
  approveUserData.value = null;
}
function confirmApproveUser() {
  if (approveUserData.value) {
    router.post(route('users.approve', approveUserData.value.id), {}, {
      onSuccess: () => {
        approvedUserName.value = approveUserData.value?.name || '';
        showApproveModal.value = false;
        showApprovedSuccessModal.value = true;
        approveUserData.value = null;
      }
    });
  }
}
function closeApprovedSuccessModal() {
  showApprovedSuccessModal.value = false;
  approvedUserName.value = '';
}

function openRejectModal(user: User) {
  rejectUserData.value = user;
  showRejectModal.value = true;
}
function closeRejectModal() {
  showRejectModal.value = false;
  rejectUserData.value = null;
}
function confirmRejectUser() {
  if (rejectUserData.value) {
    router.post(route('users.reject', rejectUserData.value.id), {}, {
      onSuccess: () => {
        rejectedUserName.value = rejectUserData.value?.name || '';
        showRejectModal.value = false;
        showRejectedSuccessModal.value = true;
        rejectUserData.value = null;
      }
    });
  }
}
function closeRejectedSuccessModal() {
  showRejectedSuccessModal.value = false;
  rejectedUserName.value = '';
}

function formatDate(dateStr?: string) {
  if (!dateStr) return '-';
  const date = new Date(dateStr);
  return date.toLocaleDateString('id-ID', {
    year: 'numeric', month: 'long', day: 'numeric',
    hour: '2-digit', minute: '2-digit'
  });
}
function statusBadge(status?: string) {
  if (status === 'active') return 'bg-green-100 text-green-700 border border-green-200';
  if (status === 'rejected') return 'bg-red-100 text-red-700 border border-red-200';
  return 'bg-yellow-100 text-yellow-800 border border-yellow-200';
}
</script>

<style scoped>
/* Tambahan styling opsional */
.modal-blur-bg {
  background: rgba(0,0,0,0.3);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}
</style>
