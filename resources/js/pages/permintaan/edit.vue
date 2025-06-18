<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft, Package, Sparkles, BadgeCheck } from 'lucide-vue-next';
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
        <div class="min-h-screen bg-gradient-to-br from-indigo-200 via-blue-100 to-purple-200 py-12 px-2 sm:px-0 relative overflow-x-hidden">
            <!-- Animated Sparkles -->
            <Sparkles class="absolute left-10 top-10 text-indigo-300 opacity-60 animate-pulse z-0" :size="48" />
            <Sparkles class="absolute right-10 bottom-10 text-purple-300 opacity-50 animate-pulse z-0" :size="40" />
            <div class="max-w-3xl mx-auto relative z-10">
                <!-- Header -->
                <div class="flex items-center gap-4 mb-10">
                    <Link :href="route('permintaan.index')" class="text-indigo-500 hover:text-indigo-700 transition">
                        <ArrowLeft class="h-8 w-8 drop-shadow" />
                    </Link>
                    <h1 class="text-4xl font-extrabold flex items-center gap-3 text-gray-800 drop-shadow-lg tracking-tight">
                        <span class="bg-gradient-to-tr from-indigo-300 via-blue-200 to-purple-200 rounded-full p-3 shadow-lg border-4 border-white/70">
                            <Package class="h-9 w-9 text-indigo-600" />
                        </span>
                        <span class="flex items-center gap-2">
                            Edit Permintaan Barang
                            <Sparkles class="text-yellow-400 animate-bounce" :size="28" />
                        </span>
                        <BadgeCheck v-if="isSuccess" class="w-8 h-8 text-green-400 animate-bounce" />
                    </h1>
                </div>
                <div class="mb-12">
                    <p class="text-xl text-gray-600 font-medium flex items-center gap-2">
                        <span>Perbarui detail permintaan barang dengan tampilan yang</span>
                        <span class="bg-gradient-to-r from-indigo-400 via-blue-400 to-purple-400 bg-clip-text text-transparent font-extrabold animate-gradient-x">super keren</span>
                        <span>dan modern.</span>
                    </p>
                </div>

                <div class="bg-white/95 shadow-2xl rounded-3xl border border-gray-100 p-10 relative overflow-hidden">
                    <!-- Decorative background shapes -->
                    <div class="absolute -top-16 -right-16 w-56 h-56 bg-gradient-to-br from-indigo-300 via-blue-200 to-purple-200 rounded-full opacity-30 blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-16 -left-16 w-44 h-44 bg-gradient-to-tr from-purple-200 via-blue-200 to-indigo-200 rounded-full opacity-20 blur-2xl pointer-events-none"></div>
                    
                    <h2 class="text-2xl font-extrabold mb-10 text-indigo-700 flex items-center gap-3 tracking-tight">
                        <span class="inline-block w-2 h-8 bg-gradient-to-b from-indigo-500 to-purple-400 rounded-r mr-2"></span>
                        Form Edit Permintaan
                        <Sparkles class="text-indigo-400 animate-pulse" :size="22" />
                    </h2>
                    
                    <!-- Informasi Permintaan -->
                    <div class="bg-gradient-to-r from-indigo-50 via-blue-50 to-purple-50 border border-indigo-100 rounded-2xl p-6 mb-10 flex flex-col sm:flex-row gap-8 shadow-lg">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="font-semibold text-indigo-700">Pemohon:</span>
                                <span class="text-gray-700">{{ permintaan.user.name }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-indigo-700">Tanggal Dibuat:</span>
                                <span class="text-gray-700">{{ new Date(permintaan.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) }}</span>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center items-end">
                            <div class="bg-gradient-to-r from-indigo-200 to-blue-100 text-indigo-700 px-6 py-2 rounded-xl font-semibold text-base shadow flex items-center gap-2">
                                <span>Status:</span>
                                <span
                                    :class="{
                                        'text-yellow-600': permintaan.status === 'pending',
                                        'text-green-600': permintaan.status === 'approved',
                                        'text-red-600': permintaan.status === 'rejected',
                                        'text-blue-600': permintaan.status === 'completed'
                                    }"
                                    class="font-bold"
                                >
                                    {{
                                        permintaan.status === 'pending' ? 'Menunggu' :
                                        permintaan.status === 'approved' ? 'Disetujui' :
                                        permintaan.status === 'rejected' ? 'Ditolak' : 'Selesai'
                                    }}
                                </span>
                                <Sparkles v-if="permintaan.status === 'approved'" class="text-green-400 animate-bounce" :size="20" />
                                <Sparkles v-else-if="permintaan.status === 'pending'" class="text-yellow-400 animate-pulse" :size="20" />
                                <Sparkles v-else-if="permintaan.status === 'rejected'" class="text-red-400 animate-pulse" :size="20" />
                                <Sparkles v-else class="text-blue-400 animate-pulse" :size="20" />
                            </div>
                        </div>
                    </div>
                    
                    <form @submit.prevent="submitPermintaan" class="space-y-10">
                        <!-- Pilih Barang -->
                        <div>
                            <Label for="barang_id" class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                                <Sparkles class="text-indigo-400" :size="18" /> Barang <span class="text-red-500">*</span>
                            </Label>
                            <div class="mt-3 relative">
                                <select
                                    id="barang_id"
                                    v-model="form.barang_id"
                                    class="w-full rounded-xl border-2 border-indigo-300 bg-white px-5 py-3 text-lg shadow focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition font-semibold"
                                    required
                                >
                                    <option value="" disabled>Pilih barang</option>
                                    <option v-for="item in barang" :key="item.id" :value="item.id">
                                        {{ item.nama_barang }} ({{ item.kode_barang }}) - Stok: {{ item.stok }}
                                    </option>
                                </select>
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-indigo-400">
                                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </div>
                            <p v-if="form.errors.barang_id" class="text-sm text-red-500 mt-2">
                                {{ form.errors.barang_id }}
                            </p>
                        </div>

                        <!-- Jumlah Permintaan -->
                        <div>
                            <Label for="jumlah" class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                                <Sparkles class="text-indigo-400" :size="18" /> Jumlah yang Diminta <span class="text-red-500">*</span>
                            </Label>
                            <div class="mt-3">
                                <Input
                                    id="jumlah"
                                    v-model="form.jumlah"
                                    type="number"
                                    min="1"
                                    placeholder="Masukkan jumlah"
                                    class="w-full rounded-xl border-2 border-indigo-300 px-5 py-3 text-lg shadow focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition font-semibold"
                                    required
                                />
                            </div>
                            <p v-if="form.errors.jumlah" class="text-sm text-red-500 mt-2">
                                {{ form.errors.jumlah }}
                            </p>
                        </div>

                        <!-- Status -->
                        <div>
                            <Label for="status" class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                                <Sparkles class="text-indigo-400" :size="18" /> Status <span class="text-red-500">*</span>
                            </Label>
                            <div class="mt-3 relative">
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="w-full rounded-xl border-2 border-indigo-300 bg-white px-5 py-3 text-lg shadow focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition font-semibold"
                                    required
                                >
                                    <option value="pending">Menunggu</option>
                                    <option value="approved">Disetujui</option>
                                    <option value="rejected">Ditolak</option>
                                    <option value="completed">Selesai</option>
                                </select>
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-indigo-400">
                                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </div>
                            <p v-if="form.errors.status" class="text-sm text-red-500 mt-2">
                                {{ form.errors.status }}
                            </p>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <Label for="keterangan" class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                                <Sparkles class="text-indigo-400" :size="18" /> Keterangan <span class="text-gray-400">(Opsional)</span>
                            </Label>
                            <div class="mt-3">
                                <textarea
                                    id="keterangan"
                                    v-model="form.keterangan"
                                    placeholder="Jelaskan alasan atau detail permintaan barang ini"
                                    rows="4"
                                    class="w-full rounded-xl border-2 border-indigo-300 px-5 py-3 text-lg shadow focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition resize-none font-semibold"
                                />
                            </div>
                            <p v-if="form.errors.keterangan" class="text-sm text-red-500 mt-2">
                                {{ form.errors.keterangan }}
                            </p>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="flex flex-col sm:flex-row gap-5 mt-10">
                            <Button 
                                type="submit" 
                                :disabled="form.processing"
                                class="flex-1 bg-gradient-to-r from-indigo-500 via-blue-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-lg font-bold py-4 shadow-xl transition duration-200 border-0"
                            >
                                <span v-if="form.processing">
                                    <svg class="animate-spin h-5 w-5 mr-2 inline-block text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>
                                    Menyimpan...
                                </span>
                                <span v-else class="flex items-center gap-2">
                                    <Sparkles class="text-white animate-pulse" :size="20" />
                                    Simpan Perubahan
                                </span>
                            </Button>
                            <Button 
                                type="button" 
                                variant="outline"
                                class="flex-1 border-indigo-300 text-indigo-600 hover:bg-indigo-50 hover:border-indigo-400 transition py-4 text-lg font-bold"
                                @click="$inertia.visit(route('permintaan.index'))"
                            >
                                Batal
                            </Button>
                        </div>
                        <transition name="fade">
                            <div v-if="isSuccess" class="fixed top-8 left-1/2 -translate-x-1/2 z-50">
                                <div class="flex items-center gap-3 bg-gradient-to-r from-green-400 to-blue-400 text-white px-6 py-3 rounded-2xl shadow-2xl font-bold text-lg animate-bounce">
                                    <BadgeCheck class="w-7 h-7 text-white" />
                                    Permintaan berhasil diperbarui!
                                </div>
                            </div>
                        </transition>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@keyframes gradient-x {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}
.animate-gradient-x {
  background-size: 200% 200%;
  animation: gradient-x 3s ease-in-out infinite;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
