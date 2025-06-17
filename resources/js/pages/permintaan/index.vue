<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Plus, Pencil, Trash2, Search, User } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';

// Declare route function globally for TypeScript
declare function route(name: string, params?: any): string;

interface Barang {
    id: number;
    kode_barang: string;
    nama_barang: string;
}

interface User {
    id: number;
    name: string;
}

interface Permintaan {
    id: number;
    user_id: number;
    barang_id: number;
    jumlah: number;
    keterangan: string;
    status: 'pending' | 'approved' | 'rejected' | 'completed';
    created_at: string;
    user: User;
    barang: Barang;
}

const props = defineProps<{
    permintaan: Permintaan[];
}>();

const searchQuery = ref('');
const selectedStatus = ref('');

const filteredPermintaan = computed(() => {
    return props.permintaan.filter(item => {
        const matchesSearch = item.barang.nama_barang.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.barang.kode_barang.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.user.name.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesStatus = !selectedStatus.value || item.status === selectedStatus.value;
        return matchesSearch && matchesStatus;
    });
});

const getStatusVariant = (status: string) => {
    switch (status) {
        case 'pending': return 'secondary';
        case 'approved': return 'default';
        case 'rejected': return 'destructive';
        case 'completed': return 'outline';
        default: return 'secondary';
    }
};

const getStatusText = (status: string) => {
    switch (status) {
        case 'pending': return 'Menunggu';
        case 'approved': return 'Disetujui';
        case 'rejected': return 'Ditolak';
        case 'completed': return 'Selesai';
        default: return status;
    }
};

const deletePermintaan = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus permintaan ini?')) {
        router.delete(route('permintaan.destroy', id));
    }
};
</script>

<template>
    <Head title="Permintaan" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h1 class="text-2xl font-semibold text-gray-800">Daftar Permintaan</h1>
                <Link :href="route('permintaan.create')">
                    <Button class="w-full sm:w-auto">
                        <Plus class="w-4 h-4 mr-2" />
                        Buat Permintaan
                    </Button>
                </Link>
            </div>

            <!-- Filter & Search -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari nama barang, kode barang, atau pemohon..."
                        class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
                <select
                    v-model="selectedStatus"
                    class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-60"
                >
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                    <option value="completed">Selesai</option>
                </select>
            </div>

            <!-- TABEL (untuk md ke atas) -->
            <div class="bg-white rounded-lg border hidden md:block">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Tanggal</TableHead>
                            <TableHead>Pemohon</TableHead>
                            <TableHead>Barang</TableHead>
                            <TableHead>Kode</TableHead>
                            <TableHead class="text-center">Jumlah</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Keterangan</TableHead>
                            <TableHead class="text-right">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="item in filteredPermintaan" :key="item.id">
                            <TableCell class="text-sm">
                                {{ new Date(item.created_at).toLocaleDateString('id-ID') }}
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <User class="w-4 h-4 text-gray-500" />
                                    <span>{{ item.user.name }}</span>
                                </div>
                            </TableCell>
                            <TableCell>{{ item.barang.nama_barang }}</TableCell>
                            <TableCell class="font-medium">{{ item.barang.kode_barang }}</TableCell>
                            <TableCell class="text-center font-semibold">{{ item.jumlah }}</TableCell>
                            <TableCell>
                                <Badge :variant="getStatusVariant(item.status)">
                                    {{ getStatusText(item.status) }}
                                </Badge>
                            </TableCell>
                            <TableCell class="max-w-xs truncate">
                                {{ item.keterangan || '-' }}
                            </TableCell>
                            <TableCell class="text-right space-x-2">
                                <Link :href="route('permintaan.edit', item.id)">
                                    <Button variant="outline" size="sm">
                                        <Pencil class="w-4 h-4" />
                                    </Button>
                                </Link>
                                <Button variant="destructive" size="sm" @click="deletePermintaan(item.id)">
                                    <Trash2 class="w-4 h-4" />
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- CARD (untuk hp/sm) -->
            <div class="space-y-4 md:hidden">
                <div
                    v-for="item in filteredPermintaan"
                    :key="item.id"
                    class="border rounded-lg p-4 bg-white shadow-sm"
                >
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="font-semibold text-lg">{{ item.barang.nama_barang }}</h2>
                        <Badge :variant="getStatusVariant(item.status)">
                            {{ getStatusText(item.status) }}
                        </Badge>
                    </div>
                    <p class="text-sm text-gray-500 mb-1"><strong>Kode:</strong> {{ item.barang.kode_barang }}</p>
                    <p class="text-sm text-gray-500 mb-1"><strong>Pemohon:</strong> {{ item.user.name }}</p>
                    <p class="text-sm text-gray-500 mb-1"><strong>Jumlah:</strong> {{ item.jumlah }}</p>
                    <p class="text-sm text-gray-500 mb-1"><strong>Tanggal:</strong> {{ new Date(item.created_at).toLocaleDateString('id-ID') }}</p>
                    <p class="text-sm text-gray-500 mb-3"><strong>Keterangan:</strong> {{ item.keterangan || '-' }}</p>
                    <div class="flex gap-2 justify-end">
                        <Link :href="route('permintaan.edit', item.id)">
                            <Button variant="outline" size="sm">
                                <Pencil class="w-4 h-4" />
                            </Button>
                        </Link>
                        <Button variant="destructive" size="sm" @click="deletePermintaan(item.id)">
                            <Trash2 class="w-4 h-4" />
                        </Button>
                    </div>
                </div>
            </div>

            <div v-if="filteredPermintaan.length === 0" class="text-center py-8">
                <div class="text-gray-500">
                    <Search class="h-12 w-12 mx-auto mb-4" />
                    <p>Tidak ada permintaan ditemukan</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

