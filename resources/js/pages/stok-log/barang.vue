<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft, Package, TrendingUp, TrendingDown, Calendar, User, Hash } from 'lucide-vue-next';

interface StokLog {
    id: number;
    jenis: 'masuk' | 'keluar';
    jumlah: number;
    stok_sebelum: number;
    stok_sesudah: number;
    keterangan: string | null;
    referensi: string | null;
    created_at: string;
    user: {
        id: number;
        name: string;
    };
}

interface Barang {
    id: number;
    kode_barang: string;
    nama_barang: string;
    deskripsi: string | null;
    kategori: string;
    stok: number;
}

interface Props {
    barang: Barang;
    stokLogs: {
        data: StokLog[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

const props = defineProps<Props>();

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

const getStokStatus = (stok: number) => {
    if (stok === 0) {
        return { status: 'Habis', variant: 'destructive' as const };
    } else if (stok <= 5) {
        return { status: 'Stok Menipis', variant: 'secondary' as const };
    } else {
        return { status: 'Tersedia', variant: 'default' as const };
    }
};
</script>

<template>
    <Head :title="`Riwayat Stok - ${barang.nama_barang}`" />

    <AppLayout>
        <div class="p-6">
            <div class="mb-6">
                <Button 
                    variant="outline" 
                    @click="router.get(route('stok-log.index'))"
                    class="mb-4"
                >
                    <ArrowLeft class="h-4 w-4 mr-2" />
                    Kembali ke Riwayat
                </Button>
                
                <h1 class="text-2xl font-semibold flex items-center gap-2">
                    <Package class="h-6 w-6" />
                    Riwayat Stok: {{ barang.nama_barang }}
                </h1>
                <p class="text-muted-foreground">Riwayat lengkap pergerakan stok untuk barang ini</p>
            </div>

            <!-- Informasi Barang -->
            <Card class="mb-6">
                <CardHeader>
                    <CardTitle>Informasi Barang</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Kode Barang</p>
                            <p class="font-mono text-lg">{{ barang.kode_barang }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Kategori</p>
                            <Badge :variant="barang.kategori === 'peminjaman' ? 'default' : 'secondary'">
                                {{ barang.kategori === 'peminjaman' ? 'Peminjaman' : 'Permintaan' }}
                            </Badge>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Status Stok</p>
                            <Badge :variant="getStokStatus(barang.stok).variant">
                                {{ getStokStatus(barang.stok).status }}
                            </Badge>
                        </div>
                    </div>
                    
                    <div v-if="barang.deskripsi" class="mt-4 pt-4 border-t">
                        <p class="text-sm font-medium text-muted-foreground mb-2">Deskripsi</p>
                        <p class="text-sm">{{ barang.deskripsi }}</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <Card>
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Total Transaksi</p>
                                <p class="text-2xl font-bold">{{ stokLogs.total }}</p>
                            </div>
                            <Hash class="h-8 w-8 text-muted-foreground" />
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

                <Card>
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Stok Saat Ini</p>
                                <p class="text-2xl font-bold">{{ barang.stok }}</p>
                            </div>
                            <Package class="h-8 w-8 text-blue-600" />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Tabel Riwayat -->
            <Card>
                <CardContent class="p-6">
                    <h3 class="text-lg font-medium mb-4">Riwayat Transaksi</h3>
                    
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Tanggal</TableHead>
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
                                    <div class="flex items-center gap-2">
                                        <User class="h-4 w-4 text-muted-foreground" />
                                        <span class="text-sm">{{ log.user.name }}</span>
                                    </div>
                                </TableCell>
                                <TableCell class="text-center">
                                    <Button 
                                        variant="outline" 
                                        size="sm"
                                        @click="router.get(route('stok-log.show', log.id))"
                                    >
                                        Detail
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <div v-if="stokLogs.data.length === 0" class="text-center py-8">
                        <Package class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
                        <p class="text-muted-foreground">Belum ada riwayat transaksi untuk barang ini</p>
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
                                @click="router.get(route('stok-log.barang', barang.id), { page: stokLogs.current_page - 1 })"
                            >
                                Sebelumnya
                            </Button>
                            <Button 
                                variant="outline" 
                                size="sm"
                                :disabled="stokLogs.current_page === stokLogs.last_page"
                                @click="router.get(route('stok-log.barang', barang.id), { page: stokLogs.current_page + 1 })"
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