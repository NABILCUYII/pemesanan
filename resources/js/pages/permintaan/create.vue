<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { ArrowLeft, Package, Search, Check, Plus, Trash2, Sparkles } from 'lucide-vue-next'

interface Barang {
    id: number
    kode_barang: string
    nama_barang: string
    kategori: 'peminjaman' | 'permintaan'
    stok: number
    deskripsi?: string
}

interface RequestItem {
    id: string
    barang_id: string
    jumlah: string
    keterangan: string
    selectedBarang: Barang | null
    [key: string]: any
}

const props = defineProps<{
    barang: Barang[]
}>()

const form = useForm<{ requests: RequestItem[] }>({
    requests: []
})

const showDropdown = ref<string | null>(null)
const searchQuery = ref('')

const filteredBarang = computed(() => {
    if (!props.barang) return []
    return props.barang.filter(item => 
        item.nama_barang.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        item.kode_barang.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
})


const initializeForm = () => {
    form.requests = [{
        id: generateId(),
        barang_id: '',
        jumlah: '',
        keterangan: '',
        selectedBarang: null
    }]
}

const generateId = (): string => {
    return Math.random().toString(36).substr(2, 9)
}

const addRequest = () => {
    const requests = [...form.requests]
    requests.push({
        id: generateId(),
        barang_id: '',
        jumlah: '',
        keterangan: '',
        selectedBarang: null
    })
    form.requests = requests
}

const removeRequest = (index: number) => {
    const requests = [...form.requests]
    if (requests.length > 1) {
        requests.splice(index, 1)
        form.requests = requests
    }
}

const handleBarangSelect = (requestIndex: number, barang: Barang) => {
    const requests = [...form.requests]
    if (requests[requestIndex]) {
        requests[requestIndex].selectedBarang = barang
        requests[requestIndex].barang_id = barang.id.toString()
        form.requests = requests
    }
    showDropdown.value = null
    searchQuery.value = ''
}

const toggleDropdown = (requestId: string) => {
    showDropdown.value = showDropdown.value === requestId ? null : requestId
    if (showDropdown.value) {
        searchQuery.value = ''
    }
}

const submitPermintaan = () => {
    const requests = form.requests
    const validRequests = requests.filter(req => req.barang_id && req.jumlah)

    if (validRequests.length === 0) {
        alert('Minimal harus ada satu permintaan yang valid')
        return
    }

    const submitData = {
        requests: validRequests.map(req => ({
            barang_id: req.barang_id,
            jumlah: req.jumlah,
            keterangan: req.keterangan || '',
            status: 'pending'
        }))
    }

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

const handleClickOutside = (event: Event) => {
    const target = event.target as HTMLElement
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
        <div class="min-h-screen bg-gradient-to-br from-[#F0F8FF] via-[#87CEEB] to-[#98FB98] py-12 px-2 sm:px-0 relative overflow-x-hidden">
            <!-- Decorative elements -->
            <Sparkles class="absolute left-10 top-10 text-[#20B2AA] opacity-60 animate-pulse z-0" :size="48" />
            <Sparkles class="absolute right-10 bottom-10 text-[#98FB98] opacity-50 animate-pulse z-0" :size="40" />

            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="mb-8 flex items-center gap-4">
                    <Link :href="route('permintaan.index')" class="text-[#20B2AA] hover:text-[#1A9A94] transition">
                        <ArrowLeft class="h-6 w-6" />
                    </Link>
                    <div class="flex items-center gap-3">
                        <span class="bg-gradient-to-tr from-[#20B2AA] via-[#87CEEB] to-[#98FB98] rounded-full p-3 shadow-lg border-4 border-white/70">
                            <Package class="h-9 w-9 text-white" />
                        </span>
                        <div>
                            <h1 class="text-3xl font-bold text-[#2F4F4F]">Buat Permintaan Barang</h1>
                            <p class="text-[#708090]">Formulir permintaan barang dengan tampilan modern dan menarik</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-12">
                    <p class="text-xl text-[#708090] font-medium flex items-center gap-2">
                        <span>Silakan isi detail permintaan barang dengan tampilan yang</span>
                        <span class="bg-gradient-to-r from-[#20B2AA] via-[#87CEEB] to-[#98FB98] bg-clip-text text-transparent font-extrabold animate-gradient-x">super keren</span>
                        <span>dan modern.</span>
                    </p>
                </div>

                <div class="bg-white/95 shadow-2xl rounded-3xl border border-[#B0C4DE] p-10 relative overflow-hidden">
                    <!-- Decorative background shapes -->
                    <div class="absolute -top-16 -right-16 w-56 h-56 bg-gradient-to-br from-[#20B2AA] via-[#87CEEB] to-[#98FB98] rounded-full opacity-30 blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-16 -left-16 w-44 h-44 bg-gradient-to-tr from-[#98FB98] via-[#87CEEB] to-[#20B2AA] rounded-full opacity-20 blur-2xl pointer-events-none"></div>
                    
                    <h2 class="text-2xl font-extrabold mb-10 text-[#2F4F4F] flex items-center gap-3 tracking-tight">
                        <span class="inline-block w-2 h-8 bg-gradient-to-b from-[#20B2AA] to-[#98FB98] rounded-r mr-2"></span>
                        Form Permintaan Barang
                        <Sparkles class="text-[#20B2AA] animate-pulse" :size="22" />
                    </h2>

                    <div class="flex justify-end mb-8">
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="addRequest"
                            class="flex items-center gap-2"
                        >
                            <Plus class="w-4 h-4" />
                            Tambah Barang
                        </Button>
                    </div>

                    <form @submit.prevent="submitPermintaan" class="space-y-8">
                        <!-- Dynamic Requests -->
                        <div class="space-y-8">
                            <div
                                v-for="(request, index) in form.requests"
                                :key="request.id"
                                class="border rounded-2xl p-6 bg-gradient-to-br from-[#F0F8FF] via-[#E6F3FF] to-[#F0FFF0] shadow-lg"
                            >
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="font-semibold text-[#2F4F4F] text-lg">Barang #{{ index + 1 }}</h3>
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

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- PILIH BARANG -->
                                    <div class="space-y-1 dropdown-container">
                                        <Label class="text-[#2F4F4F]">Pilih Barang <span class="text-red-500">*</span></Label>
                                        <div class="relative">
                                            <button
                                                type="button"
                                                @click="toggleDropdown(request.id)"
                                                class="w-full text-left rounded-md border border-[#B0C4DE] bg-background px-3 py-2 text-base ring-offset-background placeholder:text-[#B0C4DE] focus:outline-none focus:ring-2 focus:ring-[#20B2AA] focus:ring-offset-2 transition"
                                                :class="request.selectedBarang ? 'text-[#2F4F4F]' : 'text-[#B0C4DE]'"
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
                                                class="absolute z-50 w-full mt-1 bg-white border border-[#B0C4DE] rounded-xl shadow-xl max-h-60 overflow-y-auto"
                                            >
                                                <!-- Search -->
                                                <div class="p-2 border-b border-[#B0C4DE] bg-[#F0F8FF]">
                                                    <div class="relative">
                                                        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                                                        <input
                                                            v-model="searchQuery"
                                                            type="text"
                                                            placeholder="Cari barang..."
                                                            class="w-full pl-10 pr-4 py-2 border rounded-md text-base focus:outline-none focus:ring-2 focus:ring-[#20B2AA]"
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
                                                        class="w-full text-left p-3 hover:bg-[#E6F3FF] border-b last:border-b-0 border-[#B0C4DE] transition"
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
                                                                <Check v-if="request.selectedBarang && request.selectedBarang.id === item.id" class="w-4 h-4 text-blue-600" />
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
                                        <p class="text-sm text-[#708090]">Maksimal: {{ request.selectedBarang ? request.selectedBarang.stok : 0 }} unit</p>
                                    </div>

                                    <!-- JUMLAH -->
                                    <div class="space-y-1">
                                        <Label class="text-[#2F4F4F]">Jumlah yang Diminta <span class="text-red-500">*</span></Label>
                                        <Input
                                            v-model="request.jumlah"
                                            type="number"
                                            min="1"
                                            :max="request.selectedBarang ? request.selectedBarang.stok : 1"
                                            placeholder="Masukkan jumlah"
                                            required
                                            class="text-base border-[#B0C4DE]"
                                        />
                                        <p class="text-sm text-[#708090]">Maksimal: {{ request.selectedBarang ? request.selectedBarang.stok : 0 }} unit</p>
                                    </div>

                                    <!-- KETERANGAN -->
                                    <div class="space-y-1 md:col-span-2">
                                        <Label class="text-[#2F4F4F]">Keterangan (Opsional)</Label>
                                        <textarea
                                            v-model="request.keterangan"
                                            rows="4"
                                            placeholder="Jelaskan alasan atau detail permintaan barang ini"
                                            class="w-full rounded-md border border-[#B0C4DE] bg-background px-3 py-2 text-base ring-offset-background placeholder:text-[#B0C4DE] focus:outline-none focus:ring-2 focus:ring-[#20B2AA] focus:ring-offset-2 transition"
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
                                class="flex-1 bg-gradient-to-r from-[#20B2AA] to-[#98FB98] text-white font-bold text-lg shadow-lg hover:from-[#1A9A94] hover:to-[#7ED957] transition"
                            >
                                {{ form.processing ? 'Mengirim...' : `Kirim ${form.requests.length} Permintaan` }}
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                class="flex-1 border-[#20B2AA] text-[#20B2AA] font-bold text-lg"
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
</template>

<style scoped>
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
