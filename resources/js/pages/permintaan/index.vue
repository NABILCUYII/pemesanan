<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Plus, Search, User, Edit, X, Check, XCircle } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useInitials } from '@/composables/useInitials';
import { usePhotoUrl } from '@/composables/usePhotoUrl';

// Declare route function globally for TypeScript
declare function route(name: string, params?: any): string;

interface PermintaanItem {
    id: number;
    nama_barang: string;
    kode_barang: string;
    jumlah: number;
    status: 'pending' | 'approved' | 'rejected' | 'completed';
    created_at: string;
    keterangan?: string;
}

interface PermintaanGroup {
    user: string;
    user_photo?: string;
    items: PermintaanItem[];
}

const props = defineProps<{
    permintaan: PermintaanGroup[];
    isAdmin: boolean;
}>();

// Get user role from auth
const page = usePage();
const user = page.props.auth.user;
const { getInitials } = useInitials();

// Debug: Log props saat komponen dimuat
console.log('Props permintaan:', props.permintaan);

const searchQuery = ref('');
const selectedStatus = ref('');

const { getPhotoUrl } = usePhotoUrl();

const filteredPermintaan = computed(() => {
    console.log('Filtering permintaan:', props.permintaan); // Debug log
    
    if (!props.permintaan) {
        console.log('No permintaan data'); // Debug log
        return [];
    }
    
    return props.permintaan
        .map(group => {
            if (!group || !group.items) {
                console.log('Invalid group:', group); // Debug log
                return null;
            }
            
            const filteredItems = group.items.filter(item => {
                if (!item || !item.nama_barang || !item.kode_barang) {
                    console.log('Invalid item:', item); // Debug log
                    return false;
                }
                
                const matchesSearch =
                    (item.nama_barang?.toLowerCase() || '').includes(searchQuery.value.toLowerCase()) ||
                    (item.kode_barang?.toLowerCase() || '').includes(searchQuery.value.toLowerCase()) ||
                    (group.user?.toLowerCase() || '').includes(searchQuery.value.toLowerCase());

                const matchesStatus =
                    !selectedStatus.value || item.status === selectedStatus.value;

                return matchesSearch && matchesStatus;
            });

            return filteredItems.length > 0 ? { 
                user: group.user, 
                user_photo: group.user_photo,
                items: filteredItems 
            } : null;
        })
        .filter(Boolean) as PermintaanGroup[];
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

const deletePermintaan = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus permintaan ini?')) {
        router.delete(route('permintaan.destroy', id));
    }
};

const approvePermintaan = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menyetujui permintaan ini?')) {
        router.post(route('permintaan.approve'), {
            permintaan_id: id,
            action: 'approve',
            alasan: 'Permintaan disetujui',
            catatan: ''
        });
    }
};

const rejectPermintaan = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menolak permintaan ini?')) {
        router.post(route('permintaan.approve'), {
            permintaan_id: id,
            action: 'reject',
            alasan: 'Permintaan ditolak',
            catatan: ''
        });
    }
};

const completePermintaan = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menandai permintaan ini sebagai selesai?')) {
        router.patch(route('permintaan.complete', id));
    }
};
</script>

<template>
    <Head title="Permintaan" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Daftar Permintaan</h1>
                    <p v-if="!isAdmin" class="text-sm text-gray-500 mt-1">Menampilkan permintaan Anda</p>
                    <p v-else class="text-sm text-gray-500 mt-1">Menampilkan semua permintaan pengguna</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <Link v-if="isAdmin" :href="route('permintaan.approval')">
                        <Button variant="outline" class="w-full sm:w-auto">
                            <Check class="w-4 h-4 mr-2" />
                            Approval
                        </Button>
                    </Link>
                    <Link :href="route('permintaan.create')">
                        <Button class="w-full sm:w-auto">
                            <Plus class="w-4 h-4 mr-2" />
                            Buat Permintaan
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
                        :placeholder="isAdmin ? 'Cari nama barang, kode barang, atau pemohon...' : 'Cari nama barang atau kode barang...'"
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

            <!-- Table (untuk desktop) -->
            <div class="hidden md:block">
                <div v-for="group in filteredPermintaan" :key="group.user" class="mb-8">
                    <!-- User Header -->
                    <div class="flex items-center gap-2 mb-4 bg-gray-50 p-4 rounded-lg">
                        <Avatar class="w-10 h-10">
                            <AvatarImage 
                                v-if="group.user_photo && getPhotoUrl(group.user_photo)" 
                                :src="getPhotoUrl(group.user_photo) ?? ''" 
                                alt="User Photo" 
                            />
                            <AvatarFallback>{{ getInitials(group.user) }}</AvatarFallback>
                        </Avatar>
                        <h2 class="font-semibold text-lg">{{ group.user }}</h2>
                    </div>

                    <!-- Requests Table -->
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nama Barang</TableHead>
                                <TableHead>Kode Barang</TableHead>
                                <TableHead>Jumlah</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in group.items" :key="item.id">
                                <TableCell>{{ item.nama_barang }}</TableCell>
                                <TableCell>{{ item.kode_barang }}</TableCell>
                                <TableCell>{{ item.jumlah }}</TableCell>
                                <TableCell>{{ formatDate(item.created_at) }}</TableCell>
                                <TableCell>
                                    <Badge :variant="getStatusVariant(item.status)">
                                        {{ getStatusText(item.status) }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <!-- Admin Actions -->
                                        <Link
                                            v-if="item.status === 'pending'"
                                            :href="route('permintaan.edit', item.id)"
                                            class="text-blue-600 hover:text-blue-800"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </Link>
                                        <button
                                            v-if="item.status === 'pending'"
                                            @click="deletePermintaan(item.id)"
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            <X class="w-4 h-4" />
                                        </button>
                                        
                                        <!-- Admin Approval Actions -->
                                        <button
                                            v-if="isAdmin && item.status === 'pending'"
                                            @click="approvePermintaan(item.id)"
                                            class="text-green-600 hover:text-green-800"
                                            title="Setujui"
                                        >
                                            <Check class="w-4 h-4" />
                                        </button>
                                        <button
                                            v-if="isAdmin && item.status === 'pending'"
                                            @click="rejectPermintaan(item.id)"
                                            class="text-red-600 hover:text-red-800"
                                            title="Tolak"
                                        >
                                            <XCircle class="w-4 h-4" />
                                        </button>
                                        <button
                                            v-if="isAdmin && item.status === 'approved'"
                                            @click="completePermintaan(item.id)"
                                            class="text-blue-600 hover:text-blue-800"
                                            title="Tandai Selesai"
                                        >
                                            <Check class="w-4 h-4" />
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
                    v-for="group in filteredPermintaan"
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

                    <!-- Requests List -->
                    <div class="divide-y">
                        <div 
                            v-for="item in group.items" 
                            :key="item.kode_barang" 
                            class="p-4"
                        >
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="font-semibold">{{ item.nama_barang }}</h3>
                                    <p class="text-sm text-gray-500">Kode: {{ item.kode_barang }}</p>
                                    <p class="text-sm text-gray-500">Jumlah: {{ item.jumlah }}</p>
                                    <p class="text-sm text-gray-500">Tanggal: {{ formatDate(item.created_at) }}</p>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <Badge :variant="getStatusVariant(item.status)">
                                        {{ getStatusText(item.status) }}
                                    </Badge>
                                    <div class="flex gap-2">
                                        <Link
                                            v-if="item.status === 'pending'"
                                            :href="route('permintaan.edit', item.id)"
                                            class="text-blue-600 hover:text-blue-800"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </Link>
                                        <button
                                            v-if="item.status === 'pending'"
                                            @click="deletePermintaan(item.id)"
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            <X class="w-4 h-4" />
                                        </button>
                                        
                                        <!-- Admin Approval Actions -->
                                        <button
                                            v-if="isAdmin && item.status === 'pending'"
                                            @click="approvePermintaan(item.id)"
                                            class="text-green-600 hover:text-green-800"
                                            title="Setujui"
                                        >
                                            <Check class="w-4 h-4" />
                                        </button>
                                        <button
                                            v-if="isAdmin && item.status === 'pending'"
                                            @click="rejectPermintaan(item.id)"
                                            class="text-red-600 hover:text-red-800"
                                            title="Tolak"
                                        >
                                            <XCircle class="w-4 h-4" />
                                        </button>
                                        <button
                                            v-if="isAdmin && item.status === 'approved'"
                                            @click="completePermintaan(item.id)"
                                            class="text-blue-600 hover:text-blue-800"
                                            title="Tandai Selesai"
                                        >
                                            <Check class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
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
