<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
    kode_barang: props.barang.kode_barang,
    nama_barang: props.barang.nama_barang,
    kategori: props.barang.kategori,
    stok: props.barang.stok.toString(), // tetap string agar binding input tidak error
    deskripsi: props.barang.deskripsi,
});

const isSuccess = ref(false);

const submit = () => {
    const stokAngka = parseInt(form.stok);
    if (isNaN(stokAngka) || stokAngka < 0) {
        form.errors.stok = 'Stok harus berupa angka positif';
        return;
    }

    form.put(route('barang.update', props.barang.id), {
        onSuccess: () => {
            isSuccess.value = true;
            setTimeout(() => isSuccess.value = false, 2000);
        },
    });
};
</script>

<template>
    <Head title="Edit Barang" />

    <AppLayout>
        <div class="p-4 md:p-8 max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex items-center gap-4 mb-8">
                 <a :href="route('barang.index')" class="text-gray-600 hover:text-gray-900">
                    <ArrowLeft class="w-6 h-6" />
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Edit Barang</h1>
            </div>

            <!-- Success Message -->
             <div v-if="isSuccess" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-md flex items-center">
                <BadgeCheck class="w-6 h-6 mr-3" />
                <p class="font-semibold">Data barang berhasil diperbarui!</p>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-xl border p-6 md:p-8 shadow-sm">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kode Barang -->
                        <div class="space-y-2">
                            <Label for="kode_barang" class="font-semibold flex items-center">
                                <Package class="w-4 h-4 mr-2" />
                                Kode Barang
                            </Label>
                            <Input
                                id="kode_barang"
                                v-model="form.kode_barang"
                                type="text"
                                required
                                class="w-full"
                                disabled
                            />
                            <p v-if="form.errors.kode_barang" class="text-sm text-red-600 mt-1">
                                {{ form.errors.kode_barang }}
                            </p>
                        </div>

                        <!-- Nama Barang -->
                        <div class="space-y-2">
                            <Label for="nama_barang" class="font-semibold flex items-center">
                                 <Layers class="w-4 h-4 mr-2" />
                                Nama Barang
                            </Label>
                            <Input
                                id="nama_barang"
                                v-model="form.nama_barang"
                                type="text"
                                required
                                class="w-full"
                            />
                            <p v-if="form.errors.nama_barang" class="text-sm text-red-600 mt-1">
                                {{ form.errors.nama_barang }}
                            </p>
                        </div>
                    </div>

                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kategori -->
                        <div class="space-y-2">
                            <Label for="kategori" class="font-semibold">Kategori</Label>
                            <select
                                id="kategori"
                                v-model="form.kategori"
                                required
                                class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="peminjaman">Peminjaman</option>
                                <option value="permintaan">Permintaan (Habis Pakai)</option>
                            </select>
                            <p v-if="form.errors.kategori" class="text-sm text-red-600 mt-1">
                                {{ form.errors.kategori }}
                            </p>
                        </div>

                        <!-- Stok -->
                        <div class="space-y-2">
                            <Label for="stok" class="font-semibold">Stok</Label>
                            <Input
                                id="stok"
                                v-model="form.stok"
                                type="number"
                                min="0"
                                required
                                class="w-full"
                            />
                            <p v-if="form.errors.stok" class="text-sm text-red-600 mt-1">
                                {{ form.errors.stok }}
                            </p>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="space-y-2">
                        <Label for="deskripsi" class="font-semibold flex items-center">
                            <FileText class="w-4 h-4 mr-2" />
                            Deskripsi
                        </Label>
                        <textarea
                            id="deskripsi"
                            v-model="form.deskripsi"
                            rows="4"
                            class="w-full min-h-[100px] rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                        />
                         <p v-if="form.errors.deskripsi" class="text-sm text-red-600 mt-1">
                            {{ form.errors.deskripsi }}
                        </p>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex justify-end gap-4 pt-4">
                        <Button
                            type="button"
                            variant="outline"
                            @click="$inertia.visit(route('barang.index'))"
                        >
                            Batal
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="flex items-center"
                        >
                            <Save class="w-4 h-4 mr-2" />
                            Simpan Perubahan
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
