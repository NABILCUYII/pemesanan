<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Package, Check, X, Eye, Search, User, Calendar } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';

interface Barang {
    id: number;
    kode_barang: string;
    nama_barang: string;
    stok: number;
}

interface User {
    id: number;
    name: string;
    email: string;
}

interface Permintaan {
    id: number;
    user_id: number;
    barang_id: number;
    jumlah: number;
    keterangan: string;
    status: 'pending' | 'approved' | 'rejected' | 'completed';
    created_at: string;
    alasan_approval?: string;
    catatan_approval?: string;
    approved_at?: string;
    user: User;
    barang: Barang;
    approved_by?: User;
}

const props = defineProps<{
    permintaan: Permintaan[];
}>();

const searchQuery = ref('');
const selectedStatus = ref('pending');
const selectedPermintaan = ref<Permintaan | null>(null);
const showApprovalDialog = ref(false);
const approvalAction = ref<'approve' | 'reject'>('approve');

const form = useForm({
    permintaan_id: '',
    action: '',
    alasan: '',
    catatan: ''
});

const filteredPermintaan = computed(() => {
    return props.permintaan.filter(item => {
        const matchesSearch = item.barang.nama_barang.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.barang.kode_barang.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.user.name.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesStatus = item.status === selectedStatus.value;
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

const openApprovalDialog = (permintaan: Permintaan, action: 'approve' | 'reject') => {
    selectedPermintaan.value = permintaan;
    approvalAction.value = action;
    form.reset();
    form.permintaan_id = permintaan.id.toString();
    form.action = action;
    showApprovalDialog.value = true;
};

const submitApproval = () => {
    form.post(route('permintaan.approve'), {
        onSuccess: () => {
            showApprovalDialog.value = false;
            selectedPermintaan.value = null;
        }
    });
};

const getPendingCount = computed(() => {
    return props.permintaan.filter(p => p.status === 'pending').length;
});
</script>

<template>
    <Head title="Approval Permintaan" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                        <Package class="h-6 w-6" />
                        Approval Permintaan
                    </h1>
                    <p class="text-muted-foreground">Kelola dan approve permintaan barang dari user</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="bg-orange-100 text-orange-800 px-3 py-2 rounded-lg">
                        <span class="font-semibold">{{ getPendingCount }}</span> permintaan menunggu
                    </div>
                </div>
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
                    <option value="pending">Menunggu Approval</option>
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
                                <div class="flex items-center gap-1">
                                    <Calendar class="w-4 h-4 text-gray-400" />
                                    {{ new Date(item.created_at).toLocaleDateString('id-ID') }}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <User class="w-4 h-4 text-gray-500" />
                                    <div>
                                        <div class="font-medium">{{ item.user.name }}</div>
                                        <div class="text-xs text-gray-500">{{ item.user.email }}</div>
                                    </div>
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
                                <!-- View Details -->
                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button variant="outline" size="sm">
                                            <Eye class="w-4 h-4" />
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent class="sm:max-w-md">
                                        <DialogHeader>
                                            <DialogTitle>Detail Permintaan</DialogTitle>
                                        </DialogHeader>
                                        <div class="space-y-4">
                                            <div class="bg-gray-50 p-3 rounded-lg">
                                                <h4 class="font-medium mb-2">Informasi Pemohon</h4>
                                                <p class="text-sm"><strong>Nama:</strong> {{ item.user.name }}</p>
                                                <p class="text-sm"><strong>Email:</strong> {{ item.user.email }}</p>
                                                <p class="text-sm"><strong>Tanggal:</strong> {{ new Date(item.created_at).toLocaleDateString('id-ID') }}</p>
                                            </div>
                                            <div class="bg-blue-50 p-3 rounded-lg">
                                                <h4 class="font-medium mb-2">Detail Barang</h4>
                                                <p class="text-sm"><strong>Nama:</strong> {{ item.barang.nama_barang }}</p>
                                                <p class="text-sm"><strong>Kode:</strong> {{ item.barang.kode_barang }}</p>
                                                <p class="text-sm"><strong>Stok Tersedia:</strong> {{ item.barang.stok }} unit</p>
                                                <p class="text-sm"><strong>Jumlah Diminta:</strong> {{ item.jumlah }} unit</p>
                                            </div>
                                            <div v-if="item.keterangan" class="bg-yellow-50 p-3 rounded-lg">
                                                <h4 class="font-medium mb-2">Keterangan</h4>
                                                <p class="text-sm">{{ item.keterangan }}</p>
                                            </div>
                                            <div v-if="item.alasan_approval" class="bg-green-50 p-3 rounded-lg">
                                                <h4 class="font-medium mb-2">Informasi Approval</h4>
                                                <p class="text-sm"><strong>Status:</strong> {{ getStatusText(item.status) }}</p>
                                                <p class="text-sm"><strong>Alasan:</strong> {{ item.alasan_approval }}</p>
                                                <p v-if="item.catatan_approval" class="text-sm"><strong>Catatan:</strong> {{ item.catatan_approval }}</p>
                                                <p v-if="item.approved_by" class="text-sm"><strong>Disetujui oleh:</strong> {{ item.approved_by.name }}</p>
                                                <p v-if="item.approved_at" class="text-sm"><strong>Tanggal Approval:</strong> {{ new Date(item.approved_at).toLocaleDateString('id-ID') }}</p>
                                            </div>
                                        </div>
                                    </DialogContent>
                                </Dialog>

                                <!-- Approval Actions (only for pending) -->
                                <template v-if="item.status === 'pending'">
                                    <Button 
                                        variant="default" 
                                        size="sm"
                                        @click="openApprovalDialog(item, 'approve')"
                                        class="bg-green-600 hover:bg-green-700"
                                    >
                                        <Check class="w-4 h-4" />
                                    </Button>
                                    <Button 
                                        variant="destructive" 
                                        size="sm"
                                        @click="openApprovalDialog(item, 'reject')"
                                    >
                                        <X class="w-4 h-4" />
                                    </Button>
                                </template>
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
                        <Button variant="outline" size="sm">
                            <Eye class="w-4 h-4" />
                        </Button>
                        <template v-if="item.status === 'pending'">
                            <Button 
                                variant="default" 
                                size="sm"
                                @click="openApprovalDialog(item, 'approve')"
                                class="bg-green-600 hover:bg-green-700"
                            >
                                <Check class="w-4 h-4" />
                            </Button>
                            <Button 
                                variant="destructive" 
                                size="sm"
                                @click="openApprovalDialog(item, 'reject')"
                            >
                                <X class="w-4 h-4" />
                            </Button>
                        </template>
                    </div>
                </div>
            </div>

            <div v-if="filteredPermintaan.length === 0" class="text-center py-8">
                <div class="text-gray-500">
                    <Package class="h-12 w-12 mx-auto mb-4" />
                    <p>Tidak ada permintaan ditemukan</p>
                </div>
            </div>
        </div>

        <!-- Approval Dialog -->
        <Dialog :open="showApprovalDialog" @update:open="showApprovalDialog = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>
                        {{ approvalAction === 'approve' ? 'Setujui' : 'Tolak' }} Permintaan
                    </DialogTitle>
                </DialogHeader>
                <div class="space-y-4">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-sm font-medium">Barang: {{ selectedPermintaan?.barang.nama_barang }}</p>
                        <p class="text-sm text-gray-600">Pemohon: {{ selectedPermintaan?.user.name }}</p>
                        <p class="text-sm text-gray-600">Jumlah: {{ selectedPermintaan?.jumlah }} unit</p>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-sm font-medium">
                            {{ approvalAction === 'approve' ? 'Alasan Persetujuan' : 'Alasan Penolakan' }} *
                        </label>
                        <textarea 
                            v-model="form.alasan"
                            rows="3"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                            :placeholder="approvalAction === 'approve' ? 'Jelaskan alasan persetujuan...' : 'Jelaskan alasan penolakan...'"
                            required
                        />
                        <p v-if="form.errors.alasan" class="text-sm text-red-500">
                            {{ form.errors.alasan }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Catatan Tambahan (Opsional)</label>
                        <textarea 
                            v-model="form.catatan"
                            rows="2"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                            placeholder="Catatan tambahan untuk pemohon..."
                        />
                        <p v-if="form.errors.catatan" class="text-sm text-red-500">
                            {{ form.errors.catatan }}
                        </p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <Button 
                            type="button" 
                            variant="outline"
                            @click="showApprovalDialog = false"
                        >
                            Batal
                        </Button>
                        <Button 
                            type="button"
                            @click="submitApproval"
                            :disabled="form.processing || !form.alasan"
                            :class="approvalAction === 'approve' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'"
                        >
                            {{ approvalAction === 'approve' ? 'Setujui' : 'Tolak' }} Permintaan
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>


