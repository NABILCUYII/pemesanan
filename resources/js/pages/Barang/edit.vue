<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';

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
    stok: props.barang.stok.toString(),
    deskripsi: props.barang.deskripsi
});

const submit = () => {
    form.put(route('barang.update', props.barang.id));
};
</script>

<template>
    <Head title="Edit Barang" />

    <AppLayout>
        <div class="p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold">Edit Barang</h1>
            </div>

            <div class="bg-white rounded-lg border p-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="kategori">Kategori</Label>
                            <select
                                id="kategori"
                                v-model="form.kategori"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                required
                            >
                                <option value="">Pilih kategori</option>
                                <option value="peminjaman">Peminjaman</option>
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

