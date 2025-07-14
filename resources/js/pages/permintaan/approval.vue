<script setup lang="ts">
import { computed, ref, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Calendar, Search, CheckCircle, XCircle } from 'lucide-vue-next';
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
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { usePhotoUrl } from '@/composables/usePhotoUrl';


interface Barang {
    id: number;
    nama_barang: string;
    kode_barang: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    photo?: string;
    photo_url?: string;
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

const { getInitials } = useInitials();

const { getPhotoUrl } = usePhotoUrl();

const searchQuery = ref('');
const selectedStatus = ref('pending');
const activeTab = ref('permintaan');

const statusOptions = [
    { value: 'pending', label: 'Menunggu', color: 'bg-yellow-100 text-yellow-800' },
    { value: 'approved', label: 'Disetujui', color: 'bg-green-100 text-green-800' },
    { value: 'rejected', label: 'Ditolak', color: 'bg-red-100 text-red-800' },
];

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

// Animasi state
const animatingPermintaan = reactive<{ [id: number]: string }>({});
const animatingPeminjaman = reactive<{ [id: number]: string }>({});

const handleApprovePermintaan = (id: number) => {
    animatingPermintaan[id] = 'approve';
    setTimeout(() => {
        router.post('/permintaan/approve', {
            permintaan_id: id,
            action: 'approve',
            alasan: 'Permintaan disetujui',
            catatan: ''
        });
        setTimeout(() => {
            delete animatingPermintaan[id];
        }, 600);
    }, 600);
};

const handleRejectPermintaan = (id: number) => {
    animatingPermintaan[id] = 'reject';
    setTimeout(() => {
        router.post('/permintaan/approve', {
            permintaan_id: id,
            action: 'reject',
            alasan: 'Permintaan ditolak',
            catatan: ''
        });
        setTimeout(() => {
            delete animatingPermintaan[id];
        }, 600);
    }, 600);
};

const handleApprovePeminjaman = (id: number) => {
    animatingPeminjaman[id] = 'approve';
    setTimeout(() => {
        router.post('/peminjaman/approve', {
            peminjaman_id: id,
            action: 'approve',
            alasan: 'Peminjaman disetujui',
            catatan: ''
        });
        setTimeout(() => {
            delete animatingPeminjaman[id];
        }, 600);
    }, 600);
};

const handleRejectPeminjaman = (id: number) => {
    animatingPeminjaman[id] = 'reject';
    setTimeout(() => {
        router.post('/peminjaman/approve', {
            peminjaman_id: id,
            action: 'reject',
            alasan: 'Peminjaman ditolak',
            catatan: ''
        });
        setTimeout(() => {
            delete animatingPeminjaman[id];
        }, 600);
    }, 600);
};

const filteredPermintaan = computed(() => {
    return props.permintaan.filter(item => {
        const q = searchQuery.value.toLowerCase();
        const matchesSearch = item.barang.nama_barang.toLowerCase().includes(q) ||
            item.barang.kode_barang.toLowerCase().includes(q) ||
            item.user.name.toLowerCase().includes(q);
        const matchesStatus = item.status === selectedStatus.value;
        return matchesSearch && matchesStatus;
    });
});

const filteredPeminjaman = computed(() => {
    return props.peminjaman.filter(item => {
        const q = searchQuery.value.toLowerCase();
        const matchesSearch = item.barang.nama_barang.toLowerCase().includes(q) ||
            item.barang.kode_barang.toLowerCase().includes(q) ||
            item.user.name.toLowerCase().includes(q);
        const matchesStatus = item.status === selectedStatus.value;
        return matchesSearch && matchesStatus;
    });
});

const groupedPermintaan = computed(() => {
    const groups = new Map<number, Permintaan[]>();
    console.log(filteredPermintaan.value);
    filteredPermintaan.value.forEach(item => {
        if (!groups.has(item.user_id)) {
            groups.set(item.user_id, []);
        }
        groups.get(item.user_id)?.push(item);
    });
    console.log(groups);
    return Array.from(groups.entries()).map(([userId, items]) => ({
        user: items[0].user,
        items
    }));
});

const groupedPeminjaman = computed(() => {
    const groups = new Map<number, Peminjaman[]>();
    console.log(filteredPeminjaman.value);
    filteredPeminjaman.value.forEach(item => {
        if (!groups.has(item.user_id)) {
            groups.set(item.user_id, []);
        }
        groups.get(item.user_id)?.push(item);
    });
    console.log(groups);
    return Array.from(groups.entries()).map(([userId, items]) => ({
        user: items[0].user,
        items
    }));
});
</script>

<template>
    <Head title="Approval" />
    <AppLayout>
        <div class="p-4 md:p-8 space-y-8 bg-gradient-to-br from-blue-50 via-white to-indigo-100 min-h-screen">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-extrabold text-indigo-900 mb-1 tracking-tight drop-shadow-sm">Persetujuan</h1>
                    <p class="text-gray-500 text-base">Kelola permintaan dan peminjaman barang dengan mudah dan cepat.</p>
                </div>
                <div class="flex gap-2 items-center">
                    <div class="relative">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari nama barang, kode, atau user..."
                            class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition w-64 bg-white shadow-sm"
                        />
                        <Search class="absolute left-2 top-2.5 w-5 h-5 text-gray-400" />
                    </div>
                    <select
                        v-model="selectedStatus"
                        class="py-2 px-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition bg-white shadow-sm text-gray-700"
                    >
                        <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
                            {{ opt.label }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="w-full">
                <div class="flex w-full mb-6 rounded-lg overflow-hidden shadow-sm border border-gray-200 bg-white">
                    <button 
                        class="flex-1 px-4 py-3 text-lg font-semibold transition focus:outline-none"
                        :class="activeTab === 'permintaan' ? 'bg-gradient-to-r from-indigo-600 to-blue-500 text-white shadow' : 'bg-white text-indigo-700 hover:bg-indigo-50'"
                        @click="activeTab = 'permintaan'"
                    >
                        Permintaan
                    </button>
                    <button 
                        class="flex-1 px-4 py-3 text-lg font-semibold transition focus:outline-none"
                        :class="activeTab === 'peminjaman' ? 'bg-gradient-to-r from-indigo-600 to-blue-500 text-white shadow' : 'bg-white text-indigo-700 hover:bg-indigo-50'"
                        @click="activeTab = 'peminjaman'"
                    >
                        Peminjaman
                    </button>
                </div>

                <transition name="fade" mode="out-in">
                <div v-if="activeTab === 'permintaan'" key="permintaan">
                    <!-- TABEL PERMINTAAN (untuk md ke atas) -->
                    <div class="bg-white rounded-2xl border shadow-lg hidden md:block">
                        <div v-if="groupedPermintaan.length === 0" class="p-8 text-center text-gray-400">
                            <span class="text-2xl">😕</span>
                            <div>Tidak ada permintaan ditemukan.</div>
                        </div>
                        <div v-for="group in groupedPermintaan" :key="group.user.id" class="border-b last:border-b-0">
                            <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b flex items-center gap-3">
                                <Avatar class="w-14 h-14 ring-2 ring-indigo-200 shadow">
                                    <AvatarImage v-if="group.user.photo_url" :src="group.user.photo_url" alt="User Photo" />
                                    <AvatarFallback>
                                        {{ getInitials(group.user.name) }}
                                    </AvatarFallback>
                                </Avatar>
                                <div>
                                    <div class="font-bold text-indigo-800 text-lg">{{ group.user.name }}</div>
                                    <div class="text-xs text-gray-500">{{ group.user.email }}</div>
                                </div>
                            </div>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead class="text-indigo-700">Tanggal</TableHead>
                                        <TableHead class="text-indigo-700">Barang</TableHead>
                                        <TableHead class="text-indigo-700">Kode</TableHead>
                                        <TableHead class="text-center text-indigo-700">Jumlah</TableHead>
                                        <TableHead class="text-indigo-700">Status</TableHead>
                                        <TableHead class="text-indigo-700">Keterangan</TableHead>
                                        <TableHead class="text-right text-indigo-700">Aksi</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="item in group.items" :key="item.id" class="hover:bg-indigo-50 transition">
                                        <TableCell class="text-sm">
                                            <div class="flex items-center gap-1">
                                                <Calendar class="w-4 h-4 text-indigo-400" />
                                                <span class="font-medium">{{ new Date(item.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) }}</span>
                                            </div>
                                        </TableCell>
                                        <TableCell class="font-semibold text-indigo-900">{{ item.barang.nama_barang }}</TableCell>
                                        <TableCell class="font-medium text-indigo-700">{{ item.barang.kode_barang }}</TableCell>
                                        <TableCell class="text-center font-bold text-indigo-700">{{ item.jumlah }}</TableCell>
                                        <TableCell>
                                            <Badge :variant="getStatusVariant(item.status)">
                                                {{ getStatusText(item.status) }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="max-w-xs truncate text-gray-600">
                                            {{ item.keterangan || '-' }}
                                        </TableCell>
                                        <TableCell class="text-right space-x-2">
                                            <transition name="approve-anim" mode="out-in">
                                                <Button 
                                                    v-if="item.status === 'pending' && animatingPermintaan[item.id] !== 'approve' && animatingPermintaan[item.id] !== 'reject'"
                                                    variant="default" 
                                                    size="sm"
                                                    class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white shadow"
                                                    @click="handleApprovePermintaan(item.id)"
                                                    key="approve"
                                                >
                                                    <span class="font-semibold flex items-center gap-1">
                                                        <CheckCircle class="w-4 h-4 animate-pulse" /> Terima
                                                    </span>
                                                </Button>
                                                <span
                                                    v-else-if="animatingPermintaan[item.id] === 'approve'"
                                                    class="inline-flex items-center gap-1 px-3 py-2 rounded bg-green-500 text-white font-semibold animate-approve"
                                                    key="approved"
                                                >
                                                    <CheckCircle class="w-5 h-5 animate-bounce" /> Diterima!
                                                </span>
                                            </transition>
                                            <transition name="reject-anim" mode="out-in">
                                                <Button 
                                                    v-if="item.status === 'pending' && animatingPermintaan[item.id] !== 'approve' && animatingPermintaan[item.id] !== 'reject'"
                                                    variant="destructive" 
                                                    size="sm"
                                                    class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white shadow"
                                                    @click="handleRejectPermintaan(item.id)"
                                                    key="reject"
                                                >
                                                    <span class="font-semibold flex items-center gap-1">
                                                        <XCircle class="w-4 h-4 animate-pulse" /> Tolak
                                                    </span>
                                                </Button>
                                                <span
                                                    v-else-if="animatingPermintaan[item.id] === 'reject'"
                                                    class="inline-flex items-center gap-1 px-3 py-2 rounded bg-red-500 text-white font-semibold animate-reject"
                                                    key="rejected"
                                                >
                                                    <XCircle class="w-5 h-5 animate-shake" /> Ditolak!
                                                </span>
                                            </transition>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </div>

                    <!-- CARD PERMINTAAN (untuk hp/sm) -->
                    <div class="space-y-6 md:hidden">
                        <div v-if="groupedPermintaan.length === 0" class="p-8 text-center text-gray-400 bg-white rounded-2xl shadow">
                            <span class="text-2xl">😕</span>
                            <div>Tidak ada permintaan ditemukan.</div>
                        </div>
                        <div v-for="group in groupedPermintaan" :key="group.user.id" class="border rounded-2xl overflow-hidden shadow bg-white">
                            <div class="bg-gradient-to-r from-indigo-50 to-white px-4 py-3 border-b flex items-center gap-3">
                                <Avatar class="w-12 h-12 ring-2 ring-indigo-200 shadow">
                                    <AvatarImage v-if="group.user.photo_url" :src="group.user.photo_url" alt="User Photo" />
                                    <AvatarFallback>
                                        {{ getInitials(group.user.name) }}
                                    </AvatarFallback>
                                </Avatar>
                                <div>
                                    <div class="font-bold text-indigo-800">{{ group.user.name }}</div>
                                    <div class="text-xs text-gray-500">{{ group.user.email }}</div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-indigo-50 to-white px-4 py-3 border-b flex items-center gap-3"></div>
                            <div class="divide-y">
                                <div
                                    v-for="item in group.items"
                                    :key="item.id"
                                    class="p-4"
                                >
                                    <div class="flex justify-between items-center mb-2">
                                        <h2 class="font-semibold text-lg text-indigo-900">{{ item.barang.nama_barang }}</h2>
                                        <Badge :variant="getStatusVariant(item.status)">
                                            {{ getStatusText(item.status) }}
                                        </Badge>
                                    </div>
                                    <div class="flex flex-wrap gap-2 text-sm text-gray-600 mb-2">
                                        <span class="bg-indigo-50 px-2 py-1 rounded font-medium">Kode: {{ item.barang.kode_barang }}</span>
                                        <span class="bg-indigo-50 px-2 py-1 rounded font-medium">Jumlah: {{ item.jumlah }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-gray-500 mb-1">
                                        <Calendar class="w-4 h-4 text-indigo-400" />
                                        <span><strong>Tanggal:</strong> {{ new Date(item.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) }}</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mb-3"><strong>Keterangan:</strong> {{ item.keterangan || '-' }}</p>
                                    
                                    <div class="flex gap-2 justify-end">
                                        <transition name="approve-anim" mode="out-in">
                                            <Button 
                                                v-if="item.status === 'pending' && animatingPermintaan[item.id] !== 'approve' && animatingPermintaan[item.id] !== 'reject'"
                                                variant="default" 
                                                size="sm"
                                                class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white shadow"
                                                @click="handleApprovePermintaan(item.id)"
                                                key="approve"
                                            >
                                                <span class="font-semibold flex items-center gap-1">
                                                    <CheckCircle class="w-4 h-4 animate-pulse" /> Terima
                                                </span>
                                            </Button>
                                            <span
                                                v-else-if="animatingPermintaan[item.id] === 'approve'"
                                                class="inline-flex items-center gap-1 px-3 py-2 rounded bg-green-500 text-white font-semibold animate-approve"
                                                key="approved"
                                            >
                                                <CheckCircle class="w-5 h-5 animate-bounce" /> Diterima!
                                            </span>
                                        </transition>
                                        <transition name="reject-anim" mode="out-in">
                                            <Button 
                                                v-if="item.status === 'pending' && animatingPermintaan[item.id] !== 'approve' && animatingPermintaan[item.id] !== 'reject'"
                                                variant="destructive" 
                                                size="sm"
                                                class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white shadow"
                                                @click="handleRejectPermintaan(item.id)"
                                                key="reject"
                                            >
                                                <span class="font-semibold flex items-center gap-1">
                                                    <XCircle class="w-4 h-4 animate-pulse" /> Tolak
                                                </span>
                                            </Button>
                                            <span
                                                v-else-if="animatingPermintaan[item.id] === 'reject'"
                                                class="inline-flex items-center gap-1 px-3 py-2 rounded bg-red-500 text-white font-semibold animate-reject"
                                                key="rejected"
                                            >
                                                <XCircle class="w-5 h-5 animate-shake" /> Ditolak!
                                            </span>
                                        </transition>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </transition>

                <transition name="fade" mode="out-in">
                <div v-if="activeTab === 'peminjaman'" key="peminjaman">
                    <!-- TABEL PEMINJAMAN (untuk md ke atas) -->
                    <div class="bg-white rounded-2xl border shadow-lg hidden md:block">
                        <div v-if="groupedPeminjaman.length === 0" class="p-8 text-center text-gray-400">
                            <span class="text-2xl">📦</span>
                            <div>Tidak ada peminjaman ditemukan.</div>
                        </div>
                        <div v-for="group in groupedPeminjaman" :key="group.user.id" class="border-b last:border-b-0">
                            <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b flex items-center gap-3">
                                <Avatar class="w-14 h-14 ring-2 ring-indigo-200 shadow">
                                    <AvatarImage v-if="group.user.photo_url" :src="group.user.photo_url" alt="User Photo" />
                                    <AvatarFallback>
                                        {{ getInitials(group.user.name) }}
                                    </AvatarFallback>
                                </Avatar>
                                <div>
                                    <div class="font-bold text-indigo-800 text-lg">{{ group.user.name }}</div>
                                    <div class="text-xs text-gray-500">{{ group.user.email }}</div>
                                </div>
                            </div>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead class="text-indigo-700">Tanggal</TableHead>
                                        <TableHead class="text-indigo-700">Barang</TableHead>
                                        <TableHead class="text-indigo-700">Kode</TableHead>
                                        <TableHead class="text-center text-indigo-700">Jumlah</TableHead>
                                        <TableHead class="text-indigo-700">Tanggal Pengembalian</TableHead>
                                        <TableHead class="text-indigo-700">Status</TableHead>
                                        <TableHead class="text-indigo-700">Keterangan</TableHead>
                                        <TableHead class="text-right text-indigo-700">Aksi</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="item in group.items" :key="item.id" class="hover:bg-indigo-50 transition">
                                        <TableCell class="text-sm">
                                            <div class="flex items-center gap-1">
                                                <Calendar class="w-4 h-4 text-indigo-400" />
                                                <span class="font-medium">{{ new Date(item.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) }}</span>
                                            </div>
                                        </TableCell>
                                        <TableCell class="font-semibold text-indigo-900">{{ item.barang.nama_barang }}</TableCell>
                                        <TableCell class="font-medium text-indigo-700">{{ item.barang.kode_barang }}</TableCell>
                                        <TableCell class="text-center font-bold text-indigo-700">{{ item.jumlah }}</TableCell>
                                        <TableCell class="text-sm">
                                            <div class="flex items-center gap-1">
                                                <Calendar class="w-4 h-4 text-indigo-400" />
                                                <span>
                                                    {{ item.tanggal_pengembalian ? new Date(item.tanggal_pengembalian).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-' }}
                                                </span>
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <Badge :variant="getStatusVariant(item.status)">
                                                {{ getStatusText(item.status) }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="max-w-xs truncate text-gray-600">
                                            {{ item.keterangan || '-' }}
                                        </TableCell>
                                        <TableCell class="text-right space-x-2">
                                            <transition name="approve-anim" mode="out-in">
                                                <Button 
                                                    v-if="item.status === 'pending' && animatingPeminjaman[item.id] !== 'approve' && animatingPeminjaman[item.id] !== 'reject'"
                                                    variant="default" 
                                                    size="sm"
                                                    class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white shadow"
                                                    @click="handleApprovePeminjaman(item.id)"
                                                    key="approve"
                                                >
                                                    <span class="font-semibold flex items-center gap-1">
                                                        <CheckCircle class="w-4 h-4 animate-pulse" /> Terima
                                                    </span>
                                                </Button>
                                                <span
                                                    v-else-if="animatingPeminjaman[item.id] === 'approve'"
                                                    class="inline-flex items-center gap-1 px-3 py-2 rounded bg-green-500 text-white font-semibold animate-approve"
                                                    key="approved"
                                                >
                                                    <CheckCircle class="w-5 h-5 animate-bounce" /> Diterima!
                                                </span>
                                            </transition>
                                            <transition name="reject-anim" mode="out-in">
                                                <Button 
                                                    v-if="item.status === 'pending' && animatingPeminjaman[item.id] !== 'approve' && animatingPeminjaman[item.id] !== 'reject'"
                                                    variant="destructive" 
                                                    size="sm"
                                                    class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white shadow"
                                                    @click="handleRejectPeminjaman(item.id)"
                                                    key="reject"
                                                >
                                                    <span class="font-semibold flex items-center gap-1">
                                                        <XCircle class="w-4 h-4 animate-pulse" /> Tolak
                                                    </span>
                                                </Button>
                                                <span
                                                    v-else-if="animatingPeminjaman[item.id] === 'reject'"
                                                    class="inline-flex items-center gap-1 px-3 py-2 rounded bg-red-500 text-white font-semibold animate-reject"
                                                    key="rejected"
                                                >
                                                    <XCircle class="w-5 h-5 animate-shake" /> Ditolak!
                                                </span>
                                            </transition>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </div>

                    <!-- CARD PEMINJAMAN (untuk hp/sm) -->
                    <div class="space-y-6 md:hidden">
                        <div v-if="groupedPeminjaman.length === 0" class="p-8 text-center text-gray-400 bg-white rounded-2xl shadow">
                            <span class="text-2xl">📦</span>
                            <div>Tidak ada peminjaman ditemukan.</div>
                        </div>
                        <div v-for="group in groupedPeminjaman" :key="group.user.id" class="border rounded-2xl overflow-hidden shadow bg-white">
                            <div class="bg-gradient-to-r from-indigo-50 to-white px-4 py-3 border-b flex items-center gap-3">
                                <Avatar class="w-12 h-12 ring-2 ring-indigo-200 shadow">
                                    <AvatarImage v-if="group.user.photo_url" :src="group.user.photo_url || ''" alt="User Photo" />
                                    <AvatarFallback>
                                        {{ getInitials(group.user.name) }}
                                    </AvatarFallback>
                                </Avatar>
                                <div>
                                    <div class="font-bold text-indigo-800">{{ group.user.name }}</div>
                                    <div class="text-xs text-gray-500">{{ group.user.email }}</div>
                                    
                                </div>
                            </div>
                            <div class="divide-y">
                                <div
                                    v-for="item in group.items"
                                    :key="item.id"
                                    class="p-4"
                                >
                                    <div class="flex justify-between items-center mb-2">
                                        <h2 class="font-semibold text-lg text-indigo-900">{{ item.barang.nama_barang }}</h2>
                                        <Badge :variant="getStatusVariant(item.status)">
                                            {{ getStatusText(item.status) }}
                                        </Badge>
                                    </div>
                                    <div class="flex flex-wrap gap-2 text-sm text-gray-600 mb-2">
                                        <span class="bg-indigo-50 px-2 py-1 rounded font-medium">Kode: {{ item.barang.kode_barang }}</span>
                                        <span class="bg-indigo-50 px-2 py-1 rounded font-medium">Jumlah: {{ item.jumlah }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-gray-500 mb-1">
                                        <Calendar class="w-4 h-4 text-indigo-400" />
                                        <span><strong>Tanggal:</strong> {{ new Date(item.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-gray-500 mb-1">
                                        <Calendar class="w-4 h-4 text-indigo-400" />
                                        <span><strong>Tanggal Pengembalian:</strong> {{ item.tanggal_pengembalian ? new Date(item.tanggal_pengembalian).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-' }}</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mb-3"><strong>Keterangan:</strong> {{ item.keterangan || '-' }}</p>
                                    
                                    <div class="flex gap-2 justify-end">
                                        <transition name="approve-anim" mode="out-in">
                                            <Button 
                                                v-if="item.status === 'pending' && animatingPeminjaman[item.id] !== 'approve' && animatingPeminjaman[item.id] !== 'reject'"
                                                variant="default" 
                                                size="sm"
                                                class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white shadow"
                                                @click="handleApprovePeminjaman(item.id)"
                                                key="approve"
                                            >
                                                <span class="font-semibold flex items-center gap-1">
                                                    <CheckCircle class="w-4 h-4 animate-pulse" /> Terima
                                                </span>
                                            </Button>
                                            <span
                                                v-else-if="animatingPeminjaman[item.id] === 'approve'"
                                                class="inline-flex items-center gap-1 px-3 py-2 rounded bg-green-500 text-white font-semibold animate-approve"
                                                key="approved"
                                            >
                                                <CheckCircle class="w-5 h-5 animate-bounce" /> Diterima!
                                            </span>
                                        </transition>
                                        <transition name="reject-anim" mode="out-in">
                                            <Button 
                                                v-if="item.status === 'pending' && animatingPeminjaman[item.id] !== 'approve' && animatingPeminjaman[item.id] !== 'reject'"
                                                variant="destructive" 
                                                size="sm"
                                                class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white shadow"
                                                @click="handleRejectPeminjaman(item.id)"
                                                key="reject"
                                            >
                                                <span class="font-semibold flex items-center gap-1">
                                                    <XCircle class="w-4 h-4 animate-pulse" /> Tolak
                                                </span>
                                            </Button>
                                            <span
                                                v-else-if="animatingPeminjaman[item.id] === 'reject'"
                                                class="inline-flex items-center gap-1 px-3 py-2 rounded bg-red-500 text-white font-semibold animate-reject"
                                                key="rejected"
                                            >
                                                <XCircle class="w-5 h-5 animate-shake" /> Ditolak!
                                            </span>
                                        </transition>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </transition>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Animasi untuk tombol Terima */
@keyframes approve-bounce {
  0% { transform: scale(1); }
  30% { transform: scale(1.15); }
  60% { transform: scale(0.95); }
  100% { transform: scale(1); }
}
.animate-approve {
  animation: approve-bounce 0.6s;
}

/* Animasi untuk tombol Tolak */
@keyframes reject-shake {
  0% { transform: translateX(0); }
  20% { transform: translateX(-6px); }
  40% { transform: translateX(6px); }
  60% { transform: translateX(-4px); }
  80% { transform: translateX(4px); }
  100% { transform: translateX(0); }
}
.animate-reject {
  animation: reject-shake 0.6s;
}

/* Icon shake */
@keyframes icon-shake {
  0% { transform: rotate(0deg);}
  20% { transform: rotate(-15deg);}
  40% { transform: rotate(15deg);}
  60% { transform: rotate(-10deg);}
  80% { transform: rotate(10deg);}
  100% { transform: rotate(0deg);}
}
.animate-shake {
  animation: icon-shake 0.6s;
}

/* Icon bounce */
@keyframes icon-bounce {
  0% { transform: scale(1);}
  30% { transform: scale(1.3);}
  60% { transform: scale(0.9);}
  100% { transform: scale(1);}
}
.animate-bounce {
  animation: icon-bounce 0.6s;
}

/* Animasi transition wrapper */
.approve-anim-enter-active, .approve-anim-leave-active,
.reject-anim-enter-active, .reject-anim-leave-active {
  transition: all 0.3s;
}
.approve-anim-enter-from, .approve-anim-leave-to,
.reject-anim-enter-from, .reject-anim-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
