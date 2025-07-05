<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';

const form = useForm({
    kode_barang: '',
    nama_barang: '',
    kategori: '',
    stok: '',
    deskripsi: '',
    satuan: '',
    lokasi: ''
});

const manualSatuan = ref('');
const manualLokasi = ref('');

const satuanValue = computed({
    get() {
        return form.satuan === 'Lainnya' ? manualSatuan.value : form.satuan;
    },
    set(val) {
        if (form.satuan === 'Lainnya') {
            manualSatuan.value = val;
        } else {
            form.satuan = val;
        }
    }
});

const lokasiValue = computed({
    get() {
        return form.lokasi === 'Lainnya' ? manualLokasi.value : form.lokasi;
    },
    set(val) {
        if (form.lokasi === 'Lainnya') {
            manualLokasi.value = val;
        } else {
            form.lokasi = val;
        }
    }
});

const submit = () => {
    if (form.satuan === 'Lainnya') {
        form.satuan = manualSatuan.value;
    }
    if (form.lokasi === 'Lainnya') {
        form.lokasi = manualLokasi.value;
    }
    form.post(route('barang.store'));
};
</script>

<template>
    <Head title="Tambah Barang" />

    <AppLayout>
        <div class="p-6">
            <div class="mb-6">
                <div class="flex items-center gap-4 mb-4">
                    <Link :href="route('barang.index')">
                        <Button variant="outline" size="sm">
                            <ArrowLeft class="w-4 h-4 mr-2" />
                            Kembali
                        </Button>
                    </Link>
                </div>
                <h1 class="text-2xl font-semibold">Tambah Barang</h1>
            </div>

            <div class="bg-white rounded-lg border p-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <Label for="kode_barang">Kode Barang</Label>
                        <Input 
                            id="kode_barang"
                            v-model="form.kode_barang"
                            type="text"
                            required
                        />
                        <p v-if="form.errors.kode_barang" class="text-sm text-red-500">
                            {{ form.errors.kode_barang }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="nama_barang">Nama Barang</Label>
                        <Input 
                            id="nama_barang"
                            v-model="form.nama_barang"
                            type="text"
                            required
                        />
                        <p v-if="form.errors.nama_barang" class="text-sm text-red-500">
                            {{ form.errors.nama_barang }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="kategori">Kategori</Label>
                        <select
                            id="kategori"
                            v-model="form.kategori"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            required
                        >
                            <option value="">Pilih kategori</option>
                            <option value="peminjaman">Aset</option>
                            <option value="permintaan">Permintaan</option>
                        </select>
                        <p v-if="form.errors.kategori" class="text-sm text-red-500">
                            {{ form.errors.kategori }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="stok">Stok</Label>
                        <Input 
                            id="stok"
                            v-model="form.stok"
                            type="number"
                            min="0"
                            required
                        />
                        <p v-if="form.errors.stok" class="text-sm text-red-500">
                            {{ form.errors.stok }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="satuan">Satuan</Label>
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
                        <p v-if="form.errors.satuan" class="text-sm text-red-500">
                            {{ form.errors.satuan }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="lokasi">Lokasi</Label>
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
                        <p v-if="form.errors.lokasi" class="text-sm text-red-500">
                            {{ form.errors.lokasi }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="deskripsi">Deskripsi</Label>
                        <textarea 
                            id="deskripsi"
                            v-model="form.deskripsi"
                            rows="4"
                            class="w-full min-h-[80px] rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        />
                        <p v-if="form.errors.deskripsi" class="text-sm text-red-500">
                            {{ form.errors.deskripsi }}
                        </p>
                    </div>

                    <div class="flex justify-end gap-4">
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
                        >
                            Simpan
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
