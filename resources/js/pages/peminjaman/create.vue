<script setup lang="ts">
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { ArrowLeft, Package, Search, CalendarDays } from 'lucide-vue-next'
import { type BreadcrumbItem } from '@/types';

interface Barang {
    id: number
    kode_barang: string
    nama_barang: string
    kategori: 'peminjaman' | 'permintaan'
    stok: number
    deskripsi?: string
}

const props = defineProps<{
    barang: Barang[]
}>()

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Aset', href: route('peminjaman.index') },
    { title: 'Buat Aset', href: route('peminjaman.create') },
];

const form = useForm({
    barang_id: '',
    jumlah: '',
    keterangan: '',
    due_date: '',
    tanggal_pengembalian: '',
})

const showDropdown = ref(false)
const searchQuery = ref('')
const selectedBarang = ref<Barang | null>(null);

const filteredBarang = computed(() => {
    if (!searchQuery.value) return props.barang.filter(item => item.kategori === 'peminjaman');
    
    return props.barang.filter(item => 
        item.kategori === 'peminjaman' &&
        (item.nama_barang.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        item.kode_barang.toLowerCase().includes(searchQuery.value.toLowerCase()))
    )
})

const handleBarangSelect = (barang: Barang) => {
    selectedBarang.value = barang
    form.barang_id = barang.id.toString()
    showDropdown.value = false
    searchQuery.value = ''
}

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value
    if (showDropdown.value) {
        searchQuery.value = ''
    }
}

const submitPeminjaman = () => {
    form.post(route('peminjaman.store'), {
        onSuccess: () => {
            form.reset()
            selectedBarang.value = null;
        },
        onError: (errors) => {
            console.error('Form submission errors:', errors);
        }
    })
}

const handleClickOutside = (event: Event) => {
    const target = event.target as HTMLElement;
    if (!target.closest('.dropdown-container')) {
        showDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

</script>

<template>
    <Head title="Buat Peminjaman" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 md:p-6 space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <Link :href="route('peminjaman.index')" class="text-muted-foreground hover:text-foreground">
                            <ArrowLeft class="h-5 w-5" />
                        </Link>
                        <h1 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                            <Package class="h-6 w-6" />
                            Buat Peminjaman Barang
                        </h1>
                    </div>
                    <p class="text-muted-foreground">Buat peminjaman barang dengan tenggat waktu</p>
                </div>
            </div>

            <!-- Form Container -->
            <div class="max-w-xl mx-auto">
                <div class="bg-white rounded-lg border p-6">
                    <h2 class="text-lg font-medium mb-6">Form Peminjaman Barang</h2>

                    <form @submit.prevent="submitPeminjaman" class="space-y-6">
                        <!-- PILIH BARANG -->
                        <div class="space-y-1 dropdown-container">
                            <Label>Pilih Barang *</Label>
                            <div class="relative">
                                <Input
                                    type="text"
                                    v-model="searchQuery"
                                    @focus="toggleDropdown"
                                    placeholder="Cari barang..."
                                    class="pr-10"
                                />
                                <Search class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" />
                            </div>
                            <input type="hidden" v-model="form.barang_id" />
                            <p v-if="form.errors.barang_id" class="text-sm text-red-600">{{ form.errors.barang_id }}</p>

                            <div v-if="selectedBarang" class="mt-2 p-3 border rounded-md bg-gray-100 flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ selectedBarang.nama_barang }}</span>
                                    <span class="text-sm text-gray-600">Kode: {{ selectedBarang.kode_barang }} | Stok: {{ selectedBarang.stok }}</span>
                                </div>
                                <Button type="button" variant="ghost" size="sm" @click="selectedBarang = null; form.barang_id = ''; searchQuery = ''">
                                    X
                                </Button>
                            </div>

                            <ul v-if="showDropdown && filteredBarang.length > 0" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto mt-1">
                                <li
                                    v-for="item in filteredBarang"
                                    :key="item.id"
                                    @click="handleBarangSelect(item)"
                                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                                >
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ item.nama_barang }}</span>
                                        <span class="text-sm text-gray-600">Kode: {{ item.kode_barang }} | Stok: {{ item.stok }}</span>
                                    </div>
                                </li>
                            </ul>
                            <p v-if="showDropdown && filteredBarang.length === 0" class="px-4 py-2 text-sm text-gray-500">
                                Tidak ada barang tersedia untuk dipinjam.
                            </p>
                        </div>

                        <!-- JUMLAH -->
                        <div class="space-y-1">
                            <Label for="jumlah">Jumlah *</Label>
                            <Input
                                id="jumlah"
                                type="number"
                                v-model="form.jumlah"
                                :min="1"
                                :max="selectedBarang ? selectedBarang.stok : undefined"
                            />
                            <p v-if="form.errors.jumlah" class="text-sm text-red-600">{{ form.errors.jumlah }}</p>
                            <p v-if="selectedBarang && form.jumlah && parseInt(form.jumlah) > selectedBarang.stok" class="text-sm text-red-600">
                                ⚠️ Jumlah yang diminta melebihi stok yang tersedia (Stok: {{ selectedBarang.stok }})
                            </p>
                        </div>

                        <!-- TENGGAT WAKTU -->
                        <div class="space-y-1">
                            <Label for="due_date">Tenggat Waktu *</Label>
                            <div class="relative">
                                <Input
                                    id="due_date"
                                    type="date"
                                    v-model="form.due_date"
                                    class="pr-10"
                                />
                                <CalendarDays class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" />
                            </div>
                            <p v-if="form.errors.due_date" class="text-sm text-red-600">{{ form.errors.due_date }}</p>
                        </div>

                        <!-- TANGGAL KEMBALI -->
                        <div class="space-y-1">
                            <Label for="tanggal_pengembalian">Tanggal Kembali *</Label>
                            <div class="relative">
                                <Input
                                    id="tanggal_pengembalian"
                                    type="date"
                                    v-model="form.tanggal_pengembalian"
                                    class="pr-10"
                                />
                                <CalendarDays class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" />
                            </div>
                            <p v-if="form.errors.tanggal_pengembalian" class="text-sm text-red-600">{{ form.errors.tanggal_pengembalian }}</p>
                        </div>

                        <!-- KETERANGAN (Opsional) -->
                        <div class="space-y-1">
                            <Label for="keterangan">Keterangan (Opsional)</Label>
                            <textarea
                                id="keterangan"
                                v-model="form.keterangan"
                                rows="3"
                                class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            ></textarea>
                            <p v-if="form.errors.keterangan" class="text-sm text-red-600">{{ form.errors.keterangan }}</p>
                        </div>

                        <Button type="submit" :disabled="form.processing">
                            <span v-if="form.processing">Memproses...</span>
                            <span v-else>Ajukan Peminjaman</span>
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>