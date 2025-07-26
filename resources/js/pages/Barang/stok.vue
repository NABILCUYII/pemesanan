<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Package, AlertTriangle, Plus, History, Box, Handshake, ArrowUpCircle, MapPin, Ruler, Search, Info } from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface Barang {
    id: number;
    kode_barang: string;
    nama_barang: string;
    deskripsi: string;
    kategori: string;
    stok: number;
    satuan?: string;
    lokasi?: string;
}

interface Props {
    barang: {
        data: Barang[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
    filters: {
        search?: string;
        kategori?: string;
    };
    statistics: {
        total: number;
        stok_habis: number;
        stok_menipis: number;
    };
}

const props = defineProps<Props>();
const isDialogOpen = ref(false);
const selectedBarang = ref<Barang | null>(null);

const form = useForm({
    stok_tambah: '',
    keterangan: ''
});

// Search and filter functionality
const searchQuery = ref(props.filters.search || '');
const selectedKategori = ref(props.filters.kategori || '');

const applyFilters = () => {
    router.get(route('barang.stok'), {
        search: searchQuery.value,
        kategori: selectedKategori.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedKategori.value = '';
    applyFilters();
};

// Group barang by kategori
const groupedBarang = computed(() => {
    const groups: { [key: string]: Barang[] } = {};
    props.barang.data.forEach(item => {
        const kategori = item.kategori === 'peminjaman' ? 'Aset' : 'Permintaan';
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

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-break: break-word;
}

.card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1rem;
}

@media (min-width: 640px) {
    .card-grid {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }
}

@media (min-width: 1024px) {
    .card-grid {
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    }
}

@media (min-width: 1280px) {
    .card-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}
</style>

<template>
    <Head title="Stok Barang" />

    <AppLayout>
        <div class="p-0 md:p-8 min-h-[100vh] bg-gradient-to-br from-indigo-50 via-white to-blue-100">
            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold flex items-center gap-2 text-indigo-800">
                        <span class="bg-indigo-100 rounded-full p-1.5">
                            <Package class="h-6 w-6 text-indigo-500" />
                        </span>
                        Stok Barang
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola dan pantau stok barang berdasarkan kategori</p>
                </div>
                <Button 
                    @click="router.get(route('stok-log.index'))"
                    size="sm"
                    class="flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-blue-500 text-white shadow-sm hover:from-indigo-600 hover:to-blue-600"
                >
                    <History class="h-4 w-4" />
                    Riwayat Stok
                </Button>
            </div>

            <!-- Search and Filter Section -->
            <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-100 p-4">
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <Input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari nama barang atau kode barang..."
                            class="pl-9 pr-3 py-2 text-sm border-gray-200 focus:border-indigo-400 focus:ring-indigo-400"
                            @keyup.enter="applyFilters"
                        />
                    </div>
                    
                    <!-- Kategori Filter -->
                    <select
                        v-model="selectedKategori"
                        class="border border-gray-200 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 w-full sm:w-48"
                    >
                        <option value="">Semua Kategori</option>
                        <option value="peminjaman">Aset (Peminjaman)</option>
                        <option value="permintaan">Permintaan</option>
                    </select>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <Button 
                            @click="applyFilters"
                            size="sm"
                            class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white hover:from-indigo-600 hover:to-blue-600 px-4"
                        >
                            <Search class="h-4 w-4 mr-1" />
                            Cari
                        </Button>
                        <Button 
                            @click="clearFilters"
                            variant="outline"
                            size="sm"
                            class="border-gray-300 hover:bg-gray-50 px-4"
                        >
                            Reset
                        </Button>
                    </div>
                </div>
            </div>
            

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <Card class="shadow-sm border-0 bg-gradient-to-br from-indigo-100 to-blue-50">
                    <CardContent class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500">Total Barang</p>
                                <p class="text-2xl font-bold text-indigo-700">{{ statistics.total }}</p>
                            </div>
                            <span class="bg-indigo-200 rounded-full p-2">
                                <Package class="h-6 w-6 text-indigo-500" />
                            </span>
                        </div>
                    </CardContent>
                </Card>
                
                <Card class="shadow-sm border-0 bg-gradient-to-br from-red-100 to-white">
                    <CardContent class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500">Stok Habis</p>
                                <p class="text-2xl font-bold text-red-600">
                                    {{ statistics.stok_habis }}
                                </p>
                            </div>
                            <span class="bg-red-200 rounded-full p-2">
                                <AlertTriangle class="h-6 w-6 text-red-500" />
                            </span>
                        </div>
                    </CardContent>
                </Card>
                
                <Card class="shadow-sm border-0 bg-gradient-to-br from-yellow-100 to-white">
                    <CardContent class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500">Stok Menipis</p>
                                <p class="text-2xl font-bold text-yellow-600">
                                    {{ statistics.stok_menipis }}
                                </p>
                            </div>
                            <span class="bg-yellow-200 rounded-full p-2">
                                <AlertTriangle class="h-6 w-6 text-yellow-500" />
                            </span>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Category Cards -->
            <div class="space-y-6">
                <div v-for="(items, kategori) in groupedBarang" :key="kategori" class="space-y-4">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="rounded-full bg-white shadow-sm p-1.5">
                            <component :is="getKategoriIcon(kategori)" class="h-5 w-5" :class="getKategoriTextColor(kategori)" />
                        </span>
                        <h2 class="text-lg font-semibold tracking-tight" :class="getKategoriTextColor(kategori)">
                            {{ kategori }}
                        </h2>
                        <Badge variant="outline" class="ml-2 px-2 py-0.5 text-xs border border-indigo-200 bg-white">
                            {{ items.length }} barang
                        </Badge>
                    </div>

                    <div class="card-grid">
                        <Card 
                            v-for="item in items" 
                            :key="item.id"
                            class="hover:shadow-lg transition-shadow border-0 h-full flex flex-col"
                            :class="getKategoriColor(kategori)"
                        >
                            <CardHeader class="pb-2">
                                <div class="space-y-2">
                                    <div class="relative">
                                        <CardTitle 
                                            class="text-sm font-semibold text-gray-800 leading-tight line-clamp-2 mb-1 cursor-help"
                                            :title="item.nama_barang.length > 30 ? item.nama_barang : ''"
                                        >
                                            {{ item.nama_barang }}
                                        </CardTitle>
                                        <Info 
                                            v-if="item.nama_barang.length > 30" 
                                            class="absolute -top-1 -right-1 h-3 w-3 text-blue-500 opacity-60" 
                                        />
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        <Badge :variant="getStokStatus(item.stok).variant" class="px-2 py-0.5 text-xs">
                                            {{ getStokStatus(item.stok).status }}
                                        </Badge>
                                        <CardDescription class="text-xs text-gray-500 font-mono">
                                            {{ item.kode_barang }}
                                        </CardDescription>
                                    </div>
                                    
                                    <div class="flex flex-wrap gap-1">
                                        <span v-if="item.satuan" class="inline-flex items-center text-xs text-gray-500 bg-gray-100 rounded px-1 py-0.5 max-w-full">
                                            <Ruler class="w-3 h-3 mr-0.5 flex-shrink-0" />
                                            <span class="truncate">{{ item.satuan }}</span>
                                        </span>
                                        <span v-if="item.lokasi" class="inline-flex items-center text-xs text-gray-500 bg-gray-100 rounded px-1 py-0.5 max-w-full">
                                            <MapPin class="w-3 h-3 mr-0.5 flex-shrink-0" />
                                            <span class="truncate">{{ item.lokasi }}</span>
                                        </span>
                                    </div>
                                </div>
                            </CardHeader>
                            
                            <CardContent class="flex-1 flex flex-col justify-between space-y-3">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-medium text-gray-500">Stok:</span>
                                        <div class="text-right">
                                            <span :class="['text-base font-bold', getStokColor(item.stok)]">
                                                {{ item.stok }}
                                            </span>
                                            <span v-if="item.satuan" class="text-xs text-gray-500 ml-1 block">{{ item.satuan }}</span>
                                        </div>
                                    </div>
                                    
                                    <div v-if="item.deskripsi" class="text-xs text-gray-600 line-clamp-1 italic">
                                        {{ item.deskripsi }}
                                    </div>
                                </div>

                                <div class="flex items-center gap-1 pt-1 mt-auto">
                                    <Dialog :open="isDialogOpen && selectedBarang?.id === item.id" @update:open="isDialogOpen = $event">
                                        <DialogTrigger as-child>
                                            <Button 
                                                variant="outline" 
                                                size="sm"
                                                class="flex-1 border-indigo-300 hover:bg-indigo-50 text-xs px-2"
                                                @click="openAddStockDialog(item)"
                                            >
                                                <Plus class="h-3 w-3 mr-1" />
                                                Tambah
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
                                        class="border-indigo-300 hover:bg-indigo-50 p-1"
                                        @click="viewStokHistory(item.id)"
                                    >
                                        <History class="h-3 w-3" />
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="barang.last_page > 1" class="flex items-center justify-center mt-6">
                <nav class="inline-flex items-center gap-1 bg-white rounded-lg border border-gray-200 p-1 shadow-sm">
                    <Button 
                        variant="ghost" 
                        size="sm"
                        class="h-8 w-8 p-0 text-gray-500 hover:text-gray-700 hover:bg-gray-100"
                        :disabled="barang.current_page === 1"
                        @click="router.get(route('barang.stok'), { 
                            search: searchQuery, 
                            kategori: selectedKategori, 
                            page: barang.current_page - 1 
                        })"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Button>
                    
                    <span class="px-2 py-1 text-xs font-medium text-gray-700 bg-gray-50 rounded">
                        {{ barang.current_page }} / {{ barang.last_page }}
                    </span>
                    
                    <span class="px-2 py-1 text-xs text-gray-500">
                        ({{ barang.data.length }} dari {{ barang.total }})
                    </span>
                    
                    <Button 
                        variant="ghost" 
                        size="sm"
                        class="h-8 w-8 p-0 text-gray-500 hover:text-gray-700 hover:bg-gray-100"
                        :disabled="barang.current_page === barang.last_page"
                        @click="router.get(route('barang.stok'), { 
                            search: searchQuery, 
                            kategori: selectedKategori, 
                            page: barang.current_page + 1 
                        })"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Button>
                </nav>
            </div>

            <!-- Empty State -->
            <div v-if="barang.data.length === 0" class="text-center py-12">
                <span class="bg-indigo-100 rounded-full p-4 shadow-sm inline-block mb-3">
                    <Package class="h-12 w-12 text-indigo-400" />
                </span>
                <h3 class="text-lg font-semibold text-gray-400 mb-1">Belum ada data barang</h3>
                <p class="text-sm text-gray-500">Mulai dengan menambahkan barang baru ke sistem</p>
            </div>
        </div>
    </AppLayout>
</template>
