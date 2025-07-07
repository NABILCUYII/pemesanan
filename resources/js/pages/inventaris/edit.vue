<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, Package, CalendarDays, MapPin } from 'lucide-vue-next';
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

interface Inventaris {
    id: number;
    user_id: number;
    barang_id: number;
    nomor_inventaris: string;
    jumlah: number;
    keterangan: string;
    status: 'pending' | 'approved' | 'in_progress' | 'rejected' | 'returned' | 'overdue' | 'maintenance';
    created_at: string;
    tanggal_peminjaman: string;
    tanggal_pengembalian: string;
    due_date: string;
    lokasi_peminjaman: string;
    user: User;
    barang: Barang;
}

const props = defineProps<{
    inventaris: Inventaris;
    barang: Barang[];
}>();

const form = useForm({
    barang_id: props.inventaris.barang_id.toString(),
    jumlah: props.inventaris.jumlah.toString(),
    keterangan: props.inventaris.keterangan || '',
    lokasi_peminjaman: props.inventaris.lokasi_peminjaman || '',
    due_date: props.inventaris.due_date,
});

const submitInventaris = () => {
    form.put(route('inventaris.update', props.inventaris.id));
};
</script>

<template>
    <Head title="Edit Inventaris" />
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
                        Edit Inventaris
                    </h1>
                    <p class="text-gray-600">Edit permintaan peminjaman inventaris</p>
                    <p class="text-sm text-gray-500">Nomor: {{ inventaris.nomor_inventaris }}</p>
                </div>
            </div>

            <!-- Form -->
            <div class="max-w-2xl">
                <form @submit.prevent="submitInventaris" class="space-y-6">
                    <!-- Barang Selection -->
                    <div class="space-y-2">
                        <Label for="barang_id">Pilih Barang *</Label>
                        <Select v-model="form.barang_id" required>
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih barang yang akan dipinjam" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="item in barang" :key="item.id" :value="item.id.toString()">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ item.nama_barang }}</span>
                                        <span class="text-sm text-gray-500">{{ item.kode_barang }} - Stok: {{ item.stok }}</span>
                                    </div>
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.barang_id" class="text-sm text-red-600">{{ form.errors.barang_id }}</p>
                    </div>

                    <!-- Jumlah -->
                    <div class="space-y-2">
                        <Label for="jumlah">Jumlah *</Label>
                        <Input
                            id="jumlah"
                            v-model="form.jumlah"
                            type="number"
                            min="1"
                            placeholder="Masukkan jumlah yang dipinjam"
                            required
                        />
                        <p v-if="form.errors.jumlah" class="text-sm text-red-600">{{ form.errors.jumlah }}</p>
                    </div>

                    <!-- Lokasi Peminjaman -->
                    <div class="space-y-2">
                        <Label for="lokasi_peminjaman">Lokasi Peminjaman</Label>
                        <div class="relative">
                            <MapPin class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <Input
                                id="lokasi_peminjaman"
                                v-model="form.lokasi_peminjaman"
                                type="text"
                                placeholder="Contoh: Ruang Meeting, Lab Komputer, dll."
                                class="pl-10"
                            />
                        </div>
                        <p v-if="form.errors.lokasi_peminjaman" class="text-sm text-red-600">{{ form.errors.lokasi_peminjaman }}</p>
                    </div>

                    <!-- Due Date -->
                    <div class="space-y-2">
                        <Label for="due_date">Tanggal Jatuh Tempo *</Label>
                        <div class="relative">
                            <CalendarDays class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <Input
                                id="due_date"
                                v-model="form.due_date"
                                type="date"
                                :min="new Date().toISOString().split('T')[0]"
                                placeholder="Pilih tanggal jatuh tempo"
                                class="pl-10"
                                required
                            />
                        </div>
                        <p v-if="form.errors.due_date" class="text-sm text-red-600">{{ form.errors.due_date }}</p>
                    </div>

                    <!-- Keterangan -->
                    <div class="space-y-2">
                        <Label for="keterangan">Keterangan</Label>
                        <Textarea
                            id="keterangan"
                            v-model="form.keterangan"
                            placeholder="Jelaskan tujuan peminjaman inventaris..."
                            rows="4"
                        />
                        <p v-if="form.errors.keterangan" class="text-sm text-red-600">{{ form.errors.keterangan }}</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-4">
                        <Button type="submit" :disabled="form.processing" class="flex-1">
                            <Package class="w-4 h-4 mr-2" />
                            {{ form.processing ? 'Menyimpan...' : 'Update Inventaris' }}
                        </Button>
                        <Link :href="route('inventaris.index')">
                            <Button type="button" variant="outline">
                                Batal
                            </Button>
                        </Link>
                    </div>
                </form>
            </div>

            <!-- Current Info Card -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-800 mb-2">Informasi Saat Ini</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="font-medium text-blue-700">Peminjam:</p>
                        <p class="text-blue-600">{{ inventaris.user.name }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-blue-700">Status:</p>
                        <p class="text-blue-600">{{ inventaris.status }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-blue-700">Tanggal Peminjaman:</p>
                        <p class="text-blue-600">{{ new Date(inventaris.tanggal_peminjaman).toLocaleDateString('id-ID') }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-blue-700">Barang Saat Ini:</p>
                        <p class="text-blue-600">{{ inventaris.barang.nama_barang }} ({{ inventaris.barang.kode_barang }})</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template> 