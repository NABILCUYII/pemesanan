<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import { BadgeCheck, Package, Layers, FileText, ArrowLeft, Save } from 'lucide-vue-next';

const props = defineProps<{
    barang: {
        id: number;
        kode_barang: string; 
        nama_barang: string;
        kategori: string;
        stok: number;
        deskripsi: string;
    };
}>();

const form = useForm({
    nama_barang: props.barang.nama_barang,
    kategori: props.barang.kategori,
    stok: props.barang.stok.toString(),
    deskripsi: props.barang.deskripsi
});

const isSuccess = ref(false);

const submit = () => {
    form.put(route('barang.update', props.barang.id), {
        onSuccess: () => {
            isSuccess.value = true;
            setTimeout(() => isSuccess.value = false, 2000);
        }
    });
};
</script>

<template>
    <Head title="Edit Barang" />

    <AppLayout>
        <div class="min-h-screen flex flex-col bg-gradient-to-br from-gray-700 via-gray-900 to-black">
            <!-- Header -->
            <div class="w-full bg-gradient-to-r from-gray-800 to-black py-10 shadow-lg relative">
                <div class="absolute inset-0 opacity-10 bg-[url('/images/pattern.svg')] bg-repeat"></div>
                <div class="relative max-w-3xl mx-auto flex flex-col items-center">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/80 rounded-full p-3 shadow-lg">
                            <Package class="w-10 h-10 text-indigo-600" />
                        </div>
                        <h1 class="text-4xl font-extrabold text-white tracking-tight drop-shadow-lg">
                            Edit Barang
                        </h1>
                        <BadgeCheck v-if="isSuccess" class="w-7 h-7 text-green-400 animate-bounce" />
                    </div>
                    <p class="mt-2 text-lg text-white/80 font-medium">Perbarui data barang dengan tampilan yang lebih modern dan nyaman.</p>
                </div>
            </div>

            <!-- Main Card -->
            <div class="flex-1 flex items-center justify-center py-10">
                <div class="w-full max-w-2xl bg-white/95 rounded-3xl shadow-2xl border border-gray-200 p-10 relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-gradient-to-br from-gray-400 to-gray-900 rounded-full opacity-20 blur-2xl"></div>
                    <form @submit.prevent="submit" class="space-y-8 z-10 relative">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Nama Barang -->
                            <div class="space-y-2">
                                <Label for="nama_barang" class="font-semibold flex items-center gap-2 text-gray-700">
                                    <Layers class="w-5 h-5 text-indigo-500" /> Nama Barang
                                </Label>
                                <Input 
                                    id="nama_barang"
                                    v-model="form.nama_barang"
                                    type="text"
                                    placeholder="Masukkan nama barang"
                                    class="h-12 px-4 rounded-xl border-gray-200 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition-all shadow-sm hover:shadow-md"
                                    required
                                    :class="form.errors.nama_barang ? 'border-red-500' : ''"
                                />
                                <p v-if="form.errors.nama_barang" class="text-sm text-red-500 mt-1">
                                    {{ form.errors.nama_barang }}
                                </p>
                            </div>
                            <!-- Kategori -->
                            <div class="space-y-2">
                                <Label for="kategori" class="font-semibold flex items-center gap-2 text-gray-700">
                                    <Layers class="w-5 h-5 text-pink-500" /> Kategori
                                </Label>
                                <Select v-model="form.kategori">
                                    <SelectTrigger id="kategori" class="h-12 w-full rounded-xl shadow-sm hover:shadow-md transition-all" :class="form.errors.kategori ? 'border-red-500' : ''">
                                        <SelectValue placeholder="Pilih kategori" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">Pilih kategori</SelectItem>
                                        <SelectItem value="peminjaman">Peminjaman</SelectItem>
                                        <SelectItem value="permintaan">Permintaan</SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.kategori" class="text-sm text-red-500 mt-1">
                                    {{ form.errors.kategori }}
                                </p>
                            </div>
                            <!-- Stok -->
                            <div class="space-y-2">
                                <Label for="stok" class="font-semibold flex items-center gap-2 text-gray-700">
                                    <Layers class="w-5 h-5 text-green-500" /> Stok
                                </Label>
                                <Input 
                                    id="stok"
                                    v-model="form.stok"
                                    type="number"
                                    min="0"
                                    placeholder="Jumlah stok"
                                    class="h-12 px-4 rounded-xl border-gray-200 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition-all shadow-sm hover:shadow-md"
                                    required
                                    :class="form.errors.stok ? 'border-red-500' : ''"
                                />
                                <p v-if="form.errors.stok" class="text-sm text-red-500 mt-1">
                                    {{ form.errors.stok }}
                                </p>
                            </div>
                            <!-- Kode Barang -->
                            <div class="space-y-2">
                                <Label for="kode_barang" class="font-semibold flex items-center gap-2 text-gray-700">
                                    <Package class="w-5 h-5 text-yellow-500" /> Kode Barang
                                </Label>
                                <Input 
                                    id="kode_barang"
                                    :value="props.barang.kode_barang"
                                    disabled
                                    class="h-12 px-4 rounded-xl bg-gray-100 border-gray-200 cursor-not-allowed shadow-sm"
                                />
                            </div>
                        </div>
                        <!-- Deskripsi -->
                        <div class="space-y-2">
                            <Label for="deskripsi" class="font-semibold flex items-center gap-2 text-gray-700">
                                <FileText class="w-5 h-5 text-blue-400" /> Deskripsi
                            </Label>
                            <textarea 
                                id="deskripsi"
                                v-model="form.deskripsi"
                                rows="4"
                                placeholder="Tulis deskripsi barang di sini..."
                                class="w-full min-h-[100px] rounded-xl border border-gray-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all resize-none shadow-sm hover:shadow-md"
                                :class="form.errors.deskripsi ? 'border-red-500' : ''"
                            />
                            <p v-if="form.errors.deskripsi" class="text-sm text-red-500 mt-1">
                                {{ form.errors.deskripsi }}
                            </p>
                        </div>
                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-4 pt-6 border-t border-gray-100">
                            <Button 
                                type="button" 
                                variant="outline"
                                class="h-12 px-6 rounded-xl border-gray-300 text-gray-700 hover:bg-gray-50 transition-all hover:shadow-md"
                                @click="$inertia.visit(route('barang.index'))"
                            >
                                <ArrowLeft class="w-5 h-5 mr-2" /> Batal
                            </Button>
                            <Button 
                                type="submit"
                                :disabled="form.processing"
                                class="h-12 px-8 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-semibold shadow-lg hover:from-indigo-600 hover:to-purple-600 transition-all flex items-center gap-2"
                            >
                                <Save class="w-5 h-5" /> Simpan Perubahan
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>