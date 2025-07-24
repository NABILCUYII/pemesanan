<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, Package, CalendarDays, MapPin, Search } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, ref, watch } from 'vue';

interface Barang {
    id: number;
    kode_barang: string;
    nama_barang: string;
    deskripsi: string;
    kategori: string;
    stok: number;
}

const props = defineProps<{
    barang: Barang[];
}>();

const form = useForm({
    barang_id: '',
    jumlah: 1,
    keterangan: '',
    lokasi_peminjaman: '',
    due_date: '',
    kategori_peminjaman: 'peminjaman', // Tambahkan kategori peminjaman
});

const searchQuery = ref('');

// Filter barang yang memiliki stok > 0 dan berdasarkan pencarian
const availableBarang = computed(() => {
    console.log('Filtering barang:', props.barang);
    
    if (!props.barang || props.barang.length === 0) {
        console.log('No barang data available');
        return [];
    }
    
    let filtered = props.barang.filter(item => {
        console.log(`Checking item ${item.nama_barang}: stok = ${item.stok}`);
        return item.stok > 0;
    });
    
    console.log('Filtered by stok > 0:', filtered);
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(item => 
            item.nama_barang.toLowerCase().includes(query) ||
            item.kode_barang.toLowerCase().includes(query) ||
            (item.kategori && item.kategori.toLowerCase().includes(query))
        );
        console.log('Filtered by search query:', filtered);
    }
    
    return filtered;
});

// Get selected barang info
const selectedBarang = computed(() => {
    if (!form.barang_id) return null;
    return props.barang.find(item => item.id.toString() === form.barang_id);
});

// Update jumlah maksimum berdasarkan stok
const maxJumlah = computed(() => {
    return selectedBarang.value?.stok || 1;
});

const submitInventaris = () => {
    // Tambahkan kategori peminjaman ke data yang dikirim
    form.post(route('inventaris.store'));
};

// Debug: Log barang data to console
console.log('Barang data:', props.barang);
console.log('Available barang:', availableBarang.value);

// Pastikan form.barang_id selalu string
watch(() => form.barang_id, (val) => {
  if (val && typeof val !== 'string') {
    form.barang_id = String(val);
  }
});
</script>

<template>
    <Head title="Tambah Inventaris" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link :href="route('inventaris.index')">
                    <Button variant="outline" size="sm">
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Kembali
                    </Button>
                </Link>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                        <Package class="w-6 h-6 text-purple-600" />
                        Tambah Inventaris
                    </h1>
                    <p class="text-gray-600">Buat permintaan peminjaman inventaris baru</p>
                </div>
            </div>

            <!-- Form -->
            <div class="max-w-3xl space-y-6">
                <form @submit.prevent="submitInventaris" class="space-y-8">

                    <!-- Section 1: Pilih Barang -->
                    <div class="bg-white p-6 rounded-xl shadow border space-y-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">Informasi Barang</h2>
                            <p class="text-sm text-gray-500">Pilih barang yang ingin dipinjam, pastikan tersedia.</p>
                        </div>

                        <!-- Search -->
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <Input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari nama, kode, atau kategori..."
                                class="pl-10"
                            />
                        </div>
                        <!-- Dropdown Select Barang dengan Card Style dalam Box -->
                        <div class="bg-gradient-to-br from-white via-blue-50 to-purple-50 border border-gray-200 rounded-2xl shadow-2xl p-4">
                            <div class="mb-4">
                                <Label for="barang_id" class="font-semibold text-gray-800">Pilih Barang *</Label>
                            </div>
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button
                                        variant="outline"
                                        class="w-full justify-between"
                                        type="button"
                                    >
                                        <span>
                                            <span v-if="selectedBarang">
                                                {{ selectedBarang.nama_barang }} ({{ selectedBarang.kode_barang }})
                                            </span>
                                            <span v-else class="text-gray-400">Pilih Barang *</span>
                                        </span>
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent class="w-[440px] max-w-full p-0 rounded-2xl shadow-2xl border border-gray-200 bg-gradient-to-br from-white via-blue-50 to-purple-50">
                                    <div class="p-4 sticky top-0 bg-white/90 z-10 border-b rounded-t-2xl">
                                        <Label for="barang_id" class="font-semibold text-gray-800">Pilih Barang *</Label>
                                    </div>
                                    <div class="max-h-80 overflow-y-auto px-2 pb-2">
                                        <form>
                                            <div
                                                v-for="item in availableBarang"
                                                :key="item.id"
                                                class="group relative flex items-center gap-4 bg-white rounded-xl shadow-sm border border-gray-200 hover:border-purple-400 hover:shadow-lg transition mb-3 px-4 py-3 cursor-pointer"
                                                :class="{'ring-2 ring-purple-400': form.barang_id == item.id.toString()}"
                                            >
                                                <input
                                                    type="radio"
                                                    :id="'barang_' + item.id"
                                                    name="barang_id"
                                                    :value="item.id.toString()"
                                                    v-model="form.barang_id"
                                                    required
                                                    class="form-radio accent-purple-600 mr-3 mt-1"
                                                />
                                                <label :for="'barang_' + item.id" class="flex-1 cursor-pointer">
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-semibold text-gray-900 text-base group-hover:text-purple-700">
                                                            {{ item.nama_barang }}
                                                        </span>
                                                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full font-medium ml-2">
                                                            {{ item.kode_barang }}
                                                        </span>
                                                    </div>
                                                    <div class="flex flex-wrap gap-2 mt-1 text-xs">
                                                        <span class="inline-flex items-center bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">
                                                            Kategori: {{ item.kategori }}
                                                        </span>
                                                        <span class="inline-flex items-center"
                                                            :class="item.stok > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                                            <span class="px-2 py-0.5 rounded-full font-bold">
                                                                Stok: {{ item.stok }}
                                                            </span>
                                                        </span>
                                                    </div>
                                                </label>
                                                <div v-if="form.barang_id == item.id.toString()" class="absolute top-2 right-2">
                                                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div v-if="availableBarang.length === 0" class="p-4 text-center text-gray-400">
                                                Tidak ada barang tersedia.
                                            </div>
                                        </form>
                                    </div>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>

                        <!-- Info Barang -->
                        <div v-if="selectedBarang" class="text-sm text-gray-700 bg-blue-50 p-4 rounded border border-blue-200">
                            <div class="font-medium mb-2">Informasi Barang Terpilih:</div>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div><b>Nama:</b> {{ selectedBarang.nama_barang }}</div>
                                <div><b>Kode:</b> {{ selectedBarang.kode_barang }}</div>
                                <div><b>Kategori:</b> {{ selectedBarang.kategori }}</div>
                                <div><b>Stok:</b> <span class="text-green-600 font-bold">{{ selectedBarang.stok }}</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Detail Peminjaman -->
                    <div class="bg-white p-6 rounded-xl shadow border space-y-4">
                        <h2 class="text-lg font-semibold text-gray-800">Detail Peminjaman</h2>

                        <!-- Jumlah -->
                        <div>
                            <Label for="jumlah">Jumlah *</Label>
                            <Input
                                id="jumlah"
                                v-model="form.jumlah"
                                type="number"
                                :min="1"
                                :max="maxJumlah"
                                required
                            />
                            <p v-if="form.errors.jumlah" class="text-sm text-red-600">{{ form.errors.jumlah }}</p>
                            <p v-if="selectedBarang && form.jumlah > selectedBarang.stok" class="text-sm text-red-600">
                                Melebihi stok tersedia ({{ selectedBarang.stok }})
                            </p>
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <Label for="lokasi_peminjaman">Lokasi Peminjaman</Label>
                            <div class="relative">
                                <MapPin class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                <Input
                                    id="lokasi_peminjaman"
                                    v-model="form.lokasi_peminjaman"
                                    type="text"
                                    placeholder="Contoh: Lab Komputer, Aula, dll."
                                    class="pl-10"
                                />
                            </div>
                            <p v-if="form.errors.lokasi_peminjaman" class="text-sm text-red-600">{{ form.errors.lokasi_peminjaman }}</p>
                        </div>

                        <!-- Due Date -->
                        <div>
                            <Label for="due_date">Tanggal Jatuh Tempo *</Label>
                            <div class="relative">
                                <CalendarDays class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                <Input
                                    id="due_date"
                                    v-model="form.due_date"
                                    type="date"
                                    :min="new Date().toISOString().split('T')[0]"
                                    class="pl-10"
                                    required
                                />
                            </div>
                            <p v-if="form.errors.due_date" class="text-sm text-red-600">{{ form.errors.due_date }}</p>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <Label for="keterangan">Keterangan</Label>
                            <Textarea
                                id="keterangan"
                                v-model="form.keterangan"
                                :rows="4"
                                placeholder="Tujuan peminjaman"
                            />
                            <p v-if="form.errors.keterangan" class="text-sm text-red-600">{{ form.errors.keterangan }}</p>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex gap-4">
                        <Button 
                            type="submit" 
                            :disabled="form.processing || !selectedBarang || form.jumlah > maxJumlah" 
                            class="flex-1"
                        >
                            <Package class="w-4 h-4 mr-2" />
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Inventaris' }}
                        </Button>
                        <Link :href="route('inventaris.index')">
                            <Button type="button" variant="outline">Batal</Button>
                        </Link>
                    </div>
                </form>

                <!-- Info Card -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mt-10">
                    <h3 class="font-semibold text-purple-800 mb-2">Informasi Inventaris</h3>
                    <ul class="text-sm text-purple-700 space-y-1 list-disc list-inside">
                        <li>Nomor inventaris dibuat otomatis</li>
                        <li>Tanggal peminjaman = tanggal hari ini</li>
                        <li>Status awal: <b>Menunggu</b> (perlu persetujuan)</li>
                        <li>Pastikan stok mencukupi</li>
                        <li>Hanya barang dengan stok > 0 yang ditampilkan</li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>