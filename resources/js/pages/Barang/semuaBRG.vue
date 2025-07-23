<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus, Pencil, Trash2, Search, Eye, Package, Box } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
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
        id: number;
        kode_barang: string;
        nama_barang: string;
        deskripsi: string;
        kategori: string;
        stok: number;
        satuan?: string;
        lokasi?: string;
    }[];
}>();

const searchQuery = ref('');

const filteredBarang = computed(() => {
    return props.barang.filter(item => {
        return (
            item.nama_barang.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.kode_barang.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    });
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

            <!-- Search -->
            <div class="relative">
                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari nama atau kode barang..."
                    class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
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
                          {{ searchQuery ? 'Coba ubah kata kunci pencarian Anda.' : 'Belum ada barang yang ditambahkan.' }}
                      </p>
                      <Link :href="route('barang.create')">
                          <Button>
                              <Plus class="w-4 h-4 mr-2" />
                              Tambah Barang Pertama
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
                          <td colspan="6" class="text-center py-8 text-gray-500">Belum ada barang yang ditambahkan.</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
