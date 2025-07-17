<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus, Pencil, Trash2, Search, Eye, ArrowLeft, Box, LayoutGrid, Table as TableIcon } from 'lucide-vue-next';
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

// Mode tampilan: 'card' atau 'table'
const viewMode = ref<'card' | 'table'>('card');
function setViewMode(mode: 'card' | 'table') {
    viewMode.value = mode;
}

// Filter only consumable items (permintaan category)
const consumableItems = computed(() => {
    return props.barang.filter(item => item.kategori === 'permintaan');
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
                <div class="flex flex-col sm:flex-row gap-2">
                    <Link :href="route('barang.create')">
                        <Button class="w-full sm:w-auto">
                            <Plus class="w-4 h-4 mr-2" />
                            Tambah Permintaan
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-600 text-sm font-medium">Total Permintaan</p>
                            <p class="text-2xl font-bold text-green-800">{{ consumableItems.length }}</p>
                        </div>
                        <Box class="w-8 h-8 text-green-600" />
                    </div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-600 text-sm font-medium">Tersedia</p>
                            <p class="text-2xl font-bold text-blue-800">
                                {{
                                    consumableItems.filter(item => item.stok > 0).length
                                }}
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
                                {{
                                    consumableItems.filter(item => item.stok === 0).length
                                }}
                            </p>
                        </div>
                        <div class="w-8 h-8 bg-red-200 rounded-full flex items-center justify-center">
                            <span class="text-red-800 font-bold text-sm">!</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Switch View Mode -->
            <div class="flex justify-end mb-2 gap-2">
                <Button :variant="viewMode === 'card' ? 'default' : 'outline'" @click="setViewMode('card')">
                    <LayoutGrid class="w-4 h-4 mr-1" /> Card
                </Button>
                <Button :variant="viewMode === 'table' ? 'default' : 'outline'" @click="setViewMode('table')">
                    <TableIcon class="w-4 h-4 mr-1" /> Table
                </Button>
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

            <!-- Card Mode -->
            <div v-if="viewMode === 'card'" class="space-y-6">
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

            <!-- Table Mode -->
            <div v-if="viewMode === 'table'" class="bg-white rounded-lg border overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-green-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nama Barang</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Stok</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Satuan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Lokasi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="filteredBarang.length === 0">
                            <td colspan="7" class="text-center py-8 text-gray-500">
                                <Box class="w-12 h-12 mx-auto text-gray-300 mb-2" />
                                <div class="mb-2">Tidak ada barang permintaan ditemukan</div>
                                <div class="mb-2">
                                    {{ searchQuery ? 'Coba ubah kata kunci pencarian Anda.' : 'Belum ada barang permintaan yang ditambahkan.' }}
                                </div>
                                <Link :href="route('barang.create')">
                                    <Button>
                                        <Plus class="w-4 h-4 mr-2" />
                                        Tambah Barang Permintaan Pertama
                                    </Button>
                                </Link>
                            </td>
                        </tr>
                        <tr v-for="(item, idx) in filteredBarang" :key="item.id" class="hover:bg-green-50 transition">
                            <td class="px-4 py-3 text-sm text-gray-700">{{ idx + 1 }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-800">
                                <span class="flex items-center gap-2">
                                    <Box class="w-4 h-4 text-green-600" />
                                    {{ item.nama_barang }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ item.kode_barang }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span :class="item.stok > 0 ? 'text-green-600 font-bold' : 'text-red-600 font-bold'">
                                    {{ item.stok }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ item.satuan || '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ item.lokasi || '-' }}</td>
                            <td class="px-4 py-3 text-sm flex flex-wrap gap-2">
                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button size="sm" variant="outline">
                                            <Eye class="w-4 h-4 mr-1" /> Detail
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
                                <Link :href="route('barang.edit', item.id)">
                                    <Button size="sm" variant="outline">
                                        <Pencil class="w-4 h-4 mr-1" /> Edit
                                    </Button>
                                </Link>
                                <Button size="sm" variant="destructive" @click="deleteBarang(item.id)">
                                    <Trash2 class="w-4 h-4 mr-1" /> Hapus
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template> 