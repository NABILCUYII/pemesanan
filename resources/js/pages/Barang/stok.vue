<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Package, AlertTriangle, Plus, Minus } from 'lucide-vue-next';
import { ref } from 'vue';

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
</script>

<template>
    <Head title="Stok Barang" />

    <AppLayout>
        <div class="p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold flex items-center gap-2">
                    <Package class="h-6 w-6" />
                    Stok Barang
                </h1>
                <p class="text-muted-foreground">Kelola dan pantau stok barang</p>
            </div>

            <div class="bg-white rounded-lg border">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium">Daftar Stok Barang</h2>
                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                            <AlertTriangle class="h-4 w-4" />
                            <span>Total: {{ barang.length }} barang</span>
                        </div>
                    </div>

                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Kode Barang</TableHead>
                                <TableHead>Nama Barang</TableHead>
                                <TableHead>Kategori</TableHead>
                                <TableHead class="text-center">Stok</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Deskripsi</TableHead>
                                <TableHead class="text-center">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in barang" :key="item.id">
                                <TableCell class="font-medium">
                                    {{ item.kode_barang }}
                                </TableCell>
                                <TableCell>{{ item.nama_barang }}</TableCell>
                                <TableCell>
                                    <Badge :variant="item.kategori === 'peminjaman' ? 'default' : 'secondary'">
                                        {{ item.kategori === 'peminjaman' ? 'Peminjaman' : 'Permintaan' }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-center">
                                    <span :class="['font-semibold', getStokColor(item.stok)]">
                                        {{ item.stok }}
                                    </span>
                                </TableCell>
                                <TableCell>
                                    <Badge :variant="getStokStatus(item.stok).variant">
                                        {{ getStokStatus(item.stok).status }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="max-w-xs truncate">
                                    {{ item.deskripsi || '-' }}
                                </TableCell>
                                <TableCell class="text-center">
                                    <Dialog :open="isDialogOpen && selectedBarang?.id === item.id" @update:open="isDialogOpen = $event">
                                        <DialogTrigger as-child>
                                            <Button 
                                                variant="outline" 
                                                size="sm"
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
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <div v-if="barang.length === 0" class="text-center py-8">
                        <Package class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
                        <p class="text-muted-foreground">Belum ada data barang</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

