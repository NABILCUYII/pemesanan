<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { ArrowLeft, Package, Search, Check, Plus, Trash2 } from 'lucide-vue-next'

const props = defineProps({
    barang: {
        type: Array,
        default: () => []
    }
})

const form = useForm({
    requests: []
})

const showDropdown = ref(null)
const searchQuery = ref('')

const filteredBarang = computed(() => {
    if (!props.barang) return []
    return props.barang.filter(item => 
        item.nama_barang.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        item.kode_barang.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
})

// Initialize with one empty request
const initializeForm = () => {
    form.requests = [{
        id: generateId(),
        barang_id: '',
        jumlah: '',
        keterangan: '',
        selectedBarang: null
    }]
}

const generateId = () => {
    return Math.random().toString(36).substr(2, 9)
}

const addRequest = () => {
    form.requests.push({
        id: generateId(),
        barang_id: '',
        jumlah: '',
        keterangan: '',
        selectedBarang: null
    })
}

const removeRequest = (index) => {
    if (form.requests.length > 1) {
        form.requests.splice(index, 1)
    }
}

const handleBarangSelect = (requestIndex, barang) => {
    if (form.requests[requestIndex]) {
        form.requests[requestIndex].selectedBarang = barang
        form.requests[requestIndex].barang_id = barang.id.toString()
    }
    showDropdown.value = null
    searchQuery.value = ''
}

const toggleDropdown = (requestId) => {
    showDropdown.value = showDropdown.value === requestId ? null : requestId
    if (showDropdown.value) {
        searchQuery.value = ''
    }
}

const submitPermintaan = () => {
    // Filter out empty requests
    const validRequests = form.requests.filter(req => req.barang_id && req.jumlah)

    if (validRequests.length === 0) {
        alert('Minimal harus ada satu permintaan yang valid')
        return
    }

    // Create a new form data object for submission
    const submitData = {
        requests: validRequests.map(req => ({
            barang_id: req.barang_id,
            jumlah: req.jumlah,
            keterangan: req.keterangan || '',
            status: 'pending'
        }))
    }

    // Use a new form instance for submission
    const submitForm = useForm(submitData)
    
    submitForm.post(route('permintaan.store-multiple'), {
        onSuccess: () => {
            form.reset()
            initializeForm()
        },
        onError: (errors) => {
            console.error('Form submission errors:', errors)
        }
    })
}

const handleClickOutside = (event) => {
    const target = event.target
    if (!target.closest('.dropdown-container')) {
        showDropdown.value = null
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    initializeForm()
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
    <Head title="Buat Permintaan" />
    <AppLayout>
        <div class="p-4 md:p-6 space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <Link :href="route('permintaan.index')" class="text-muted-foreground hover:text-foreground">
                            <ArrowLeft class="h-5 w-5" />
                        </Link>
                        <h1 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                            <Package class="h-6 w-6" />
                            Buat Permintaan Barang
                        </h1>
                    </div>
                    <p class="text-muted-foreground">Buat permintaan barang untuk kebutuhan operasional</p>
                </div>
            </div>

            <!-- Form Container -->
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-lg border p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-medium">Form Permintaan Barang</h2>
                        <div class="flex gap-2">
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                @click="addRequest"
                            >
                                <Plus class="w-4 h-4 mr-1" />
                                Tambah Barang
                            </Button>
                        </div>
                    </div>

                    <form @submit.prevent="submitPermintaan" class="space-y-6">
                        <!-- Dynamic Requests -->
                        <div class="space-y-4">
                            <div
                                v-for="(request, index) in form.requests"
                                :key="request.id"
                                class="border rounded-lg p-4 bg-gray-50"
                            >
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="font-medium">Barang #{{ index + 1 }}</h3>
                                    <Button
                                        v-if="form.requests.length > 1"
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        @click="removeRequest(index)"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </Button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- PILIH BARANG -->
                                    <div class="space-y-1 dropdown-container">
                                        <Label>Pilih Barang *</Label>
                                        <div class="relative">
                                            <button
                                                type="button"
                                                @click="toggleDropdown(request.id)"
                                                class="w-full text-left rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                                :class="request.selectedBarang ? 'text-foreground' : 'text-muted-foreground'"
                                            >
                                                <div v-if="request.selectedBarang" class="flex items-center justify-between">
                                                    <span>{{ request.selectedBarang.nama_barang }} ({{ request.selectedBarang.kode_barang }})</span>
                                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                                        Stok: {{ request.selectedBarang.stok }}
                                                    </span>
                                                </div>
                                                <span v-else>Pilih barang...</span>
                                            </button>

                                            <!-- Dropdown -->
                                            <div
                                                v-if="showDropdown === request.id"
                                                class="absolute z-50 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-60 overflow-y-auto"
                                            >
                                                <!-- Search -->
                                                <div class="p-2 border-b">
                                                    <div class="relative">
                                                        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                                                        <input
                                                            v-model="searchQuery"
                                                            type="text"
                                                            placeholder="Cari barang..."
                                                            class="w-full pl-10 pr-4 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                            @click.stop
                                                        />
                                                    </div>
                                                </div>

                                                <!-- Results -->
                                                <div class="max-h-48 overflow-y-auto">
                                                    <button
                                                        v-for="item in filteredBarang"
                                                        :key="item.id"
                                                        type="button"
                                                        @click="handleBarangSelect(index, item)"
                                                        class="w-full text-left p-3 hover:bg-gray-50 border-b last:border-b-0"
                                                    >
                                                        <div class="flex items-center justify-between">
                                                            <div>
                                                                <div class="font-medium">{{ item.nama_barang }}</div>
                                                                <div class="text-sm text-gray-500">{{ item.kode_barang }}</div>
                                                            </div>
                                                            <div class="flex items-center gap-2">
                                                                <span class="text-xs px-2 py-1 rounded-full"
                                                                      :class="item.kategori === 'peminjaman' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'">
                                                                    {{ item.kategori === 'peminjaman' ? 'Peminjaman' : 'Permintaan' }}
                                                                </span>
                                                                <Check v-if="request.selectedBarang?.id === item.id" class="w-4 h-4 text-blue-600" />
                                                            </div>
                                                        </div>
                                                    </button>

                                                    <!-- No Results -->
                                                    <div v-if="filteredBarang.length === 0" class="p-4 text-center text-gray-500">
                                                        Tidak ada barang ditemukan
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- JUMLAH -->
                                    <div class="space-y-1">
                                        <Label>Jumlah yang Diminta *</Label>
                                        <Input
                                            v-model="request.jumlah"
                                            type="number"
                                            min="1"
                                            :max="request.selectedBarang?.stok || 1"
                                            placeholder="Masukkan jumlah"
                                            required
                                        />
                                        <p class="text-sm text-muted-foreground">Maksimal: {{ request.selectedBarang?.stok || 0 }} unit</p>
                                    </div>

                                    <!-- KETERANGAN -->
                                    <div class="space-y-1 md:col-span-2">
                                        <Label>Keterangan (Opsional)</Label>
                                        <textarea
                                            v-model="request.keterangan"
                                            rows="4"
                                            placeholder="Jelaskan alasan atau detail permintaan barang ini"
                                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1"
                            >
                                {{ form.processing ? 'Mengirim...' : `Kirim ${form.requests.length} Permintaan` }}
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                class="flex-1"
                                @click="initializeForm"
                            >
                                Reset
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template><style scoped>
/* Close dropdown when clicking outside */
.dropdown-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 40;
}
</style> 

