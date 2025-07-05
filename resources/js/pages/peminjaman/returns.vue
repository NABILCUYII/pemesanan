<template>
    <AppLayout title="Daftar Pengembalian">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Daftar Pengembalian
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Daftar Pengembalian Barang</h3>
                                <p class="text-sm text-gray-600">Menampilkan semua pengembalian yang telah diproses</p>
                            </div>
                            <Link
                                :href="route('peminjaman.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                            >
                                <User class="w-4 h-4 mr-2" />
                                Kembali ke Aset
                            </Link>
                        </div>

                        <!-- Filter dan Search -->
                        <div class="flex flex-col sm:flex-row gap-4 mb-6">
                            <div class="flex-1">
                                <div class="relative">
                                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
                                    <input
                                        v-model="searchQuery"
                                        type="text"
                                        placeholder="Cari berdasarkan nama barang, peminjam, atau catatan..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>
                            </div>
                            <div class="sm:w-48">
                                <select
                                    v-model="selectedCondition"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">Semua Kondisi</option>
                                    <option value="baik">Baik</option>
                                    <option value="rusak_ringan">Rusak Ringan</option>
                                    <option value="rusak_berat">Rusak Berat</option>
                                </select>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>No</TableHead>
                                        <TableHead>Barang</TableHead>
                                        <TableHead>Peminjam</TableHead>
                                        <TableHead>Jumlah Dipinjam</TableHead>
                                        <TableHead>Jumlah Dikembalikan</TableHead>
                                        <TableHead>Kondisi</TableHead>
                                        <TableHead>Tanggal Pinjam</TableHead>
                                        <TableHead>Tanggal Kembali</TableHead>
                                        <TableHead>Catatan</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="(item, index) in filteredReturns" :key="item.id">
                                        <TableCell>{{ index + 1 }}</TableCell>
                                        <TableCell>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ item.nama_barang }}</div>
                                                <div class="text-sm text-gray-500">{{ item.kode_barang }}</div>
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <div class="flex items-center">
                                                <User class="w-4 h-4 mr-2 text-gray-400" />
                                                {{ item.nama_peminjam }}
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <Badge variant="outline">{{ item.jumlah }} unit</Badge>
                                        </TableCell>
                                        <TableCell>
                                            <Badge :variant="getReturnQuantityVariant(item)">
                                                {{ item.jumlah_dikembalikan }} unit
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            <Badge :variant="getConditionVariant(item.kondisi_barang)">
                                                {{ getConditionLabel(item.kondisi_barang) }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>{{ formatDate(item.tanggal_peminjaman) }}</TableCell>
                                        <TableCell>{{ formatDate(item.tanggal_pengembalian) }}</TableCell>
                                        <TableCell>
                                            <span v-if="item.catatan_pengembalian" class="text-sm text-gray-600">
                                                {{ item.catatan_pengembalian }}
                                            </span>
                                            <span v-else class="text-sm text-gray-400">-</span>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>

                        <!-- Empty State -->
                        <div v-if="filteredReturns.length === 0" class="text-center py-8">
                            <div class="text-gray-500">
                                <Search class="h-12 w-12 mx-auto mb-4" />
                                <p>Tidak ada data pengembalian ditemukan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Search, User } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';

interface ReturnItem {
    id: number;
    nama_barang: string;
    kode_barang: string;
    nama_peminjam: string;
    jumlah: number;
    jumlah_dikembalikan: number;
    kondisi_barang: string;
    tanggal_peminjaman: string;
    tanggal_pengembalian: string;
    catatan_pengembalian?: string;
}

interface Props {
    returns: ReturnItem[];
}

const props = defineProps<Props>();

const searchQuery = ref('');
const selectedCondition = ref('');

const filteredReturns = computed(() => {
    let filtered = props.returns;

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(item =>
            item.nama_barang.toLowerCase().includes(query) ||
            item.nama_peminjam.toLowerCase().includes(query) ||
            item.kode_barang.toLowerCase().includes(query) ||
            (item.catatan_pengembalian && item.catatan_pengembalian.toLowerCase().includes(query))
        );
    }

    if (selectedCondition.value) {
        filtered = filtered.filter(item => item.kondisi_barang === selectedCondition.value);
    }

    return filtered;
});

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const getConditionLabel = (condition: string) => {
    const labels = {
        'baik': 'Baik',
        'rusak_ringan': 'Rusak Ringan',
        'rusak_berat': 'Rusak Berat'
    };
    return labels[condition as keyof typeof labels] || condition;
};

const getConditionVariant = (condition: string) => {
    const variants = {
        'baik': 'default',
        'rusak_ringan': 'secondary',
        'rusak_berat': 'destructive'
    };
    return (variants[condition as keyof typeof variants] || 'default') as 'default' | 'secondary' | 'destructive' | 'outline';
};

const getReturnQuantityVariant = (item: ReturnItem) => {
    const percentage = (item.jumlah_dikembalikan / item.jumlah) * 100;
    if (percentage === 100) return 'default';
    if (percentage >= 50) return 'secondary';
    return 'destructive';
};
</script> 