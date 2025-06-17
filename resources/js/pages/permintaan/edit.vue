<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft, Package } from 'lucide-vue-next';
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

interface Permintaan {
    id: number;
    user_id: number;
    barang_id: number;
    jumlah: number;
    keterangan: string;
    status: 'pending' | 'approved' | 'rejected' | 'completed';
    created_at: string;
    user: User;
    barang: Barang;
}

const props = defineProps<{
    permintaan: Permintaan;
    barang: Barang[];
}>();

const form = useForm({
    barang_id: props.permintaan.barang_id.toString(),
    jumlah: props.permintaan.jumlah.toString(),
    keterangan: props.permintaan.keterangan || '',
    status: props.permintaan.status
});

const submitPermintaan = () => {
    form.put(route('permintaan.update', props.permintaan.id));
};
</script>

<template>
    <Head title="Edit Permintaan" />
    <AppLayout>
        <div class="p-6">
            <div class="mb-6">
                <div class="flex items-center gap-4 mb-4">
                    <Link :href="route('permintaan.index')" class="text-muted-foreground hover:text-foreground">
                        <ArrowLeft class="h-5 w-5" />
                    </Link>
                    <h1 class="text-2xl font-semibold flex items-center gap-2">
                        <Package class="h-6 w-6" />
                        Edit Permintaan Barang
                    </h1>
                </div>
                <p class="text-muted-foreground">Edit detail permintaan barang</p>
            </div>

            <div class="max-w-2xl mx-auto">
                <div class="bg-white rounded-lg border p-6">
                    <h2 class="text-lg font-medium mb-6">Form Edit Permintaan</h2>
                    
                    <!-- Informasi Permintaan -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                        <h3 class="font-medium text-gray-900 mb-2">Informasi Permintaan:</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium">Pemohon:</span>
                                <p>{{ permintaan.user.name }}</p>
                            </div>
                            <div>
                                <span class="font-medium">Tanggal Dibuat:</span>
                                <p>{{ new Date(permintaan.created_at).toLocaleDateString('id-ID') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <form @submit.prevent="submitPermintaan" class="space-y-6">
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

                        <!-- Jumlah Permintaan -->
                        <div class="space-y-2">
                            <Label for="jumlah">Jumlah yang Diminta *</Label>
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

                        <!-- Status -->
                        <div class="space-y-2">
                            <Label for="status">Status *</Label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                required
                            >
                                <option value="pending">Menunggu</option>
                                <option value="approved">Disetujui</option>
                                <option value="rejected">Ditolak</option>
                                <option value="completed">Selesai</option>
                            </select>
                            <p v-if="form.errors.status" class="text-sm text-red-500">
                                {{ form.errors.status }}
                            </p>
                        </div>

                        <!-- Keterangan -->
                        <div class="space-y-2">
                            <Label for="keterangan">Keterangan (Opsional)</Label>
                            <textarea
                                id="keterangan"
                                v-model="form.keterangan"
                                placeholder="Jelaskan alasan atau detail permintaan barang ini"
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
                                @click="$inertia.visit(route('permintaan.index'))"
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
