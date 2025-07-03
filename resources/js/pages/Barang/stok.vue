<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Package, AlertTriangle, Plus, History, Box, Handshake, ArrowUpCircle } from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface Barang {
    id: number;
    kode_barang: string;
    nama_barang: string;
    deskripsi: string;
    kategori: string;
    stok: number;
}

interface Props {
    barang: Barang[];
}

const props = defineProps<Props>();
const isDialogOpen = ref(false);
const selectedBarang = ref<Barang | null>(null);

const form = useForm({
    stok_tambah: '',
    keterangan: ''
});

// Group barang by kategori
const groupedBarang = computed(() => {
    const groups: { [key: string]: Barang[] } = {};
    props.barang.forEach(item => {
        const kategori = item.kategori === 'peminjaman' ? 'Peminjaman' : 'Permintaan';
        if (!groups[kategori]) {
            groups[kategori] = [];
        }
        groups[kategori].push(item);
    });
    return groups;
});

const getStokStatus = (stok: number) => {
    if (stok === 0) {
        return { status: 'Habis', variant: 'destructive' as const };
    } else if (stok <= 5) {
        return { status: 'Stok Menipis', variant: 'secondary' as const };
    } else {
        return { status: 'Tersedia', variant: 'default' as const };
    }
};

const openAddStockDialog = (barang: Barang) => {
    selectedBarang.value = barang;
    form.reset();
    form.stok_tambah = '';
    form.keterangan = '';
    isDialogOpen.value = true;
};

const submitAddStock = () => {
    if (!selectedBarang.value) return;
    
    form.post(route('barang.add-stok', selectedBarang.value.id), {
        onSuccess: () => {
            isDialogOpen.value = false;
            selectedBarang.value = null;
        }
    });
};

const getStokColor = (stok: number) => {
    if (stok === 0) return 'text-red-600';
    if (stok <= 5) return 'text-yellow-600';
    return 'text-green-600';
};

const viewStokHistory = (barangId: number) => {
    router.get(route('stok-log.barang', barangId));
};

// Perbaikan: kategori bisa string | number, jadi tambahkan typing dan konversi ke string
const getKategoriIcon = (kategori: string | number) => {
    const kategoriStr = String(kategori);
    return kategoriStr === 'Peminjaman' ? Handshake : Box;
};

const getKategoriColor = (kategori: string | number) => {
    const kategoriStr = String(kategori);
    return kategoriStr === 'Peminjaman' ? 'bg-gradient-to-br from-blue-100 to-blue-50 border-blue-200' : 'bg-gradient-to-br from-green-100 to-green-50 border-green-200';
};

const getKategoriTextColor = (kategori: string | number) => {
    const kategoriStr = String(kategori);
    return kategoriStr === 'Peminjaman' ? 'text-blue-700' : 'text-green-700';
};
</script>

<template>
    <Head title="Stok Barang" />

    <AppLayout>
        <div class="p-0 md:p-8 min-h-[100vh] bg-gradient-to-br from-indigo-50 via-white to-blue-100">
            <!-- Header -->
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold flex items-center gap-3 text-indigo-800 drop-shadow-sm">
                        <span class="bg-indigo-100 rounded-full p-2 shadow">
                            <Package class="h-8 w-8 text-indigo-500" />
                        </span>
                        Stok Barang
                    </h1>
                    <p class="text-base text-gray-500 mt-1">Kelola dan pantau stok barang berdasarkan kategori</p>
                </div>
                <Button 
                    @click="router.get(route('stok-log.index'))"
                    class="flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-blue-500 text-white shadow-lg hover:from-indigo-600 hover:to-blue-600"
                >
                    <History class="h-5 w-5" />
                    Lihat Riwayat Stok
                </Button>
            </div>
            

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <Card class="shadow-lg border-0 bg-gradient-to-br from-indigo-100 to-blue-50">
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Barang</p>
                                <p class="text-3xl font-extrabold text-indigo-700 drop-shadow">{{ barang.length }}</p>
                            </div>
                            <span class="bg-indigo-200 rounded-full p-3">
                                <Package class="h-9 w-9 text-indigo-500" />
                            </span>
                        </div>
                    </CardContent>
                </Card>
                
                <Card class="shadow-lg border-0 bg-gradient-to-br from-red-100 to-white">
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Stok Habis</p>
                                <p class="text-3xl font-extrabold text-red-600 drop-shadow">
                                    {{ barang.filter(item => item.stok === 0).length }}
                                </p>
                            </div>
                            <span class="bg-red-200 rounded-full p-3">
                                <AlertTriangle class="h-9 w-9 text-red-500" />
                            </span>
                        </div>
                    </CardContent>
                </Card>
                
                <Card class="shadow-lg border-0 bg-gradient-to-br from-yellow-100 to-white">
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Stok Menipis</p>
                                <p class="text-3xl font-extrabold text-yellow-600 drop-shadow">
                                    {{ barang.filter(item => item.stok > 0 && item.stok <= 5).length }}
                                </p>
                            </div>
                            <span class="bg-yellow-200 rounded-full p-3">
                                <AlertTriangle class="h-9 w-9 text-yellow-500" />
                            </span>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Category Cards -->
            <div class="space-y-10">
                <div v-for="(items, kategori) in groupedBarang" :key="kategori" class="space-y-6">
                    <div class="flex items-center gap-4 mb-2">
                        <span class="rounded-full bg-white shadow p-2">
                            <component :is="getKategoriIcon(kategori)" class="h-7 w-7" :class="getKategoriTextColor(kategori)" />
                        </span>
                        <h2 class="text-2xl font-bold tracking-tight" :class="getKategoriTextColor(kategori)">
                            {{ kategori }}
                        </h2>
                        <Badge variant="outline" class="ml-2 px-3 py-1 text-base border-2 border-indigo-200 bg-white shadow">
                            {{ items.length }} barang
                        </Badge>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">
                        <Card 
                            v-for="item in items" 
                            :key="item.id"
                            class="hover:shadow-2xl transition-shadow border-0"
                            :class="getKategoriColor(kategori)"
                        >
                            <CardHeader class="pb-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <CardTitle class="text-xl font-bold text-gray-800 flex items-center gap-2">
                                            <span class="truncate">{{ item.nama_barang }}</span>
                                        </CardTitle>
                                        <CardDescription class="text-sm text-gray-500">
                                            {{ item.kode_barang }}
                                        </CardDescription>
                                    </div>
                                    <Badge :variant="getStokStatus(item.stok).variant" class="ml-2 px-3 py-1 text-base">
                                        {{ getStokStatus(item.stok).status }}
                                    </Badge>
                                </div>
                            </CardHeader>
                            
                            <CardContent class="space-y-4">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Stok:</span>
                                        <span :class="['text-2xl font-extrabold', getStokColor(item.stok)]">
                                            {{ item.stok }}
                                        </span>
                                    </div>
                                    
                                    <div v-if="item.deskripsi" class="text-sm text-gray-600 line-clamp-2 italic">
                                        {{ item.deskripsi }}
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 pt-2">
                                    <Dialog :open="isDialogOpen && selectedBarang?.id === item.id" @update:open="isDialogOpen = $event">
                                        <DialogTrigger as-child>
                                            <Button 
                                                variant="outline" 
                                                size="sm"
                                                class="flex-1 border-indigo-300 hover:bg-indigo-50"
                                                @click="openAddStockDialog(item)"
                                            >
                                                <Plus class="h-4 w-4 mr-1" />
                                                Tambah Stok
                                            </Button>
                                        </DialogTrigger>
                                        <DialogContent class="sm:max-w-lg p-0 overflow-hidden">
                                            <div class="bg-gradient-to-br from-indigo-500 via-blue-400 to-blue-200 p-0 rounded-t-lg">
                                                <div class="flex items-center gap-3 px-6 py-5">
                                                    <span class="bg-white rounded-full p-3 shadow">
                                                        <ArrowUpCircle class="h-8 w-8 text-indigo-500" />
                                                    </span>
                                                    <div>
                                                        <DialogHeader>
                                                            <DialogTitle class="text-white text-2xl font-bold drop-shadow">Tambah Stok Barang</DialogTitle>
                                                        </DialogHeader>
                                                        <p class="text-white/80 text-sm mt-1">Isi form berikut untuk menambah stok barang</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-white px-6 py-6">
                                                <div class="mb-5">
                                                    <div class="flex items-center gap-4">
                                                        <span class="bg-indigo-100 rounded-full p-2 shadow">
                                                            <Package class="h-7 w-7 text-indigo-500" />
                                                        </span>
                                                        <div>
                                                            <p class="text-lg font-semibold text-indigo-800">{{ selectedBarang?.nama_barang }}</p>
                                                            <p class="text-xs text-gray-500">Kode: <span class="font-mono">{{ selectedBarang?.kode_barang }}</span></p>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3 flex items-center gap-2">
                                                        <span class="text-xs text-gray-500">Stok Saat Ini:</span>
                                                        <span class="text-lg font-bold" :class="getStokColor(selectedBarang?.stok || 0)">
                                                            {{ selectedBarang?.stok }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <form @submit.prevent="submitAddStock" class="space-y-5">
                                                    <div>
                                                        <Label for="stok_tambah" class="font-semibold text-gray-700">Jumlah Stok yang Ditambahkan</Label>
                                                        <div class="relative mt-1">
                                                            <Input 
                                                                id="stok_tambah"
                                                                v-model="form.stok_tambah"
                                                                type="number"
                                                                min="1"
                                                                placeholder="Masukkan jumlah stok"
                                                                required
                                                                class="pr-12"
                                                            />
                                                            <!-- HILANGKAN TANDA + -->
                                                        </div>
                                                        <p v-if="form.errors.stok_tambah" class="text-sm text-red-500 mt-1">
                                                            {{ form.errors.stok_tambah }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <Label for="keterangan" class="font-semibold text-gray-700">Keterangan <span class="text-gray-400 font-normal">(Opsional)</span></Label>
                                                        <textarea 
                                                            id="keterangan"
                                                            v-model="form.keterangan"
                                                            rows="3"
                                                            class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm ring-offset-background placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 transition"
                                                            placeholder="Contoh: Pembelian dari supplier, retur barang, dll."
                                                        />
                                                        <p v-if="form.errors.keterangan" class="text-sm text-red-500 mt-1">
                                                            {{ form.errors.keterangan }}
                                                        </p>
                                                    </div>
                                                    <div class="flex justify-end gap-3 pt-2">
                                                        <Button 
                                                            type="button" 
                                                            variant="outline"
                                                            class="border-gray-300"
                                                            @click="isDialogOpen = false"
                                                        >
                                                            Batal
                                                        </Button>
                                                        <Button 
                                                            type="submit"
                                                            class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white font-semibold shadow-lg hover:from-indigo-600 hover:to-blue-600"
                                                            :disabled="form.processing || !form.stok_tambah"
                                                        >
                                                            <Plus class="h-4 w-4 mr-1" />
                                                            Tambah Stok
                                                        </Button>
                                                    </div>
                                                </form>
                                            </div>
                                        </DialogContent>
                                    </Dialog>
                                    
                                    <Button 
                                        variant="outline" 
                                        size="sm"
                                        class="border-indigo-300 hover:bg-indigo-50"
                                        @click="viewStokHistory(item.id)"
                                    >
                                        <History class="h-4 w-4" />
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="barang.length === 0" class="text-center py-20">
                <span class="bg-indigo-100 rounded-full p-6 shadow inline-block mb-4">
                    <Package class="h-16 w-16 text-indigo-400" />
                </span>
                <h3 class="text-2xl font-bold text-gray-400 mb-2">Belum ada data barang</h3>
                <p class="text-gray-500">Mulai dengan menambahkan barang baru ke sistem</p>
            </div>
        </div>
    </AppLayout>
</template>

