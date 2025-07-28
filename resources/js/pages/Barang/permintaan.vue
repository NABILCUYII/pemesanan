<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus, Pencil, Trash2, Search, Eye, ArrowLeft, Box } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { onMounted, ref, computed } from 'vue';
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
    // ... properti pagination lain dari Laravel ...
  };
  stats: {
    total_permintaan: number;
    total_tersedia: number;
    total_habis: number;
  };
}>();

const searchQuery = ref('');
const items = ref<BarangItem[]>(Array.isArray(props.barang.data) ? [...props.barang.data] : []);

// Filter only consumable items (permintaan category)
const consumableItems = computed(() => {
  return items.value.filter(item => item.kategori === 'permintaan');
});
const filteredBarang = computed(() => {
  return consumableItems.value.filter(item => {
    return item.nama_barang.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.kode_barang.toLowerCase().includes(searchQuery.value.toLowerCase());
  });
});

const deleteBarang = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus barang permintaan ini?')) {
        router.delete(route('barang.destroy', id));
    }
};

// Mode tampilan: 'card' atau 'table'
const viewMode = ref<'card' | 'table'>('card');
function toggleViewMode() {
  viewMode.value = viewMode.value === 'card' ? 'table' : 'card';
}

// Pagination helper function
function getVisiblePages() {
  const current = props.barang.current_page;
  const last = props.barang.last_page;
  const pages = [];
  
  if (last <= 7) {
    // If total pages <= 7, show all pages
    for (let i = 1; i <= last; i++) {
      pages.push(i);
    }
  } else {
    // Always show first page
    pages.push(1);
    
    if (current > 3) {
      pages.push('...');
    }
    
    // Show pages around current page
    const start = Math.max(2, current - 1);
    const end = Math.min(last - 1, current + 1);
    
    for (let i = start; i <= end; i++) {
      pages.push(i);
    }
    
    if (current < last - 2) {
      pages.push('...');
    }
    
    // Always show last page
    if (last > 1) {
      pages.push(last);
    }
  }
  
  return pages;
}
</script>

<template>
    <Head title="Barang Permintaan" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <Link :href="route('barang.index')">
                        <Button variant="outline" size="sm">
                            <ArrowLeft class="w-4 h-4 mr-2" />
                            Kembali
                        </Button>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                            <Box class="w-6 h-6 text-green-600" />
                            Barang Permintaan
                        </h1>
                        <p class="text-gray-600">Kelola barang-barang habis pakai</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                  <button @click="toggleViewMode" class="px-4 py-2 rounded border text-sm font-medium bg-white hover:bg-gray-100 transition">
                    {{ viewMode === 'card' ? 'Tampilan Tabel' : 'Tampilan Kartu' }}
                  </button>
                  <div class="flex flex-col sm:flex-row gap-2">
                      <Link :href="route('barang.create')">
                          <Button class="w-full sm:w-auto">
                              <Plus class="w-4 h-4 mr-2" />
                              Tambah Permintaan
                          </Button>
                      </Link>
                  </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-600 text-sm font-medium">Total Permintaan</p>
                            <p class="text-2xl font-bold text-green-800">{{ props.stats.total_permintaan }}</p>
                        </div>
                        <Box class="w-8 h-8 text-green-600" />
                    </div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-600 text-sm font-medium">Tersedia</p>
                            <p class="text-2xl font-bold text-blue-800">
                                {{ props.stats.total_tersedia }}
                            </p>
                        </div>
                        <div class="w-8 h-8 bg-blue-200 rounded-full flex items-center justify-center">
                            <span class="text-blue-800 font-bold text-sm">✓</span>
                        </div>
                    </div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-600 text-sm font-medium">Habis</p>
                            <p class="text-2xl font-bold text-red-800">
                                {{ props.stats.total_habis }}
                            </p>
                        </div>
                        <div class="w-8 h-8 bg-red-200 rounded-full flex items-center justify-center">
                            <span class="text-red-800 font-bold text-sm">!</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="relative">
                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari nama atau kode barang permintaan..."
                    class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                />
            </div>

            <!-- Consumable List: Card or Table View -->
            <div class="space-y-6">
                <div v-if="viewMode === 'card'">
                  <div v-if="filteredBarang.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                      <div
                          v-for="item in filteredBarang"
                          :key="item.id"
                          class="bg-white border border-green-200 rounded-lg shadow-sm hover:shadow-md transition-shadow"
                      >
                          <div class="p-5">
                              <div class="flex items-center justify-between mb-3">
                                  <h3 class="font-bold text-lg text-gray-800 truncate">{{ item.nama_barang }}</h3>
                                  <span class="px-2 py-1 text-xs rounded-full font-medium bg-green-100 text-green-800">
                                      Permintaan
                                  </span>
                              </div>
                              <p class="text-sm text-gray-600 mb-2">{{ item.kode_barang }}</p>
                              <div class="flex items-center justify-between mb-4">
                                  <span class="text-sm font-medium">Stok:</span>
                                  <span :class="[
                                      'font-bold text-lg',
                                      item.stok > 0 ? 'text-green-600' : 'text-red-600'
                                  ]">
                                      {{ item.stok }} <span v-if="item.satuan"> {{ item.satuan }}</span>
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
                                              <Box class="w-5 h-5 text-green-600" />
                                              {{ item.nama_barang }}
                                          </DialogTitle>
                                          <DialogDescription>
                                              Detail lengkap barang permintaan. Klik tombol aksi di bawah jika diperlukan.
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
                                                  <p class="text-green-600 font-medium">Permintaan</p>
                                              </div>
                                          </div>
                                          <div class="grid grid-cols-2 gap-4">
                                              <div>
                                                  <p class="font-semibold text-gray-700">Stok Tersedia:</p>
                                                  <p :class="[
                                                      'font-bold text-lg',
                                                      item.stok > 0 ? 'text-green-600' : 'text-red-600'
                                                  ]">
                                                      {{ item.stok }} <span v-if="item.satuan"> {{ item.satuan }}</span>
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
                                      <!-- DialogFooter with Edit and Hapus buttons removed -->
                                  </DialogContent>
                              </Dialog>
                          </div>
                      </div>
                  </div>
                  <div v-else class="text-center py-16 text-gray-500 bg-white rounded-lg border">
                      <Box class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                      <p class="text-xl font-medium mb-2">Tidak ada barang permintaan ditemukan</p>
                      <p class="text-gray-600 mb-4">
                          {{ searchQuery ? 'Coba ubah kata kunci pencarian Anda.' : 'Belum ada barang permintaan yang ditambahkan.' }}
                      </p>
                      <Link :href="route('barang.create')">
                          <Button>
                              <Plus class="w-4 h-4 mr-2" />
                              Tambah Barang Permintaan Pertama
                          </Button>
                      </Link>
                  </div>
                </div>
                <div v-else>
                  <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="item in filteredBarang" :key="item.id">
                          <td class="px-4 py-2 whitespace-nowrap">{{ item.kode_barang }}</td>
                          <td class="px-4 py-2 whitespace-nowrap">{{ item.nama_barang }}</td>
                          <td class="px-4 py-2 whitespace-nowrap">{{ item.stok }}</td>
                          <td class="px-4 py-2 whitespace-nowrap">{{ item.satuan || '-' }}</td>
                          <td class="px-4 py-2 whitespace-nowrap">
                            <Link :href="route('barang.edit', item.id)" class="text-blue-600 hover:underline mr-2">Edit</Link>
                            <button @click="deleteBarang(item.id)" class="text-red-600 hover:underline">Hapus</button>
                          </td>
                        </tr>
                        <tr v-if="filteredBarang.length === 0">
                          <td colspan="5" class="text-center py-8 text-gray-500">Belum ada barang permintaan yang ditambahkan.</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
            </div>
            
            <!-- Pagination -->
            <div v-if="props.barang.last_page > 1" class="flex justify-center mt-6">
                <div class="flex items-center space-x-2">
                    <!-- Previous Page -->
                    <Link 
                        v-if="props.barang.current_page > 1"
                        :href="route('barang.permintaan', { page: props.barang.current_page - 1 })"
                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                    >
                        Previous
                    </Link>
                    
                    <!-- Page Numbers -->
                    <template v-for="page in getVisiblePages()" :key="page">
                        <Link 
                            v-if="page !== '...'"
                            :href="route('barang.permintaan', { page: page })"
                            :class="[
                                'px-3 py-2 text-sm font-medium rounded-md',
                                page === props.barang.current_page
                                    ? 'bg-green-600 text-white'
                                    : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50'
                            ]"
                        >
                            {{ page }}
                        </Link>
                        <span v-else class="px-3 py-2 text-sm text-gray-500">...</span>
                    </template>
                    
                    <!-- Next Page -->
                    <Link 
                        v-if="props.barang.current_page < props.barang.last_page"
                        :href="route('barang.permintaan', { page: props.barang.current_page + 1 })"
                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                    >
                        Next
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template> 