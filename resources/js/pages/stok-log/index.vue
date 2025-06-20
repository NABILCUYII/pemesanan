<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { History, Filter, Eye, Calendar, Package, TrendingUp, TrendingDown } from 'lucide-vue-next';
import { ref } from 'vue';

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
</script>

<template>
    <Head title="Riwayat Stok" />

    <AppLayout>
        <div class="p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold flex items-center gap-2">
                    <History class="h-6 w-6" />
                    Riwayat Stok
                </h1>
                <p class="text-muted-foreground">Pantau pergerakan stok barang masuk dan keluar</p>
            </div>

            <!-- Filter Section -->
            <Card class="mb-6">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Filter class="h-5 w-5" />
                        Filter Riwayat
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="space-y-2">
                            <Label for="barang">Barang</Label>
                            <select 
                                id="barang"
                                v-model="filters.barang_id"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                            >
                                <option value="">Semua Barang</option>
                                <option v-for="item in barang" :key="item.id" :value="item.id.toString()">
                                    {{ item.nama_barang }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <Label for="jenis">Jenis</Label>
                            <select 
                                id="jenis"
                                v-model="filters.jenis"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                            >
                                <option value="">Semua</option>
                                <option value="masuk">Stok Masuk</option>
                                <option value="keluar">Stok Keluar</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <Label for="tanggal_dari">Tanggal Dari</Label>
                            <Input 
                                id="tanggal_dari"
                                v-model="filters.tanggal_dari"
                                type="date"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="tanggal_sampai">Tanggal Sampai</Label>
                            <Input 
                                id="tanggal_sampai"
                                v-model="filters.tanggal_sampai"
                                type="date"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <Button variant="outline" @click="clearFilters">
                            Reset Filter
                        </Button>
                        <Button @click="applyFilters">
                            Terapkan Filter
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <Card>
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Total Transaksi</p>
                                <p class="text-2xl font-bold">{{ stokLogs.total }}</p>
                            </div>
                            <History class="h-8 w-8 text-muted-foreground" />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Stok Masuk</p>
                                <p class="text-2xl font-bold text-green-600">
                                    {{ stokLogs.data.filter(log => log.jenis === 'masuk').length }}
                                </p>
                            </div>
                            <TrendingUp class="h-8 w-8 text-green-600" />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Stok Keluar</p>
                                <p class="text-2xl font-bold text-red-600">
                                    {{ stokLogs.data.filter(log => log.jenis === 'keluar').length }}
                                </p>
                            </div>
                            <TrendingDown class="h-8 w-8 text-red-600" />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Table -->
            <Card>
                <CardContent class="p-6">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>Barang</TableHead>
                                <TableHead>Jenis</TableHead>
                                <TableHead class="text-center">Jumlah</TableHead>
                                <TableHead class="text-center">Stok Sebelum</TableHead>
                                <TableHead class="text-center">Stok Sesudah</TableHead>
                                <TableHead>Keterangan</TableHead>
                                <TableHead>User</TableHead>
                                <TableHead class="text-center">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="log in stokLogs.data" :key="log.id">
                                <TableCell>
                                    <div class="flex items-center gap-2">
                                        <Calendar class="h-4 w-4 text-muted-foreground" />
                                        <span class="text-sm">{{ formatDate(log.created_at) }}</span>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div>
                                        <p class="font-medium">{{ log.barang.nama_barang }}</p>
                                        <p class="text-sm text-muted-foreground">{{ log.barang.kode_barang }}</p>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <Badge :variant="getJenisBadge(log.jenis)">
                                        <component :is="getJenisIcon(log.jenis)" class="h-3 w-3 mr-1" />
                                        {{ getJenisLabel(log.jenis) }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-center">
                                    <span :class="['font-semibold', getJenisColor(log.jenis)]">
                                        {{ log.jenis === 'masuk' ? '+' : '-' }}{{ log.jumlah }}
                                    </span>
                                </TableCell>
                                <TableCell class="text-center">
                                    <span class="font-medium">{{ log.stok_sebelum }}</span>
                                </TableCell>
                                <TableCell class="text-center">
                                    <span class="font-medium">{{ log.stok_sesudah }}</span>
                                </TableCell>
                                <TableCell class="max-w-xs">
                                    <span class="text-sm">{{ log.keterangan || '-' }}</span>
                                </TableCell>
                                <TableCell>
                                    <span class="text-sm">{{ log.user.name }}</span>
                                </TableCell>
                                <TableCell class="text-center">
                                    <Button 
                                        variant="outline" 
                                        size="sm"
                                        @click="router.get(route('stok-log.show', log.id))"
                                    >
                                        <Eye class="h-4 w-4" />
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <div v-if="stokLogs.data.length === 0" class="text-center py-8">
                        <History class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
                        <p class="text-muted-foreground">Belum ada riwayat stok</p>
                    </div>

                    <!-- Pagination -->
                    <div v-if="stokLogs.last_page > 1" class="flex items-center justify-between mt-6">
                        <p class="text-sm text-muted-foreground">
                            Menampilkan {{ stokLogs.data.length }} dari {{ stokLogs.total }} data
                        </p>
                        <div class="flex gap-2">
                            <Button 
                                variant="outline" 
                                size="sm"
                                :disabled="stokLogs.current_page === 1"
                                @click="router.get(route('stok-log.index'), { ...filters, page: stokLogs.current_page - 1 })"
                            >
                                Sebelumnya
                            </Button>
                            <Button 
                                variant="outline" 
                                size="sm"
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