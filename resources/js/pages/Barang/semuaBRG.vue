<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus, Pencil, Trash2, Search, Eye, Package, Box, QrCode, X } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import BarcodeDisplay from '@/components/BarcodeDisplay.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

interface BarangItem {
  id: number;
  kode_barang: string;
  nama_barang: string;
  deskripsi: string;
  kategori: string;
  stok: number;
  satuan?: string;
  lokasi?: string;
}
const props = defineProps<{
    barang: {
        data: BarangItem[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links?: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
    filters?: {
        search?: string;
        kategori?: string;
    };
    stats: {
        total_barang: number;
        total_aset: number;
        total_permintaan: number;
        total_tersedia: number;
        total_habis: number;
    };
}>();

// Search and filter functionality (same as stok page)
// Provide default values to prevent crashes when props are not provided
const searchQuery = ref(props.filters?.search || '');
const selectedKategori = ref(props.filters?.kategori || '');

const applyFilters = () => {
    // Use server-side filtering to search entire database
    router.get(route('barang.semuaBRG'), {
        search: searchQuery.value,
        kategori: selectedKategori.value,
        page: 1 // Always start from first page when searching
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedKategori.value = '';
    applyFilters();
};

// Debounced search to avoid too many requests
let searchTimeout: number;
const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500); // 500ms delay
};

// Use server data directly (server handles filtering)
const filteredBarang = computed(() => {
    return props.barang?.data || [];
});

const deleteBarang = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
        router.delete(route('barang.destroy', id));
    }
};

// Mode tampilan: 'card' atau 'table'
const viewMode = ref<'card' | 'table'>('card');
function toggleViewMode() {
  viewMode.value = viewMode.value === 'card' ? 'table' : 'card';
}

// Pagination is now handled by the simple navigation component
</script>

<template>
    <Head title="Semua Barang" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h1 class="text-2xl font-semibold text-gray-800">Daftar Semua Barang</h1>
                <div class="flex items-center gap-2">
                  <button @click="toggleViewMode" class="px-4 py-2 rounded border text-sm font-medium bg-white hover:bg-gray-100 transition">
                    {{ viewMode === 'card' ? 'Tampilan Tabel' : 'Tampilan Kartu' }}
                  </button>
                  <div class="flex flex-col sm:flex-row gap-2">
                      <Link :href="route('barang.create')">
                          <Button class="w-full sm:w-auto">
                              <Plus class="w-4 h-4 mr-2" />
                              Tambah Barang
                          </Button>
                      </Link>
                  </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-600 text-sm font-medium">Total Barang</p>
                            <p class="text-2xl font-bold text-blue-800">{{ props.stats.total_barang }}</p>
                        </div>
                        <Package class="w-8 h-8 text-blue-600" />
                    </div>
                </div>
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-indigo-600 text-sm font-medium">Total Aset</p>
                            <p class="text-2xl font-bold text-indigo-800">{{ props.stats.total_aset }}</p>
                        </div>
                        <Package class="w-8 h-8 text-indigo-600" />
                    </div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-600 text-sm font-medium">Total Permintaan</p>
                            <p class="text-2xl font-bold text-green-800">{{ props.stats.total_permintaan }}</p>
                        </div>
                        <Box class="w-8 h-8 text-green-600" />
                    </div>
                </div>
                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-emerald-600 text-sm font-medium">Tersedia</p>
                            <p class="text-2xl font-bold text-emerald-800">{{ props.stats.total_tersedia }}</p>
                        </div>
                        <div class="w-8 h-8 bg-emerald-200 rounded-full flex items-center justify-center">
                            <span class="text-emerald-800 font-bold text-sm">✓</span>
                        </div>
                    </div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-600 text-sm font-medium">Habis</p>
                            <p class="text-2xl font-bold text-red-800">{{ props.stats.total_habis }}</p>
                        </div>
                        <div class="w-8 h-8 bg-red-200 rounded-full flex items-center justify-center">
                            <span class="text-red-800 font-bold text-sm">!</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        v-model="searchQuery"
                        @input="debouncedSearch"
                        @keyup.enter="applyFilters"
                        type="text"
                        placeholder="Cari nama atau kode barang..."
                        class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
                <select
                    v-model="selectedKategori"
                    @change="applyFilters"
                    class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-60"
                >
                    <option value="">Semua Kategori</option>
                    <option value="peminjaman">Aset</option>
                    <option value="permintaan">Permintaan</option>
                </select>
                <div class="flex gap-2">
                    <Button @click="applyFilters" variant="outline">
                        <Search class="w-4 h-4 mr-2" />
                        Cari
                    </Button>
                    <Button 
                        v-if="searchQuery || selectedKategori"
                        @click="clearFilters" 
                        variant="outline"
                    >
                        <X class="w-4 h-4 mr-2" />
                        Hapus Filter
                    </Button>
                </div>
            </div>

            <!-- Barang List: Card or Table View -->
            <div class="space-y-6">
                <div v-if="viewMode === 'card'">
                  <div v-if="filteredBarang.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                      <div
                          v-for="item in filteredBarang"
                          :key="item.id"
                          class="bg-white border rounded-lg shadow-sm hover:shadow-md transition-shadow"
                          :class="item.kategori === 'peminjaman' ? 'border-blue-200 bg-blue-50' : 'border-green-200 bg-green-50'"
                      >
                          <div class="p-5">
                              <div class="flex items-center justify-between mb-3">
                                  <h3 class="font-bold text-lg text-gray-800 truncate">{{ item.nama_barang }}</h3>
                                  <span class="px-2 py-1 text-xs rounded-full font-medium"
                                      :class="item.kategori === 'peminjaman' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'">
                                      {{ item.kategori === 'peminjaman' ? 'Aset' : 'Permintaan' }}
                                  </span>
                              </div>
                              <p class="text-sm text-gray-600 mb-2">{{ item.kode_barang }}</p>
                              <div class="flex items-center justify-between mb-4">
                                  <span class="text-sm font-medium">Stok:</span>
                                  <span :class="['font-bold text-lg', item.stok > 0 ? 'text-green-600' : 'text-red-600']">
                                      {{ item.stok }} <span v-if="item.satuan">{{ item.satuan }}</span>
                                  </span>
                              </div>
                              <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                  {{ item.deskripsi || 'Tidak ada deskripsi.' }}
                              </p>
                          </div>
                          <div class="p-5 bg-gray-50 rounded-b-lg border-t">
                              <Dialog>
                                  <DialogTrigger as-child>
                                      <Button class="w-full mb-2">
                                          <Eye class="w-4 h-4 mr-2" />
                                          Detail
                                      </Button>
                                  </DialogTrigger>
                                  <DialogContent class="sm:max-w-lg">
                                      <DialogHeader>
                                          <DialogTitle class="flex items-center gap-2">
                                              <component :is="item.kategori === 'peminjaman' ? Package : Box" class="w-5 h-5" :class="item.kategori === 'peminjaman' ? 'text-blue-600' : 'text-green-600'" />
                                              {{ item.nama_barang }}
                                          </DialogTitle>
                                          <DialogDescription>
                                              Detail lengkap barang. Klik tombol aksi di bawah jika diperlukan.
                                          </DialogDescription>
                                      </DialogHeader>
                                      <div class="space-y-3 py-4 text-sm">
                                          <div class="grid grid-cols-2 gap-4">
                                              <div>
                                                  <p class="font-semibold text-gray-700">Kode Barang:</p>
                                                  <p class="text-gray-600">{{ item.kode_barang }}</p>
                                              </div>
                                              <div>
                                                  <p class="font-semibold text-gray-700">Kategori:</p>
                                                  <p :class="item.kategori === 'peminjaman' ? 'text-blue-600' : 'text-green-600'">
                                                      {{ item.kategori === 'peminjaman' ? 'Aset' : 'Permintaan' }}
                                                  </p>
                                              </div>
                                          </div>
                                          <div class="grid grid-cols-2 gap-4">
                                              <div>
                                                  <p class="font-semibold text-gray-700">Stok Tersedia:</p>
                                                  <p :class="['font-bold text-lg', item.stok > 0 ? 'text-green-600' : 'text-red-600']">
                                                      {{ item.stok }} <span v-if="item.satuan">{{ item.satuan }}</span>
                                                  </p>
                                              </div>
                                              <div>
                                                  <p class="font-semibold text-gray-700">Lokasi:</p>
                                                  <p class="text-gray-600">{{ item.lokasi || 'Tidak ditentukan' }}</p>
                                              </div>
                                          </div>
                                          <div>
                                              <p class="font-semibold text-gray-700">Deskripsi:</p>
                                              <p class="text-gray-600">{{ item.deskripsi || 'Tidak ada deskripsi.' }}</p>
                                          </div>
                                      </div>
                                      <!-- Barcode Section -->
                                      <div class="border-t pt-4 mt-4">
                                          <div class="flex items-center gap-2 mb-3">
                                              <QrCode class="w-4 h-4 text-indigo-600" />
                                              <h4 class="font-semibold text-indigo-800">Barcode</h4>
                                          </div>
                                          <BarcodeDisplay 
                                              :barang-data="item"
                                              :show-info="false"
                                              :show-actions="true"
                                              :allow-download="true"
                                              :allow-print="true"
                                              :allow-copy="true"
                                              container-class="bg-gray-50 p-3 rounded border"
                                              image-class="max-w-full h-auto"
                                              :use-styled-barcode="true"
                                          />
                                      </div>
                                      
                                      <DialogFooter class="flex flex-col sm:flex-row gap-2 mt-4">
                                          <Link :href="route('barang.edit', item.id)" class="w-full sm:w-auto">
                                              <Button variant="outline" class="w-full">
                                                  <Pencil class="w-4 h-4 mr-2" /> Edit
                                              </Button>
                                          </Link>
                                          <Button variant="destructive" @click="deleteBarang(item.id)" class="w-full sm:w-auto">
                                              <Trash2 class="w-4 h-4 mr-2" /> Hapus
                                          </Button>
                                      </DialogFooter>
                                  </DialogContent>
                              </Dialog>
                          </div>
                      </div>
                  </div>
                  <div v-else class="text-center py-16 text-gray-500 bg-white rounded-lg border">
                      <Package class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                      <p class="text-xl font-medium mb-2">Tidak ada barang ditemukan</p>
                      <p class="text-gray-600 mb-4">
                          {{ searchQuery ? 'Tidak ada barang yang sesuai dengan pencarian Anda. Coba kata kunci lain.' : 'Belum ada barang yang ditambahkan.' }}
                      </p>
                      <div v-if="!searchQuery">
                          <Link :href="route('barang.create')">
                              <Button>
                                  <Plus class="w-4 h-4 mr-2" />
                                  Tambah Barang Pertama
                              </Button>
                          </Link>
                      </div>
                      <div v-else>
                          <Button @click="clearFilters" variant="outline">
                              <Search class="w-4 h-4 mr-2" />
                              Hapus Pencarian
                          </Button>
                      </div>
                  </div>
                </div>
                <div v-else>
                  <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="item in filteredBarang" :key="item.id">
                          <td class="px-4 py-2 whitespace-nowrap">{{ item.kode_barang }}</td>
                          <td class="px-4 py-2 whitespace-nowrap">{{ item.nama_barang }}</td>
                          <td class="px-4 py-2 whitespace-nowrap">
                            <span :class="[
                              'px-2 py-1 text-xs rounded-full font-medium',
                              item.kategori === 'peminjaman'
                                ? 'bg-blue-200 text-blue-800'
                                : 'bg-green-200 text-green-800'
                            ]">
                              {{ item.kategori === 'peminjaman' ? 'Aset' : 'Permintaan' }}
                            </span>
                          </td>
                          <td class="px-4 py-2 whitespace-nowrap">{{ item.stok }}</td>
                          <td class="px-4 py-2 whitespace-nowrap">{{ item.satuan || '-' }}</td>
                          <td class="px-4 py-2 whitespace-nowrap">
                            <Link :href="route('barang.edit', item.id)" class="text-blue-600 hover:underline mr-2">Edit</Link>
                            <button @click="deleteBarang(item.id)" class="text-red-600 hover:underline">Hapus</button>
                          </td>
                        </tr>
                        <tr v-if="filteredBarang.length === 0">
                          <td colspan="6" class="text-center py-8 text-gray-500">
                              {{ searchQuery ? 'Tidak ada barang yang sesuai dengan pencarian Anda.' : 'Belum ada barang yang ditambahkan.' }}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
            </div>
            
            <!-- Search Results Info -->
            <div v-if="props.filters?.search || props.filters?.kategori" class="text-center py-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 inline-block">
                    <p class="text-sm text-blue-800">
                        <Search class="w-4 h-4 inline mr-2" />
                        Menampilkan {{ filteredBarang.length }} hasil pencarian
                        <span v-if="props.filters?.search">untuk "{{ props.filters.search }}"</span>
                        <span v-if="props.filters?.kategori">dari kategori {{ props.filters.kategori === 'peminjaman' ? 'Aset' : 'Permintaan' }}</span>
                    </p>
                </div>
            </div>
            
            <!-- Pagination -->
            <div v-if="barang && barang.last_page > 1 && !props.filters?.search && !props.filters?.kategori" class="flex items-center justify-center mt-6">
                <nav class="inline-flex items-center gap-1 bg-white rounded-lg border border-gray-200 p-1 shadow-sm">
                    <Button 
                        variant="ghost" 
                        size="sm"
                        class="h-8 w-8 p-0 text-gray-500 hover:text-gray-700 hover:bg-gray-100"
                        :disabled="barang.current_page === 1"
                        @click="router.get(route('barang.semuaBRG'), { 
                            search: props.filters?.search || '', 
                            kategori: props.filters?.kategori || '', 
                            page: barang.current_page - 1 
                        })"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Button>
                    
                    <span class="px-2 py-1 text-xs font-medium text-gray-700 bg-gray-50 rounded">
                        {{ barang.current_page }} / {{ barang.last_page }}
                    </span>
                    
                    <span class="px-2 py-1 text-xs text-gray-500">
                        ({{ barang.data.length }} dari {{ barang.total }})
                    </span>
                    
                    <Button 
                        variant="ghost" 
                        size="sm"
                        class="h-8 w-8 p-0 text-gray-500 hover:text-gray-700 hover:bg-gray-100"
                        :disabled="barang.current_page === barang.last_page"
                        @click="router.get(route('barang.semuaBRG'), { 
                            search: props.filters?.search || '', 
                            kategori: props.filters?.kategori || '', 
                            page: barang.current_page + 1 
                        })"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Button>
                </nav>
            </div>
        </div>
    </AppLayout>
</template>
