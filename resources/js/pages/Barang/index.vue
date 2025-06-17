<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Plus, Pencil, Trash2, Search } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';

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

            <!-- TABEL (untuk md ke atas) -->
            <div class="bg-white rounded-lg border hidden md:block">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Kode</TableHead>
                            <TableHead>Nama</TableHead>
                            <TableHead>Kategori</TableHead>
                            <TableHead>Stok</TableHead>
                            <TableHead>Deskripsi</TableHead>
                            <TableHead class="text-right">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="item in filteredBarang" :key="item.id">
                            <TableCell>{{ item.kode_barang }}</TableCell>
                            <TableCell>{{ item.nama_barang }}</TableCell>
                            <TableCell>
                                <span :class="[
                                    'px-2 py-1 rounded-full text-xs font-semibold',
                                    item.kategori === 'peminjaman'
                                        ? 'bg-blue-100 text-blue-700'
                                        : 'bg-green-100 text-green-700'
                                ]">
                                    {{ item.kategori === 'peminjaman' ? 'Peminjaman' : 'Permintaan' }}
                                </span>
                            </TableCell>
                            <TableCell>{{ item.stok }}</TableCell>
                            <TableCell>{{ item.deskripsi }}</TableCell>
                            <TableCell class="text-right space-x-2">
                                <Link :href="route('barang.edit', item.id)">
                                    <Button variant="outline" size="sm">
                                        <Pencil class="w-4 h-4" />
                                    </Button>
                                </Link>
                                <Button variant="destructive" size="sm" @click="deleteBarang(item.id)">
                                    <Trash2 class="w-4 h-4" />
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- CARD (untuk hp/sm) -->
            <div class="space-y-4 md:hidden">
                <div
                    v-for="item in filteredBarang"
                    :key="item.id"
                    class="border rounded-lg p-4 bg-white shadow-sm"
                >
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="font-semibold text-lg">{{ item.nama_barang }}</h2>
                        <span :class="[
                            'px-2 py-1 text-xs rounded-full font-medium',
                            item.kategori === 'peminjaman'
                                ? 'bg-blue-100 text-blue-700'
                                : 'bg-green-100 text-green-700'
                        ]">
                            {{ item.kategori }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mb-1"><strong>Kode:</strong> {{ item.kode_barang }}</p>
                    <p class="text-sm text-gray-500 mb-1"><strong>Stok:</strong> {{ item.stok }}</p>
                    <p class="text-sm text-gray-500 mb-3"><strong>Deskripsi:</strong> {{ item.deskripsi }}</p>
                    <div class="flex gap-2 justify-end">
                        <Link :href="route('barang.edit', item.id)">
                            <Button variant="outline" size="sm">
                                <Pencil class="w-4 h-4" />
                            </Button>
                        </Link>
                        <Button variant="destructive" size="sm" @click="deleteBarang(item.id)">
                            <Trash2 class="w-4 h-4" />
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

