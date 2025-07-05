<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import { BadgeCheck, Package, Layers, FileText, ArrowLeft, Save } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    barang: {
        id: number;
        kode_barang: string;
        nama_barang: string;
        kategori: string;
        stok: number;
        deskripsi: string;
        satuan?: string;
        lokasi?: string;
    };
}>();

const form = useForm({
    kode_barang: props.barang.kode_barang,
    nama_barang: props.barang.nama_barang,
    kategori: props.barang.kategori,
    stok: props.barang.stok.toString(), // tetap string agar binding input tidak error
    deskripsi: props.barang.deskripsi,
    satuan: props.barang.satuan || '',
    lokasi: props.barang.lokasi || '',
});

const isSuccess = ref(false);

const manualSatuan = ref(form.satuan && ![
    'buah','pcs','Pak','set','box','unit','lembar','paket','botol','kg','liter','meter'
].includes(form.satuan.toLowerCase()) ? form.satuan : '');
const manualLokasi = ref(form.lokasi && ![
    'Gudang A','Gudang B','Rak 1','Rak 2','Lemari 1','Lemari 2','Laboratorium','Ruang Kelas'
].includes(form.lokasi) ? form.lokasi : '');

// Helper untuk set value dropdown jika bukan preset
if (manualSatuan.value) form.satuan = 'Lainnya';
if (manualLokasi.value) form.lokasi = 'Lainnya';

const submit = () => {
    const stokAngka = parseInt(form.stok);
    if (isNaN(stokAngka) || stokAngka < 0) {
        form.errors.stok = 'Stok harus berupa angka positif';
        return;
    }
    if (form.satuan === 'Lainnya') {
        form.satuan = manualSatuan.value;
    }
    if (form.lokasi === 'Lainnya') {
        form.lokasi = manualLokasi.value;
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
                <Link :href="route('barang.index')" class="text-gray-600 hover:text-gray-900">
                    <ArrowLeft class="w-6 h-6" />
                </Link>
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
                                <option value="peminjaman">Aset</option>
                                <option value="permintaan">Permintaan</option>
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Satuan -->
                        <div class="space-y-2">
                            <Label for="satuan" class="font-semibold">Satuan</Label>
                            <select
                                id="satuan"
                                v-model="form.satuan"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="">Pilih satuan</option>
                                <option value="buah">Buah</option>
                                <option value="pcs">Pcs</option>
                                <option value="Pak">Pak</option>
                                <option value="set">Set</option>
                                <option value="box">Box</option>
                                <option value="unit">Unit</option>
                                <option value="lembar">Lembar</option>
                                <option value="paket">Paket</option>
                                <option value="botol">Botol</option>
                                <option value="kg">Kg</option>
                                <option value="liter">Liter</option>
                                <option value="meter">Meter</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <input
                                v-if="form.satuan === 'Lainnya'"
                                v-model="manualSatuan"
                                type="text"
                                placeholder="Masukkan satuan manual"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm mt-2"
                            />
                            <p v-if="form.errors.satuan" class="text-sm text-red-600 mt-1">
                                {{ form.errors.satuan }}
                            </p>
                        </div>

                        <!-- Lokasi -->
                        <div class="space-y-2">
                            <Label for="lokasi" class="font-semibold">Lokasi</Label>
                            <select
                                id="lokasi"
                                v-model="form.lokasi"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="">Pilih lokasi</option>
                                <option value="Gudang A">Gudang A</option>
                                <option value="Gudang B">Gudang B</option>
                                <option value="Rak 1">Rak 1</option>
                                <option value="Rak 2">Rak 2</option>
                                <option value="Lemari 1">Lemari 1</option>
                                <option value="Lemari 2">Lemari 2</option>
                                <option value="Laboratorium">Laboratorium</option>
                                <option value="Ruang Kelas">Ruang Kelas</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <input
                                v-if="form.lokasi === 'Lainnya'"
                                v-model="manualLokasi"
                                type="text"
                                placeholder="Masukkan lokasi manual"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm mt-2"
                            />
                            <p v-if="form.errors.lokasi" class="text-sm text-red-600 mt-1">
                                {{ form.errors.lokasi }}
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
