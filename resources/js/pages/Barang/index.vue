<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus, AlertTriangle, Package, Box } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
// Removed: import { useAuthStore } from '@/stores/auth';

const props = defineProps<{
    barang: {
        data: {
            id: number;
            kode_barang: string;
            nama_barang: string;
            deskripsi: string;
            kategori: string;
            stok: number;
            satuan?: string;
            lokasi?: string;
        }[];
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
        meta: {
            current_page: number;
            last_page: number;
            per_page: number;
            total: number;
            asset_count: number;
            consumable_count: number;
        };
    };
}>();

const searchQuery = ref('');
const selectedKategori = ref('');

const filteredBarang = computed(() => {
    return props.barang.data.filter(item => {
        const matchesSearch = item.nama_barang.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.kode_barang.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesKategori = !selectedKategori.value || item.kategori === selectedKategori.value;
        return matchesSearch && matchesKategori;
    });
});

const groupedBarang = computed(() => {
    return filteredBarang.value.reduce((acc, item) => {
        const key = item.kategori;
        if (!acc[key]) {
            acc[key] = [];
        }
        acc[key].push(item);
        return acc;
    }, {} as Record<string, typeof props.barang.data>);
});

const deleteBarang = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
        router.delete(route('barang.destroy', id));
    }
};

// Count items by category
const assetCount = computed(() => props.barang?.meta?.asset_count ?? 0);
const consumableCount = computed(() => props.barang?.meta?.consumable_count ?? 0);
const totalBarang = computed(() => assetCount.value + consumableCount.value);

// Mode tampilan: 'card' atau 'table'
const viewMode = ref<'card' | 'table'>('card');

function toggleViewMode() {
  viewMode.value = viewMode.value === 'card' ? 'table' : 'card';
}

// Dialog for detail
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Pencil, Eye } from 'lucide-vue-next';
const selectedItem = ref<typeof props.barang.data[0] | null>(null);
const showDetail = ref(false);
function openDetail(item: typeof props.barang.data[0]) {
  selectedItem.value = item;
  showDetail.value = true;
}
function closeDetail() {
  showDetail.value = false;
  selectedItem.value = null;
}
</script>

<template>
    <Head title="Barang" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h1 class="text-2xl font-semibold text-gray-800">Daftar Barang</h1>
                <div class="flex items-center gap-2">
                  <button @click="toggleViewMode" class="px-4 py-2 rounded border text-sm font-medium bg-white hover:bg-gray-100 transition">
                    {{ viewMode === 'card' ? 'Tampilan Tabel' : 'Tampilan Kartu' }}
                  </button>
                  <div class="flex flex-col sm:flex-row gap-2">
                      <Link :href="route('barang-rusak.index')">
                          <Button variant="outline" class="w-full sm:w-auto">
                              <AlertTriangle class="w-4 h-4 mr-2" />
                              Barang Rusak
                          </Button>
                      </Link>
                      <Link :href="route('barang.create')">
                          <Button class="w-full sm:w-auto">
                              <Plus class="w-4 h-4 mr-2" />
                              Tambah Barang
                          </Button>
                      </Link>
                  </div>
                </div>
            </div>

            <!-- Category Selection Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Card Semua Barang -->
                <Link :href="route('barang.semuaBRG')" class="block">
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 hover:scale-105 cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-purple-500 p-3 rounded-lg">
                                    <Package class="w-8 h-8 text-white" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-purple-800">Semua Barang</h2>
                                    <p class="text-purple-600 text-sm">Lihat seluruh barang</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-purple-800">{{ totalBarang }}</div>
                                <div class="text-purple-600 text-sm">item</div>
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-50 rounded-lg p-3">
                            <p class="text-purple-700 text-sm">
                                Lihat dan kelola seluruh barang yang tersedia di sistem, baik aset maupun barang habis pakai.
                            </p>
                        </div>
                    </div>
                </Link>

                <!-- Asset Card -->
                <Link :href="route('barang.aset')" class="block">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 hover:scale-105 cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-blue-500 p-3 rounded-lg">
                                    <Package class="w-8 h-8 text-white" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-blue-800">Barang Aset</h2>
                                    <p class="text-blue-600 text-sm">Barang untuk peminjaman</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-blue-800">{{ assetCount }}</div>
                                <div class="text-blue-600 text-sm">item</div>
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-50 rounded-lg p-3">
                            <p class="text-blue-700 text-sm">
                                Kelola barang-barang yang dapat dipinjam seperti laptop, projector, dan peralatan lainnya.
                            </p>
                        </div>
                    </div>
                </Link>

                <!-- Consumable Card -->
                <Link :href="route('barang.permintaan')" class="block">
                    <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 hover:scale-105 cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-green-500 p-3 rounded-lg">
                                    <Box class="w-8 h-8 text-white" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-green-800">Barang Permintaan</h2>
                                    <p class="text-green-600 text-sm">Barang habis pakai</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-green-800">{{ consumableCount }}</div>
                                <div class="text-green-600 text-sm">item</div>
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-50 rounded-lg p-3">
                            <p class="text-green-700 text-sm">
                                Kelola barang-barang habis pakai seperti kertas, tinta printer, dan perlengkapan kantor.
                            </p>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-lg border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Barang</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ assetCount }}</div>
                        <div class="text-blue-800 font-medium">Total Aset</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ consumableCount }}</div>
                        <div class="text-green-800 font-medium">Total Permintaan</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-gray-600">{{ totalBarang }}</div>
                        <div class="text-gray-800 font-medium">Total Semua</div>
                    </div>
                </div>
            </div>

            <!-- Barang List: Card or Table View -->
            <div class="bg-white rounded-lg border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Barang</h3>
                <div v-if="viewMode === 'card'">
                  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                      <div
                          v-for="item in filteredBarang"
                          :key="item.id"
                          class="border rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
                          :class="[item.kategori === 'peminjaman' ? 'border-blue-200 bg-blue-50' : 'border-green-200 bg-green-50']"
                          @click="openDetail(item)"
                      >
                          <div class="flex items-center justify-between mb-2">
                              <h4 class="font-medium text-gray-800 truncate">{{ item.nama_barang }}</h4>
                              <span :class="[
                                  'px-2 py-1 text-xs rounded-full font-medium',
                                  item.kategori === 'peminjaman'
                                      ? 'bg-blue-200 text-blue-800'
                                      : 'bg-green-200 text-green-800'
                              ]">
                                  {{ item.kategori === 'peminjaman' ? 'Aset' : 'Permintaan' }}
                              </span>
                          </div>
                          <p class="text-sm text-gray-600 mb-2">{{ item.kode_barang }}</p>
                          <p class="text-sm font-medium">
                              Stok: {{ item.stok }}
                              <span v-if="item.satuan" class="ml-1 text-gray-500">({{ item.satuan }})</span>
                          </p>
                      </div>
                  </div>
                  <div v-if="filteredBarang.length === 0" class="text-center py-8 text-gray-500">
                      <p>Belum ada barang yang ditambahkan.</p>
                  </div>
                  <!-- Detail Dialog -->
                  <Dialog v-model:open="showDetail">
                    <DialogContent class="sm:max-w-lg">
                      <DialogHeader>
                        <DialogTitle class="flex items-center gap-2">
                          <component :is="selectedItem?.kategori === 'peminjaman' ? Package : Box" class="w-5 h-5" :class="selectedItem?.kategori === 'peminjaman' ? 'text-blue-600' : 'text-green-600'" />
                          {{ selectedItem?.nama_barang }}
                        </DialogTitle>
                        <DialogDescription>
                          Detail lengkap barang. Klik tombol aksi di bawah jika diperlukan.
                        </DialogDescription>
                      </DialogHeader>
                      <div v-if="selectedItem" class="space-y-3 py-4 text-sm">
                        <div class="grid grid-cols-2 gap-4">
                          <div>
                            <p class="font-semibold text-gray-700">Kode Barang:</p>
                            <p class="text-gray-600">{{ selectedItem.kode_barang }}</p>
                          </div>
                          <div>
                            <p class="font-semibold text-gray-700">Kategori:</p>
                            <p :class="selectedItem.kategori === 'peminjaman' ? 'text-blue-600' : 'text-green-600'">
                              {{ selectedItem.kategori === 'peminjaman' ? 'Aset' : 'Permintaan' }}
                            </p>
                          </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                          <div>
                            <p class="font-semibold text-gray-700">Stok Tersedia:</p>
                            <p :class="['font-bold text-lg', selectedItem.stok > 0 ? 'text-green-600' : 'text-red-600']">
                              {{ selectedItem.stok }} <span v-if="selectedItem.satuan">{{ selectedItem.satuan }}</span>
                            </p>
                          </div>
                          <div>
                            <p class="font-semibold text-gray-700">Lokasi:</p>
                            <p class="text-gray-600">{{ selectedItem.lokasi || 'Tidak ditentukan' }}</p>
                          </div>
                        </div>
                        <div>
                          <p class="font-semibold text-gray-700">Deskripsi:</p>
                          <p class="text-gray-600">{{ selectedItem.deskripsi || 'Tidak ada deskripsi.' }}</p>
                        </div>
                      </div>
                      <DialogFooter class="flex flex-col sm:flex-row gap-2 mt-4">
                        <template v-if="totalBarang === 0">
                          <Link v-if="selectedItem" :href="route('barang.edit', selectedItem.id)" class="w-full sm:w-auto">
                            <Button variant="outline" class="w-full">
                              <Pencil class="w-4 h-4 mr-2" /> Edit
                            </Button>
                          </Link>
                          <Button v-if="selectedItem" variant="destructive" @click="deleteBarang(selectedItem.id)" class="w-full sm:w-auto">
                            <Eye class="w-4 h-4 mr-2" /> Hapus
                          </Button>
                        </template>
                      </DialogFooter>
                    </DialogContent>
                  </Dialog>
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
                <!-- Pagination -->
                <div v-if="props.barang.links && props.barang.links.length > 3" class="flex justify-center mt-6">
                  <nav class="inline-flex -space-x-px">
                    <button
                      v-for="(link, i) in props.barang.links"
                      :key="i"
                      :disabled="!link.url"
                      @click="link.url && router.get(link.url)"
                      v-html="link.label"
                      :class="[
                        'px-3 py-1 border text-sm',
                        link.active ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100',
                        !link.url ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                      ]"
                    />
                  </nav>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
