<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Package, AlertTriangle, Plus, Minus, History, Box, Handshake } from 'lucide-vue-next';
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

const getKategoriIcon = (kategori: string) => {
    return kategori === 'Peminjaman' ? Handshake : Box;
};

const getKategoriColor = (kategori: string) => {
    return kategori === 'Peminjaman' ? 'bg-blue-50 border-blue-200' : 'bg-green-50 border-green-200';
};

const getKategoriTextColor = (kategori: string) => {
    return kategori === 'Peminjaman' ? 'text-blue-700' : 'text-green-700';
};
</script>

<template>
    <Head title="Stok Barang" />

    <AppLayout>
        <div class="p-6">
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold flex items-center gap-2">
                            <Package class="h-6 w-6" />
                            Stok Barang
                        </h1>
                        <p class="text-muted-foreground">Kelola dan pantau stok barang berdasarkan kategori</p>
                    </div>
                    <Button 
                        @click="router.get(route('stok-log.index'))"
                        class="flex items-center gap-2"
                    >
                        <History class="h-4 w-4" />
                        Lihat Riwayat Stok
                    </Button>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <Card>
                    <CardContent class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Total Barang</p>
                                <p class="text-2xl font-bold">{{ barang.length }}</p>
                            </div>
                            <Package class="h-8 w-8 text-muted-foreground" />
                        </div>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardContent class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Stok Habis</p>
                                <p class="text-2xl font-bold text-red-600">
                                    {{ barang.filter(item => item.stok === 0).length }}
                                </p>
                            </div>
                            <AlertTriangle class="h-8 w-8 text-red-500" />
                        </div>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardContent class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Stok Menipis</p>
                                <p class="text-2xl font-bold text-yellow-600">
                                    {{ barang.filter(item => item.stok > 0 && item.stok <= 5).length }}
                                </p>
                            </div>
                            <AlertTriangle class="h-8 w-8 text-yellow-500" />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Category Cards -->
            <div class="space-y-6">
                <div v-for="(items, kategori) in groupedBarang" :key="kategori" class="space-y-4">
                    <div class="flex items-center gap-3">
                        <component :is="getKategoriIcon(kategori)" class="h-6 w-6" :class="getKategoriTextColor(kategori)" />
                        <h2 class="text-xl font-semibold" :class="getKategoriTextColor(kategori)">
                            {{ kategori }}
                        </h2>
                        <Badge variant="outline" class="ml-2">
                            {{ items.length }} barang
                        </Badge>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Card 
                            v-for="item in items" 
                            :key="item.id"
                            class="hover:shadow-md transition-shadow"
                            :class="getKategoriColor(kategori)"
                        >
                            <CardHeader class="pb-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <CardTitle class="text-lg font-semibold">
                                            {{ item.nama_barang }}
                                        </CardTitle>
                                        <CardDescription class="text-sm">
                                            {{ item.kode_barang }}
                                        </CardDescription>
                                    </div>
                                    <Badge :variant="getStokStatus(item.stok).variant" class="ml-2">
                                        {{ getStokStatus(item.stok).status }}
                                    </Badge>
                                </div>
                            </CardHeader>
                            
                            <CardContent class="space-y-4">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-muted-foreground">Stok:</span>
                                        <span :class="['text-lg font-bold', getStokColor(item.stok)]">
                                            {{ item.stok }}
                                        </span>
                                    </div>
                                    
                                    <div v-if="item.deskripsi" class="text-sm text-muted-foreground line-clamp-2">
                                        {{ item.deskripsi }}
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 pt-2">
                                    <Dialog :open="isDialogOpen && selectedBarang?.id === item.id" @update:open="isDialogOpen = $event">
                                        <DialogTrigger as-child>
                                            <Button 
                                                variant="outline" 
                                                size="sm"
                                                class="flex-1"
                                                @click="openAddStockDialog(item)"
                                            >
                                                <Plus class="h-4 w-4 mr-1" />
                                                Tambah Stok
                                            </Button>
                                        </DialogTrigger>
                                        <DialogContent class="sm:max-w-md">
                                            <DialogHeader>
                                                <DialogTitle>Tambah Stok Barang</DialogTitle>
                                            </DialogHeader>
                                            <div class="space-y-4">
                                                <div class="bg-gray-50 p-3 rounded-lg">
                                                    <p class="text-sm font-medium">Barang: {{ selectedBarang?.nama_barang }}</p>
                                                    <p class="text-sm text-gray-600">Kode: {{ selectedBarang?.kode_barang }}</p>
                                                    <p class="text-sm text-gray-600">Stok Saat Ini: {{ selectedBarang?.stok }}</p>
                                                </div>
                                                
                                                <div class="space-y-2">
                                                    <Label for="stok_tambah">Jumlah Stok yang Ditambahkan</Label>
                                                    <Input 
                                                        id="stok_tambah"
                                                        v-model="form.stok_tambah"
                                                        type="number"
                                                        min="1"
                                                        placeholder="Masukkan jumlah stok"
                                                        required
                                                    />
                                                    <p v-if="form.errors.stok_tambah" class="text-sm text-red-500">
                                                        {{ form.errors.stok_tambah }}
                                                    </p>
                                                </div>

                                                <div class="space-y-2">
                                                    <Label for="keterangan">Keterangan (Opsional)</Label>
                                                    <textarea 
                                                        id="keterangan"
                                                        v-model="form.keterangan"
                                                        rows="3"
                                                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                                        placeholder="Contoh: Pembelian dari supplier, retur barang, dll."
                                                    />
                                                    <p v-if="form.errors.keterangan" class="text-sm text-red-500">
                                                        {{ form.errors.keterangan }}
                                                    </p>
                                                </div>

                                                <div class="flex justify-end gap-3 pt-4">
                                                    <Button 
                                                        type="button" 
                                                        variant="outline"
                                                        @click="isDialogOpen = false"
                                                    >
                                                        Batal
                                                    </Button>
                                                    <Button 
                                                        type="button"
                                                        @click="submitAddStock"
                                                        :disabled="form.processing || !form.stok_tambah"
                                                    >
                                                        <Plus class="h-4 w-4 mr-1" />
                                                        Tambah Stok
                                                    </Button>
                                                </div>
                                            </div>
                                        </DialogContent>
                                    </Dialog>
                                    
                                    <Button 
                                        variant="outline" 
                                        size="sm"
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
            <div v-if="barang.length === 0" class="text-center py-12">
                <Package class="h-16 w-16 mx-auto text-muted-foreground mb-4" />
                <h3 class="text-lg font-medium text-muted-foreground mb-2">Belum ada data barang</h3>
                <p class="text-muted-foreground">Mulai dengan menambahkan barang baru ke sistem</p>
            </div>
        </div>
    </AppLayout>
</template>

