<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus, Pencil, Trash2, Search, Eye } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

const props = defineProps<{
    barang: {
        id: number;
        kode_barang: string;
        nama_barang: string;
        deskripsi: string;
        kategori: string;
        stok: number;
    }[];
}>();

const searchQuery = ref('');
const selectedKategori = ref('');

const filteredBarang = computed(() => {
    return props.barang.filter(item => {
        const matchesSearch = item.nama_barang.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.kode_barang.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesKategori = !selectedKategori.value || item.kategori === selectedKategori.value;
        return matchesSearch && matchesKategori;
    });
});

const groupedBarang = computed(() => {
    return filteredBarang.value.reduce((acc, item) => {
        const key = item.kategori;
        if (!acc[key]) {
            acc[key] = [];
        }
        acc[key].push(item);
        return acc;
    }, {} as Record<string, typeof props.barang>);
});

const deleteBarang = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
        router.delete(route('barang.destroy', id));
    }
};
</script>

<template>
    <Head title="Barang" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h1 class="text-2xl font-semibold text-gray-800">Daftar Barang</h1>
                <Link :href="route('barang.create')">
                    <Button class="w-full sm:w-auto">
                        <Plus class="w-4 h-4 mr-2" />
                        Tambah Barang
                    </Button>
                </Link>
            </div>

            <!-- Filter & Search -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari nama atau kode barang..."
                        class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
                <select
                    v-model="selectedKategori"
                    class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-60"
                >
                    <option value="">Semua Kategori</option>
                    <option value="peminjaman">Peminjaman</option>
                    <option value="permintaan">Permintaan</option>
                </select>
            </div>

            <!-- Card Grid Layout -->
            <div class="space-y-8">
                <template v-if="Object.keys(groupedBarang).length > 0">
                    <div v-for="(items, kategori) in groupedBarang" :key="kategori" class="space-y-4">
                        <h2 class="font-semibold text-2xl text-gray-800 capitalize">
                           {{ kategori === 'peminjaman' ? 'Barang Peminjaman' : 'Barang Habis Pakai (Permintaan)' }}
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            <div
                                v-for="item in items"
                                :key="item.id"
                                class="rounded-lg shadow-lg flex flex-col transition-transform transform hover:-translate-y-1"
                                :class="[item.kategori === 'peminjaman' ? 'bg-blue-50' : 'bg-green-50']"
                            >
                                <div class="p-5 flex-grow">
                                    <h3 class="font-bold text-lg mb-2 text-gray-800">{{ item.nama_barang }}</h3>
                                    <span :class="[
                                        'px-2 py-1 text-xs rounded-full font-medium whitespace-nowrap',
                                        item.kategori === 'peminjaman'
                                            ? 'bg-blue-200 text-blue-800'
                                            : 'bg-green-200 text-green-800'
                                    ]">
                                        {{ item.kategori === 'peminjaman' ? 'Peminjaman' : 'Permintaan' }}
                                    </span>
                                </div>
                                <div class="p-5 bg-white rounded-b-lg border-t">
                                    <Dialog>
                                        <DialogTrigger as-child>
                                            <Button class="w-full">
                                                <Eye class="w-4 h-4 mr-2" />
                                                Lihat Detail
                                            </Button>
                                        </DialogTrigger>
                                        <DialogContent class="sm:max-w-lg">
                                            <DialogHeader>
                                                <DialogTitle>{{ item.nama_barang }}</DialogTitle>
                                                <DialogDescription>
                                                   Detail lengkap untuk barang. Klik tombol aksi di bawah jika diperlukan.
                                                </DialogDescription>
                                            </DialogHeader>
                                            <div class="space-y-3 py-4 text-sm">
                                               <p><strong>Kode Barang:</strong> {{ item.kode_barang }}</p>
                                               <p><strong>Kategori:</strong> <span class="capitalize">{{ item.kategori === 'peminjaman' ? 'Peminjaman' : 'Permintaan' }}</span></p>
                                               <p><strong>Stok Tersedia:</strong> {{ item.stok }}</p>
                                               <div>
                                                   <p class="font-semibold"><strong>Deskripsi:</strong></p>
                                                   <p class="text-gray-600">{{ item.deskripsi || 'Tidak ada deskripsi.' }}</p>
                                               </div>
                                            </div>
                                            <DialogFooter class="flex flex-col sm:flex-row gap-2 mt-4">
                                                <Link :href="route('barang.edit', item.id)" class="w-full sm:w-auto">
                                                    <Button variant="outline" class="w-full">
                                                        <Pencil class="w-4 h-4 mr-2" /> Edit
                                                    </Button>
                                                </Link>
                                                <Button variant="destructive" @click="deleteBarang(item.id)" class="w-full sm:w-auto">
                                                    <Trash2 class="w-4 h-4 mr-2" /> Hapus
                                                </Button>
                                            </DialogFooter>
                                        </DialogContent>
                                    </Dialog>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                 <div v-else class="text-center py-16 text-gray-500 bg-white rounded-lg border">
                    <p class="text-xl">Tidak ada barang ditemukan.</p>
                    <p>Coba ubah kata kunci pencarian atau filter kategori Anda.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

