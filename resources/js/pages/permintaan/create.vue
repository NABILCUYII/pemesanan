<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed, watch } from 'vue';
import { BadgeCheck, Package, Layers, FileText, Save, ArrowLeft, Sparkles, Info, Loader2 } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    barang: {
        id: number;
        nama_barang: string;
        stok: number;
        kategori: string;
        deskripsi: string;
    }[];
}>();

const form = useForm({
    barang_id: '',
    jumlah: '',
    keterangan: ''
});

const isSuccess = ref(false);
const isError = ref(false);
const isPreview = ref(false);
const isLoading = ref(false);

const selectedBarang = computed(() => {
    return props.barang.find(b => b.id.toString() === form.barang_id) || null;
});

watch(() => form.barang_id, (val) => {
    if (val && selectedBarang.value) {
        form.jumlah = '';
        form.keterangan = '';
        isPreview.value = true;
    } else {
        isPreview.value = false;
    }
});

const handlePreview = () => {
    isPreview.value = !!form.barang_id;
};

const submit = () => {
    if (!form.barang_id || !form.jumlah) {
        isError.value = true;
        setTimeout(() => isError.value = false, 2000);
        return;
    }
    isLoading.value = true;
    form.post(route('permintaan.store'), {
        onSuccess: () => {
            isSuccess.value = true;
            form.reset();
            isPreview.value = false;
            setTimeout(() => isSuccess.value = false, 2000);
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
};
</script>

<template>
    <Head title="Permintaan Barang Keren" />

    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-indigo-300 via-blue-200 to-purple-200 py-12 px-2 sm:px-0 relative overflow-x-hidden">
            <!-- Animated Sparkles & Floating Shapes -->
            <Sparkles class="absolute left-10 top-10 text-indigo-300 opacity-60 animate-pulse z-0" :size="56" />
            <Sparkles class="absolute right-10 bottom-10 text-purple-300 opacity-50 animate-pulse z-0" :size="48" />
            <div class="absolute left-1/2 -translate-x-1/2 top-0 w-96 h-96 bg-gradient-to-br from-indigo-400 via-blue-200 to-purple-300 rounded-full opacity-20 blur-3xl pointer-events-none"></div>
            <div class="max-w-3xl mx-auto relative z-10">
                <!-- Header -->
                <div class="flex items-center gap-4 mb-10">
                    <Link :href="route('permintaan.index')" class="text-indigo-500 hover:text-indigo-700 transition">
                        <ArrowLeft class="h-8 w-8 drop-shadow" />
                    </Link>
                    <h1 class="text-4xl font-extrabold flex items-center gap-3 text-gray-800 drop-shadow-lg tracking-tight">
                        <span class="bg-gradient-to-tr from-indigo-300 via-blue-200 to-purple-200 rounded-full p-3 shadow-lg border-4 border-white/70">
                            <Package class="h-10 w-10 text-indigo-600" />
                        </span>
                        <span class="flex items-center gap-2">
                            Permintaan Barang <Sparkles class="text-yellow-400 animate-bounce" :size="32" />
                        </span>
                        <BadgeCheck v-if="isSuccess" class="w-8 h-8 text-green-400 animate-bounce" />
                    </h1>
                </div>
                <div class="mb-12">
                    <p class="text-xl text-gray-600 font-medium flex items-center gap-2">
                        <span>Ajukan permintaan barang dengan fitur</span>
                        <span class="bg-gradient-to-r from-indigo-400 via-blue-400 to-purple-400 bg-clip-text text-transparent font-extrabold animate-gradient-x">super keren</span>
                        <span>dan interaktif.</span>
                    </p>
                </div>

                <div class="bg-white/95 shadow-2xl rounded-3xl border border-gray-100 p-10 relative overflow-hidden">
                    <!-- Decorative background shapes -->
                    <div class="absolute -top-16 -right-16 w-56 h-56 bg-gradient-to-br from-indigo-300 via-blue-200 to-purple-200 rounded-full opacity-30 blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-16 -left-16 w-44 h-44 bg-gradient-to-tr from-purple-200 via-blue-200 to-indigo-200 rounded-full opacity-20 blur-2xl pointer-events-none"></div>
                    
                    <h2 class="text-2xl font-extrabold mb-10 text-indigo-700 flex items-center gap-3 tracking-tight">
                        <span class="inline-block w-2 h-8 bg-gradient-to-b from-indigo-500 to-purple-400 rounded-r mr-2"></span>
                        Form Permintaan Barang
                        <Sparkles class="text-indigo-400 animate-pulse" :size="22" />
                    </h2>
                    
                    <form @submit.prevent="submit" class="space-y-8 z-10 relative">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Pilih Barang -->
                            <div class="space-y-2">
                                <Label for="barang_id" class="font-semibold flex items-center gap-2 text-gray-700">
                                    <Layers class="w-5 h-5 text-indigo-500" /> Pilih Barang
                                </Label>
                                <Select v-model="form.barang_id" @update:modelValue="handlePreview">
                                    <SelectTrigger class="h-12 px-4 rounded-xl border-gray-200 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition-all shadow-sm hover:shadow-md" :class="form.errors.barang_id ? 'border-red-500' : ''">
                                        <SelectValue placeholder="Pilih barang" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="item in props.barang" :key="item.id" :value="item.id.toString()">
                                            <span class="flex flex-col">
                                                <span class="font-semibold">{{ item.nama_barang }}</span>
                                                <span class="text-xs text-gray-500">Stok: {{ item.stok }} | {{ item.kategori }}</span>
                                            </span>
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.barang_id" class="text-sm text-red-500 mt-1">
                                    {{ form.errors.barang_id }}
                                </p>
                            </div>
                            <!-- Jumlah -->
                            <div class="space-y-2">
                                <Label for="jumlah" class="font-semibold flex items-center gap-2 text-gray-700">
                                    <Layers class="w-5 h-5 text-indigo-500" /> Jumlah
                                </Label>
                                <Input 
                                    id="jumlah"
                                    v-model="form.jumlah"
                                    type="number"
                                    min="1"
                                    :max="selectedBarang && selectedBarang.stok ? selectedBarang.stok : undefined"
                                    placeholder="Masukkan jumlah permintaan"
                                    class="h-12 px-4 rounded-xl border-gray-200 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition-all shadow-sm hover:shadow-md"
                                    required
                                    :class="form.errors.jumlah ? 'border-red-500' : ''"
                                />
                                <p v-if="form.errors.jumlah" class="text-sm text-red-500 mt-1">
                                    {{ form.errors.jumlah }}
                                </p>
                                <transition name="fade">
                                    <p v-if="selectedBarang" class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                        <Info class="w-4 h-4 text-indigo-400" /> Stok tersedia: <span class="font-semibold">{{ selectedBarang.stok }}</span>
                                    </p>
                                </transition>
                            </div>
                            <!-- Keterangan -->
                            <div class="space-y-2 md:col-span-2">
                                <Label for="keterangan" class="font-semibold flex items-center gap-2 text-gray-700">
                                    <FileText class="w-5 h-5 text-indigo-500" /> Keterangan (Opsional)
                                </Label>
                                <Input 
                                    id="keterangan"
                                    v-model="form.keterangan"
                                    type="text"
                                    placeholder="Tulis keterangan tambahan (opsional)"
                                    class="h-12 px-4 rounded-xl border-gray-200 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition-all shadow-sm hover:shadow-md"
                                    :class="form.errors.keterangan ? 'border-red-500' : ''"
                                />
                                <p v-if="form.errors.keterangan" class="text-sm text-red-500 mt-1">
                                    {{ form.errors.keterangan }}
                                </p>
                            </div>
                        </div>
                        <!-- Preview Barang -->
                        <transition name="fade">
                        <div v-if="isPreview && selectedBarang" class="bg-gradient-to-r from-indigo-50 via-blue-50 to-purple-50 border border-indigo-100 rounded-2xl p-6 mb-6 shadow-lg animate-fade-in">
                            <h3 class="font-bold text-indigo-700 mb-2 flex items-center gap-2">
                                <Package class="w-5 h-5" /> Detail Barang
                            </h3>
                            <div class="text-gray-700 text-sm grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div><span class="font-semibold">Nama:</span> {{ selectedBarang.nama_barang }}</div>
                                <div><span class="font-semibold">Kategori:</span> {{ selectedBarang.kategori }}</div>
                                <div><span class="font-semibold">Stok:</span> {{ selectedBarang.stok }}</div>
                                <div class="sm:col-span-2"><span class="font-semibold">Deskripsi:</span> {{ selectedBarang.deskripsi }}</div>
                            </div>
                        </div>
                        </transition>
                        <div class="flex justify-between items-center gap-4">
                            <Button type="button" variant="outline" class="h-12 px-8 rounded-xl flex items-center gap-2" @click="form.reset(); isPreview = false" :disabled="form.processing || isLoading">
                                Reset
                            </Button>
                            <Button type="submit" class="h-12 px-8 rounded-xl flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-lg hover:scale-105 transition-transform" :disabled="form.processing || isLoading">
                                <Loader2 v-if="isLoading" class="w-5 h-5 animate-spin" />
                                <Save v-else class="w-5 h-5" /> Ajukan Permintaan
                            </Button>
                        </div>
                        <transition name="fade">
                            <div v-if="isSuccess" class="fixed top-6 right-6 bg-green-100 border border-green-300 text-green-800 px-6 py-4 rounded-xl shadow-lg flex items-center gap-3 z-50 animate-bounce-in">
                                <BadgeCheck class="w-6 h-6 text-green-500" />
                                Permintaan berhasil diajukan!
                            </div>
                        </transition>
                        <transition name="fade">
                            <div v-if="isError" class="fixed top-6 right-6 bg-red-100 border border-red-300 text-red-800 px-6 py-4 rounded-xl shadow-lg flex items-center gap-3 z-50 animate-shake">
                                Mohon lengkapi semua data yang diperlukan.
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
  transition: opacity 0.4s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
@keyframes fade-in {
  from { opacity: 0; transform: translateY(16px);}
  to { opacity: 1; transform: translateY(0);}
}
.animate-fade-in {
  animation: fade-in 0.5s;
}
@keyframes bounce-in {
  0% { transform: scale(0.7); opacity: 0; }
  60% { transform: scale(1.1); opacity: 1; }
  100% { transform: scale(1); }
}
.animate-bounce-in {
  animation: bounce-in 0.6s;
}
@keyframes shake {
  0%, 100% { transform: translateX(0);}
  20%, 60% { transform: translateX(-8px);}
  40%, 80% { transform: translateX(8px);}
}
.animate-shake {
  animation: shake 0.5s;
}
</style>
