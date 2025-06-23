<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft, Package, Sparkles, BadgeCheck, Clock, CheckCircle, XCircle, ChevronDown, Loader2 } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';

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
    status: props.permintaan.status,
});

const isSuccess = ref(false);

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', { 
        day: '2-digit', 
        month: 'long', 
        year: 'numeric' 
    });
};

const getStatusText = (status: string) => {
    switch (status) {
        case 'pending':
            return 'Menunggu';
        case 'approved':
            return 'Disetujui';
        case 'rejected':
            return 'Ditolak';
        case 'completed':
            return 'Selesai';
        default:
            return status;
    }
};

const submitPermintaan = () => {
    form.put(route('permintaan.update', props.permintaan.id), {
        onSuccess: () => {
            isSuccess.value = true;
            setTimeout(() => isSuccess.value = false, 2000);
        }
    });
};
</script>

<template>
    <Head title="Edit Permintaan" />
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-[#F0F8FF] via-[#87CEEB] to-[#98FB98] py-12 px-2 sm:px-0 relative overflow-x-hidden">
            <!-- Decorative elements -->
            <Sparkles class="absolute left-10 top-10 text-[#20B2AA] opacity-60 animate-pulse z-0" :size="48" />
            <Sparkles class="absolute right-10 bottom-10 text-[#98FB98] opacity-50 animate-pulse z-0" :size="40" />
            
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="mb-8 flex items-center gap-4">
                    <Link :href="route('permintaan.index')" class="text-[#20B2AA] hover:text-[#1A9A94] transition">
                        <ArrowLeft class="h-6 w-6" />
                    </Link>
                    <div class="flex items-center gap-3">
                        <span class="bg-gradient-to-tr from-[#20B2AA] via-[#87CEEB] to-[#98FB98] rounded-full p-3 shadow-lg border-4 border-white/70">
                            <Package class="h-9 w-9 text-white" />
                        </span>
                        <div>
                            <h1 class="text-3xl font-bold text-[#2F4F4F]">Edit Permintaan</h1>
                            <p class="text-[#708090]">Perbarui detail permintaan barang</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-12">
                    <p class="text-xl text-[#708090] font-medium flex items-center gap-2">
                        <span>Perbarui detail permintaan barang dengan tampilan yang</span>
                        <span class="bg-gradient-to-r from-[#20B2AA] via-[#87CEEB] to-[#98FB98] bg-clip-text text-transparent font-extrabold animate-gradient-x">super keren</span>
                        <span>dan modern.</span>
                    </p>
                </div>

                <div class="bg-white/95 shadow-2xl rounded-3xl border border-[#B0C4DE] p-10 relative overflow-hidden">
                    <!-- Decorative background shapes -->
                    <div class="absolute -top-16 -right-16 w-56 h-56 bg-gradient-to-br from-[#20B2AA] via-[#87CEEB] to-[#98FB98] rounded-full opacity-30 blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-16 -left-16 w-44 h-44 bg-gradient-to-tr from-[#98FB98] via-[#87CEEB] to-[#20B2AA] rounded-full opacity-20 blur-2xl pointer-events-none"></div>
                    
                    <h2 class="text-2xl font-extrabold mb-10 text-[#2F4F4F] flex items-center gap-3 tracking-tight">
                        <span class="inline-block w-2 h-8 bg-gradient-to-b from-[#20B2AA] to-[#98FB98] rounded-r mr-2"></span>
                        Form Edit Permintaan
                        <Sparkles class="text-[#20B2AA] animate-pulse" :size="22" />
                    </h2>
                    
                    <!-- Informasi Permintaan -->
                    <div class="bg-gradient-to-r from-[#F0F8FF] via-[#E6F3FF] to-[#F0FFF0] border border-[#B0C4DE] rounded-2xl p-6 mb-10 flex flex-col sm:flex-row gap-8 shadow-lg">
                        <div class="flex-1">
                            <p class="text-[#708090] mb-2">
                                <span class="font-semibold text-[#2F4F4F]">Pemohon:</span>
                                <span class="ml-2">{{ permintaan.user?.name }}</span>
                            </p>
                            <p class="text-[#708090]">
                                <span class="font-semibold text-[#2F4F4F]">Tanggal Dibuat:</span>
                                <span class="ml-2">{{ formatDate(permintaan.created_at) }}</span>
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="bg-gradient-to-r from-[#20B2AA] to-[#87CEEB] text-white px-6 py-2 rounded-xl font-semibold text-base shadow flex items-center gap-2">
                                <Clock v-if="permintaan.status === 'pending'" class="h-4 w-4" />
                                <CheckCircle v-else-if="permintaan.status === 'approved'" class="h-4 w-4" />
                                <XCircle v-else-if="permintaan.status === 'rejected'" class="h-4 w-4" />
                                <Package v-else-if="permintaan.status === 'completed'" class="h-4 w-4" />
                                <Sparkles v-else class="text-[#87CEEB] animate-pulse" :size="20" />
                                <span :class="{
                                    'text-yellow-600': permintaan.status === 'pending',
                                    'text-green-600': permintaan.status === 'approved',
                                    'text-red-600': permintaan.status === 'rejected',
                                    'text-[#20B2AA]': permintaan.status === 'completed'
                                }">
                                    {{ getStatusText(permintaan.status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submitPermintaan" class="space-y-8">
                        <!-- Barang -->
                        <div>
                            <label class="block text-lg font-semibold text-[#2F4F4F] mb-3 flex items-center gap-2">
                                <Sparkles class="text-[#20B2AA]" :size="18" /> Barang <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select
                                    v-model="form.barang_id"
                                    class="w-full rounded-xl border-2 border-[#B0C4DE] bg-white px-5 py-3 text-lg shadow focus:border-[#20B2AA] focus:ring-2 focus:ring-[#E6F3FF] transition font-semibold"
                                    required
                                >
                                    <option value="">Pilih Barang</option>
                                    <option v-for="barang in barang" :key="barang.id" :value="barang.id">
                                        {{ barang.nama_barang }} ({{ barang.kode_barang }}) - Stok: {{ barang.stok }}
                                    </option>
                                </select>
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-[#20B2AA]">
                                    <ChevronDown class="h-5 w-5" />
                                </span>
                            </div>
                        </div>

                        <!-- Jumlah -->
                        <div>
                            <label class="block text-lg font-semibold text-[#2F4F4F] mb-3 flex items-center gap-2">
                                <Sparkles class="text-[#20B2AA]" :size="18" /> Jumlah yang Diminta <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model.number="form.jumlah"
                                type="number"
                                min="1"
                                class="w-full rounded-xl border-2 border-[#B0C4DE] px-5 py-3 text-lg shadow focus:border-[#20B2AA] focus:ring-2 focus:ring-[#E6F3FF] transition font-semibold"
                                required
                            />
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-lg font-semibold text-[#2F4F4F] mb-3 flex items-center gap-2">
                                <Sparkles class="text-[#20B2AA]" :size="18" /> Status <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select
                                    v-model="form.status"
                                    class="w-full rounded-xl border-2 border-[#B0C4DE] bg-white px-5 py-3 text-lg shadow focus:border-[#20B2AA] focus:ring-2 focus:ring-[#E6F3FF] transition font-semibold"
                                    required
                                >
                                    <option value="pending">Pending</option>
                                    <option value="approved">Disetujui</option>
                                    <option value="rejected">Ditolak</option>
                                    <option value="completed">Selesai</option>
                                </select>
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-[#20B2AA]">
                                    <ChevronDown class="h-5 w-5" />
                                </span>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label class="block text-lg font-semibold text-[#2F4F4F] mb-3 flex items-center gap-2">
                                <Sparkles class="text-[#20B2AA]" :size="18" /> Keterangan <span class="text-[#708090]">(Opsional)</span>
                            </label>
                            <textarea
                                v-model="form.keterangan"
                                rows="4"
                                class="w-full rounded-xl border-2 border-[#B0C4DE] px-5 py-3 text-lg shadow focus:border-[#20B2AA] focus:ring-2 focus:ring-[#E6F3FF] transition resize-none font-semibold"
                                placeholder="Tambahkan keterangan jika diperlukan..."
                            ></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-4 pt-6">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1 bg-gradient-to-r from-[#20B2AA] via-[#87CEEB] to-[#98FB98] hover:from-[#1A9A94] hover:to-[#90EE90] text-lg font-bold py-4 shadow-xl transition duration-200 border-0"
                            >
                                <Loader2 v-if="form.processing" class="mr-2 h-5 w-5 animate-spin" />
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                            </button>
                            <Link
                                :href="route('permintaan.index')"
                                class="flex-1 border-[#B0C4DE] text-[#2F4F4F] hover:bg-[#F0F8FF] hover:border-[#20B2AA] transition py-4 text-lg font-bold"
                            >
                                Batal
                            </Link>
                        </div>
                    </form>

                    <!-- Success Message -->
                    <div v-if="isSuccess" class="mt-8 p-4 bg-gradient-to-r from-[#98FB98] to-[#87CEEB] text-white px-6 py-3 rounded-2xl shadow-2xl font-bold text-lg animate-bounce">
                        <div class="flex items-center gap-3">
                            <CheckCircle class="h-6 w-6" />
                            <span>Permintaan berhasil diperbarui!</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.animate-gradient-x {
    animation: gradient-x 15s ease infinite;
    background-size: 200% 200%;
}

@keyframes gradient-x {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}
</style>
