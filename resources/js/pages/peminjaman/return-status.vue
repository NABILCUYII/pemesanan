<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Search, User } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';

interface PeminjamanItem {
    id: number;
    nama_barang: string;
    kode_barang: string;
    jumlah: number;
    status: 'returned' | 'not_returned';
    tanggal_peminjaman: string;
    tanggal_pengembalian: string;
    due_date: string;
    keterangan?: string;
}

interface PeminjamanGroup {
    user: string;
    items: PeminjamanItem[];
}

const props = defineProps<{
    peminjaman: PeminjamanGroup[];
    isAdmin: boolean;
}>();

const searchQuery = ref('');

const filteredPeminjaman = computed(() => {
    if (!props.peminjaman) return [];
    
    return props.peminjaman
        .map(group => {
            const filteredItems = group.items.filter(item => {
                const matchesSearch =
                    (item.nama_barang?.toLowerCase() || '').includes(searchQuery.value.toLowerCase()) ||
                    (item.kode_barang?.toLowerCase() || '').includes(searchQuery.value.toLowerCase()) ||
                    (group.user?.toLowerCase() || '').includes(searchQuery.value.toLowerCase());

                return matchesSearch;
            });

            return filteredItems.length > 0 ? { user: group.user, items: filteredItems } : null;
        })
        .filter(Boolean) as PeminjamanGroup[];
});

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

const getStatusVariant = (status: string) => {
    switch (status) {
        case 'returned': return 'default';
        case 'not_returned': return 'secondary';
        default: return 'secondary';
    }
};

const getStatusText = (status: string) => {
    switch (status) {
        case 'returned': return 'Sudah Dikembalikan';
        case 'not_returned': return 'Belum Dikembalikan';
        default: return status;
    }
};
</script>

<template>
    <Head title="Status Pengembalian Barang" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Status Pengembalian Barang</h1>
                    <p class="text-sm text-gray-500 mt-1">Menampilkan status pengembalian barang</p>
                </div>
            </div>

            <!-- Search -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari nama barang, kode barang, atau peminjam..."
                        class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
            </div>

            <!-- Table (untuk desktop) -->
            <div class="hidden md:block">
                <div v-for="group in filteredPeminjaman" :key="group.user" class="mb-8">
                    <!-- User Header -->
                    <div class="flex items-center gap-2 mb-4 bg-gray-50 p-4 rounded-lg">
                        <User class="w-5 h-5 text-gray-500" />
                        <h2 class="font-semibold text-lg">{{ group.user }}</h2>
                    </div>

                    <!-- Loans Table -->
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nama Barang</TableHead>
                                <TableHead>Kode Barang</TableHead>
                                <TableHead>Jumlah</TableHead>
                                <TableHead>Tanggal Pinjam</TableHead>
                                <TableHead>Tanggal Kembali</TableHead>
                                <TableHead>Status</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in group.items" :key="item.id">
                                <TableCell>{{ item.nama_barang }}</TableCell>
                                <TableCell>{{ item.kode_barang }}</TableCell>
                                <TableCell>{{ item.jumlah }}</TableCell>
                                <TableCell>{{ formatDate(item.tanggal_peminjaman) }}</TableCell>
                                <TableCell>{{ formatDate(item.tanggal_pengembalian) }}</TableCell>
                                <TableCell>
                                    <Badge :variant="getStatusVariant(item.status)">
                                        {{ getStatusText(item.status) }}
                                    </Badge>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>

            <!-- CARD (untuk hp/sm) -->
            <div class="space-y-6 md:hidden">
                <div
                    v-for="group in filteredPeminjaman"
                    :key="group.user"
                    class="border rounded-lg overflow-hidden bg-white shadow-sm"
                >
                    <!-- User Header -->
                    <div class="bg-gray-50 p-4 border-b">
                        <div class="flex items-center gap-2">
                            <User class="w-5 h-5 text-gray-500" />
                            <h2 class="font-semibold text-lg">{{ group.user }}</h2>
                        </div>
                    </div>

                    <!-- Loans List -->
                    <div class="divide-y">
                        <div 
                            v-for="item in group.items" 
                            :key="item.id" 
                            class="p-4"
                        >
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="font-semibold">{{ item.nama_barang }}</h3>
                                    <p class="text-sm text-gray-500">Kode: {{ item.kode_barang }}</p>
                                    <p class="text-sm text-gray-500">Jumlah: {{ item.jumlah }}</p>
                                    <p class="text-sm text-gray-500">Tanggal Pinjam: {{ formatDate(item.tanggal_peminjaman) }}</p>
                                    <p class="text-sm text-gray-500">Tanggal Kembali: {{ formatDate(item.tanggal_pengembalian) }}</p>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <Badge :variant="getStatusVariant(item.status)">
                                        {{ getStatusText(item.status) }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="filteredPeminjaman.length === 0" class="text-center py-8">
                <div class="text-gray-500">
                    <Search class="h-12 w-12 mx-auto mb-4" />
                    <p>Tidak ada data pengembalian ditemukan</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template> 