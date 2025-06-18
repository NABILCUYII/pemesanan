<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { User, Calendar } from 'lucide-vue-next';
import {
    Table,
    TableHeader,
    TableBody,
    TableHead,
    TableRow,
    TableCell,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';


interface Barang {
    id: number;
    nama_barang: string;
    kode_barang: string;
}

interface User {
    id: number;
    name: string;
    email: string;
}

interface Permintaan {
    id: number;
    user_id: number;
    user: User;
    barang: Barang;
    jumlah: number;
    status: string;
    keterangan?: string;
    created_at: string;
}

interface Peminjaman {
    id: number;
    user_id: number;
    user: User;
    barang: Barang;
    jumlah: number;
    status: string;
    keterangan?: string;
    created_at: string;
    tanggal_pinjam: string;
    tanggal_pengembalian: string;
}

const props = defineProps<{
    permintaan: Permintaan[];
    peminjaman: Peminjaman[];
}>();

const searchQuery = ref('');
const selectedStatus = ref('pending');
const activeTab = ref('permintaan');

const getStatusVariant = (status: string): 'default' | 'secondary' | 'destructive' | 'outline' => {
    switch (status) {
        case 'approved':
            return 'secondary';
        case 'rejected':
            return 'destructive';
        default:
            return 'default';
    }
};

const getStatusText = (status: string) => {
    switch (status) {
        case 'approved':
            return 'Disetujui';
        case 'rejected':
            return 'Ditolak';
        default:
            return 'Menunggu';
    }
};

const handleApprovePermintaan = (id: number) => {
    router.post('/permintaan/approve', {
        permintaan_id: id,
        action: 'approve',
        alasan: 'Permintaan disetujui',
        catatan: ''
    });
};

const handleRejectPermintaan = (id: number) => {
    router.post('/permintaan/approve', {
        permintaan_id: id,
        action: 'reject',
        alasan: 'Permintaan ditolak',
        catatan: ''
    });
};

const handleApprovePeminjaman = (id: number) => {
    router.post('/peminjaman/approve', {
        peminjaman_id: id,
        action: 'approve',
        alasan: 'Peminjaman disetujui',
        catatan: ''
    });
};

const handleRejectPeminjaman = (id: number) => {
    router.post('/peminjaman/approve', {
        peminjaman_id: id,
        action: 'reject',
        alasan: 'Peminjaman ditolak',
        catatan: ''
    });
};

const filteredPermintaan = computed(() => {
    return props.permintaan.filter(item => {
        const matchesSearch = item.barang.nama_barang.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.barang.kode_barang.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.user.name.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesStatus = item.status === selectedStatus.value;
        return matchesSearch && matchesStatus;
    });
});

const filteredPeminjaman = computed(() => {
    return props.peminjaman.filter(item => {
        const matchesSearch = item.barang.nama_barang.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.barang.kode_barang.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.user.name.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesStatus = item.status === selectedStatus.value;
        return matchesSearch && matchesStatus;
    });
});

const groupedPermintaan = computed(() => {
    const groups = new Map<number, Permintaan[]>();
    
    filteredPermintaan.value.forEach(item => {
        if (!groups.has(item.user_id)) {
            groups.set(item.user_id, []);
        }
        groups.get(item.user_id)?.push(item);
    });
    
    return Array.from(groups.entries()).map(([userId, items]) => ({
        user: items[0].user,
        items
    }));
});

const groupedPeminjaman = computed(() => {
    const groups = new Map<number, Peminjaman[]>();
    
    filteredPeminjaman.value.forEach(item => {
        if (!groups.has(item.user_id)) {
            groups.set(item.user_id, []);
        }
        groups.get(item.user_id)?.push(item);
    });
    
    return Array.from(groups.entries()).map(([userId, items]) => ({
        user: items[0].user,
        items
    }));
});
</script>

<template>
    <Head title="Approval" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <h1 class="text-2xl font-bold mb-6">Persetujuan</h1>
            
            <div class="w-full">
                <div class="grid w-full grid-cols-2 mb-4">
                    <button 
                        class="px-4 py-2 text-sm font-medium rounded-l-lg"
                        :class="activeTab === 'permintaan' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700'"
                        @click="activeTab = 'permintaan'"
                    >
                        Permintaan
                    </button>
                    <button 
                        class="px-4 py-2 text-sm font-medium rounded-r-lg"
                        :class="activeTab === 'peminjaman' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700'"
                        @click="activeTab = 'peminjaman'"
                    >
                        Peminjaman
                    </button>
                </div>

                <div v-if="activeTab === 'permintaan'">
                    <!-- TABEL PERMINTAAN (untuk md ke atas) -->
                    <div class="bg-white rounded-lg border hidden md:block">
                        <div v-for="group in groupedPermintaan" :key="group.user.id" class="border-b last:border-b-0">
                            <div class="bg-gray-50 px-4 py-3 border-b">
                                <div class="flex items-center gap-2">
                                    <User class="w-5 h-5 text-gray-500" />
                                    <div>
                                        <div class="font-medium">{{ group.user.name }}</div>
                                        <div class="text-xs text-gray-500">{{ group.user.email }}</div>
                                    </div>
                                </div>
                            </div>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Tanggal</TableHead>
                                        <TableHead>Barang</TableHead>
                                        <TableHead>Kode</TableHead>
                                        <TableHead class="text-center">Jumlah</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Keterangan</TableHead>
                                        <TableHead class="text-right">Aksi</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="item in group.items" :key="item.id">
                                        <TableCell class="text-sm">
                                            <div class="flex items-center gap-1">
                                                <Calendar class="w-4 h-4 text-gray-400" />
                                                {{ new Date(item.created_at).toLocaleDateString('id-ID') }}
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
                                            <Button 
                                                v-if="item.status === 'pending'"
                                                variant="default" 
                                                size="sm"
                                                @click="handleApprovePermintaan(item.id)"
                                            >
                                                Terima
                                            </Button>
                                            <Button 
                                                v-if="item.status === 'pending'"
                                                variant="destructive" 
                                                size="sm"
                                                @click="handleRejectPermintaan(item.id)"
                                            >
                                                Tolak
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </div>

                    <!-- CARD PERMINTAAN (untuk hp/sm) -->
                    <div class="space-y-4 md:hidden">
                        <div v-for="group in groupedPermintaan" :key="group.user.id" class="border rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 border-b">
                                <div class="flex items-center gap-2">
                                    <User class="w-5 h-5 text-gray-500" />
                                    <div>
                                        <div class="font-medium">{{ group.user.name }}</div>
                                        <div class="text-xs text-gray-500">{{ group.user.email }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="divide-y">
                                <div
                                    v-for="item in group.items"
                                    :key="item.id"
                                    class="p-4"
                                >
                                    <div class="flex justify-between items-center mb-2">
                                        <h2 class="font-semibold text-lg">{{ item.barang.nama_barang }}</h2>
                                        <Badge :variant="getStatusVariant(item.status)">
                                            {{ getStatusText(item.status) }}
                                        </Badge>
                                    </div>
                                    <p class="text-sm text-gray-500 mb-1"><strong>Kode:</strong> {{ item.barang.kode_barang }}</p>
                                    <p class="text-sm text-gray-500 mb-1"><strong>Jumlah:</strong> {{ item.jumlah }}</p>
                                    <p class="text-sm text-gray-500 mb-1"><strong>Tanggal:</strong> {{ new Date(item.created_at).toLocaleDateString('id-ID') }}</p>
                                    <p class="text-sm text-gray-500 mb-3"><strong>Keterangan:</strong> {{ item.keterangan || '-' }}</p>
                                    
                                    <div class="flex gap-2 justify-end">
                                        <Button 
                                            v-if="item.status === 'pending'"
                                            variant="default" 
                                            size="sm"
                                            @click="handleApprovePermintaan(item.id)"
                                        >
                                            Terima
                                        </Button>
                                        <Button 
                                            v-if="item.status === 'pending'"
                                            variant="destructive" 
                                            size="sm"
                                            @click="handleRejectPermintaan(item.id)"
                                        >
                                            Tolak
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="activeTab === 'peminjaman'">
                    <!-- TABEL PEMINJAMAN (untuk md ke atas) -->
                    <div class="bg-white rounded-lg border hidden md:block">
                        <div v-for="group in groupedPeminjaman" :key="group.user.id" class="border-b last:border-b-0">
                            <div class="bg-gray-50 px-4 py-3 border-b">
                                <div class="flex items-center gap-2">
                                    <User class="w-5 h-5 text-gray-500" />
                                    <div>
                                        <div class="font-medium">{{ group.user.name }}</div>
                                        <div class="text-xs text-gray-500">{{ group.user.email }}</div>
                                    </div>
                                </div>
                            </div>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Tanggal</TableHead>
                                        <TableHead>Barang</TableHead>
                                        <TableHead>Kode</TableHead>
                                        <TableHead class="text-center">Jumlah</TableHead>
                                        <TableHead>Tanggal Pengembalian</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Keterangan</TableHead>
                                        <TableHead class="text-right">Aksi</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="item in group.items" :key="item.id">
                                        <TableCell class="text-sm">
                                            <div class="flex items-center gap-1">
                                                <Calendar class="w-4 h-4 text-gray-400" />
                                                {{ new Date(item.created_at).toLocaleDateString('id-ID') }}
                                            </div>
                                        </TableCell>
                                        <TableCell>{{ item.barang.nama_barang }}</TableCell>
                                        <TableCell class="font-medium">{{ item.barang.kode_barang }}</TableCell>
                                        <TableCell class="text-center font-semibold">{{ item.jumlah }}</TableCell>
                                        <TableCell class="text-sm">
                                            <div class="flex items-center gap-1">
                                                <Calendar class="w-4 h-4 text-gray-400" />
                                                {{ item.tanggal_pengembalian ? new Date(item.tanggal_pengembalian).toLocaleDateString('id-ID') : '-' }}
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <Badge :variant="getStatusVariant(item.status)">
                                                {{ getStatusText(item.status) }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="max-w-xs truncate">
                                            {{ item.keterangan || '-' }}
                                        </TableCell>
                                        <TableCell class="text-right space-x-2">
                                            <Button 
                                                v-if="item.status === 'pending'"
                                                variant="default" 
                                                size="sm"
                                                @click="handleApprovePeminjaman(item.id)"
                                            >
                                                Terima
                                            </Button>
                                            <Button 
                                                v-if="item.status === 'pending'"
                                                variant="destructive" 
                                                size="sm"
                                                @click="handleRejectPeminjaman(item.id)"
                                            >
                                                Tolak
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </div>

                    <!-- CARD PEMINJAMAN (untuk hp/sm) -->
                    <div class="space-y-4 md:hidden">
                        <div v-for="group in groupedPeminjaman" :key="group.user.id" class="border rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 border-b">
                                <div class="flex items-center gap-2">
                                    <User class="w-5 h-5 text-gray-500" />
                                    <div>
                                        <div class="font-medium">{{ group.user.name }}</div>
                                        <div class="text-xs text-gray-500">{{ group.user.email }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="divide-y">
                                <div
                                    v-for="item in group.items"
                                    :key="item.id"
                                    class="p-4"
                                >
                                    <div class="flex justify-between items-center mb-2">
                                        <h2 class="font-semibold text-lg">{{ item.barang.nama_barang }}</h2>
                                        <Badge :variant="getStatusVariant(item.status)">
                                            {{ getStatusText(item.status) }}
                                        </Badge>
                                    </div>
                                    <p class="text-sm text-gray-500 mb-1"><strong>Kode:</strong> {{ item.barang.kode_barang }}</p>
                                    <p class="text-sm text-gray-500 mb-1"><strong>Jumlah:</strong> {{ item.jumlah }}</p>
                                    <p class="text-sm text-gray-500 mb-1"><strong>Tanggal:</strong> {{ new Date(item.created_at).toLocaleDateString('id-ID') }}</p>
                                    <p class="text-sm text-gray-500 mb-1"><strong>Tanggal Pengembalian:</strong> {{ item.tanggal_pengembalian ? new Date(item.tanggal_pengembalian).toLocaleDateString('id-ID') : '-' }}</p>
                                    <p class="text-sm text-gray-500 mb-3"><strong>Keterangan:</strong> {{ item.keterangan || '-' }}</p>
                                    
                                    <div class="flex gap-2 justify-end">
                                        <Button 
                                            v-if="item.status === 'pending'"
                                            variant="default" 
                                            size="sm"
                                            @click="handleApprovePeminjaman(item.id)"
                                        >
                                            Terima
                                        </Button>
                                        <Button 
                                            v-if="item.status === 'pending'"
                                            variant="destructive" 
                                            size="sm"
                                            @click="handleRejectPeminjaman(item.id)"
                                        >
                                            Tolak
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
