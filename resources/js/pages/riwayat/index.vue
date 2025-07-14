<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Search, Package, Calendar, Edit, X, Trash2, ClipboardList, CheckCircle2, Clock, XCircle } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { useInitials } from '@/composables/useInitials';
import { usePhotoUrl } from '@/composables/usePhotoUrl';

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
    if (!dateString) return '-';
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

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'pending': return Clock;
        case 'approved': return CheckCircle2;
        case 'rejected': return XCircle;
        case 'completed': return ClipboardList;
        default: return Clock;
    }
};

const getTypeText = (type: string) => {
    return type === 'permintaan' ? 'Permintaan' : 'Aset';
};

const getTypeColor = (type: string) => {
    return type === 'permintaan'
        ? 'bg-gradient-to-br from-blue-100 to-blue-50 border-blue-200 text-blue-700'
        : 'bg-gradient-to-br from-green-100 to-green-50 border-green-200 text-green-700';
};

const cancelItem = (item: RiwayatItem) => {
    if (confirm('Apakah Anda yakin ingin membatalkan ' + getTypeText(item.type).toLowerCase() + ' ini?')) {
        router.post(route('riwayat.cancel'), {
            type: item.type,
            id: item.id
        });
    }
};

const deleteAllHistory = () => {
    if (confirm('Apakah Anda yakin ingin menghapus semua riwayat? Tindakan ini tidak dapat dibatalkan.')) {
        router.delete(route('riwayat.delete-all'));
    }
};

const { getPhotoUrl } = usePhotoUrl();
</script>

<template>
    <Head title="Riwayat" />
    <AppLayout>
        <div class="p-4 md:p-8 space-y-8 bg-gradient-to-br from-blue-50 via-white to-green-50 min-h-screen">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-indigo-800 flex items-center gap-2">
                        <ClipboardList class="w-7 h-7 text-indigo-500" />
                        Riwayat
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Riwayat permintaan dan peminjaman Anda</p>
                </div>
                <Button 
                    @click="deleteAllHistory" 
                    variant="destructive" 
                    class="flex items-center gap-2 shadow hover:scale-105 transition"
                >
                    <Trash2 class="w-4 h-4" />
                    Hapus Semua
                </Button>
            </div>

            <!-- Filter & Search -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari nama barang atau kode barang..."
                        class="w-full pl-10 pr-4 py-2 border border-blue-200 rounded-lg bg-white shadow focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                    />
                </div>
                <select
                    v-model="selectedType"
                    class="border border-blue-200 rounded-lg px-4 py-2 bg-white shadow focus:outline-none focus:ring-2 focus:ring-blue-400 w-full md:w-48"
                >
                    <option value="">Semua Tipe</option>
                    <option value="permintaan">Permintaan</option>
                    <option value="peminjaman">Aset</option>
                </select>
                <select
                    v-model="selectedStatus"
                    class="border border-blue-200 rounded-lg px-4 py-2 bg-white shadow focus:outline-none focus:ring-2 focus:ring-blue-400 w-full md:w-48"
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
                <div class="rounded-xl overflow-hidden shadow-lg border border-blue-100 bg-white">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-gradient-to-r from-blue-100 to-green-100">
                                <TableHead class="py-4 text-indigo-700 font-bold">Tipe</TableHead>
                                <TableHead class="py-4 text-indigo-700 font-bold">Nama Barang</TableHead>
                                <TableHead class="py-4 text-indigo-700 font-bold">Kode Barang</TableHead>
                                <TableHead class="py-4 text-indigo-700 font-bold">Jumlah</TableHead>
                                <TableHead class="py-4 text-indigo-700 font-bold">Tanggal</TableHead>
                                <TableHead class="py-4 text-indigo-700 font-bold">Status</TableHead>
                                <TableHead class="py-4 text-indigo-700 font-bold">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in filteredRiwayat" :key="item.id" class="hover:bg-blue-50 transition">
                                <TableCell>
                                    <span :class="`inline-flex items-center gap-2 px-3 py-1 rounded-full border text-xs font-semibold ${getTypeColor(item.type)}`">
                                        <Package class="w-4 h-4" v-if="item.type === 'permintaan'" />
                                        <Calendar class="w-4 h-4" v-else />
                                        {{ getTypeText(item.type) }}
                                    </span>
                                </TableCell>
                                <TableCell>
                                    <span class="font-semibold text-gray-800">{{ item.nama_barang }}</span>
                                </TableCell>
                                <TableCell>
                                    <span class="font-mono text-blue-700 bg-blue-50 px-2 py-1 rounded">{{ item.kode_barang }}</span>
                                </TableCell>
                                <TableCell>
                                    <span class="font-bold text-indigo-700">{{ item.jumlah }}</span>
                                </TableCell>
                                <TableCell>
                                    <span class="text-gray-600">{{ formatDate(item.created_at) }}</span>
                                </TableCell>
                                <TableCell>
                                    <span class="inline-flex items-center gap-2">
                                        <component :is="getStatusIcon(item.status)" class="w-4 h-4"
                                            :class="{
                                                'text-gray-400': item.status === 'pending',
                                                'text-green-500': item.status === 'approved' || item.status === 'completed',
                                                'text-red-500': item.status === 'rejected'
                                            }"
                                        />
                                        <Badge :variant="getStatusVariant(item.status)">
                                            {{ getStatusText(item.status) }}
                                        </Badge>
                                    </span>
                                </TableCell>
                                <TableCell>
                                    <div class="flex gap-2">
                                        <Button 
                                            v-if="item.status === 'pending'"
                                            @click="cancelItem(item)" 
                                            variant="outline" 
                                            size="sm"
                                            class="flex items-center gap-1 border-red-200 hover:bg-red-50 text-red-600"
                                        >
                                            <X class="w-3 h-3" />
                                            Batalkan
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>

            <!-- CARD (untuk hp/sm) -->
            <div class="space-y-6 md:hidden">
                <div
                    v-for="item in filteredRiwayat"
                    :key="item.id"
                    class="rounded-xl border border-blue-100 bg-white shadow-lg p-5 relative overflow-hidden"
                >
                    <!-- Decorative gradient bar -->
                    <div
                        :class="[
                            'absolute left-0 top-0 h-2 w-full',
                            item.type === 'permintaan'
                                ? 'bg-gradient-to-r from-blue-400 to-blue-200'
                                : 'bg-gradient-to-r from-green-400 to-green-200'
                        ]"
                    ></div>
                    <div class="flex justify-between items-center mb-3">
                        <span :class="`inline-flex items-center gap-2 px-3 py-1 rounded-full border text-xs font-semibold ${getTypeColor(item.type)}`">
                            <Package class="w-4 h-4" v-if="item.type === 'permintaan'" />
                            <Calendar class="w-4 h-4" v-else />
                            {{ getTypeText(item.type) }}
                        </span>
                        <span class="inline-flex items-center gap-2">
                            <component :is="getStatusIcon(item.status)" class="w-4 h-4"
                                :class="{
                                    'text-gray-400': item.status === 'pending',
                                    'text-green-500': item.status === 'approved' || item.status === 'completed',
                                    'text-red-500': item.status === 'rejected'
                                }"
                            />
                            <Badge :variant="getStatusVariant(item.status)">
                                {{ getStatusText(item.status) }}
                            </Badge>
                        </span>
                    </div>

                    <div class="space-y-2">
                        <h3 class="font-semibold text-lg text-indigo-800">{{ item.nama_barang }}</h3>
                        <div class="flex flex-wrap gap-2 text-sm">
                            <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-2 py-1 rounded font-mono">
                                <Package class="w-3 h-3" /> {{ item.kode_barang }}
                            </span>
                            <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 px-2 py-1 rounded font-semibold">
                                <ClipboardList class="w-3 h-3" /> {{ item.jumlah }} 
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <Calendar class="w-4 h-4" />
                            <span>{{ formatDate(item.created_at) }}</span>
                        </div>
                        
                        <template v-if="item.type === 'peminjaman'">
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <span class="font-semibold">Pinjam:</span>
                                <span>{{ formatDate(item.tanggal_peminjaman!) }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <span class="font-semibold">Kembali:</span>
                                <span>{{ formatDate(item.tanggal_pengembalian!) }}</span>
                            </div>
                        </template>

                        <div v-if="item.keterangan" class="text-sm text-gray-600 bg-gray-50 rounded px-2 py-1">
                            <strong>Keterangan:</strong> {{ item.keterangan }}
                        </div>

                        <div v-if="item.alasan_approval" class="text-sm text-red-600 bg-red-50 rounded px-2 py-1">
                            <strong>Alasan:</strong> {{ item.alasan_approval }}
                        </div>

                        <div v-if="item.catatan_approval" class="text-sm text-blue-600 bg-blue-50 rounded px-2 py-1">
                            <strong>Catatan:</strong> {{ item.catatan_approval }}
                        </div>
                    </div>

                    <div v-if="item.status === 'pending'" class="flex gap-4 mt-4 pt-4 border-t border-blue-100">
                        <Button 
                            @click="cancelItem(item)" 
                            variant="outline" 
                            size="sm"
                            class="flex items-center gap-1 border-red-200 hover:bg-red-50 text-red-600"
                        >
                            <X class="w-3 h-3" />
                            Batalkan
                        </Button>
                        <span class="text-sm text-gray-500 flex items-center gap-1">
                            <Clock class="w-4 h-4" /> Status: {{ getStatusText(item.status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div v-if="filteredRiwayat.length === 0" class="text-center py-16">
                <div class="flex flex-col items-center justify-center">
                    <ClipboardList class="h-16 w-16 text-blue-200 mb-4" />
                    <h3 class="text-xl font-bold text-gray-400 mb-2">Tidak ada riwayat ditemukan</h3>
                    <p class="text-gray-500">Coba ubah filter atau lakukan permintaan/peminjaman barang.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>