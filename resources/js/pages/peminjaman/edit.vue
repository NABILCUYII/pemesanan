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
    <Head title="Edit Aset" />
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100 py-10">
            <div class="max-w-2xl mx-auto">
                <!-- Header -->
                <div class="flex items-center gap-3 mb-8">
                    <Link :href="route('peminjaman.index')" class="text-blue-500 hover:text-blue-700 transition">
                        <ArrowLeft class="h-6 w-6" />
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                        <span class="inline-flex items-center justify-center bg-blue-100 text-blue-600 rounded-full p-2 shadow">
                            <Package class="h-7 w-7" />
                        </span>
                        Edit Peminjaman Barang
                    </h1>
                </div>
                <div class="bg-white/90 shadow-xl rounded-2xl border border-blue-100 p-8">
                    <h2 class="text-xl font-semibold text-blue-700 mb-7 flex items-center gap-2">
                        <span class="inline-block w-2 h-6 bg-blue-400 rounded-full mr-2"></span>
                        Formulir Edit Peminjaman
                    </h2>
                    <!-- Informasi Peminjaman -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-8">
                        <h3 class="font-medium text-blue-900 mb-2">Informasi Peminjaman:</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium">Peminjam:</span>
                                <p>{{ props.peminjaman?.user?.name || 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="font-medium">Tanggal Dibuat:</span>
                                <p>{{ new Date(props.peminjaman?.created_at).toLocaleDateString('id-ID') }}</p>
                            </div>
                        </div>
                    </div>
                    <form @submit.prevent="submitPeminjaman" class="space-y-7">
                        <!-- Pilih Barang -->
                        <div>
                            <Label for="barang_id" class="font-semibold text-gray-700 mb-1">Pilih Barang <span class="text-red-500">*</span></Label>
                            <select
                                id="barang_id"
                                v-model="form.barang_id"
                                class="w-full rounded-xl border border-blue-200 bg-blue-50 px-3 py-3 text-sm focus:ring-2 focus:ring-blue-400 transition"
                                required
                            >
                                <option value="">Pilih barang</option>
                                <option v-for="item in barang" :key="item.id" :value="item.id">
                                    {{ item.nama_barang }} ({{ item.kode_barang }}) - Stok: {{ item.stok }}
                                </option>
                            </select>
                            <transition name="fade">
                                <p v-if="form.errors.barang_id" class="text-xs text-red-600 mt-1">{{ form.errors.barang_id }}</p>
                            </transition>
                        </div>
                        
                        <!-- Jumlah -->
                        <div>
                            <Label for="jumlah" class="font-semibold text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></Label>
                            <Input
                                id="jumlah"
                                v-model="form.jumlah"
                                type="number"
                                min="1"
                                placeholder="Masukkan jumlah"
                                class="rounded-xl border-blue-200 py-3"
                                required
                            />
                            <transition name="fade">
                                <p v-if="form.errors.jumlah" class="text-xs text-red-600 mt-1">{{ form.errors.jumlah }}</p>
                            </transition>
                        </div>

                        <!-- Tenggat Waktu -->
                        <div>
                            <Label for="due_date" class="font-semibold text-gray-700 mb-1">Tenggat Waktu <span class="text-red-500">*</span></Label>
                            <div class="relative">
                                <Input
                                    id="due_date"
                                    v-model="form.due_date"
                                    type="date"
                                    class="pr-10 rounded-xl border-blue-200 py-3"
                                    required
                                />
                                <CalendarDays class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-blue-400 pointer-events-none" />
                            </div>
                            <transition name="fade">
                                <p v-if="form.errors.due_date" class="text-xs text-red-600 mt-1">{{ form.errors.due_date }}</p>
                            </transition>
                        </div>

                        <!-- Tanggal Kembali -->
                        <div>
                            <Label for="tanggal_pengembalian" class="font-semibold text-gray-700 mb-1">Tanggal Kembali <span class="text-red-500">*</span></Label>
                            <div class="relative">
                                <Input
                                    id="tanggal_pengembalian"
                                    v-model="form.tanggal_pengembalian"
                                    type="date"
                                    class="pr-10 rounded-xl border-blue-200 py-3"
                                    required
                                />
                                <CalendarDays class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-blue-400 pointer-events-none" />
                            </div>
                            <transition name="fade">
                                <p v-if="form.errors.tanggal_pengembalian" class="text-xs text-red-600 mt-1">{{ form.errors.tanggal_pengembalian }}</p>
                            </transition>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <Label for="keterangan" class="font-semibold text-gray-700 mb-1">Keterangan (Opsional)</Label>
                            <textarea
                                id="keterangan"
                                v-model="form.keterangan"
                                rows="4"
                                class="w-full rounded-xl border border-blue-200 bg-blue-50 px-3 py-3 text-sm focus:ring-2 focus:ring-blue-400 transition"
                            />
                            <transition name="fade">
                                <p v-if="form.errors.keterangan" class="text-xs text-red-600 mt-1">{{ form.errors.keterangan }}</p>
                            </transition>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="flex gap-4 pt-2">
                            <Button 
                                type="submit" 
                                :disabled="form.processing"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl py-3 transition"
                            >
                                <span v-if="form.processing">
                                    <svg class="animate-spin h-5 w-5 inline-block mr-2" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                    </svg>
                                    Menyimpan...
                                </span>
                                <span v-else>
                                    Simpan Perubahan
                                </span>
                            </Button>
                            <Button 
                                type="button" 
                                variant="outline"
                                class="flex-1 border-blue-400 text-blue-600 hover:bg-blue-50 rounded-xl py-3"
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