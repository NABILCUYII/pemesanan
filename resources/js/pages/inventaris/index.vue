<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Plus, Search, User, Edit, X, RotateCcw, Play, Check, XCircle, Package, CalendarDays, MapPin, AlertTriangle, Info } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed, inject } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useInitials } from '@/composables/useInitials';
import { Input } from '@/components/ui/input';
import { Select } from '@/components/ui/select';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import Toast from '@/components/Toast.vue';
import { useToast } from '@/composables/useToast';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/components/ui/dropdown-menu';

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
    kategori?: string;
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
const { addToast } = useToast();

const searchQuery = ref('');
const selectedStatus = ref('');
const showReturnModal = ref(false);
const showApprovalModal = ref(false);
const showDeleteModal = ref(false);
const selectedInventaris = ref<InventarisItem | null>(null);
const approvalAction = ref<'approve' | 'reject'>('approve');

const returnForm = ref({
    tanggal_pengembalian: new Date().toISOString().split('T')[0],
    kondisi_barang: 'baik',
    catatan_pengembalian: ''
});

const approvalForm = ref({
    alasan: '',
    catatan: ''
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

                const matchesKategori =
                    !selectedKategori.value || item.kategori === selectedKategori.value;

                return matchesSearch && matchesStatus && matchesKategori;
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
    router.delete(route('inventaris.destroy', id.toString()), {
        onSuccess: () => {
            showDeleteModal.value = false;
            addToast({
                type: 'success',
                title: 'Berhasil',
                message: 'Inventaris berhasil dihapus'
            });
        },
        onError: (errors: any) => {
            console.error('Delete error:', errors);
            addToast({
                type: 'error',
                title: 'Gagal',
                message: 'Gagal menghapus inventaris'
            });
        }
    });
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
    router.patch(route('inventaris.start-progress', inventaris.id.toString()), {}, {
        onSuccess: () => {
            addToast({
                type: 'success',
                title: 'Berhasil',
                message: 'Proses inventaris telah dimulai'
            });
        },
        onError: (errors: any) => {
            console.error('Start progress error:', errors);
            addToast({
                type: 'error',
                title: 'Gagal',
                message: 'Gagal memulai proses inventaris'
            });
        }
    });
};

const submitReturn = () => {
    if (!selectedInventaris.value) return;
    
    router.post(route('inventaris.process-return', selectedInventaris.value.id.toString()), returnForm.value, {
        onSuccess: () => {
            showReturnModal.value = false;
            selectedInventaris.value = null;
            addToast({
                type: 'success',
                title: 'Berhasil',
                message: 'Pengembalian inventaris berhasil diproses'
            });
        },
        onError: (errors: any) => {
            console.error('Return error:', errors);
            addToast({
                type: 'error',
                title: 'Gagal',
                message: 'Gagal memproses pengembalian inventaris'
            });
        }
    });
};

const openApprovalModal = (inventaris: InventarisItem, action: 'approve' | 'reject') => {
    selectedInventaris.value = inventaris;
    approvalAction.value = action;
    approvalForm.value = {
        alasan: '',
        catatan: ''
    };
    showApprovalModal.value = true;
};

const submitApproval = () => {
    if (!selectedInventaris.value) return;
    
    if (!approvalForm.value.alasan.trim()) {
        addToast({
            type: 'error',
            title: 'Gagal',
            message: 'Alasan harus diisi'
        });
        return;
    }

    router.post(route('inventaris.approve'), {
        inventaris_id: selectedInventaris.value.id,
        action: approvalAction.value,
        alasan: approvalForm.value.alasan,
        catatan: approvalForm.value.catatan
    }, {
        onSuccess: () => {
            showApprovalModal.value = false;
            selectedInventaris.value = null;
            addToast({
                type: 'success',
                title: 'Berhasil',
                message: `Inventaris berhasil ${approvalAction.value === 'approve' ? 'disetujui' : 'ditolak'}`
            });
        },
        onError: (errors: any) => {
            console.error('Approval error:', errors);
            addToast({
                type: 'error',
                title: 'Gagal',
                message: `Gagal ${approvalAction.value === 'approve' ? 'menyetujui' : 'menolak'} inventaris`
            });
        }
    });
};

const openDeleteModal = (inventaris: InventarisItem) => {
    selectedInventaris.value = inventaris;
    showDeleteModal.value = true;
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedStatus.value = '';
    selectedKategori.value = '';
};

// Kategori dropdown
const kategoriOptions = [
    { value: '', label: 'Semua Kategori' },
    { value: 'elektronik', label: 'Elektronik' },
    { value: 'furniture', label: 'Furniture' },
    { value: 'kendaraan', label: 'Kendaraan' },
    { value: 'alat_tulis', label: 'Alat Tulis' },
    { value: 'lainnya', label: 'Lainnya' }
];
const selectedKategori = ref('');

// statusOptions for dropdown
const statusOptions = [
  { value: '', label: 'Semua Status' },
  { value: 'pending', label: 'Menunggu' },
  { value: 'approved', label: 'Disetujui' },
  { value: 'in_progress', label: 'Proses' },
  { value: 'rejected', label: 'Ditolak' },
  { value: 'returned', label: 'Dikembalikan' },
  { value: 'overdue', label: 'Terlambat' },
  { value: 'maintenance', label: 'Maintenance' },
];

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
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-4">
                <!-- Search -->
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <Input
                        v-model="searchQuery"
                        placeholder="Cari nomor inventaris, nama barang, atau peminjam..."
                        class="pl-10 w-full"
                    />
                </div>
                <!-- Filter Dropdowns (as Button Dropdown) -->
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto sm:items-end">
                  <!-- Status Dropdown -->
                  <div class="w-full sm:w-40">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <DropdownMenu>
                      <DropdownMenuTrigger as="button" class="w-full px-3 py-2 border rounded text-left bg-white">
                        {{ statusOptions.find(opt => opt.value === selectedStatus)?.label || 'Semua Status' }}
                      </DropdownMenuTrigger>
                      <DropdownMenuContent>
                        <DropdownMenuItem
                          v-for="option in statusOptions"
                          :key="option.value"
                          @click="selectedStatus = option.value"
                          :class="{ 'font-bold text-blue-600': selectedStatus === option.value }"
                        >
                          {{ option.label }}
                        </DropdownMenuItem>
                      </DropdownMenuContent>
                    </DropdownMenu>
                  </div>
                  <!-- Kategori Dropdown -->
                  <div class="w-full sm:w-40">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kategori</label>
                    <DropdownMenu>
                      <DropdownMenuTrigger as="button" class="w-full px-3 py-2 border rounded text-left bg-white">
                        {{ kategoriOptions.find(opt => opt.value === selectedKategori)?.label || 'Semua Kategori' }}
                      </DropdownMenuTrigger>
                      <DropdownMenuContent>
                        <DropdownMenuItem
                          v-for="option in kategoriOptions"
                          :key="option.value"
                          @click="selectedKategori = option.value"
                          :class="{ 'font-bold text-blue-600': selectedKategori === option.value }"
                        >
                          {{ option.label }}
                        </DropdownMenuItem>
                      </DropdownMenuContent>
                    </DropdownMenu>
                  </div>
                  <!-- Clear Filters Button -->
                  <div class="flex items-end w-full sm:w-auto">
                    <Button 
                      v-if="searchQuery || selectedStatus || selectedKategori"
                      @click="clearFilters" 
                      variant="outline" 
                      size="sm"
                      class="w-full sm:w-auto"
                    >
                      <X class="w-4 h-4 mr-2" />
                      Bersihkan Filter
                    </Button>
                  </div>
                </div>
            </div>

            <!-- Filter Status -->
            <div v-if="searchQuery || selectedStatus || selectedKategori" class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Info class="w-4 h-4 text-blue-600" />
                        <span class="text-sm font-medium text-blue-800">Filter Aktif:</span>
                        <div class="flex flex-wrap gap-2">
                            <Badge v-if="searchQuery" variant="secondary" class="text-xs">
                                Pencarian: "{{ searchQuery }}"
                            </Badge>
                            <Badge v-if="selectedStatus" variant="secondary" class="text-xs">
                                Status: {{ selectedStatus === 'pending' ? 'Menunggu' : 
                                          selectedStatus === 'approved' ? 'Disetujui' : 
                                          selectedStatus === 'in_progress' ? 'Proses' : 
                                          selectedStatus === 'rejected' ? 'Ditolak' : 
                                          selectedStatus === 'returned' ? 'Dikembalikan' : 
                                          selectedStatus === 'overdue' ? 'Terlambat' : 
                                          selectedStatus === 'maintenance' ? 'Maintenance' : selectedStatus }}
                            </Badge>
                            <Badge v-if="selectedKategori" variant="secondary" class="text-xs">
                                Kategori: {{ kategoriOptions.find(opt => opt.value === selectedKategori)?.label }}
                            </Badge>
                        </div>
                    </div>
                    <Button @click="clearFilters" variant="ghost" size="sm">
                        <X class="w-4 h-4" />
                    </Button>
                </div>
            </div>

            <!-- Inventaris Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <!-- Desktop Table -->
                <div v-if="filteredInventaris.length > 0" class="overflow-x-auto hidden md:block">
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
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-200 overflow-hidden">
                                            <img
                                                v-if="group.user_photo"
                                                :src="group.user_photo.startsWith('http') ? group.user_photo : `/storage/${group.user_photo}`"
                                                alt="User avatar"
                                                class="w-full h-full object-cover"
                                                @error="(e) => { 
                                                    const target = e.target as HTMLImageElement | null;
                                                    if (target) {
                                                        target.style.display = 'none';
                                                        const parent = target.parentElement as HTMLElement | null;
                                                        if (parent) {
                                                            parent.innerHTML = `<span class='text-gray-600 font-semibold'>${group.user?.charAt(0) || '?'}</span>`;
                                                        }
                                                    }
                                                }"
                                            />
                                            <span v-else class="text-gray-600 font-semibold">
                                                {{ group.user?.charAt(0) || '?' }}
                                            </span>
                                        </span>
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
                                            <Link :href="route('inventaris.show', item.id.toString())">
                                                <Button variant="outline" size="sm">
                                                    <User class="w-3 h-3" />
                                                </Button>
                                            </Link>

                                            <!-- Admin Actions -->
                                            <div v-if="isAdmin" class="flex gap-1">
                                                <!-- Approve/Reject for pending -->
                                                <div v-if="item.status === 'pending'" class="flex gap-1">
                                                    <Button @click="openApprovalModal(item, 'approve')" size="sm" variant="outline">
                                                        <Check class="w-3 h-3 text-green-600" />
                                                    </Button>
                                                    <Button @click="openApprovalModal(item, 'reject')" size="sm" variant="outline">
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
                                                <Link v-if="item.status === 'pending'" :href="route('inventaris.edit', item.id.toString())">
                                                    <Button variant="outline" size="sm">
                                                        <Edit class="w-3 h-3" />
                                                    </Button>
                                                </Link>

                                                <!-- Delete for pending -->
                                                <Button v-if="item.status === 'pending'" @click="openDeleteModal(item)" size="sm" variant="outline">
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

                <!-- Mobile Card List -->
                <div v-if="filteredInventaris.length > 0" class="block md:hidden">
                    <div v-for="group in filteredInventaris" :key="group.user" class="mb-4">
                        <div v-for="item in group.items" :key="item.id" class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-3 shadow-sm">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
                                    <img
                                        v-if="group.user_photo"
                                        :src="group.user_photo.startsWith('http') ? group.user_photo : `/storage/${group.user_photo}`"
                                        alt="User avatar"
                                        class="w-full h-full object-cover"
                                        @error="(e) => { 
                                            const target = e.target as HTMLImageElement | null;
                                            if (target) {
                                                target.style.display = 'none';
                                                const parent = target.parentElement as HTMLElement | null;
                                                if (parent) {
                                                    parent.innerHTML = `<span class='text-gray-600 font-semibold'>${group.user?.charAt(0) || '?'}</span>`;
                                                }
                                            }
                                        }"
                                    />
                                    <span v-else class="text-gray-600 font-semibold">
                                        {{ group.user?.charAt(0) || '?' }}
                                    </span>
                                </span>
                                <div>
                                    <p class="font-medium text-base">{{ group.user }}</p>
                                    <p class="text-xs text-gray-500">Nomor: <span class="font-mono text-purple-600">{{ item.nomor_inventaris }}</span></p>
                                </div>
                                <div class="ml-auto">
                                    <Badge :variant="getStatusVariant(item.status)">
                                        {{ item.status_text }}
                                    </Badge>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-x-2 gap-y-1 text-xs mb-2">
                                <div class="font-semibold text-gray-700">Barang</div>
                                <div>{{ item.nama_barang }}</div>
                                <div class="font-semibold text-gray-700">Kode</div>
                                <div>{{ item.kode_barang }}</div>
                                <div class="font-semibold text-gray-700">Jumlah</div>
                                <div>{{ item.jumlah }}</div>
                                <div class="font-semibold text-gray-700">Lokasi</div>
                                <div>
                                    <span v-if="item.lokasi_peminjaman" class="flex items-center gap-1">
                                        <MapPin class="w-3 h-3 text-gray-400" />
                                        {{ item.lokasi_peminjaman }}
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </div>
                                <div class="font-semibold text-gray-700">Tgl Pinjam</div>
                                <div>{{ formatDate(item.tanggal_peminjaman) }}</div>
                                <div class="font-semibold text-gray-700">Jatuh Tempo</div>
                                <div>
                                    <span :class="['font-medium', item.is_overdue ? 'text-red-600' : 'text-gray-700']">
                                        {{ formatDate(item.due_date) }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-1 justify-end">
                                <!-- View Details -->
                                <Link :href="route('inventaris.show', item.id.toString())">
                                    <Button variant="outline" size="sm">
                                        <User class="w-3 h-3" />
                                    </Button>
                                </Link>
                                <!-- Admin Actions -->
                                <template v-if="isAdmin">
                                    <Button v-if="item.status === 'pending'" @click="openApprovalModal(item, 'approve')" size="sm" variant="outline">
                                        <Check class="w-3 h-3 text-green-600" />
                                    </Button>
                                    <Button v-if="item.status === 'pending'" @click="openApprovalModal(item, 'reject')" size="sm" variant="outline">
                                        <XCircle class="w-3 h-3 text-red-600" />
                                    </Button>
                                    <Button v-if="item.status === 'approved'" @click="startInventaris(item)" size="sm" variant="outline">
                                        <Play class="w-3 h-3 text-blue-600" />
                                    </Button>
                                    <Button v-if="item.status === 'in_progress'" @click="processReturn(item)" size="sm" variant="outline">
                                        <RotateCcw class="w-3 h-3 text-orange-600" />
                                    </Button>
                                </template>
                                <!-- User Actions -->
                                <template v-else>
                                    <Link v-if="item.status === 'pending'" :href="route('inventaris.edit', item.id.toString())">
                                        <Button variant="outline" size="sm">
                                            <Edit class="w-3 h-3" />
                                        </Button>
                                    </Link>
                                    <Button v-if="item.status === 'pending'" @click="openDeleteModal(item)" size="sm" variant="outline">
                                        <X class="w-3 h-3 text-red-600" />
                                    </Button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="filteredInventaris.length === 0" class="text-center py-16 text-gray-500">
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
        <Dialog v-model:open="showReturnModal">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <RotateCcw class="w-5 h-5 text-orange-600" />
                        Proses Pengembalian Inventaris
                    </DialogTitle>
                    <DialogDescription>
                        Masukkan detail pengembalian untuk inventaris: {{ selectedInventaris?.nama_barang }}
                    </DialogDescription>
                </DialogHeader>
                <div class="space-y-4">
                    <div>
                        <Label for="tanggal_pengembalian">Tanggal Pengembalian</Label>
                        <Input 
                            id="tanggal_pengembalian"
                            v-model="returnForm.tanggal_pengembalian" 
                            type="date" 
                        />
                    </div>
                  <!-- Kondisi Barang Dropdown -->
<div class="mb-4">
  <label for="kondisi_barang" class="block text-sm font-medium text-gray-700 mb-2">Kondisi Barang</label>
  <select
    id="kondisi_barang"
    v-model="returnForm.kondisi_barang"
    class="block w-64 max-w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base py-2 px-3"
  >
    <option value="">Pilih kondisi</option>
    <option value="baik">Baik</option>
    <option value="rusak_ringan">Rusak Ringan</option>
    <option value="rusak_berat">Rusak Berat</option>
    <option value="hilang">Hilang</option>
  </select>
</div>



                    <div>
                        <Label for="catatan_pengembalian">Catatan Pengembalian</Label>
                        <Textarea 
                            id="catatan_pengembalian"
                            v-model="returnForm.catatan_pengembalian" 
                            placeholder="Masukkan catatan pengembalian (opsional)"
                            :rows="3"
                        />
                    </div>
                </div>
                <DialogFooter>
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            class="w-full sm:w-auto"
                            @click="showReturnModal = false"
                        >
                            Batal
                        </Button>
                        <Button
                            type="button"
                            class="w-full sm:w-auto"
                            @click="submitReturn"
                        >
                            <RotateCcw class="w-4 h-4 mr-2" />
                            Proses Pengembalian
                        </Button>
                    </div>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Approval Modal -->
        <Dialog v-model:open="showApprovalModal">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <Check v-if="approvalAction === 'approve'" class="w-5 h-5 text-green-600" />
                        <XCircle v-else class="w-5 h-5 text-red-600" />
                        {{ approvalAction === 'approve' ? 'Setujui' : 'Tolak' }} Inventaris
                    </DialogTitle>
                    <DialogDescription>
                        {{ approvalAction === 'approve' ? 'Setujui' : 'Tolak' }} inventaris: {{ selectedInventaris?.nama_barang }}
                    </DialogDescription>
                </DialogHeader>
                <div class="space-y-4">
                    <div>
                        <Label for="alasan">Alasan {{ approvalAction === 'approve' ? 'Persetujuan' : 'Penolakan' }} *</Label>
                        <Textarea 
                            id="alasan"
                            v-model="approvalForm.alasan" 
                            :placeholder="`Masukkan alasan ${approvalAction === 'approve' ? 'persetujuan' : 'penolakan'}...`"
                            :rows="3"
                            required
                        />
                    </div>
                    <div>
                        <Label for="catatan">Catatan (Opsional)</Label>
                        <Textarea 
                            id="catatan"
                            v-model="approvalForm.catatan" 
                            placeholder="Masukkan catatan tambahan (opsional)"
                            :rows="2"
                        />
                    </div>
                </div>
                <DialogFooter>
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            class="w-full sm:w-auto"
                            @click="showApprovalModal = false"
                        >
                            Batal
                        </Button>
                        <Button
                            type="button"
                            class="w-full sm:w-auto"
                            @click="submitApproval"
                        >
                            <Check v-if="approvalAction === 'approve'" class="w-4 h-4 mr-2" />
                            <XCircle v-else class="w-4 h-4 mr-2" />
                            {{ approvalAction === 'approve' ? 'Setujui' : 'Tolak' }}
                        </Button>
                    </div>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Modal -->
        <Dialog v-model:open="showDeleteModal">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <AlertTriangle class="w-5 h-5 text-red-600" />
                        Konfirmasi Hapus
                    </DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus inventaris ini? Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <AlertTriangle class="w-5 h-5 text-red-600 mt-0.5" />
                        <div>
                            <p class="font-medium text-red-800">{{ selectedInventaris?.nama_barang }}</p>
                            <p class="text-sm text-red-600 mt-1">
                                Nomor: {{ selectedInventaris?.nomor_inventaris }}<br>
                                Kode: {{ selectedInventaris?.kode_barang }}
                            </p>
                        </div>
                    </div>
                </div>
                <DialogFooter>
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            class="w-full sm:w-auto"
                            @click="showDeleteModal = false"
                        >
                            Batal
                        </Button>
                        <Button
                            type="button"
                            variant="destructive"
                            class="w-full sm:w-auto"
                            @click="selectedInventaris?.id ? deleteInventaris(selectedInventaris.id) : null"
                        >
                            <X class="w-4 h-4 mr-2" />
                            Hapus Inventaris
                        </Button>
                    </div>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <Toast />
    </AppLayout>
</template> 