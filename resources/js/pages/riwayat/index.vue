<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Search, Package, Calendar, Edit, X } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { useInitials } from '@/composables/useInitials';

interface RiwayatItem {
    id: number;
    type: 'permintaan' | 'peminjaman';
    nama_barang: string;
    kode_barang: string;
    jumlah: number;
    status: string;
    created_at: string;
    keterangan?: string;
    tanggal_peminjaman?: string;
    tanggal_pengembalian?: string;
    due_date?: string;
    alasan_approval?: string;
    catatan_approval?: string;
    user_photo?: string;
}

const props = defineProps<{
    riwayat: RiwayatItem[];
}>();

const { getInitials } = useInitials();

const searchQuery = ref('');
const selectedType = ref('');
const selectedStatus = ref('');

const filteredRiwayat = computed(() => {
    return props.riwayat.filter(item => {
        const matchesSearch =
            (item.nama_barang?.toLowerCase() || '').includes(searchQuery.value.toLowerCase()) ||
            (item.kode_barang?.toLowerCase() || '').includes(searchQuery.value.toLowerCase());

        const matchesType = !selectedType.value || item.type === selectedType.value;
        const matchesStatus = !selectedStatus.value || item.status === selectedStatus.value;

        return matchesSearch && matchesType && matchesStatus;
    });
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

const getTypeText = (type: string) => {
    return type === 'permintaan' ? 'Permintaan' : 'Peminjaman';
};

const cancelItem = (item: RiwayatItem) => {
    if (confirm('Apakah Anda yakin ingin membatalkan ' + getTypeText(item.type).toLowerCase() + ' ini?')) {
        router.post(route('riwayat.cancel'), {
            type: item.type,
            id: item.id
        });
    }
};

const getPhotoUrl = (photoPath: string) => {
    return `/storage/${photoPath}`;
};
</script>

<template>
    <Head title="Riwayat" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Riwayat</h1>
                    <p class="text-sm text-gray-500 mt-1">Riwayat permintaan dan peminjaman Anda</p>
                </div>
            </div>

            <!-- Filter & Search -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari nama barang atau kode barang..."
                        class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
                <select
                    v-model="selectedType"
                    class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-48"
                >
                    <option value="">Semua Tipe</option>
                    <option value="permintaan">Permintaan</option>
                    <option value="peminjaman">Peminjaman</option>
                </select>
                <select
                    v-model="selectedStatus"
                    class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-48"
                >
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                    <option value="completed">Selesai</option>
                </select>
            </div>

            <!-- Table (untuk desktop) -->
            <div class="hidden md:block">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Tipe</TableHead>
                            <TableHead>Nama Barang</TableHead>
                            <TableHead>Kode Barang</TableHead>
                            <TableHead>Jumlah</TableHead>
                            <TableHead>Tanggal</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="item in filteredRiwayat" :key="item.id">
                            <TableCell>
                                <Badge :variant="item.type === 'permintaan' ? 'default' : 'secondary'">
                                    {{ getTypeText(item.type) }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{ item.nama_barang }}</TableCell>
                            <TableCell>{{ item.kode_barang }}</TableCell>
                            <TableCell>{{ item.jumlah }}</TableCell>
                            <TableCell>{{ formatDate(item.created_at) }}</TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Badge :variant="getStatusVariant(item.status)">
                                        {{ getStatusText(item.status) }}
                                    </Badge>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="flex gap-2">
                                    <Badge :variant="getStatusVariant(item.status)">
                                        {{ getStatusText(item.status) }}
                                    </Badge>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- CARD (untuk hp/sm) -->
            <div class="space-y-4 md:hidden">
                <div
                    v-for="item in filteredRiwayat"
                    :key="item.id"
                    class="border rounded-lg p-4 bg-white shadow-sm"
                >
                    <div class="flex justify-between items-start mb-4">
                        <Badge :variant="item.type === 'permintaan' ? 'default' : 'secondary'">
                            {{ getTypeText(item.type) }}
                        </Badge>
                        <Badge :variant="getStatusVariant(item.status)">
                            {{ getStatusText(item.status) }}
                        </Badge>
                    </div>

                    <div class="space-y-2">
                        <h3 class="font-semibold">{{ item.nama_barang }}</h3>
                        <p class="text-sm text-gray-500"><strong>Kode:</strong> {{ item.kode_barang }}</p>
                        <p class="text-sm text-gray-500"><strong>Jumlah:</strong> {{ item.jumlah }}</p>
                        <p class="text-sm text-gray-500"><strong>Tanggal:</strong> {{ formatDate(item.created_at) }}</p>
                        
                        <template v-if="item.type === 'peminjaman'">
                            <p class="text-sm text-gray-500">
                                <strong>Tanggal Pinjam:</strong> {{ formatDate(item.tanggal_peminjaman!) }}
                            </p>
                            <p class="text-sm text-gray-500">
                                <strong>Tanggal Kembali:</strong> {{ formatDate(item.tanggal_pengembalian!) }}
                            </p>
                        </template>

                        <div v-if="item.keterangan" class="text-sm text-gray-500">
                            <strong>Keterangan:</strong> {{ item.keterangan }}
                        </div>

                        <div v-if="item.alasan_approval" class="text-sm text-gray-500">
                            <strong>Alasan:</strong> {{ item.alasan_approval }}
                        </div>

                        <div v-if="item.catatan_approval" class="text-sm text-gray-500">
                            <strong>Catatan:</strong> {{ item.catatan_approval }}
                        </div>
                    </div>

                    <div v-if="item.status === 'pending'" class="flex gap-4 mt-4 pt-4 border-t">
                        <p class="text-sm text-gray-500">Status: {{ getStatusText(item.status) }}</p>
                    </div>
                </div>
            </div>

            <div v-if="filteredRiwayat.length === 0" class="text-center py-8">
                <div class="text-gray-500">
                    <Search class="h-12 w-12 mx-auto mb-4" />
                    <p>Tidak ada riwayat ditemukan</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template> 