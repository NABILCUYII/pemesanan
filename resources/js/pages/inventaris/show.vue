<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Package, CalendarDays, MapPin, User, CheckCircle, XCircle, Clock, RotateCcw } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

interface User {
    id: number;
    name: string;
    email: string;
    photo?: string;
}

interface Barang {
    id: number;
    kode_barang: string;
    nama_barang: string;
    deskripsi: string;
    kategori: string;
    stok: number;
}

interface Inventaris {
    id: number;
    nomor_inventaris: string;
    jumlah: number;
    keterangan: string;
    lokasi_peminjaman: string;
    tanggal_peminjaman: string;
    tanggal_pengembalian: string;
    due_date: string;
    status: string;
    status_text: string;
    kondisi_barang: string;
    kondisi_barang_text: string;
    catatan_pengembalian: string;
    alasan_approval: string;
    catatan_approval: string;
    approved_at: string;
    returned_at: string;
    started_at: string;
    is_overdue: boolean;
    user: User;
    barang: Barang;
    approvedBy?: User;
    returnedBy?: User;
    startedBy?: User;
}

const props = defineProps<{
    inventaris: Inventaris;
}>();

// Status badge component
const getStatusBadge = (status: string) => {
    const statusConfig = {
        pending: { color: 'bg-yellow-100 text-yellow-800', icon: Clock },
        approved: { color: 'bg-green-100 text-green-800', icon: CheckCircle },
        rejected: { color: 'bg-red-100 text-red-800', icon: XCircle },
        in_progress: { color: 'bg-blue-100 text-blue-800', icon: Package },
        returned: { color: 'bg-purple-100 text-purple-800', icon: RotateCcw }
    };
    
    const config = statusConfig[status as keyof typeof statusConfig] || statusConfig.pending;
    return config;
};

const statusConfig = getStatusBadge(props.inventaris.status);
</script>

<template>
    <Head :title="`Detail Inventaris - ${inventaris.nomor_inventaris}`" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link :href="route('inventaris.index')">
                    <Button variant="outline" size="sm">
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Kembali
                    </Button>
                </Link>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                        <Package class="w-6 h-6 text-purple-600" />
                        Detail Inventaris
                    </h1>
                    <p class="text-gray-600">{{ inventaris.nomor_inventaris }}</p>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="flex items-center gap-2">
                <component :is="statusConfig.icon" class="w-4 h-4" />
                <span :class="`px-3 py-1 rounded-full text-sm font-medium ${statusConfig.color}`">
                    {{ inventaris.status_text }}
                </span>
                <span v-if="inventaris.is_overdue" class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    Terlambat
                </span>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Barang Information -->
                    <div class="bg-white rounded-lg border p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <Package class="w-5 h-5 text-purple-600" />
                            Informasi Barang
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nama Barang:</span>
                                <span class="font-medium">{{ inventaris.barang.nama_barang }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kode Barang:</span>
                                <span class="font-medium">{{ inventaris.barang.kode_barang }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kategori:</span>
                                <span class="font-medium">{{ inventaris.barang.kategori }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jumlah Dipinjam:</span>
                                <span class="font-medium">{{ inventaris.jumlah }} unit</span>
                            </div>
                            <div v-if="inventaris.keterangan" class="flex justify-between">
                                <span class="text-gray-600">Keterangan:</span>
                                <span class="font-medium text-right max-w-xs">{{ inventaris.keterangan }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Peminjaman Information -->
                    <div class="bg-white rounded-lg border p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <CalendarDays class="w-5 h-5 text-blue-600" />
                            Informasi Peminjaman
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal Peminjaman:</span>
                                <span class="font-medium">{{ new Date(inventaris.tanggal_peminjaman).toLocaleDateString('id-ID') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jatuh Tempo:</span>
                                <span class="font-medium">{{ new Date(inventaris.due_date).toLocaleDateString('id-ID') }}</span>
                            </div>
                            <div v-if="inventaris.lokasi_peminjaman" class="flex justify-between">
                                <span class="text-gray-600">Lokasi:</span>
                                <span class="font-medium">{{ inventaris.lokasi_peminjaman }}</span>
                            </div>
                            <div v-if="inventaris.tanggal_pengembalian" class="flex justify-between">
                                <span class="text-gray-600">Tanggal Pengembalian:</span>
                                <span class="font-medium">{{ new Date(inventaris.tanggal_pengembalian).toLocaleDateString('id-ID') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- User Information -->
                    <div class="bg-white rounded-lg border p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <User class="w-5 h-5 text-green-600" />
                            Informasi Peminjam
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nama:</span>
                                <span class="font-medium">{{ inventaris.user.name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email:</span>
                                <span class="font-medium">{{ inventaris.user.email }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Information -->
                    <div v-if="inventaris.approvedBy || inventaris.alasan_approval" class="bg-white rounded-lg border p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <CheckCircle class="w-5 h-5 text-green-600" />
                            Informasi Persetujuan
                        </h3>
                        <div class="space-y-3">
                            <div v-if="inventaris.approvedBy" class="flex justify-between">
                                <span class="text-gray-600">Disetujui Oleh:</span>
                                <span class="font-medium">{{ inventaris.approvedBy.name }}</span>
                            </div>
                            <div v-if="inventaris.approved_at" class="flex justify-between">
                                <span class="text-gray-600">Tanggal Persetujuan:</span>
                                <span class="font-medium">{{ new Date(inventaris.approved_at).toLocaleDateString('id-ID') }}</span>
                            </div>
                            <div v-if="inventaris.alasan_approval" class="flex justify-between">
                                <span class="text-gray-600">Alasan:</span>
                                <span class="font-medium text-right max-w-xs">{{ inventaris.alasan_approval }}</span>
                            </div>
                            <div v-if="inventaris.catatan_approval" class="flex justify-between">
                                <span class="text-gray-600">Catatan:</span>
                                <span class="font-medium text-right max-w-xs">{{ inventaris.catatan_approval }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Return Information -->
                    <div v-if="inventaris.returnedBy || inventaris.kondisi_barang" class="bg-white rounded-lg border p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <RotateCcw class="w-5 h-5 text-purple-600" />
                            Informasi Pengembalian
                        </h3>
                        <div class="space-y-3">
                            <div v-if="inventaris.returnedBy" class="flex justify-between">
                                <span class="text-gray-600">Dikembalikan Oleh:</span>
                                <span class="font-medium">{{ inventaris.returnedBy.name }}</span>
                            </div>
                            <div v-if="inventaris.returned_at" class="flex justify-between">
                                <span class="text-gray-600">Tanggal Pengembalian:</span>
                                <span class="font-medium">{{ new Date(inventaris.returned_at).toLocaleDateString('id-ID') }}</span>
                            </div>
                            <div v-if="inventaris.kondisi_barang" class="flex justify-between">
                                <span class="text-gray-600">Kondisi Barang:</span>
                                <span class="font-medium">{{ inventaris.kondisi_barang_text }}</span>
                            </div>
                            <div v-if="inventaris.catatan_pengembalian" class="flex justify-between">
                                <span class="text-gray-600">Catatan:</span>
                                <span class="font-medium text-right max-w-xs">{{ inventaris.catatan_pengembalian }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 justify-end">
                <Link :href="route('inventaris.index')">
                    <Button variant="outline">
                        Kembali ke Daftar
                    </Button>
                </Link>
                <Link v-if="inventaris.status === 'pending' && (inventaris.user.id === $page.props.auth.user.id || $page.props.auth.user.role === 'admin')" 
                      :href="route('inventaris.edit', inventaris.id)">
                    <Button>
                        Edit
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template> 