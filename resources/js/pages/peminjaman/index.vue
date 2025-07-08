<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Plus, Search, User, Edit, X, RotateCcw } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useInitials } from '@/composables/useInitials';

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

interface PeminjamanItem {
    id: number;
    nama_barang: string;
    kode_barang: string;
    jumlah: number;
    status: 'pending' | 'approved' | 'rejected' | 'returned' | 'completed';
    tanggal_peminjaman: string;
    tanggal_pengembalian: string | null;
    due_date: string;
    keterangan?: string;
    kondisi_barang?: string;
    catatan_pengembalian?: string;
    returned_at?: string;
}

interface PeminjamanGroup {
    user: string;
    user_photo?: string;
    items: PeminjamanItem[];
}

const props = defineProps<{
    peminjaman: PeminjamanGroup[];
    isAdmin: boolean;
}>();

const { getInitials } = useInitials();

const searchQuery = ref('');
const selectedStatus = ref('');
const showReturnModal = ref(false);
const selectedPeminjaman = ref<PeminjamanItem | null>(null);
const returnForm = ref({
    tanggal_pengembalian: new Date().toISOString().split('T')[0],
    kondisi_barang: 'baik',
    catatan_pengembalian: ''
});

const filteredPeminjaman = computed(() => {
    if (!props.peminjaman) return [];
    
    return props.peminjaman
        .map(group => {
            const filteredItems = group.items.filter(item => {
                const matchesSearch =
                    (item.nama_barang?.toLowerCase() || '').includes(searchQuery.value.toLowerCase()) ||
                    (item.kode_barang?.toLowerCase() || '').includes(searchQuery.value.toLowerCase()) ||
                    (group.user?.toLowerCase() || '').includes(searchQuery.value.toLowerCase());

                const matchesStatus =
                    !selectedStatus.value || item.status === selectedStatus.value;

                return matchesSearch && matchesStatus;
            });

            return filteredItems.length > 0 ? { user: group.user, user_photo: group.user_photo, items: filteredItems } : null;
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
        case 'pending': return 'secondary';
        case 'approved': return 'default';
        case 'rejected': return 'destructive';
        case 'returned': return 'outline';
        case 'completed': return 'outline';
        default: return 'secondary';
    }
};

const getStatusText = (status: string) => {
    switch (status) {
        case 'pending': return 'Menunggu';
        case 'approved': return 'Disetujui';
        case 'rejected': return 'Ditolak';
        case 'returned': return 'Dikembalikan';
        case 'completed': return 'Selesai';
        default: return status;
    }
};

const deletePeminjaman = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus peminjaman ini?')) {
        router.delete(route('peminjaman.destroy', id));
    }
};

const processReturn = (peminjaman: PeminjamanItem) => {
    selectedPeminjaman.value = peminjaman;
    returnForm.value = {
        tanggal_pengembalian: new Date().toISOString().split('T')[0],
        kondisi_barang: 'baik',
        catatan_pengembalian: ''
    };
    showReturnModal.value = true;
};

const submitReturn = () => {
    if (!selectedPeminjaman.value) return;
    
    router.post(route('peminjaman.process-return', selectedPeminjaman.value.id), returnForm.value, {
        onSuccess: () => {
            showReturnModal.value = false;
            selectedPeminjaman.value = null;
        }
    });
};

const closeReturnModal = () => {
    showReturnModal.value = false;
    selectedPeminjaman.value = null;
};

const getPhotoUrl = (photoPath: string) => {
    return `/storage/${photoPath}`;
};
</script>

<template>
    <Head title="Peminjaman" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Daftar Peminjaman</h1>
                    <p v-if="!isAdmin" class="text-sm text-gray-500 mt-1">Menampilkan peminjaman Anda</p>
                    <p v-else class="text-sm text-gray-500 mt-1">Menampilkan semua peminjaman</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('peminjaman.returns')">
                        <Button variant="outline" class="w-full sm:w-auto">
                            <RotateCcw class="w-4 h-4 mr-2" />
                            Daftar Pengembalian
                        </Button>
                    </Link>
                    <Link :href="route('peminjaman.create')">
                        <Button class="w-full sm:w-auto">
                            <Plus class="w-4 h-4 mr-2" />
                            Buat Peminjaman
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Filter & Search -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        :placeholder="isAdmin ? 'Cari nama barang, kode barang, atau peminjam...' : 'Cari nama barang atau kode barang...'"
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
                    <option value="returned">Dikembalikan</option>
                    <option value="completed">Selesai</option>
                </select>
            </div>

            <!-- Table (untuk desktop) -->
            <div class="hidden md:block">
                <div v-for="group in filteredPeminjaman" :key="group.user" class="mb-8">
                    <!-- User Header -->
                    <div class="flex items-center gap-3 mb-4 bg-gray-50 p-4 rounded-lg">
                        <Avatar class="w-12 h-12">
                            <AvatarImage v-if="group.user_photo" :src="getPhotoUrl(group.user_photo)" alt="User Photo" />
                            <AvatarFallback>{{ getInitials(group.user) }}</AvatarFallback>
                        </Avatar>
                        <div>
                            <h2 class="font-semibold text-lg">{{ group.user }}</h2>
                            <p class="text-sm text-gray-500">Peminjam</p>
                        </div>
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
                                <TableHead>Kondisi</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in group.items" :key="item.id">
                                <TableCell>{{ item.nama_barang }}</TableCell>
                                <TableCell>{{ item.kode_barang }}</TableCell>
                                <TableCell>{{ item.jumlah }}</TableCell>
                                <TableCell>{{ formatDate(item.tanggal_peminjaman) }}</TableCell>
                                <TableCell>{{ item.tanggal_pengembalian ? formatDate(item.tanggal_pengembalian) : '-' }}</TableCell>
                                <TableCell>{{ item.kondisi_barang ? (item.kondisi_barang === 'baik' ? 'Baik' : item.kondisi_barang === 'rusak_ringan' ? 'Rusak Ringan' : 'Rusak Berat') : '-' }}</TableCell>
                                <TableCell>
                                    <Badge :variant="getStatusVariant(item.status)">
                                        {{ getStatusText(item.status) }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link
                                            v-if="item.status === 'pending'"
                                            :href="route('peminjaman.edit', item.id)"
                                            class="text-blue-600 hover:text-blue-800"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </Link>
                                        <button
                                            v-if="item.status === 'approved' && isAdmin"
                                            @click="processReturn(item)"
                                            class="text-green-600 hover:text-green-800"
                                            title="Kembalikan"
                                        >
                                            <RotateCcw class="w-4 h-4" />
                                        </button>
                                        <button
                                            v-if="item.status === 'pending'"
                                            @click="deletePeminjaman(item.id)"
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            <X class="w-4 h-4" />
                                        </button>
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
                    v-for="group in filteredPeminjaman"
                    :key="group.user"
                    class="border rounded-lg overflow-hidden bg-white shadow-sm"
                >
                    <!-- User Header -->
                    <div class="bg-gray-50 p-4 border-b">
                        <div class="flex items-center gap-2">
                            <Avatar class="w-10 h-10">
                                <AvatarImage v-if="group.user_photo" :src="getPhotoUrl(group.user_photo)" alt="User Photo" />
                                <AvatarFallback>{{ getInitials(group.user) }}</AvatarFallback>
                            </Avatar>
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
                                    <p class="text-sm text-gray-500">Tanggal Kembali: {{ item.tanggal_pengembalian ? formatDate(item.tanggal_pengembalian) : '-' }}</p>
                                    <p v-if="item.kondisi_barang" class="text-sm text-gray-500">Kondisi: {{ item.kondisi_barang === 'baik' ? 'Baik' : item.kondisi_barang === 'rusak_ringan' ? 'Rusak Ringan' : 'Rusak Berat' }}</p>
                                    <p v-if="item.catatan_pengembalian" class="text-sm text-gray-500">Catatan: {{ item.catatan_pengembalian }}</p>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <Badge :variant="getStatusVariant(item.status)">
                                        {{ getStatusText(item.status) }}
                                    </Badge>
                                    <div class="flex gap-2">
                                        <Link
                                            v-if="item.status === 'pending'"
                                            :href="route('peminjaman.edit', item.id)"
                                            class="text-blue-600 hover:text-blue-800"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </Link>
                                        <button
                                            v-if="item.status === 'approved' && isAdmin"
                                            @click="processReturn(item)"
                                            class="text-green-600 hover:text-green-800"
                                            title="Kembalikan"
                                        >
                                            <RotateCcw class="w-4 h-4" />
                                        </button>
                                        <button
                                            v-if="item.status === 'pending'"
                                            @click="deletePeminjaman(item.id)"
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            <X class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="filteredPeminjaman.length === 0" class="text-center py-8">
                <div class="text-gray-500">
                    <Search class="h-12 w-12 mx-auto mb-4" />
                    <p>Tidak ada peminjaman ditemukan</p>
                </div>
            </div>

            <!-- Modal Pengembalian -->
            <div v-if="showReturnModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Form Pengembalian</h3>
                        <button @click="closeReturnModal" class="text-gray-500 hover:text-gray-700">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <div v-if="selectedPeminjaman" class="space-y-4">
                        <!-- Informasi Peminjaman -->
                        <div class="bg-gray-50 p-3 rounded">
                            <h4 class="font-medium text-gray-800">Informasi Peminjaman</h4>
                            <p class="text-sm text-gray-600">Barang: {{ selectedPeminjaman.nama_barang }}</p>
                            <p class="text-sm text-gray-600">Jumlah: {{ selectedPeminjaman.jumlah }}</p>
                            <p class="text-sm text-gray-600">Tanggal Pinjam: {{ formatDate(selectedPeminjaman.tanggal_peminjaman) }}</p>
                        </div>

                        <!-- Form Pengembalian -->
                        <form @submit.prevent="submitReturn" class="space-y-4">
                            <!-- Tanggal Pengembalian -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal Pengembalian *
                                </label>
                                <input
                                    v-model="returnForm.tanggal_pengembalian"
                                    type="date"
                                    :min="selectedPeminjaman.tanggal_peminjaman"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                />
                            </div>

                            <!-- Kondisi Barang -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kondisi Barang *
                                </label>
                                <select
                                    v-model="returnForm.kondisi_barang"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                                    <option value="baik">Baik (100% stok dikembalikan)</option>
                                    <option value="rusak_ringan">Rusak Ringan (50% stok dikembalikan)</option>
                                    <option value="rusak_berat">Rusak Berat (0% stok dikembalikan)</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">
                                    Pilih kondisi barang untuk menentukan jumlah stok yang dikembalikan
                                </p>
                            </div>

                            <!-- Catatan Pengembalian -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Catatan Pengembalian
                                </label>
                                <textarea
                                    v-model="returnForm.catatan_pengembalian"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Masukkan catatan pengembalian (opsional)"
                                ></textarea>
                            </div>

                            <!-- Tombol Submit -->
                            <div class="flex gap-3 pt-4">
                                <button
                                    type="submit"
                                    class="flex-1 bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                                >
                                    <RotateCcw class="w-4 h-4 inline mr-2" />
                                    Proses Pengembalian
                                </button>
                                <button
                                    type="button"
                                    @click="closeReturnModal"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500"
                                >
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Daftar Peminjaman</h3>
                    <p class="text-sm text-gray-600">Kelola semua peminjaman barang</p>
                </div>
                <div class="flex gap-3">
                    <Link
                        :href="route('peminjaman.returns')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <RotateCcw class="w-4 h-4 mr-2" />
                        Lihat Pengembalian
                    </Link>
                    <Link
                        :href="route('peminjaman.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <Plus class="w-4 h-4 mr-2" />
                        Tambah Peminjaman
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>