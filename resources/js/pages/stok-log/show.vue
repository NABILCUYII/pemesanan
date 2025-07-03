<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Calendar, Package, User, TrendingUp, TrendingDown, Hash, Info, Mail, Layers } from 'lucide-vue-next';

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

const getJenisBg = (jenis: string) => {
    return jenis === 'masuk' ? 'bg-green-50' : 'bg-red-50';
};

const getJenisBadge = (jenis: string) => {
    return jenis === 'masuk' ? 'default' : 'destructive';
};

const getJenisLabel = (jenis: string) => {
    return jenis === 'masuk' ? 'Stok Masuk' : 'Stok Keluar';
};

const kategoriColor = (kategori: string) => {
    return kategori === 'peminjaman'
        ? 'bg-blue-100 text-blue-700'
        : 'bg-yellow-100 text-yellow-700';
};
</script>

<template>
    <Head :title="`Detail Log Stok - ${stokLog.barang.nama_barang}`" />

    <AppLayout>
        <div class="p-6 bg-gradient-to-br from-gray-50 via-white to-blue-50 min-h-screen">
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <Button 
                        variant="outline" 
                        @click="router.get(route('stok-log.index'))"
                        class="mb-4"
                    >
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Kembali ke Riwayat
                    </Button>
                    <h1 class="text-3xl font-extrabold flex items-center gap-3 text-blue-900 drop-shadow-sm">
                        <Hash class="h-7 w-7 text-blue-500" />
                        Detail Log Stok <span class="text-blue-400">#{{ stokLog.id }}</span>
                    </h1>
                    <p class="text-muted-foreground text-base mt-1">Informasi lengkap transaksi stok barang secara visual dan informatif.</p>
                </div>
                <div class="flex items-center gap-2">
                    <Badge :variant="getJenisBadge(stokLog.jenis)" class="text-base px-3 py-1.5">
                        <component :is="getJenisIcon(stokLog.jenis)" class="h-4 w-4 mr-1" />
                        {{ getJenisLabel(stokLog.jenis) }}
                    </Badge>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Informasi Transaksi -->
                <Card class="shadow-lg border-2 border-blue-100 hover:shadow-xl transition-all duration-200">
                    <CardHeader class="bg-blue-50 rounded-t-lg">
                        <CardTitle class="flex items-center gap-2 text-blue-700">
                            <Info class="h-5 w-5" />
                            <span>Informasi Transaksi</span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-5 py-6">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Jenis Transaksi:</span>
                            <Badge :variant="getJenisBadge(stokLog.jenis)" class="px-2 py-1">
                                <component :is="getJenisIcon(stokLog.jenis)" class="h-3 w-3 mr-1" />
                                {{ getJenisLabel(stokLog.jenis) }}
                            </Badge>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Jumlah:</span>
                            <span :class="['font-bold text-xl', getJenisColor(stokLog.jenis)]">
                                {{ stokLog.jenis === 'masuk' ? '+' : '-' }}{{ stokLog.jumlah }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Stok Sebelum:</span>
                            <span class="font-semibold text-gray-800">{{ stokLog.stok_sebelum }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Stok Sesudah:</span>
                            <span class="font-semibold text-gray-800">{{ stokLog.stok_sesudah }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Tanggal Transaksi:</span>
                            <div class="flex items-center gap-2">
                                <Calendar class="h-4 w-4 text-blue-400" />
                                <span class="text-sm font-semibold text-gray-700">{{ formatDate(stokLog.created_at) }}</span>
                            </div>
                        </div>

                        <div v-if="stokLog.referensi" class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Referensi:</span>
                            <span class="text-sm text-blue-700 font-semibold">{{ stokLog.referensi }}</span>
                        </div>

                        <div v-if="stokLog.keterangan" class="pt-4 border-t border-blue-100">
                            <span class="text-sm font-medium block mb-2 text-gray-600">Keterangan:</span>
                            <p class="text-sm text-gray-700 bg-blue-50 p-3 rounded-lg italic">
                                {{ stokLog.keterangan }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Informasi Barang -->
                <Card class="shadow-lg border-2 border-yellow-100 hover:shadow-xl transition-all duration-200">
                    <CardHeader class="bg-yellow-50 rounded-t-lg">
                        <CardTitle class="flex items-center gap-2 text-yellow-700">
                            <Package class="h-5 w-5" />
                            <span>Informasi Barang</span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-5 py-6">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Nama Barang:</span>
                            <span class="font-bold text-gray-800">{{ stokLog.barang.nama_barang }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Kode Barang:</span>
                            <span class="font-mono text-base text-yellow-700 bg-yellow-100 px-2 py-1 rounded">{{ stokLog.barang.kode_barang }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Kategori:</span>
                            <span :class="['rounded px-2 py-1 font-semibold text-xs', kategoriColor(stokLog.barang.kategori)]">
                                {{ stokLog.barang.kategori === 'peminjaman' ? 'Peminjaman' : 'Permintaan' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Stok Saat Ini:</span>
                            <span class="font-bold text-lg text-yellow-700">{{ stokLog.barang.stok }}</span>
                        </div>

                        <div v-if="stokLog.barang.deskripsi" class="pt-4 border-t border-yellow-100">
                            <span class="text-sm font-medium block mb-2 text-gray-600">Deskripsi:</span>
                            <p class="text-sm text-gray-700 bg-yellow-50 p-3 rounded-lg">
                                {{ stokLog.barang.deskripsi }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Informasi User -->
                <Card class="shadow-lg border-2 border-purple-100 hover:shadow-xl transition-all duration-200">
                    <CardHeader class="bg-purple-50 rounded-t-lg">
                        <CardTitle class="flex items-center gap-2 text-purple-700">
                            <User class="h-5 w-5" />
                            <span>Informasi User</span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-5 py-6">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Nama:</span>
                            <span class="font-semibold text-gray-800">{{ stokLog.user.name }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Email:</span>
                            <span class="text-sm text-purple-700 flex items-center gap-1">
                                <Mail class="h-4 w-4" /> {{ stokLog.user.email }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">ID User:</span>
                            <span class="font-mono text-sm text-purple-700 bg-purple-100 px-2 py-1 rounded">{{ stokLog.user.id }}</span>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Riwayat Barang -->
            <div class="mt-10 max-w-2xl mx-auto">
                <Card class="shadow-lg border-2 border-green-100 hover:shadow-xl transition-all duration-200">
                    <CardHeader class="bg-green-50 rounded-t-lg">
                        <CardTitle class="flex items-center gap-2 text-green-700">
                            <Layers class="h-5 w-5" />
                            <span>Riwayat Barang Ini</span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-base text-muted-foreground mb-4">
                            Ingin tahu lebih banyak? Lihat semua riwayat transaksi untuk barang ini.
                        </p>
                        <Button 
                            @click="router.get(route('stok-log.barang', stokLog.barang.id))"
                            class="w-full font-bold text-lg bg-gradient-to-r from-green-400 to-green-600 text-white hover:from-green-500 hover:to-green-700 transition-all duration-200"
                        >
                            Lihat Riwayat Lengkap
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template> 