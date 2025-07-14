<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Plus, Search, User, Edit, X, RotateCcw, Play, Check, XCircle, AlertTriangle } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed, inject } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useInitials } from '@/composables/useInitials';
import { usePhotoUrl } from '@/composables/usePhotoUrl';
import { Input } from '@/components/ui/input';
import { Select } from '@/components/ui/select';
import { ToastContainer } from '@/components/ui/toast';
import { provide } from 'vue';
import { useToast } from '@/composables/useToast'; // Adjust path if needed

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
    status: 'pending' | 'approved' | 'in_progress' | 'rejected' | 'returned' | 'completed';
    tanggal_peminjaman: string;
    tanggal_pengembalian: string | null;
    due_date: string;
    keterangan?: string;
    alasan_approval?: string;
    catatan_approval?: string;
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

// Toast notification system
const { addToast } = useToast();
provide('addToast', addToast);

const searchQuery = ref('');
const selectedStatus = ref('');
const showReturnModal = ref(false);
const selectedPeminjaman = ref<PeminjamanItem | null>(null);
const returnForm = ref({
    tanggal_pengembalian: new Date().toISOString().split('T')[0],
    kondisi_barang: 'baik',
    catatan_pengembalian: ''
});

// Modal for approval/rejection
const showApprovalModal = ref(false);
const approvalType = ref<'approve' | 'reject' | null>(null);
const approvalPeminjaman = ref<PeminjamanItem | null>(null);
const approvalForm = ref({
    alasan: '',
    catatan: ''
});

// Modal for delete confirmation
const showDeleteModal = ref(false);
const deletePeminjamanId = ref<number | null>(null);
const deletePeminjamanItem = ref<PeminjamanItem | null>(null);

const openDeleteModal = (item: PeminjamanItem) => {
    showDeleteModal.value = true;
    deletePeminjamanId.value = item.id;
    deletePeminjamanItem.value = item;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    deletePeminjamanId.value = null;
    deletePeminjamanItem.value = null;
};

const confirmDeletePeminjaman = () => {
    if (!deletePeminjamanId.value) return;
    router.delete(route('peminjaman.destroy', deletePeminjamanId.value), {
        onSuccess: () => {
            closeDeleteModal();
            addToast({
                type: 'success',
                title: 'Berhasil',
                message: 'Peminjaman berhasil dihapus'
            });
        },
        onError: () => {
            addToast({
                type: 'error',
                title: 'Gagal',
                message: 'Gagal menghapus peminjaman'
            });
        }
    });
};

const openApprovalModal = (type: 'approve' | 'reject', peminjaman: PeminjamanItem) => {
    showApprovalModal.value = true;
    approvalType.value = type;
    approvalPeminjaman.value = peminjaman;
    approvalForm.value = {
        alasan: type === 'approve' ? 'Peminjaman disetujui' : 'Peminjaman ditolak',
        catatan: ''
    };
};

const closeApprovalModal = () => {
    showApprovalModal.value = false;
    approvalType.value = null;
    approvalPeminjaman.value = null;
    approvalForm.value = {
        alasan: '',
        catatan: ''
    };
};

const submitApproval = () => {
    if (!approvalPeminjaman.value || !approvalType.value) return;
    router.post('/peminjaman/approve', {
        peminjaman_id: approvalPeminjaman.value.id,
        action: approvalType.value,
        alasan: approvalForm.value.alasan,
        catatan: approvalForm.value.catatan
    }, {
        onSuccess: () => {
            closeApprovalModal();
            addToast({
                type: approvalType.value === 'approve' ? 'success' : 'success',
                title: approvalType.value === 'approve' ? 'Disetujui' : 'Ditolak',
                message: `Peminjaman ${approvalPeminjaman.value?.nama_barang} berhasil ${approvalType.value === 'approve' ? 'disetujui' : 'ditolak'}`
            });
        },
        onError: () => {
            addToast({
                type: 'error',
                title: 'Gagal',
                message: `Gagal ${approvalType.value === 'approve' ? 'menyetujui' : 'menolak'} peminjaman`
            });
        }
    });
};

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
        case 'in_progress': return 'default';
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
        case 'in_progress': return 'Proses Peminjaman';
        case 'rejected': return 'Ditolak';
        case 'returned': return 'Dikembalikan';
        case 'completed': return 'Selesai';
        default: return status;
    }
};

// Remove old confirm dialog, use modal instead
// const deletePeminjaman = (id: number) => { ... }

const processReturn = (peminjaman: PeminjamanItem) => {
    selectedPeminjaman.value = peminjaman;
    returnForm.value = {
        tanggal_pengembalian: new Date().toISOString().split('T')[0],
        kondisi_barang: 'baik',
        catatan_pengembalian: ''
    };
    showReturnModal.value = true;
};

const startPeminjaman = (peminjaman: PeminjamanItem) => {
    if (confirm('Apakah Anda yakin ingin memulai proses peminjaman ini?')) {
        router.patch(route('peminjaman.start-progress', peminjaman.id), {}, {
            onSuccess: () => {
                addToast({
                    type: 'success',
                    title: 'Berhasil',
                    message: 'Proses peminjaman telah dimulai'
                });
            },
            onError: () => {
                addToast({
                    type: 'error',
                    title: 'Gagal',
                    message: 'Gagal memulai proses peminjaman'
                });
            }
        });
    }
};

const submitReturn = () => {
    if (!selectedPeminjaman.value) return;
    
    router.post(route('peminjaman.process-return', selectedPeminjaman.value.id), returnForm.value, {
        onSuccess: () => {
            showReturnModal.value = false;
            selectedPeminjaman.value = null;
            addToast({
                type: 'success',
                title: 'Berhasil',
                message: 'Pengembalian berhasil diproses'
            });
        },
        onError: () => {
            addToast({
                type: 'error',
                title: 'Gagal',
                message: 'Gagal memproses pengembalian'
            });
        }
    });
};

const closeReturnModal = () => {
    showReturnModal.value = false;
    selectedPeminjaman.value = null;
};

const { getPhotoUrl } = usePhotoUrl();
</script>

<template>
    <Head title="Aset" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Daftar Aset</h1>
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
                            Buat Aset
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
                    <option value="in_progress">Proses Peminjaman</option>
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
                            <AvatarImage v-if="group.user_photo" :src="getPhotoUrl(group.user_photo) ?? ''" alt="User Photo" />
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
                                        
                                        <!-- Admin Approval Actions -->
                                        <button
                                            v-if="isAdmin && item.status === 'pending'"
                                            @click="openApprovalModal('approve', item)"
                                            class="text-green-600 hover:text-green-800"
                                            title="Setujui"
                                        >
                                            <Check class="w-4 h-4" />
                                        </button>
                                        <button
                                            v-if="isAdmin && item.status === 'pending'"
                                            @click="openApprovalModal('reject', item)"
                                            class="text-red-600 hover:text-red-800"
                                            title="Tolak"
                                        >
                                            <XCircle class="w-4 h-4" />
                                        </button>
                                        
                                        <button
                                            v-if="item.status === 'approved' && isAdmin"
                                            @click="startPeminjaman(item)"
                                            class="text-blue-600 hover:text-blue-800"
                                            title="Mulai Peminjaman"
                                        >
                                            <Play class="w-4 h-4" />
                                        </button>
                                        <button
                                            v-if="item.status === 'in_progress' && isAdmin"
                                            @click="processReturn(item)"
                                            class="text-green-600 hover:text-green-800"
                                            title="Kembalikan"
                                        >
                                            <RotateCcw class="w-4 h-4" />
                                        </button>
                                        <button
                                            v-if="item.status === 'pending'"
                                            @click="openDeleteModal(item)"
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
                                <AvatarImage v-if="group.user_photo" :src="getPhotoUrl(group.user_photo) ?? ''" alt="User Photo" />
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
                                        
                                        <!-- Admin Approval Actions -->
                                        <button
                                            v-if="isAdmin && item.status === 'pending'"
                                            @click="openApprovalModal('approve', item)"
                                            class="text-green-600 hover:text-green-800"
                                            title="Setujui"
                                        >
                                            <Check class="w-4 h-4" />
                                        </button>
                                        <button
                                            v-if="isAdmin && item.status === 'pending'"
                                            @click="openApprovalModal('reject', item)"
                                            class="text-red-600 hover:text-red-800"
                                            title="Tolak"
                                        >
                                            <XCircle class="w-4 h-4" />
                                        </button>
                                        
                                        <button
                                            v-if="item.status === 'approved' && isAdmin"
                                            @click="startPeminjaman(item)"
                                            class="text-blue-600 hover:text-blue-800"
                                            title="Mulai Peminjaman"
                                        >
                                            <Play class="w-4 h-4" />
                                        </button>
                                        <button
                                            v-if="item.status === 'in_progress' && isAdmin"
                                            @click="processReturn(item)"
                                            class="text-green-600 hover:text-green-800"
                                            title="Kembalikan"
                                        >
                                            <RotateCcw class="w-4 h-4" />
                                        </button>
                                        <button
                                            v-if="item.status === 'pending'"
                                            @click="openDeleteModal(item)"
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
            <div v-if="showReturnModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Form Pengembalian</h3>
                        <button @click="closeReturnModal" class="text-gray-500 hover:text-gray-700">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <div v-if="selectedPeminjaman" class="space-y-4">
                        <!-- Informasi Aset -->
                        <div class="space-y-2">
                            <h4 class="font-medium text-gray-800">Informasi Aset</h4>
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

            <!-- Modal Approve/Reject -->
            <div v-if="showApprovalModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            {{ approvalType === 'approve' ? 'Setujui Peminjaman' : 'Tolak Peminjaman' }}
                        </h3>
                        <button @click="closeApprovalModal" class="text-gray-500 hover:text-gray-700">
                            <X class="w-5 h-5" />
                        </button>
                    </div>
                    <div v-if="approvalPeminjaman" class="space-y-4">
                        <div class="space-y-2">
                            <h4 class="font-medium text-gray-800">Informasi Aset</h4>
                            <p class="text-sm text-gray-600">Barang: {{ approvalPeminjaman.nama_barang }}</p>
                            <p class="text-sm text-gray-600">Jumlah: {{ approvalPeminjaman.jumlah }}</p>
                            <p class="text-sm text-gray-600">Tanggal Pinjam: {{ formatDate(approvalPeminjaman.tanggal_peminjaman) }}</p>
                        </div>
                        <form @submit.prevent="submitApproval" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Alasan {{ approvalType === 'approve' ? 'Persetujuan' : 'Penolakan' }} *
                                </label>
                                <input
                                    v-model="approvalForm.alasan"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Catatan (opsional)
                                </label>
                                <textarea
                                    v-model="approvalForm.catatan"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Masukkan catatan tambahan"
                                ></textarea>
                            </div>
                            <div class="flex gap-3 pt-4">
                                <button
                                    type="submit"
                                    :class="approvalType === 'approve' ? 'flex-1 bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500' : 'flex-1 bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500'"
                                >
                                    <Check v-if="approvalType === 'approve'" class="w-4 h-4 inline mr-2" />
                                    <XCircle v-else class="w-4 h-4 inline mr-2" />
                                    {{ approvalType === 'approve' ? 'Setujui' : 'Tolak' }}
                                </button>
                                <button
                                    type="button"
                                    @click="closeApprovalModal"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500"
                                >
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Delete Confirmation -->
            <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
                    <div class="flex items-center gap-2 mb-4">
                        <AlertTriangle class="w-6 h-6 text-red-600" />
                        <h3 class="text-lg font-semibold text-red-700">Konfirmasi Hapus</h3>
                    </div>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start gap-3">
                            <AlertTriangle class="w-5 h-5 text-red-600 mt-0.5" />
                            <div>
                                <p class="font-medium text-red-800">{{ deletePeminjamanItem?.nama_barang }}</p>
                                <p class="text-sm text-red-600 mt-1">
                                    Kode: {{ deletePeminjamanItem?.kode_barang }}<br>
                                    Jumlah: {{ deletePeminjamanItem?.jumlah }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                        <button
                            type="button"
                            class="w-full sm:w-auto border border-gray-300 text-gray-700 rounded-md px-4 py-2 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500"
                            @click="closeDeleteModal"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            class="w-full sm:w-auto bg-red-600 text-white rounded-md px-4 py-2 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 flex items-center justify-center"
                            @click="confirmDeletePeminjaman"
                        >
                            <X class="w-4 h-4 mr-2" />
                            Hapus Peminjaman
                        </button>
                    </div>
                </div>
            </div>
           
        </div>
    </AppLayout>
</template>