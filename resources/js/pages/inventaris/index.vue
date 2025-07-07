<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Plus, Search, User, Edit, X, RotateCcw, Play, Check, XCircle, Package, CalendarDays, MapPin } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed, inject } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useInitials } from '@/composables/useInitials';
import { Input } from '@/components/ui/input';
import { Select } from '@/components/ui/select';
import { ToastContainer } from '@/components/ui/toast';

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

interface InventarisItem {
    id: number;
    nomor_inventaris: string;
    nama_barang: string;
    kode_barang: string;
    jumlah: number;
    status: 'pending' | 'approved' | 'in_progress' | 'rejected' | 'returned' | 'overdue' | 'maintenance';
    status_text: string;
    tanggal_peminjaman: string;
    tanggal_pengembalian: string | null;
    due_date: string;
    keterangan?: string;
    lokasi_peminjaman?: string;
    alasan_approval?: string;
    catatan_approval?: string;
    kondisi_barang?: string;
    kondisi_barang_text?: string;
    catatan_pengembalian?: string;
    returned_at?: string;
    is_overdue: boolean;
}

interface InventarisGroup {
    user: string;
    user_photo?: string;
    items: InventarisItem[];
}

const props = defineProps<{
    inventaris: InventarisGroup[];
    isAdmin: boolean;
}>();

const { getInitials } = useInitials();

// Toast notification system
const addToast = inject('addToast') as ((toast: any) => void);

const searchQuery = ref('');
const selectedStatus = ref('');
const showReturnModal = ref(false);
const selectedInventaris = ref<InventarisItem | null>(null);
const returnForm = ref({
    tanggal_pengembalian: new Date().toISOString().split('T')[0],
    kondisi_barang: 'baik',
    catatan_pengembalian: ''
});

const filteredInventaris = computed(() => {
    if (!props.inventaris) return [];
    
    return props.inventaris
        .map(group => {
            const filteredItems = group.items.filter(item => {
                const matchesSearch =
                    (item.nama_barang?.toLowerCase() || '').includes(searchQuery.value.toLowerCase()) ||
                    (item.kode_barang?.toLowerCase() || '').includes(searchQuery.value.toLowerCase()) ||
                    (item.nomor_inventaris?.toLowerCase() || '').includes(searchQuery.value.toLowerCase()) ||
                    (group.user?.toLowerCase() || '').includes(searchQuery.value.toLowerCase());

                const matchesStatus =
                    !selectedStatus.value || item.status === selectedStatus.value;

                return matchesSearch && matchesStatus;
            });

            return filteredItems.length > 0 ? { user: group.user, user_photo: group.user_photo, items: filteredItems } : null;
        })
        .filter(Boolean) as InventarisGroup[];
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
        case 'overdue': return 'destructive';
        case 'maintenance': return 'secondary';
        default: return 'secondary';
    }
};

const deleteInventaris = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus inventaris ini?')) {
        router.delete(route('inventaris.destroy', id), {
            onSuccess: () => {
                addToast({
                    type: 'success',
                    title: 'Berhasil',
                    message: 'Inventaris berhasil dihapus'
                });
            },
            onError: () => {
                addToast({
                    type: 'error',
                    title: 'Gagal',
                    message: 'Gagal menghapus inventaris'
                });
            }
        });
    }
};

const processReturn = (inventaris: InventarisItem) => {
    selectedInventaris.value = inventaris;
    returnForm.value = {
        tanggal_pengembalian: new Date().toISOString().split('T')[0],
        kondisi_barang: 'baik',
        catatan_pengembalian: ''
    };
    showReturnModal.value = true;
};

const startInventaris = (inventaris: InventarisItem) => {
    if (confirm('Apakah Anda yakin ingin memulai proses inventaris ini?')) {
        router.patch(route('inventaris.start-progress', inventaris.id), {}, {
            onSuccess: () => {
                addToast({
                    type: 'success',
                    title: 'Berhasil',
                    message: 'Proses inventaris telah dimulai'
                });
            },
            onError: () => {
                addToast({
                    type: 'error',
                    title: 'Gagal',
                    message: 'Gagal memulai proses inventaris'
                });
            }
        });
    }
};

const submitReturn = () => {
    if (!selectedInventaris.value) return;
    
    router.post(route('inventaris.process-return', selectedInventaris.value.id), returnForm.value, {
        onSuccess: () => {
            showReturnModal.value = false;
            selectedInventaris.value = null;
            addToast({
                type: 'success',
                title: 'Berhasil',
                message: 'Pengembalian inventaris berhasil diproses'
            });
        },
        onError: () => {
            addToast({
                type: 'error',
                title: 'Gagal',
                message: 'Gagal memproses pengembalian inventaris'
            });
        }
    });
};

const approveInventaris = (inventaris: InventarisItem, action: 'approve' | 'reject') => {
    const alasan = prompt(`Masukkan alasan ${action === 'approve' ? 'persetujuan' : 'penolakan'}:`);
    if (!alasan) return;

    const catatan = prompt('Masukkan catatan (opsional):');

    router.post(route('inventaris.approve'), {
        inventaris_id: inventaris.id,
        action: action,
        alasan: alasan,
        catatan: catatan || ''
    }, {
        onSuccess: () => {
            addToast({
                type: 'success',
                title: 'Berhasil',
                message: `Inventaris berhasil ${action === 'approve' ? 'disetujui' : 'ditolak'}`
            });
        },
        onError: () => {
            addToast({
                type: 'error',
                title: 'Gagal',
                message: `Gagal ${action === 'approve' ? 'menyetujui' : 'menolak'} inventaris`
            });
        }
    });
};
</script>

<template>
    <Head title="Inventaris" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                            <Package class="w-6 h-6 text-purple-600" />
                            Inventaris
                        </h1>
                        <p class="text-gray-600">Kelola peminjaman inventaris barang</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <Link :href="route('inventaris.create')">
                        <Button class="w-full sm:w-auto">
                            <Plus class="w-4 h-4 mr-2" />
                            Tambah Inventaris
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-600 text-sm font-medium">Total Inventaris</p>
                            <p class="text-2xl font-bold text-purple-800">{{ inventaris.reduce((total, group) => total + group.items.length, 0) }}</p>
                        </div>
                        <Package class="w-8 h-8 text-purple-600" />
                    </div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-600 text-sm font-medium">Menunggu</p>
                            <p class="text-2xl font-bold text-blue-800">{{ inventaris.reduce((total, group) => total + group.items.filter(item => item.status === 'pending').length, 0) }}</p>
                        </div>
                        <div class="w-8 h-8 bg-blue-200 rounded-full flex items-center justify-center">
                            <span class="text-blue-800 font-bold text-sm">⏳</span>
                        </div>
                    </div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-600 text-sm font-medium">Disetujui</p>
                            <p class="text-2xl font-bold text-green-800">{{ inventaris.reduce((total, group) => total + group.items.filter(item => item.status === 'approved').length, 0) }}</p>
                        </div>
                        <div class="w-8 h-8 bg-green-200 rounded-full flex items-center justify-center">
                            <span class="text-green-800 font-bold text-sm">✓</span>
                        </div>
                    </div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-600 text-sm font-medium">Terlambat</p>
                            <p class="text-2xl font-bold text-red-800">{{ inventaris.reduce((total, group) => total + group.items.filter(item => item.is_overdue).length, 0) }}</p>
                        </div>
                        <div class="w-8 h-8 bg-red-200 rounded-full flex items-center justify-center">
                            <span class="text-red-800 font-bold text-sm">!</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <Input
                        v-model="searchQuery"
                        placeholder="Cari nomor inventaris, nama barang, atau peminjam..."
                        class="pl-10"
                    />
                </div>
                <Select v-model="selectedStatus" class="w-full sm:w-48">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="approved">Disetujui</option>
                    <option value="in_progress">Proses</option>
                    <option value="rejected">Ditolak</option>
                    <option value="returned">Dikembalikan</option>
                    <option value="overdue">Terlambat</option>
                    <option value="maintenance">Maintenance</option>
                </Select>
            </div>

            <!-- Inventaris Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div v-if="filteredInventaris.length > 0" class="overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Peminjam</TableHead>
                                <TableHead>Nomor Inventaris</TableHead>
                                <TableHead>Barang</TableHead>
                                <TableHead>Jumlah</TableHead>
                                <TableHead>Lokasi</TableHead>
                                <TableHead>Tanggal Pinjam</TableHead>
                                <TableHead>Due Date</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="group in filteredInventaris" :key="group.user">
                                <TableCell class="font-medium">
                                    <div class="flex items-center gap-3">
                                        <Avatar class="w-8 h-8">
                                            <AvatarImage :src="group.user_photo" />
                                            <AvatarFallback>{{ getInitials(group.user) }}</AvatarFallback>
                                        </Avatar>
                                        <div>
                                            <p class="font-medium">{{ group.user }}</p>
                                            <p class="text-sm text-gray-500">{{ group.items.length }} item</p>
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="space-y-1">
                                        <div v-for="item in group.items" :key="item.id" class="text-sm">
                                            <span class="font-mono text-purple-600">{{ item.nomor_inventaris }}</span>
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="space-y-1">
                                        <div v-for="item in group.items" :key="item.id">
                                            <p class="font-medium">{{ item.nama_barang }}</p>
                                            <p class="text-sm text-gray-500">{{ item.kode_barang }}</p>
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="space-y-1">
                                        <div v-for="item in group.items" :key="item.id" class="text-sm">
                                            {{ item.jumlah }}
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="space-y-1">
                                        <div v-for="item in group.items" :key="item.id" class="text-sm">
                                            <div v-if="item.lokasi_peminjaman" class="flex items-center gap-1">
                                                <MapPin class="w-3 h-3 text-gray-400" />
                                                {{ item.lokasi_peminjaman }}
                                            </div>
                                            <span v-else class="text-gray-400">-</span>
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="space-y-1">
                                        <div v-for="item in group.items" :key="item.id" class="text-sm">
                                            {{ formatDate(item.tanggal_peminjaman) }}
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="space-y-1">
                                        <div v-for="item in group.items" :key="item.id" class="text-sm">
                                            <span :class="[
                                                'font-medium',
                                                item.is_overdue ? 'text-red-600' : 'text-gray-700'
                                            ]">
                                                {{ formatDate(item.due_date) }}
                                            </span>
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="space-y-1">
                                        <div v-for="item in group.items" :key="item.id">
                                            <Badge :variant="getStatusVariant(item.status)">
                                                {{ item.status_text }}
                                            </Badge>
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="space-y-2">
                                        <div v-for="item in group.items" :key="item.id" class="flex justify-end gap-1">
                                            <!-- View Details -->
                                            <Link :href="route('inventaris.show', item.id)">
                                                <Button variant="outline" size="sm">
                                                    <User class="w-3 h-3" />
                                                </Button>
                                            </Link>

                                            <!-- Admin Actions -->
                                            <div v-if="isAdmin" class="flex gap-1">
                                                <!-- Approve/Reject for pending -->
                                                <div v-if="item.status === 'pending'" class="flex gap-1">
                                                    <Button @click="approveInventaris(item, 'approve')" size="sm" variant="outline">
                                                        <Check class="w-3 h-3 text-green-600" />
                                                    </Button>
                                                    <Button @click="approveInventaris(item, 'reject')" size="sm" variant="outline">
                                                        <XCircle class="w-3 h-3 text-red-600" />
                                                    </Button>
                                                </div>

                                                <!-- Start Progress for approved -->
                                                <Button v-if="item.status === 'approved'" @click="startInventaris(item)" size="sm" variant="outline">
                                                    <Play class="w-3 h-3 text-blue-600" />
                                                </Button>

                                                <!-- Return for in_progress -->
                                                <Button v-if="item.status === 'in_progress'" @click="processReturn(item)" size="sm" variant="outline">
                                                    <RotateCcw class="w-3 h-3 text-orange-600" />
                                                </Button>
                                            </div>

                                            <!-- User Actions -->
                                            <div v-else class="flex gap-1">
                                                <!-- Edit for pending -->
                                                <Link v-if="item.status === 'pending'" :href="route('inventaris.edit', item.id)">
                                                    <Button variant="outline" size="sm">
                                                        <Edit class="w-3 h-3" />
                                                    </Button>
                                                </Link>

                                                <!-- Delete for pending -->
                                                <Button v-if="item.status === 'pending'" @click="deleteInventaris(item.id)" size="sm" variant="outline">
                                                    <X class="w-3 h-3 text-red-600" />
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-16 text-gray-500">
                    <Package class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                    <p class="text-xl font-medium mb-2">Tidak ada inventaris ditemukan</p>
                    <p class="text-gray-600 mb-4">
                        {{ searchQuery || selectedStatus ? 'Coba ubah filter pencarian Anda.' : 'Belum ada inventaris yang ditambahkan.' }}
                    </p>
                    <Link :href="route('inventaris.create')">
                        <Button>
                            <Plus class="w-4 h-4 mr-2" />
                            Tambah Inventaris Pertama
                        </Button>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Return Modal -->
        <div v-if="showReturnModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-semibold mb-4">Proses Pengembalian</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal Pengembalian</label>
                        <Input v-model="returnForm.tanggal_pengembalian" type="date" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Kondisi Barang</label>
                        <Select v-model="returnForm.kondisi_barang">
                            <option value="baik">Baik</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                            <option value="hilang">Hilang</option>
                        </Select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Catatan</label>
                        <textarea v-model="returnForm.catatan_pengembalian" class="w-full border rounded-md p-2" rows="3"></textarea>
                    </div>
                </div>
                <div class="flex gap-2 mt-6">
                    <Button @click="submitReturn" class="flex-1">Proses</Button>
                    <Button @click="showReturnModal = false" variant="outline" class="flex-1">Batal</Button>
                </div>
            </div>
        </div>

        <ToastContainer />
    </AppLayout>
</template> 