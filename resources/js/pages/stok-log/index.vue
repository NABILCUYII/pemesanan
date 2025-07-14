<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { History, Filter, Eye, Calendar, TrendingUp, TrendingDown } from 'lucide-vue-next';
import { ref } from 'vue';
import { useInitials } from '@/composables/useInitials';
import { usePhotoUrl } from '@/composables/usePhotoUrl';

interface StokLog {
    id: number;
    jenis: 'masuk' | 'keluar';
    jumlah: number;
    stok_sebelum: number;
    stok_sesudah: number;
    keterangan: string | null;
    referensi: string | null;
    created_at: string;
    barang: {
        id: number;
        kode_barang: string;
        nama_barang: string;
    };
    user: {
        id: number;
        name: string;
    };
    user_photo?: string;
}

interface Barang {
    id: number;
    kode_barang: string;
    nama_barang: string;
}

interface Props {
    stokLogs: {
        data: StokLog[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    barang: Barang[];
    filters: {
        barang_id?: string;
        jenis?: string;
        tanggal_dari?: string;
        tanggal_sampai?: string;
    };
}

const props = defineProps<Props>();
const { getInitials } = useInitials();

const filters = ref({
    barang_id: props.filters.barang_id || '',
    jenis: props.filters.jenis || '',
    tanggal_dari: props.filters.tanggal_dari || '',
    tanggal_sampai: props.filters.tanggal_sampai || ''
});

const applyFilters = () => {
    router.get(route('stok-log.index'), filters.value, {
        preserveState: true,
        preserveScroll: true
    });
};

const clearFilters = () => {
    filters.value = {
        barang_id: '',
        jenis: '',
        tanggal_dari: '',
        tanggal_sampai: ''
    };
    applyFilters();
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getJenisIcon = (jenis: string) => {
    return jenis === 'masuk' ? TrendingUp : TrendingDown;
};

const getJenisColor = (jenis: string) => {
    return jenis === 'masuk' ? 'text-green-600' : 'text-red-600';
};

const getJenisBadge = (jenis: string) => {
    return jenis === 'masuk' ? 'default' : 'destructive';
};

const getJenisLabel = (jenis: string) => {
    return jenis === 'masuk' ? 'Stok Masuk' : 'Stok Keluar';
};

const { getPhotoUrl } = usePhotoUrl();
</script>

<template>
    <Head title="Riwayat Stok" />

    <AppLayout>
        <div class="p-0 md:p-8 bg-gradient-to-br from-blue-50 via-white to-green-50 min-h-screen">
            <div class="mb-8">
                <div class="flex items-center gap-3">
                    <History class="h-8 w-8 text-blue-600 drop-shadow" />
                    <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Riwayat Stok</h1>
                </div>
                <p class="text-lg text-gray-500 mt-1">Pantau pergerakan stok barang masuk dan keluar secara real-time</p>
            </div>

            <!-- Filter Section -->
            <Card class="mb-8 shadow-lg border-0 bg-white/80 backdrop-blur">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-blue-700">
                        <Filter class="h-5 w-5" />
                        <span class="font-semibold">Filter Riwayat</span>
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="space-y-2">
                            <Label for="barang" class="font-semibold text-gray-700">Barang</Label>
                            <select 
                                id="barang"
                                v-model="filters.barang_id"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-200 transition"
                            >
                                <option value="">Semua Barang</option>
                                <option v-for="item in barang" :key="item.id" :value="item.id.toString()">
                                    {{ item.nama_barang }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <Label for="jenis" class="font-semibold text-gray-700">Jenis</Label>
                            <select 
                                id="jenis"
                                v-model="filters.jenis"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-200 transition"
                            >
                                <option value="">Semua</option>
                                <option value="masuk">Stok Masuk</option>
                                <option value="keluar">Stok Keluar</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <Label for="tanggal_dari" class="font-semibold text-gray-700">Tanggal Dari</Label>
                            <Input 
                                id="tanggal_dari"
                                v-model="filters.tanggal_dari"
                                type="date"
                                class="rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-200 transition"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="tanggal_sampai" class="font-semibold text-gray-700">Tanggal Sampai</Label>
                            <Input 
                                id="tanggal_sampai"
                                v-model="filters.tanggal_sampai"
                                type="date"
                                class="rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-200 transition"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <Button variant="outline" class="border-gray-300" @click="clearFilters">
                            Reset Filter
                        </Button>
                        <Button class="bg-blue-600 hover:bg-blue-700 text-white" @click="applyFilters">
                            Terapkan Filter
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <Card class="shadow-md border-0 bg-gradient-to-br from-blue-100 to-blue-50">
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-700">Total Transaksi</p>
                                <p class="text-3xl font-extrabold text-blue-900">{{ stokLogs.total }}</p>
                            </div>
                            <History class="h-10 w-10 text-blue-400 drop-shadow" />
                        </div>
                    </CardContent>
                </Card>

                <Card class="shadow-md border-0 bg-gradient-to-br from-green-100 to-green-50">
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-green-700">Stok Masuk</p>
                                <p class="text-3xl font-extrabold text-green-700">
                                    {{ stokLogs.data.filter(log => log.jenis === 'masuk').length }}
                                </p>
                            </div>
                            <TrendingUp class="h-10 w-10 text-green-500 drop-shadow" />
                        </div>
                    </CardContent>
                </Card>

                <Card class="shadow-md border-0 bg-gradient-to-br from-red-100 to-red-50">
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-red-700">Stok Keluar</p>
                                <p class="text-3xl font-extrabold text-red-700">
                                    {{ stokLogs.data.filter(log => log.jenis === 'keluar').length }}
                                </p>
                            </div>
                            <TrendingDown class="h-10 w-10 text-red-500 drop-shadow" />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Table -->
            <Card class="shadow-xl border-0 bg-white/90 backdrop-blur">
                <CardContent class="p-0 md:p-6">
                    <div class="overflow-x-auto rounded-xl">
                        <Table class="min-w-full">
                            <TableHeader>
                                <TableRow class="bg-gradient-to-r from-blue-100 to-green-100">
                                    <TableHead class="py-3 px-4 text-gray-700 font-bold">Tanggal</TableHead>
                                    <TableHead class="py-3 px-4 text-gray-700 font-bold">Barang</TableHead>
                                    <TableHead class="py-3 px-4 text-gray-700 font-bold">Jenis</TableHead>
                                    <TableHead class="py-3 px-4 text-center text-gray-700 font-bold">Jumlah</TableHead>
                                    <TableHead class="py-3 px-4 text-center text-gray-700 font-bold">Stok Sebelum</TableHead>
                                    <TableHead class="py-3 px-4 text-center text-gray-700 font-bold">Stok Sesudah</TableHead>
                                    <TableHead class="py-3 px-4 text-gray-700 font-bold">Keterangan</TableHead>
                                    <TableHead class="py-3 px-4 text-gray-700 font-bold">User</TableHead>
                                    <TableHead class="py-3 px-4 text-center text-gray-700 font-bold">Aksi</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="log in stokLogs.data" :key="log.id" class="hover:bg-blue-50/40 transition">
                                    <TableCell class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <Calendar class="h-4 w-4 text-blue-400" />
                                            <span class="text-sm text-gray-700">{{ formatDate(log.created_at) }}</span>
                                        </div>
                                    </TableCell>
                                    <TableCell class="py-3 px-4">
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ log.barang.nama_barang }}</p>
                                            <p class="text-xs text-gray-400">{{ log.barang.kode_barang }}</p>
                                        </div>
                                    </TableCell>
                                    <TableCell class="py-3 px-4">
                                        <Badge :variant="getJenisBadge(log.jenis)" class="px-2 py-1 rounded-full text-xs font-semibold flex items-center gap-1"
                                            :class="log.jenis === 'masuk' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                            <component :is="getJenisIcon(log.jenis)" class="h-3 w-3 mr-1" />
                                            {{ getJenisLabel(log.jenis) }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="py-3 px-4 text-center">
                                        <span :class="['font-bold text-lg', getJenisColor(log.jenis)]">
                                            {{ log.jenis === 'masuk' ? '+' : '-' }}{{ log.jumlah }}
                                        </span>
                                    </TableCell>
                                    <TableCell class="py-3 px-4 text-center">
                                        <span class="font-medium text-gray-700">{{ log.stok_sebelum }}</span>
                                    </TableCell>
                                    <TableCell class="py-3 px-4 text-center">
                                        <span class="font-medium text-gray-700">{{ log.stok_sesudah }}</span>
                                    </TableCell>
                                    <TableCell class="py-3 px-4 max-w-xs">
                                        <span class="text-sm text-gray-600">{{ log.keterangan || '-' }}</span>
                                    </TableCell>
                                    <TableCell class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <Avatar class="h-8 w-8 ring-2 ring-blue-200">
                                                <AvatarImage v-if="log.user_photo" :src="getPhotoUrl(log.user_photo)" alt="User Photo" />
                                                <AvatarFallback class="bg-blue-100 text-blue-700 font-bold">
                                                    {{ getInitials(log.user.name) }}
                                                </AvatarFallback>
                                            </Avatar>
                                            <span class="text-sm text-gray-700 font-semibold">{{ log.user.name }}</span>
                                        </div>
                                    </TableCell>
                                    <TableCell class="py-3 px-4 text-center">
                                        <Button 
                                            variant="outline" 
                                            size="sm"
                                            class="border-blue-200 hover:bg-blue-100"
                                            @click="router.get(route('stok-log.show', log.id))"
                                        >
                                            <Eye class="h-4 w-4 text-blue-600" />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div v-if="stokLogs.data.length === 0" class="text-center py-16">
                        <History class="h-16 w-16 mx-auto text-blue-200 mb-4" />
                        <p class="text-lg text-gray-400">Belum ada riwayat stok</p>
                    </div>

                    <!-- Pagination -->
                    <div v-if="stokLogs.last_page > 1" class="flex flex-col md:flex-row items-center justify-between mt-8 gap-4">
                        <p class="text-sm text-gray-500">
                            Menampilkan <span class="font-bold text-blue-700">{{ stokLogs.data.length }}</span> dari <span class="font-bold text-blue-700">{{ stokLogs.total }}</span> data
                        </p>
                        <div class="flex gap-2">
                            <Button 
                                variant="outline" 
                                size="sm"
                                class="border-gray-300"
                                :disabled="stokLogs.current_page === 1"
                                @click="router.get(route('stok-log.index'), { ...filters, page: stokLogs.current_page - 1 })"
                            >
                                Sebelumnya
                            </Button>
                            <span class="px-3 py-1 rounded bg-blue-50 text-blue-700 font-semibold text-sm border border-blue-100">
                                Halaman {{ stokLogs.current_page }} / {{ stokLogs.last_page }}
                            </span>
                            <Button 
                                variant="outline" 
                                size="sm"
                                class="border-gray-300"
                                :disabled="stokLogs.current_page === stokLogs.last_page"
                                @click="router.get(route('stok-log.index'), { ...filters, page: stokLogs.current_page + 1 })"
                            >
                                Selanjutnya
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>