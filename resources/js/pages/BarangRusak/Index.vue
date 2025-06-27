<template>
  <Head title="Daftar Barang Rusak & Hilang" />
  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Daftar Barang Rusak & Hilang
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <div>
                <h3 class="text-lg font-medium text-gray-900">
                  Barang Rusak & Hilang ({{ barangRusak.length }} item)
                </h3>
                <p class="text-sm text-gray-500">
                  Daftar semua barang yang memiliki status rusak atau hilang
                </p>
                <!-- Statistics -->
                <div class="flex space-x-4 mt-2">
                  <div class="flex items-center">
                    <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-600">
                      Rusak: {{ getBarangByStatus('rusak').length }}
                    </span>
                  </div>
                  <div class="flex items-center">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-600">
                      Hilang: {{ getBarangByStatus('hilang').length }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="flex space-x-2">
                <button
                  @click="tambahBarangRusak"
                  class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  Tambah Barang Rusak
                </button>
                <button
                  @click="tambahBarangHilang"
                  class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  Tambah Barang Hilang
                </button>
              </div>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
                <input
                  v-model="searchQuery"
                  type="text"
                  class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  placeholder="Cari barang berdasarkan nama atau kode barang..."
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
            <div class="mb-6">
              <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                  <button
                    @click="activeFilter = 'all'"
                    :class="[
                      'py-2 px-1 border-b-2 font-medium text-sm',
                      activeFilter === 'all'
                        ? 'border-blue-500 text-blue-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                    ]"
                  >
                    Semua ({{ barangRusak.length }})
                  </button>
                  <button
                    @click="activeFilter = 'rusak'"
                    :class="[
                      'py-2 px-1 border-b-2 font-medium text-sm',
                      activeFilter === 'rusak'
                        ? 'border-red-500 text-red-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                    ]"
                  >
                    Rusak ({{ getBarangByStatus('rusak').length }})
                  </button>
                  <button
                    @click="activeFilter = 'hilang'"
                    :class="[
                      'py-2 px-1 border-b-2 font-medium text-sm',
                      activeFilter === 'hilang'
                        ? 'border-yellow-500 text-yellow-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                    ]"
                  >
                    Hilang ({{ getBarangByStatus('hilang').length }})
                  </button>
                </nav>
              </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Kode Barang
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Nama Barang
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Kategori
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Stok
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Aksi
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="barang in filteredBarang" :key="barang.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ barang.kode_barang }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ barang.nama_barang }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                        :class="{
                          'bg-blue-100 text-blue-800': barang.kategori === 'peminjaman',
                          'bg-green-100 text-green-800': barang.kategori === 'permintaan'
                        }">
                        {{ barang.kategori }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ barang.stok }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                        :class="{
                          'bg-red-100 text-red-800': barang.status === 'rusak',
                          'bg-yellow-100 text-yellow-800': barang.status === 'hilang'
                        }">
                        {{ barang.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div class="flex space-x-2">
                        <select 
                          v-model="barang.status" 
                          @change="updateStatus(barang)"
                          class="text-sm border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                          <option value="baik">Baik</option>
                          <option value="rusak">Rusak</option>
                          <option value="hilang">Hilang</option>
                        </select>
                        <button
                          @click="deleteBarang(barang)"
                          class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition ease-in-out duration-150"
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
            <div v-if="filteredBarang.length === 0" class="text-center py-12">
              <div class="text-gray-400">
                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">
                  {{ searchQuery ? 'Tidak ada hasil pencarian' : 
                     activeFilter === 'all' ? 'Tidak ada barang rusak atau hilang' : 
                     activeFilter === 'rusak' ? 'Tidak ada barang rusak' : 'Tidak ada barang hilang' }}
                </h3>
                <p class="mt-1 text-sm text-gray-500">
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

const filteredBarang = computed(() => {
  let filtered = props.barangRusak;
  
  // Apply status filter
  if (activeFilter.value !== 'all') {
    filtered = filtered.filter(barang => barang.status === activeFilter.value);
  }
  
  // Apply search filter
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim();
    filtered = filtered.filter(barang => 
      barang.nama_barang.toLowerCase().includes(query) ||
      barang.kode_barang.toLowerCase().includes(query)
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