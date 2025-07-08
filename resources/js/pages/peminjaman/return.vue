<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft, Package, CalendarDays, User, RotateCcw } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

interface Barang {
    id: number;
    kode_barang: string;
    nama_barang: string;
    deskripsi: string;
    kategori: string;
    stok: number;
}

interface User {
    id: number;
    name: string;
}

interface Peminjaman {
    id: number;
    user_id: number;
    barang_id: number;
    jumlah: number;
    keterangan: string;
    status: 'pending' | 'approved' | 'rejected' | 'returned';
    created_at: string;
    tanggal_peminjaman: string;
    tanggal_pengembalian: string | null;
    due_date: string;
    user: User;
    barang: Barang;
}

const props = defineProps<{
    peminjaman: Peminjaman;
}>();

const form = useForm({
    tanggal_pengembalian: new Date().toISOString().split('T')[0],
    kondisi_barang: 'baik',
    catatan_pengembalian: '',
});

const submitReturn = () => {
    form.post(route('peminjaman.process-return', props.peminjaman.id));
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Pengembalian Peminjaman" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link :href="route('peminjaman.index')" class="text-gray-600 hover:text-gray-800">
                    <ArrowLeft class="w-5 h-5" />
                </Link>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Pengembalian Peminjaman</h1>
                    <p class="text-sm text-gray-500 mt-1">Proses pengembalian barang yang dipinjam</p>
                </div>
            </div>

            <!-- Informasi Peminjaman -->
            <div class="bg-white rounded-lg border p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Peminjaman</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informasi Peminjam -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <User class="w-4 h-4 text-gray-500" />
                            <span class="font-medium text-gray-700">Peminjam:</span>
                        </div>
                        <p class="text-gray-600 ml-6">{{ peminjaman.user.name }}</p>
                    </div>

                    <!-- Informasi Barang -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <Package class="w-4 h-4 text-gray-500" />
                            <span class="font-medium text-gray-700">Barang:</span>
                        </div>
                        <div class="ml-6 space-y-1">
                            <p class="text-gray-600">{{ peminjaman.barang.nama_barang }}</p>
                            <p class="text-sm text-gray-500">Kode: {{ peminjaman.barang.kode_barang }}</p>
                            <p class="text-sm text-gray-500">Jumlah: {{ peminjaman.jumlah }}</p>
                        </div>
                    </div>

                    <!-- Tanggal Peminjaman -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <CalendarDays class="w-4 h-4 text-gray-500" />
                            <span class="font-medium text-gray-700">Tanggal Pinjam:</span>
                        </div>
                        <p class="text-gray-600 ml-6">{{ formatDate(peminjaman.tanggal_peminjaman) }}</p>
                    </div>

                    <!-- Tanggal Jatuh Tempo -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <CalendarDays class="w-4 h-4 text-gray-500" />
                            <span class="font-medium text-gray-700">Jatuh Tempo:</span>
                        </div>
                        <p class="text-gray-600 ml-6">{{ formatDate(peminjaman.due_date) }}</p>
                    </div>
                </div>

                <!-- Keterangan Peminjaman -->
                <div v-if="peminjaman.keterangan" class="space-y-2">
                    <span class="font-medium text-gray-700">Keterangan Peminjaman:</span>
                    <p class="text-gray-600">{{ peminjaman.keterangan }}</p>
                </div>
            </div>

            <!-- Form Pengembalian -->
            <div class="bg-white rounded-lg border p-6">
                <div class="flex items-center gap-2 mb-6">
                    <RotateCcw class="w-5 h-5 text-blue-600" />
                    <h2 class="text-lg font-semibold text-gray-800">Form Pengembalian</h2>
                </div>

                <form @submit.prevent="submitReturn" class="space-y-6">
                    <!-- Tanggal Pengembalian -->
                    <div class="space-y-2">
                        <Label for="tanggal_pengembalian">Tanggal Pengembalian *</Label>
                        <Input
                            id="tanggal_pengembalian"
                            v-model="form.tanggal_pengembalian"
                            type="date"
                            :min="peminjaman.tanggal_peminjaman"
                            required
                        />
                        <p v-if="form.errors.tanggal_pengembalian" class="text-sm text-red-500">
                            {{ form.errors.tanggal_pengembalian }}
                        </p>
                    </div>

                    <!-- Kondisi Barang -->
                    <div class="space-y-2">
                        <Label for="kondisi_barang">Kondisi Barang *</Label>
                        <select
                            id="kondisi_barang"
                            v-model="form.kondisi_barang"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                            required
                        >
                            <option value="baik">Baik (100% stok dikembalikan)</option>
                            <option value="rusak_ringan">Rusak Ringan (50% stok dikembalikan)</option>
                            <option value="rusak_berat">Rusak Berat (0% stok dikembalikan)</option>
                        </select>
                        <p v-if="form.errors.kondisi_barang" class="text-sm text-red-500">
                            {{ form.errors.kondisi_barang }}
                        </p>
                        <p class="text-sm text-gray-600">
                            <strong>Keterangan:</strong><br>
                            • <strong>Baik:</strong> Barang dalam kondisi baik, 100% stok akan dikembalikan<br>
                            • <strong>Rusak Ringan:</strong> Barang sedikit rusak, 50% stok akan dikembalikan<br>
                            • <strong>Rusak Berat:</strong> Barang rusak parah, tidak ada stok yang dikembalikan
                        </p>
                    </div>

                    <!-- Catatan Pengembalian -->
                    <div class="space-y-2">
                        <Label for="catatan_pengembalian">Catatan Pengembalian</Label>
                        <Textarea
                            id="catatan_pengembalian"
                            v-model="form.catatan_pengembalian"
                            placeholder="Masukkan catatan pengembalian (opsional)"
                            :rows="3"
                        />
                        <p v-if="form.errors.catatan_pengembalian" class="text-sm text-red-500">
                            {{ form.errors.catatan_pengembalian }}
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-4">
                        <Button 
                            type="submit" 
                            :disabled="form.processing"
                            class="flex-1"
                        >
                            <RotateCcw class="w-4 h-4 mr-2" />
                            {{ form.processing ? 'Memproses...' : 'Proses Pengembalian' }}
                        </Button>
                        <Link :href="route('peminjaman.index')">
                            <Button type="button" variant="outline">
                                Batal
                            </Button>
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template> 