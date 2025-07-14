<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { ArrowLeft } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import { PackagePlus } from 'lucide-vue-next';

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

const satuanOptions = [
    'Buah', 'Pcs', 'Pak', 'Set', 'Box', 'Unit', 'Lembar', 'Paket', 'Botol', 'Kg', 'Liter', 'Meter', 'Lainnya'
];

const lokasiOptions = [
    'Gudang A', 'Gudang B', 'Rak 1', 'Rak 2', 'Lemari 1', 'Lemari 2', 'Laboratorium', 'Ruang Kelas', 'Lainnya'
];

const showSatuanDropdown = ref(false);
const showLokasiDropdown = ref(false);

function selectSatuan(option: string) {
    form.satuan = option;
    if (option !== 'Lainnya') {
        manualSatuan.value = '';
    }
    showSatuanDropdown.value = false;
}

function selectLokasi(option: string) {
    form.lokasi = option;
    if (option !== 'Lainnya') {
        manualLokasi.value = '';
    }
    showLokasiDropdown.value = false;
}

const submit = () => {
    // Validasi kode barang harus diisi
    if (!form.kode_barang.trim()) {
        alert('Silakan masukkan kode barang');
        return;
    }
    
    // Validasi nama barang harus diisi
    if (!form.nama_barang.trim()) {
        alert('Silakan masukkan nama barang');
        return;
    }
    
    // Validasi kategori harus dipilih
    if (!form.kategori) {
        alert('Silakan pilih kategori barang');
        return;
    }
    
    // Validasi stok harus diisi
    if (!form.stok || Number(form.stok) <= 0) {
        alert('Silakan masukkan jumlah stok yang valid');
        return;
    }
    
    // Validasi satuan harus dipilih
    if (!form.satuan) {
        alert('Silakan pilih satuan barang');
        return;
    }
    
    // Validasi lokasi harus dipilih
    if (!form.lokasi) {
        alert('Silakan pilih lokasi barang');
        return;
    }
    
    // Validasi jika memilih "Lainnya" untuk satuan
    if (form.satuan === 'Lainnya' && !manualSatuan.value.trim()) {
        alert('Silakan masukkan satuan manual');
        return;
    }
    
    // Validasi jika memilih "Lainnya" untuk lokasi
    if (form.lokasi === 'Lainnya' && !manualLokasi.value.trim()) {
        alert('Silakan masukkan lokasi manual');
        return;
    }
    
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
        <div class="flex flex-col items-center min-h-[90vh] bg-gradient-to-br from-blue-50 to-indigo-100 py-10">
            <Card class="w-full max-w-xl shadow-xl border-0">
                <CardHeader class="flex flex-row items-center gap-3 pb-2">
                    <div class="bg-indigo-100 rounded-full p-2">
                        <PackagePlus class="w-7 h-7 text-indigo-600" />
                    </div>
                    <div>
                        <CardTitle class="text-2xl font-bold text-indigo-800">Tambah Barang</CardTitle>
                        <CardDescription class="text-gray-500">Isi data barang baru dengan lengkap dan benar.</CardDescription>
                    </div>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6 mt-2">
                        <div class="grid grid-cols-1 gap-5">
                            <div>
                                <Label for="kode_barang" class="font-medium text-gray-700">Kode Barang *</Label>
                                <Input 
                                    id="kode_barang"
                                    v-model="form.kode_barang"
                                    type="text"
                                    placeholder="Masukkan kode unik barang"
                                    class="mt-1"
                                    :class="{ 'border-red-500': !form.kode_barang.trim() && form.errors.kode_barang }"
                                    required
                                />
                                <p v-if="form.errors.kode_barang" class="text-xs text-red-500 mt-1">
                                    {{ form.errors.kode_barang }}
                                </p>
                                <p v-else-if="!form.kode_barang.trim()" class="text-xs text-gray-500 mt-1">
                                    Kode barang harus diisi
                                </p>
                            </div>

                            <div>
                                <Label for="nama_barang" class="font-medium text-gray-700">Nama Barang *</Label>
                                <Input 
                                    id="nama_barang"
                                    v-model="form.nama_barang"
                                    type="text"
                                    placeholder="Masukkan nama barang"
                                    class="mt-1"
                                    :class="{ 'border-red-500': !form.nama_barang.trim() && form.errors.nama_barang }"
                                    required
                                />
                                <p v-if="form.errors.nama_barang" class="text-xs text-red-500 mt-1">
                                    {{ form.errors.nama_barang }}
                                </p>
                                <p v-else-if="!form.nama_barang.trim()" class="text-xs text-gray-500 mt-1">
                                    Nama barang harus diisi
                                </p>
                            </div>

                            <div>
                                <Label for="kategori" class="font-medium text-gray-700">Kategori *</Label>
                                <select
                                    id="kategori"
                                    v-model="form.kategori"
                                    required
                                    class="w-full mt-1 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    :class="{ 'border-red-500': !form.kategori && form.errors.kategori }"
                                >
                                    <option value="" disabled selected>Pilih kategori</option>
                                    <option value="peminjaman">Peminjaman</option>
                                    <option value="permintaan">Permintaan</option>
                                </select>
                                <p v-if="form.errors.kategori" class="text-xs text-red-500 mt-1">
                                    {{ form.errors.kategori }}
                                </p>
                                <p v-else-if="!form.kategori" class="text-xs text-gray-500 mt-1">
                                    Kategori harus dipilih
                                </p>
                            </div>

                            <div>
                                <Label for="stok" class="font-medium text-gray-700">Stok *</Label>
                                <Input 
                                    id="stok"
                                    v-model="form.stok"
                                    type="number"
                                    min="0"
                                    step="1"
                                    placeholder="Masukkan jumlah stok"
                                    class="mt-1"
                                    :class="{ 'border-red-500': (!form.stok || Number(form.stok) <= 0) && form.errors.stok }"
                                    required
                                />
                                <p v-if="form.errors.stok" class="text-xs text-red-500 mt-1">
                                    {{ form.errors.stok }}
                                </p>
                                <p v-else-if="!form.stok || Number(form.stok) <= 0" class="text-xs text-red-500 mt-1">
                                    Stok harus diisi dengan angka yang valid
                                </p>
                            </div>

                            <!-- Satuan Dropdown -->
                            <div>
                                <Label for="satuan" class="font-medium text-gray-700">Satuan *</Label>
                                <div class="relative">
                                    <button
                                        type="button"
                                        id="satuan"
                                        class="w-full mt-1 px-3 py-2 border rounded-md text-left bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                        :class="{ 'border-red-500': !form.satuan && form.errors.satuan }"
                                        @click="showSatuanDropdown = !showSatuanDropdown"
                                    >
                                        <span v-if="form.satuan && form.satuan !== 'Lainnya'">{{ form.satuan }}</span>
                                        <span v-else-if="form.satuan === 'Lainnya' && manualSatuan.trim()">{{ manualSatuan }}</span>
                                        <span v-else class="text-gray-400">Pilih satuan</span>
                                        <svg class="w-4 h-4 inline-block float-right" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <ul
                                        v-show="showSatuanDropdown"
                                        class="absolute z-10 w-full bg-white border rounded-md mt-1 shadow-lg max-h-60 overflow-auto"
                                    >
                                        <li
                                            v-for="option in satuanOptions"
                                            :key="option"
                                            @click="selectSatuan(option)"
                                            class="px-4 py-2 hover:bg-indigo-100 cursor-pointer"
                                        >
                                            {{ option }}
                                        </li>
                                    </ul>
                                </div>
                                <Input
                                    v-if="form.satuan === 'Lainnya'"
                                    v-model="manualSatuan"
                                    type="text"
                                    placeholder="Masukkan satuan manual"
                                    class="w-full mt-2"
                                    :class="{ 'border-red-500': form.satuan === 'Lainnya' && !manualSatuan.trim() }"
                                    required
                                />
                                <p v-if="form.errors.satuan" class="text-xs text-red-500 mt-1">
                                    {{ form.errors.satuan }}
                                </p>
                                <p v-else-if="!form.satuan" class="text-xs text-gray-500 mt-1">
                                    Satuan harus dipilih
                                </p>
                                <p v-else-if="form.satuan === 'Lainnya' && !manualSatuan.trim()" class="text-xs text-red-500 mt-1">
                                    Silakan masukkan satuan manual
                                </p>
                            </div>

                            <!-- Lokasi Dropdown -->
                            <div>
                                <Label for="lokasi" class="font-medium text-gray-700">Lokasi *</Label>
                                <div class="relative">
                                    <button
                                        type="button"
                                        id="lokasi"
                                        class="w-full mt-1 px-3 py-2 border rounded-md text-left bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                        :class="{ 'border-red-500': !form.lokasi && form.errors.lokasi }"
                                        @click="showLokasiDropdown = !showLokasiDropdown"
                                    >
                                        <span v-if="form.lokasi && form.lokasi !== 'Lainnya'">{{ form.lokasi }}</span>
                                        <span v-else-if="form.lokasi === 'Lainnya' && manualLokasi.trim()">{{ manualLokasi }}</span>
                                        <span v-else class="text-gray-400">Pilih lokasi</span>
                                        <svg class="w-4 h-4 inline-block float-right" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <ul
                                        v-show="showLokasiDropdown"
                                        class="absolute z-10 w-full bg-white border rounded-md mt-1 shadow-lg max-h-60 overflow-auto"
                                    >
                                        <li
                                            v-for="option in lokasiOptions"
                                            :key="option"
                                            @click="selectLokasi(option)"
                                            class="px-4 py-2 hover:bg-indigo-100 cursor-pointer"
                                        >
                                            {{ option }}
                                        </li>
                                    </ul>
                                </div>
                                <Input
                                    v-if="form.lokasi === 'Lainnya'"
                                    v-model="manualLokasi"
                                    type="text"
                                    placeholder="Masukkan lokasi manual"
                                    class="w-full mt-2"
                                    :class="{ 'border-red-500': form.lokasi === 'Lainnya' && !manualLokasi.trim() }"
                                    required
                                />
                                <p v-if="form.errors.lokasi" class="text-xs text-red-500 mt-1">
                                    {{ form.errors.lokasi }}
                                </p>
                                <p v-else-if="!form.lokasi" class="text-xs text-gray-500 mt-1">
                                    Lokasi harus dipilih
                                </p>
                                <p v-else-if="form.lokasi === 'Lainnya' && !manualLokasi.trim()" class="text-xs text-red-500 mt-1">
                                    Silakan masukkan lokasi manual
                                </p>
                            </div>

                            <div>
                                <Label for="deskripsi" class="font-medium text-gray-700">Deskripsi</Label>
                                <textarea 
                                    id="deskripsi"
                                    v-model="form.deskripsi"
                                    rows="4"
                                    placeholder="Tulis deskripsi barang (opsional)"
                                    class="w-full min-h-[80px] rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1"
                                />
                                <p v-if="form.errors.deskripsi" class="text-xs text-red-500 mt-1">
                                    {{ form.errors.deskripsi }}
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-2">
                            <Button 
                                type="button" 
                                variant="ghost"
                                class="flex items-center gap-2 text-gray-600 hover:text-indigo-700"
                                @click="$inertia.visit(route('barang.index'))"
                            >
                                <ArrowLeft class="w-4 h-4" /> Kembali
                            </Button>
                            <Button 
                                type="submit"
                                :disabled="form.processing"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6"
                            >
                                <span v-if="form.processing" class="inline-flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Menyimpan...
                                </span>
                                <span v-else>Simpan</span>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
