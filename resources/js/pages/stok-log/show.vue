<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Calendar, Package, User, TrendingUp, TrendingDown, Hash } from 'lucide-vue-next';

interface StokLog {
    id: number;
    jenis: 'masuk' | 'keluar';
    jumlah: number;
    stok_sebelum: number;
    stok_sesudah: number;
    keterangan: string | null;
    referensi: string | null;
    created_at: string;
    updated_at: string;
    barang: {
        id: number;
        kode_barang: string;
        nama_barang: string;
        deskripsi: string | null;
        kategori: string;
        stok: number;
    };
    user: {
        id: number;
        name: string;
        email: string;
    };
}

interface Props {
    stokLog: StokLog;
}

const props = defineProps<Props>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
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
    <Head :title="`Detail Log Stok - ${stokLog.barang.nama_barang}`" />

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
                    <Hash class="h-6 w-6" />
                    Detail Log Stok #{{ stokLog.id }}
                </h1>
                <p class="text-muted-foreground">Informasi lengkap transaksi stok</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Informasi Transaksi -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <TrendingUp class="h-5 w-5" />
                            Informasi Transaksi
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Jenis Transaksi:</span>
                            <Badge :variant="getJenisBadge(stokLog.jenis)">
                                <component :is="getJenisIcon(stokLog.jenis)" class="h-3 w-3 mr-1" />
                                {{ getJenisLabel(stokLog.jenis) }}
                            </Badge>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Jumlah:</span>
                            <span :class="['font-semibold text-lg', getJenisColor(stokLog.jenis)]">
                                {{ stokLog.jenis === 'masuk' ? '+' : '-' }}{{ stokLog.jumlah }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Stok Sebelum:</span>
                            <span class="font-semibold">{{ stokLog.stok_sebelum }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Stok Sesudah:</span>
                            <span class="font-semibold">{{ stokLog.stok_sesudah }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Tanggal Transaksi:</span>
                            <div class="flex items-center gap-2">
                                <Calendar class="h-4 w-4 text-muted-foreground" />
                                <span class="text-sm">{{ formatDate(stokLog.created_at) }}</span>
                            </div>
                        </div>

                        <div v-if="stokLog.referensi" class="flex items-center justify-between">
                            <span class="text-sm font-medium">Referensi:</span>
                            <span class="text-sm">{{ stokLog.referensi }}</span>
                        </div>

                        <div v-if="stokLog.keterangan" class="pt-4 border-t">
                            <span class="text-sm font-medium block mb-2">Keterangan:</span>
                            <p class="text-sm text-muted-foreground bg-gray-50 p-3 rounded-lg">
                                {{ stokLog.keterangan }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Informasi Barang -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Package class="h-5 w-5" />
                            Informasi Barang
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Nama Barang:</span>
                            <span class="font-semibold">{{ stokLog.barang.nama_barang }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Kode Barang:</span>
                            <span class="font-mono text-sm">{{ stokLog.barang.kode_barang }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Kategori:</span>
                            <Badge :variant="stokLog.barang.kategori === 'peminjaman' ? 'default' : 'secondary'">
                                {{ stokLog.barang.kategori === 'peminjaman' ? 'Aset' : 'Permintaan' }}
                            </Badge>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Stok Saat Ini:</span>
                            <span class="font-semibold">{{ stokLog.barang.stok }}</span>
                        </div>

                        <div v-if="stokLog.barang.deskripsi" class="pt-4 border-t">
                            <span class="text-sm font-medium block mb-2">Deskripsi:</span>
                            <p class="text-sm text-muted-foreground bg-gray-50 p-3 rounded-lg">
                                {{ stokLog.barang.deskripsi }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Informasi User -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <User class="h-5 w-5" />
                            Informasi User
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Nama:</span>
                            <span class="font-semibold">{{ stokLog.user.name }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Email:</span>
                            <span class="text-sm">{{ stokLog.user.email }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">ID User:</span>
                            <span class="font-mono text-sm">{{ stokLog.user.id }}</span>
                        </div>
                    </CardContent>
                </Card>

                <!-- Riwayat Barang -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <TrendingUp class="h-5 w-5" />
                            Riwayat Barang Ini
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground mb-4">
                            Lihat semua riwayat transaksi untuk barang ini
                        </p>
                        <Button 
                            @click="router.get(route('stok-log.barang', stokLog.barang.id))"
                            class="w-full"
                        >
                            Lihat Riwayat Lengkap
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template> 