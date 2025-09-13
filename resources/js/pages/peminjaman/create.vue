<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { ArrowLeft, Package, Search, CalendarDays, X } from 'lucide-vue-next'
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

const showBarangModal = ref(false)
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
    showBarangModal.value = false
    searchQuery.value = ''
}

const openBarangModal = () => {
    showBarangModal.value = true
    searchQuery.value = ''
}

const closeBarangModal = () => {
    showBarangModal.value = false
    searchQuery.value = ''
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
</script>

<template>
    <Head title="Buat Peminjaman" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100 py-10">
            <div class="max-w-2xl mx-auto">
                <!-- Header -->
                <div class="flex items-center gap-3 mb-8">
                    <Link :href="route('peminjaman.index')" class="text-blue-500 hover:text-blue-700 transition">
                        <ArrowLeft class="h-6 w-6" />
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                        <span class="inline-flex items-center justify-center bg-blue-100 text-blue-600 rounded-full p-2 shadow">
                            <Package class="h-7 w-7" />
                        </span>
                        Buat Peminjaman Barang
                    </h1>
                </div>
                <div class="bg-white/90 shadow-xl rounded-2xl border border-blue-100 p-8">
                    <h2 class="text-xl font-semibold text-blue-700 mb-7 flex items-center gap-2">
                        <span class="inline-block w-2 h-6 bg-blue-400 rounded-full mr-2"></span>
                        Formulir Peminjaman
                    </h2>
                    <form @submit.prevent="submitPeminjaman" class="space-y-7">
                        <!-- PILIH BARANG (MODAL) -->
                        <div>
                            <Label class="font-semibold text-gray-700 mb-1">Pilih Barang <span class="text-red-500">*</span></Label>
                            <div>
                                <Button
                                    type="button"
                                    class="w-full flex items-center justify-between border border-blue-200 rounded-xl py-3 px-4 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold transition"
                                    @click="openBarangModal"
                                    :tabindex="0"
                                    :autofocus="!selectedBarang"
                                >
                                    <span v-if="selectedBarang">
                                        {{ selectedBarang.nama_barang }} <span class="text-xs text-gray-500 ml-2">(Kode: {{ selectedBarang.kode_barang }})</span>
                                    </span>
                                    <span v-else class="text-gray-400">Klik untuk memilih barang...</span>
                                    <Search class="w-5 h-5 ml-2 text-blue-400" />
                                </Button>
                                <input type="hidden" v-model="form.barang_id" />
                                <transition name="fade">
                                    <p v-if="form.errors.barang_id" class="text-xs text-red-600 mt-1">{{ form.errors.barang_id }}</p>
                                </transition>
                                <transition name="fade">
                                    <div v-if="selectedBarang" class="mt-3 p-4 border border-blue-200 rounded-xl bg-blue-50 flex items-center justify-between shadow-sm">
                                        <div>
                                            <span class="font-semibold text-blue-700">{{ selectedBarang.nama_barang }}</span>
                                            <div class="text-xs text-gray-500 mt-1">
                                                Kode: <span class="font-mono">{{ selectedBarang.kode_barang }}</span> &nbsp;|&nbsp; Stok: <span class="font-semibold">{{ selectedBarang.stok }}</span>
                                            </div>
                                        </div>
                                        <Button type="button" variant="ghost" size="icon" class="text-red-500 hover:bg-red-100" @click="selectedBarang = null; form.barang_id = ''">
                                            <span class="text-lg font-bold">&times;</span>
                                        </Button>
                                    </div>
                                </transition>
                            </div>
                        </div>

                        <!-- Modal Pilih Barang -->
                        <transition name="fade">
                        <div v-if="showBarangModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
                            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 relative border border-blue-200">
                                <button @click="closeBarangModal" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 transition">
                                    <X class="w-6 h-6" />
                                </button>
                                <h3 class="text-lg font-semibold text-blue-700 mb-4 flex items-center gap-2">
                                    <Search class="w-5 h-5" /> Pilih Barang
                                </h3>
                                <Input
                                    type="text"
                                    v-model="searchQuery"
                                    placeholder="Cari nama/kode barang..."
                                    class="mb-4 pr-12 py-3 rounded-xl border-blue-200 focus:ring-2 focus:ring-blue-400 transition"
                                    autofocus
                                />
                                <div class="max-h-64 overflow-y-auto">
                                    <ul v-if="filteredBarang.length > 0">
                                        <li
                                            v-for="item in filteredBarang"
                                            :key="item.id"
                                            @click="handleBarangSelect(item)"
                                            class="px-5 py-3 hover:bg-blue-50 cursor-pointer transition flex flex-col rounded-lg mb-1"
                                        >
                                            <span class="font-semibold text-gray-800">{{ item.nama_barang }}</span>
                                            <span class="text-xs text-gray-500">Kode: {{ item.kode_barang }} | Stok: {{ item.stok }}</span>
                                        </li>
                                    </ul>
                                    <p v-else class="text-xs text-gray-500 bg-gray-50 rounded-xl px-5 py-3 text-center">
                                        Tidak ada barang tersedia untuk dipinjam.
                                    </p>
                                </div>
                            </div>
                        </div>
                        </transition>

                        <!-- JUMLAH -->
                        <div>
                            <Label for="jumlah" class="font-semibold text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></Label>
                            <Input
                                id="jumlah"
                                type="number"
                                v-model="form.jumlah"
                                :min="1"
                                :max="selectedBarang ? selectedBarang.stok : undefined"
                                class="rounded-xl border-blue-200 py-3 focus:ring-2 focus:ring-blue-400 transition"
                                placeholder="Masukkan jumlah"
                            />
                            <transition name="fade">
                                <p v-if="form.errors.jumlah" class="text-xs text-red-600 mt-1">{{ form.errors.jumlah }}</p>
                            </transition>
                            <p v-if="selectedBarang && form.jumlah && parseInt(form.jumlah) > selectedBarang.stok" class="text-sm text-red-600">
                                ⚠️ Jumlah yang diminta melebihi stok yang tersedia (Stok: {{ selectedBarang.stok }})
                            </p>
                        </div>

                        <!-- WAKTU PEMINJAMAN -->
                        <div>
                            <Label for="due_date" class="font-semibold text-gray-700 mb-1"> Waktu Peminjaman <span class="text-red-500">*</span></Label>
                            <div class="relative">
                                <Input
                                    id="due_date"
                                    type="date"
                                    v-model="form.due_date"
                                    class="pr-12 rounded-xl border-blue-200 py-3 focus:ring-2 focus:ring-blue-400 transition"
                                />
                                <CalendarDays class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-blue-400 pointer-events-none" />
                            </div>
                            <transition name="fade">
                                <p v-if="form.errors.due_date" class="text-xs text-red-600 mt-1">{{ form.errors.due_date }}</p>
                            </transition>
                        </div>

                        <!-- TANGGAL KEMBALI -->
                        <div>
                            <Label for="tanggal_pengembalian" class="font-semibold text-gray-700 mb-1">Tanggal Kembali <span class="text-red-500">*</span></Label>
                            <div class="relative">
                                <Input
                                    id="tanggal_pengembalian"
                                    type="date"
                                    v-model="form.tanggal_pengembalian"
                                    class="pr-12 rounded-xl border-blue-200 py-3 focus:ring-2 focus:ring-blue-400 transition"
                                />
                                <CalendarDays class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-blue-400 pointer-events-none" />
                            </div>
                            <transition name="fade">
                                <p v-if="form.errors.tanggal_pengembalian" class="text-xs text-red-600 mt-1">{{ form.errors.tanggal_pengembalian }}</p>
                            </transition>
                        </div>

                        <!-- KETERANGAN (Opsional) -->
                        <div>
                            <Label for="keterangan" class="font-semibold text-gray-700 mb-1">Keterangan <span class="text-gray-400">(Opsional)</span></Label>
                            <textarea
                                id="keterangan"
                                v-model="form.keterangan"
                                rows="3"
                                class="w-full p-3 border border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 transition resize-none"
                                placeholder="Tulis keterangan tambahan jika diperlukan..."
                            ></textarea>
                            <transition name="fade">
                                <p v-if="form.errors.keterangan" class="text-xs text-red-600 mt-1">{{ form.errors.keterangan }}</p>
                            </transition>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold px-8 py-3 rounded-xl shadow-lg transition disabled:opacity-60"
                                autofocus
                            >
                                <span v-if="form.processing">Memproses...</span>
                                <span v-else>Ajukan Peminjaman</span>
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>