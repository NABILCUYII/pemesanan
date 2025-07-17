<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus, Pencil, Trash2, Search, Eye, AlertTriangle, Package, Box } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { Table, TableHead, TableBody, TableRow, TableCell } from '@/components/ui/table'; // pastikan komponen table ada, jika tidak bisa gunakan <table>
import { LayoutGrid, Table as TableIcon } from 'lucide-vue-next';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

// Tambahkan props user untuk akses role
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
    user?: {
        role?: string;
    }
}>();

const searchQuery = ref('');
const selectedKategori = ref('');

const filteredBarang = computed(() => {
    return props.barang.filter(item => {
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
    }, {} as Record<string, typeof props.barang>);
});

const deleteBarang = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
        router.delete(route('barang.destroy', id));
    }
};

// Count items by category
const assetCount = computed(() => props.barang.filter(item => item.kategori === 'peminjaman').length);
const consumableCount = computed(() => props.barang.filter(item => item.kategori === 'permintaan').length);

// Cek apakah user adalah user biasa (bukan admin/operator)
const isUserOnly = computed(() => {
    // Ganti 'user' dengan nama role user biasa sesuai aplikasi Anda
    // Misal: 'user', 'anggota', dsb.
    // Di sini diasumsikan role 'user' adalah user biasa
    return props.user && props.user.role === 'user';
});

// Mode tampilan: 'card' atau 'table'
const viewMode = ref<'card' | 'table'>('card');

function setViewMode(mode: 'card' | 'table') {
    // Jika user biasa, tidak bisa ganti mode
    if (isUserOnly.value) return;
    viewMode.value = mode;
}

// Dialog state for detail
const showDetailDialog = ref(false);
const selectedBarang = ref<typeof props.barang[0] | null>(null);

function openDetail(item: typeof props.barang[0]) {
    selectedBarang.value = item;
    showDetailDialog.value = true;
}
function closeDetail() {
    showDetailDialog.value = false;
    selectedBarang.value = null;
}
</script>

<template>
    <Head title="Barang" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h1 class="text-2xl font-semibold text-gray-800">Daftar Barang</h1>
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

            <!-- Switch View Mode -->
           
           
            <div  class="flex justify-end mb-2 gap-2">
                <Button :variant="viewMode === 'card' ? 'default' : 'outline'" @click="setViewMode('card')">
                    <LayoutGrid class="w-4 h-4 mr-1" /> Card
                </Button>
                <Button :variant="viewMode === 'table' ? 'default' : 'outline'" @click="setViewMode('table')">
                    <TableIcon class="w-4 h-4 mr-1" /> Table
                </Button>
            </div>

          
            <!-- Category Selection Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                        <div class="text-2xl font-bold text-gray-600">{{ props.barang.length }}</div>
                        <div class="text-gray-800 font-medium">Total Semua</div>
                    </div>
                </div>
            </div>

            <!-- Barang List: Card or Table -->
            <div class="bg-white rounded-lg border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Barang</h3>
                <div v-if="viewMode === 'card' || isUserOnly">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <button
                            v-for="item in filteredBarang"
                            :key="item.id"
                            type="button"
                            @click="openDetail(item)"
                            class="border rounded-lg p-4 hover:shadow-md transition-shadow text-left focus:outline-none focus:ring-2 focus:ring-blue-400"
                            :class="[item.kategori === 'peminjaman' ? 'border-blue-200 bg-blue-50' : 'border-green-200 bg-green-50']"
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
                        </button>
                    </div>
                    <div v-if="filteredBarang.length === 0" class="text-center py-8 text-gray-500">
                        <p>Belum ada barang yang ditampilkan.</p>
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
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="item in filteredBarang" :key="item.id">
                                    <td class="px-4 py-2">{{ item.kode_barang }}</td>
                                    <td class="px-4 py-2">{{ item.nama_barang }}</td>
                                    <td class="px-4 py-2">
                                        <span :class="item.kategori === 'peminjaman' ? 'text-blue-700' : 'text-green-700'">
                                            {{ item.kategori === 'peminjaman' ? 'Aset' : 'Permintaan' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">{{ item.stok }}</td>
                                    <td class="px-4 py-2">{{ item.satuan || '-' }}</td>
                                    <td class="px-4 py-2">{{ item.lokasi || '-' }}</td>
                                    <td class="px-4 py-2 flex gap-2">
                                        <Button size="icon" variant="outline" @click="openDetail(item)"><Eye class="w-4 h-4" /></Button>
                                        <Link :href="route('barang.edit', item.id)"><Button size="icon" variant="outline"><Pencil class="w-4 h-4" /></Button></Link>
                                        <Button size="icon" variant="destructive" @click="deleteBarang(item.id)"><Trash2 class="w-4 h-4" /></Button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="filteredBarang.length === 0" class="text-center py-8 text-gray-500">
                            <p>Belum ada barang yang ditampilkan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dialog for Barang Detail -->
            <Dialog v-model:open="showDetailDialog">
                <DialogContent class="max-w-md">
                    <DialogHeader>
                        <DialogTitle>
                            <span v-if="selectedBarang">{{ selectedBarang.nama_barang }}</span>
                        </DialogTitle>
                        <DialogDescription>
                            <span v-if="selectedBarang">
                                Detail barang berikut adalah informasi lengkap dari barang yang dipilih.
                            </span>
                        </DialogDescription>
                    </DialogHeader>
                    <div v-if="selectedBarang" class="space-y-2 py-2">
                        <div>
                            <span class="font-semibold">Kode Barang:</span>
                            <span class="ml-2">{{ selectedBarang.kode_barang }}</span>
                        </div>
                        <div>
                            <span class="font-semibold">Kategori:</span>
                            <span class="ml-2">
                                <span v-if="selectedBarang.kategori === 'peminjaman'">Aset</span>
                                <span v-else>Permintaan</span>
                            </span>
                        </div>
                        <div>
                            <span class="font-semibold">Stok:</span>
                            <span class="ml-2">{{ selectedBarang.stok }} <span v-if="selectedBarang.satuan">({{ selectedBarang.satuan }})</span></span>
                        </div>
                        <div v-if="selectedBarang.lokasi">
                            <span class="font-semibold">Lokasi:</span>
                            <span class="ml-2">{{ selectedBarang.lokasi }}</span>
                        </div>
                        <div>
                            <span class="font-semibold">Deskripsi:</span>
                            <span class="ml-2">{{ selectedBarang.deskripsi || '-' }}</span>
                        </div>
                    </div>
                    <DialogFooter>
                        <Button variant="outline" @click="closeDetail">Tutup</Button>
                        <Link :href="route('barang.edit', selectedBarang?.id)" v-if="selectedBarang">
                            <Button class="ml-2" variant="default">
                                <Pencil class="w-4 h-4 mr-1" /> Edit Barang
                            </Button>
                        </Link>
                    </DialogFooter>
                </DialogContent>
                   </Dialog>
        </div>
    </AppLayout>
</template>
