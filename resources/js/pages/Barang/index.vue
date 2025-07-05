<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus, Pencil, Trash2, Search, Eye, AlertTriangle, Package, Box } from 'lucide-vue-next';
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

            <!-- Recent Items Preview -->
            <div class="bg-white rounded-lg border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Barang Terbaru</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div
                        v-for="item in props.barang.slice(0, 8)"
                        :key="item.id"
                        class="border rounded-lg p-4 hover:shadow-md transition-shadow"
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
                    </div>
                </div>
                <div v-if="props.barang.length === 0" class="text-center py-8 text-gray-500">
                    <p>Belum ada barang yang ditambahkan.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
