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
            <div class="max-w-2xl">
                <form @submit.prevent="submitInventaris" class="space-y-6">
                    <!-- Kategori Peminjaman (Hidden) -->
                    <input type="hidden" v-model="form.kategori_peminjaman" />
                    
                    <!-- Barang Selection -->
                    <div class="space-y-2">
                        <Label for="barang_id">Pilih Barang *</Label>
                        <!-- Debug Info -->
                        <div class="text-xs text-gray-500 bg-gray-100 p-2 rounded">
                            Debug: Total barang: {{ props.barang?.length || 0 }}, 
                            Available: {{ availableBarang.length }}, 
                            Search: "{{ searchQuery }}"
                        </div>
                        <!-- Search Input -->
                        <div class="relative mb-2">
                            <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <Input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari barang berdasarkan nama, kode, atau kategori..."
                                class="pl-10"
                            />
                        </div>
                        <!-- Native Select -->
                        <select v-model="form.barang_id" required class="border p-2 w-full">
                            <option value="" disabled>Pilih barang yang akan dipinjam</option>
                            <option v-for="item in availableBarang" :key="item.id" :value="item.id.toString()">
                                {{ item.nama_barang }} (Stok: {{ item.stok }})
                            </option>
                        </select>
                        <div class="text-xs text-blue-500">Selected barang_id: {{ form.barang_id }}</div>
                        <div class="text-xs text-blue-500">SelectedBarang: {{ selectedBarang }}</div>
                        <p v-if="form.errors.barang_id" class="text-sm text-red-600">{{ form.errors.barang_id }}</p>
                        <!-- Info stok -->
                        <div v-if="selectedBarang" class="text-sm text-blue-600 bg-blue-50 p-3 rounded-lg border border-blue-200">
                            <div class="font-medium mb-1">Informasi Barang:</div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div><span class="font-medium">Nama:</span> {{ selectedBarang.nama_barang }}</div>
                                <div><span class="font-medium">Kode:</span> {{ selectedBarang.kode_barang }}</div>
                                <div><span class="font-medium">Kategori:</span> {{ selectedBarang.kategori }}</div>
                                <div><span class="font-medium">Stok Tersedia:</span> <span class="text-green-600 font-bold">{{ selectedBarang.stok }}</span> unit</div>
                            </div>
                        </div>
                    </div>

                    <!-- Jumlah -->
                    <div class="space-y-2">
                        <Label for="jumlah">Jumlah *</Label>
                        <Input
                            id="jumlah"
                            v-model="form.jumlah"
                            type="number"
                            :min="1"
                            :max="maxJumlah"
                            placeholder="Masukkan jumlah yang dipinjam"
                            required
                        />
                        <p v-if="form.errors.jumlah" class="text-sm text-red-600">{{ form.errors.jumlah }}</p>
                        <p v-if="selectedBarang && form.jumlah > selectedBarang.stok" class="text-sm text-red-600">
                            Jumlah tidak boleh melebihi stok tersedia ({{ selectedBarang.stok }})
                        </p>
                    </div>

                    <!-- Lokasi Peminjaman -->
                    <div class="space-y-2">
                        <Label for="lokasi_peminjaman">Lokasi Peminjaman</Label>
                        <div class="relative">
                            <MapPin class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <Input
                                id="lokasi_peminjaman"
                                v-model="form.lokasi_peminjaman"
                                type="text"
                                placeholder="Contoh: Ruang Meeting, Lab Komputer, dll."
                                class="pl-10"
                            />
                        </div>
                        <p v-if="form.errors.lokasi_peminjaman" class="text-sm text-red-600">{{ form.errors.lokasi_peminjaman }}</p>
                    </div>

                    <!-- Due Date -->
                    <div class="space-y-2">
                        <Label for="due_date">Tanggal Jatuh Tempo *</Label>
                        <div class="relative">
                            <CalendarDays class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <Input
                                id="due_date"
                                v-model="form.due_date"
                                type="date"
                                :min="new Date().toISOString().split('T')[0]"
                                placeholder="Pilih tanggal jatuh tempo"
                                class="pl-10"
                                required
                            />
                        </div>
                        <p v-if="form.errors.due_date" class="text-sm text-red-600">{{ form.errors.due_date }}</p>
                    </div>

                    <!-- Keterangan -->
                    <div class="space-y-2">
                        <Label for="keterangan">Keterangan</Label>
                        <Textarea
                            id="keterangan"
                            v-model="form.keterangan"
                            placeholder="Jelaskan tujuan peminjaman inventaris..."
                            :rows="4"
                        />
                        <p v-if="form.errors.keterangan" class="text-sm text-red-600">{{ form.errors.keterangan }}</p>
                    </div>

                    <!-- Submit Button -->
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
                            <Button type="button" variant="outline">
                                Batal
                            </Button>
                        </Link>
                    </div>
                </form>
            </div>

            <!-- Info Card -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <h3 class="font-semibold text-purple-800 mb-2">Informasi Inventaris</h3>
                <ul class="text-sm text-purple-700 space-y-1">
                    <li>• Nomor inventaris akan dibuat otomatis setelah disimpan</li>
                    <li>• Tanggal peminjaman akan diisi otomatis dengan tanggal hari ini</li>
                    <li>• Status awal akan diset sebagai "Menunggu" dan perlu persetujuan admin</li>
                    <li>• Pastikan stok barang mencukupi sebelum membuat permintaan</li>
                    <li>• Hanya barang dengan stok tersedia yang dapat dipilih</li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>