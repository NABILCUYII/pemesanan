<template>
  <Head title="Daftar Barang Rusak & Hilang" />
  <AppLayout>
    <template #header>
      <div class="flex items-center gap-3">
        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 21h6l-.75-4M4 7h16M5 7l.867 12.142A2 2 0 007.862 21h8.276a2 2 0 001.995-1.858L19 7M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3" />
        </svg>
        <h2 class="font-bold text-2xl text-gray-800 leading-tight tracking-tight">
          Daftar Barang Rusak & Hilang
        </h2>
      </div>
    </template>

    <div class="py-10 bg-gradient-to-br from-gray-50 to-white min-h-screen">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/80 backdrop-blur-md shadow-2xl rounded-2xl border border-gray-100">
          <div class="p-8">
            <!-- Header & Actions -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-6 mb-8">
              <div>
                <h3 class="text-2xl font-semibold text-gray-900 flex items-center gap-2">
                  <span>Barang Rusak & Hilang</span>
                  <span class="ml-2 px-2 py-0.5 rounded-full bg-gray-200 text-xs font-bold text-gray-700">
                    {{ barangRusak.length }} item
                  </span>
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                  Daftar semua barang yang memiliki status <span class="font-semibold text-red-500">rusak</span> atau <span class="font-semibold text-yellow-500">hilang</span>.
                </p>
                <!-- Statistics -->
                <div class="flex gap-4 mt-3">
                  <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="text-sm text-gray-700 font-medium">
                      Rusak: <span class="font-bold">{{ getBarangByStatus('rusak').length }}</span>
                    </span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                    <span class="text-sm text-gray-700 font-medium">
                      Hilang: <span class="font-bold">{{ getBarangByStatus('hilang').length }}</span>
                    </span>
                  </div>
                </div>
              </div>
              <div class="flex gap-2">
                <button
                  @click="tambahBarangRusak"
                  class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-500 to-pink-500 shadow-lg border-none rounded-lg font-semibold text-sm text-white uppercase tracking-wider hover:scale-105 hover:from-red-600 hover:to-pink-600 transition-all duration-150"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  Tambah Rusak
                </button>
                <button
                  @click="tambahBarangHilang"
                  class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-yellow-400 to-yellow-600 shadow-lg border-none rounded-lg font-semibold text-sm text-white uppercase tracking-wider hover:scale-105 hover:from-yellow-500 hover:to-yellow-700 transition-all duration-150"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  Tambah Hilang
                </button>
              </div>
            </div>

            <!-- Search Bar -->
            <div class="mb-8">
              <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
                <input
                  v-model="searchQuery"
                  type="text"
                  class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg shadow-sm bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-base"
                  placeholder="Cari barang berdasarkan kode, nama, kategori, deskripsi, atau status..."
                />
                <div v-if="searchQuery" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                  <button
                    @click="clearSearch"
                    class="text-gray-400 hover:text-gray-600"
                  >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Filter Tabs -->
            <div class="mb-8">
              <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                <button
                  @click="activeFilter = 'all'"
                  :class="[
                    'px-5 py-2 rounded-full font-semibold text-sm transition-all duration-150',
                    activeFilter === 'all'
                      ? 'bg-blue-100 text-blue-700 shadow'
                      : 'bg-gray-100 text-gray-500 hover:bg-blue-50 hover:text-blue-600'
                  ]"
                >
                  Semua ({{ barangRusak.length }})
                </button>
                <button
                  @click="activeFilter = 'rusak'"
                  :class="[
                    'px-5 py-2 rounded-full font-semibold text-sm transition-all duration-150',
                    activeFilter === 'rusak'
                      ? 'bg-red-100 text-red-700 shadow'
                      : 'bg-gray-100 text-gray-500 hover:bg-red-50 hover:text-red-600'
                  ]"
                >
                  Rusak ({{ getBarangByStatus('rusak').length }})
                </button>
                <button
                  @click="activeFilter = 'hilang'"
                  :class="[
                    'px-5 py-2 rounded-full font-semibold text-sm transition-all duration-150',
                    activeFilter === 'hilang'
                      ? 'bg-yellow-100 text-yellow-700 shadow'
                      : 'bg-gray-100 text-gray-500 hover:bg-yellow-50 hover:text-yellow-600'
                  ]"
                >
                  Hilang ({{ getBarangByStatus('hilang').length }})
                </button>
                
              </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-xl shadow-inner border border-gray-100">
              <table class="min-w-full divide-y divide-gray-200 bg-white rounded-xl">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                  <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                      Kode Barang
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                      Nama Barang
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                      Kategori
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                      Stok
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                      Status
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                      Aksi
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                  <tr v-for="barang in filteredBarang" :key="barang.id" class="hover:bg-blue-50/50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                      {{ barang.kode_barang }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ barang.nama_barang }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <span class="inline-flex px-3 py-1 text-xs font-bold rounded-full"
                        :class="{
                          'bg-blue-100 text-blue-800': barang.kategori === 'peminjaman',
                          'bg-green-100 text-green-800': barang.kategori === 'permintaan',
                          'bg-gray-100 text-gray-700': barang.kategori !== 'peminjaman' && barang.kategori !== 'permintaan'
                        }">
                        {{ barang.kategori }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <span class="font-mono font-bold text-gray-700">{{ barang.stok }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <span class="inline-flex px-3 py-1 text-xs font-bold rounded-full capitalize"
                        :class="{
                          'bg-red-100 text-red-700': barang.status === 'rusak',
                          'bg-yellow-100 text-yellow-700': barang.status === 'hilang',
                          'bg-green-100 text-green-700': barang.status === 'baik'
                        }">
                        {{ barang.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div class="flex gap-2">
                        <select 
                          v-model="barang.status" 
                          @change="updateStatus(barang)"
                          class="text-sm border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white shadow"
                        >
                          <option value="baik">Baik</option>
                          <option value="rusak">Rusak</option>
                          <option value="hilang">Hilang</option>
                        </select>
                        <button
                          @click="deleteBarang(barang)"
                          class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-bold rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-400 transition"
                          title="Hapus barang"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Empty State -->
            <div v-if="filteredBarang.length === 0" class="flex flex-col items-center justify-center py-16">
              <div class="bg-gradient-to-br from-gray-100 to-white rounded-full p-6 shadow-inner">
                <svg class="mx-auto h-14 w-14 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
              </div>
              <h3 class="mt-6 text-lg font-bold text-gray-700">
                {{ searchQuery ? 'Tidak ada hasil pencarian' : 
                   activeFilter === 'all' ? 'Tidak ada barang rusak atau hilang' : 
                   activeFilter === 'rusak' ? 'Tidak ada barang rusak' : 'Tidak ada barang hilang' }}
              </h3>
              <p class="mt-2 text-base text-gray-500">
                {{ searchQuery ? 'Coba ubah kata kunci pencarian Anda.' :
                   activeFilter === 'all' ? 'Saat ini tidak ada barang yang memiliki status rusak atau hilang.' :
                   activeFilter === 'rusak' ? 'Saat ini tidak ada barang yang memiliki status rusak.' :
                   'Saat ini tidak ada barang yang memiliki status hilang.' }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface Barang {
  id: number;
  kode_barang: string;
  nama_barang: string;
  deskripsi: string;
  kategori: string;
  stok: number;
  status: string;
}

const props = defineProps<{
  barangRusak: Barang[];
}>();

const activeFilter = ref<'all' | 'rusak' | 'hilang'>('all');
const searchQuery = ref('');

const getBarangByStatus = (status: string) => {
  return props.barangRusak.filter(barang => barang.status === status);
};

// Full search: kode_barang, nama_barang, kategori, deskripsi, status, stok
const filteredBarang = computed(() => {
  let filtered = props.barangRusak;
  
  // Apply status filter
  if (activeFilter.value !== 'all') {
    filtered = filtered.filter(barang => barang.status === activeFilter.value);
  }
  
  // Apply full search filter
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim();
    filtered = filtered.filter(barang => 
      (barang.nama_barang && barang.nama_barang.toLowerCase().includes(query)) ||
      (barang.kode_barang && barang.kode_barang.toLowerCase().includes(query)) ||
      (barang.kategori && barang.kategori.toLowerCase().includes(query)) ||
      (barang.deskripsi && barang.deskripsi.toLowerCase().includes(query)) ||
      (barang.status && barang.status.toLowerCase().includes(query)) ||
      (barang.stok !== undefined && barang.stok.toString().toLowerCase().includes(query))
    );
  }
  
  return filtered;
});

const clearSearch = () => {
  searchQuery.value = '';
};

const updateStatus = (barang: Barang) => {
  router.put(`/barang-rusak/${barang.id}/status`, {
    status: barang.status
  }, {
    onSuccess: () => {
      // Refresh the page to show updated data
      router.reload()
    }
  })
}

const deleteBarang = (barang: Barang) => {
  if (confirm(`Apakah Anda yakin ingin menghapus barang "${barang.nama_barang}"?`)) {
    router.delete(`/barang/${barang.id}`, {
      onSuccess: () => {
        router.reload()
      }
    });
  }
}

const tambahBarangRusak = () => {
  router.visit('/barang-rusak/create')
}

const tambahBarangHilang = () => {
  router.visit('/barang-hilang/create')
}
</script>