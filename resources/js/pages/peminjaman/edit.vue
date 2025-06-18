<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft, Package, CalendarDays } from 'lucide-vue-next';
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
    status: 'pending' | 'approved' | 'rejected' | 'completed';
    created_at: string;
    tanggal_peminjaman: string;
    tanggal_pengembalian: string;
    due_date: string;
    user: User;
    barang: Barang;
}

const props = defineProps<{
    peminjaman: Peminjaman;
    barang: Barang[];
}>();

const form = useForm({
    barang_id: props.peminjaman.barang_id.toString(),
    jumlah: props.peminjaman.jumlah.toString(),
    keterangan: props.peminjaman.keterangan || '',
    tanggal_pengembalian: props.peminjaman.tanggal_pengembalian,
    due_date: props.peminjaman.due_date,
});

const submitPeminjaman = () => {
    form.put(route('peminjaman.update', props.peminjaman.id));
};
</script>

<template>
    <Head title="Edit Peminjaman" />
    <AppLayout>
        <div class="p-6">
            <div class="mb-6">
                <div class="flex items-center gap-4 mb-4">
                    <Link :href="route('peminjaman.index')" class="text-muted-foreground hover:text-foreground">
                        <ArrowLeft class="h-5 w-5" />
                    </Link>
                    <h1 class="text-2xl font-semibold flex items-center gap-2">
                        <Package class="h-6 w-6" />
                        Edit Peminjaman Barang
                    </h1>
                </div>
                <p class="text-muted-foreground">Edit detail peminjaman barang</p>
            </div>

            <div class="max-w-2xl mx-auto">
                <div class="bg-white rounded-lg border p-6">
                    <h2 class="text-lg font-medium mb-6">Form Edit Peminjaman</h2>
                    
                    <!-- Informasi Peminjaman -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                        <h3 class="font-medium text-gray-900 mb-2">Informasi Peminjaman:</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium">Peminjam:</span>
                                <p>{{ peminjaman?.user?.name || 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="font-medium">Tanggal Dibuat:</span>
                                <p>{{ new Date(peminjaman?.created_at).toLocaleDateString('id-ID') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <form @submit.prevent="submitPeminjaman" class="space-y-6">
                        <!-- Pilih Barang -->
                        <div class="space-y-2">
                            <Label for="barang_id">Barang *</Label>
                            <select
                                id="barang_id"
                                v-model="form.barang_id"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                required
                            >
                                <option value="">Pilih barang</option>
                                <option v-for="item in barang" :key="item.id" :value="item.id">
                                    {{ item.nama_barang }} ({{ item.kode_barang }}) - Stok: {{ item.stok }}
                                </option>
                            </select>
                            <p v-if="form.errors.barang_id" class="text-sm text-red-500">
                                {{ form.errors.barang_id }}
                            </p>
                        </div>

                        <!-- Jumlah -->
                        <div class="space-y-2">
                            <Label for="jumlah">Jumlah *</Label>
                            <Input
                                id="jumlah"
                                v-model="form.jumlah"
                                type="number"
                                min="1"
                                placeholder="Masukkan jumlah"
                                required
                            />
                            <p v-if="form.errors.jumlah" class="text-sm text-red-500">
                                {{ form.errors.jumlah }}
                            </p>
                        </div>

                        <!-- Tenggat Waktu -->
                        <div class="space-y-2">
                            <Label for="due_date">Tenggat Waktu *</Label>
                            <div class="relative">
                                <Input
                                    id="due_date"
                                    v-model="form.due_date"
                                    type="date"
                                    class="pr-10"
                                    required
                                />
                                <CalendarDays class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" />
                            </div>
                            <p v-if="form.errors.due_date" class="text-sm text-red-500">
                                {{ form.errors.due_date }}
                            </p>
                        </div>

                        <!-- Tanggal Kembali -->
                        <div class="space-y-2">
                            <Label for="tanggal_pengembalian">Tanggal Kembali *</Label>
                            <div class="relative">
                                <Input
                                    id="tanggal_pengembalian"
                                    v-model="form.tanggal_pengembalian"
                                    type="date"
                                    class="pr-10"
                                    required
                                />
                                <CalendarDays class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" />
                            </div>
                            <p v-if="form.errors.tanggal_pengembalian" class="text-sm text-red-500">
                                {{ form.errors.tanggal_pengembalian }}
                            </p>
                        </div>

                        <!-- Keterangan -->
                        <div class="space-y-2">
                            <Label for="keterangan">Keterangan (Opsional)</Label>
                            <textarea
                                id="keterangan"
                                v-model="form.keterangan"
                                rows="4"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                            />
                            <p v-if="form.errors.keterangan" class="text-sm text-red-500">
                                {{ form.errors.keterangan }}
                            </p>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="flex gap-4">
                            <Button 
                                type="submit" 
                                :disabled="form.processing"
                                class="flex-1"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                            </Button>
                            <Button 
                                type="button" 
                                variant="outline"
                                @click="$inertia.visit(route('peminjaman.index'))"
                            >
                                Batal
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template> 