<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import { PackagePlus, ArrowLeft } from 'lucide-vue-next';

const form = useForm({
    kode_barang: '',
    nama_barang: '',
    kategori: '',
    stok: '',
    deskripsi: ''
});

const kategoriOptions = [
    { value: 'peminjaman', label: 'Peminjaman' },
    { value: 'permintaan', label: 'Permintaan' }
];

const submit = () => {
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
                                <Label for="kode_barang" class="font-medium text-gray-700">Kode Barang</Label>
                                <Input 
                                    id="kode_barang"
                                    v-model="form.kode_barang"
                                    type="text"
                                    placeholder="Masukkan kode unik barang"
                                    class="mt-1"
                                    required
                                />
                                <p v-if="form.errors.kode_barang" class="text-xs text-red-500 mt-1">
                                    {{ form.errors.kode_barang }}
                                </p>
                            </div>

                            <div>
                                <Label for="nama_barang" class="font-medium text-gray-700">Nama Barang</Label>
                                <Input 
                                    id="nama_barang"
                                    v-model="form.nama_barang"
                                    type="text"
                                    placeholder="Masukkan nama barang"
                                    class="mt-1"
                                    required
                                />
                                <p v-if="form.errors.nama_barang" class="text-xs text-red-500 mt-1">
                                    {{ form.errors.nama_barang }}
                                </p>
                            </div>

                            <div>
                                <Label for="kategori" class="font-medium text-gray-700">Kategori</Label>
                                <Select v-model="form.kategori">
                                    <SelectTrigger id="kategori" class="w-full mt-1" required>
                                        <SelectValue placeholder="Pilih kategori" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="opt in kategoriOptions" :key="opt.value" :value="opt.value">
                                            {{ opt.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.kategori" class="text-xs text-red-500 mt-1">
                                    {{ form.errors.kategori }}
                                </p>
                            </div>

                            <div>
                                <Label for="stok" class="font-medium text-gray-700">Stok</Label>
                                <Input 
                                    id="stok"
                                    v-model="form.stok"
                                    type="number"
                                    min="0"
                                    placeholder="Masukkan jumlah stok"
                                    class="mt-1"
                                    required
                                />
                                <p v-if="form.errors.stok" class="text-xs text-red-500 mt-1">
                                    {{ form.errors.stok }}
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
                                Simpan
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
