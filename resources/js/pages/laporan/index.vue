<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { FileText, Download, Calendar, ChevronDown, ChevronRight, User, Search, FileSpreadsheet, Users, Package, Handshake, Box } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { usePhotoUrl } from '@/composables/usePhotoUrl';

interface User {
    id: number;
    name: string;
    photo?: string;
    photo_url?: string;
    permintaan: any[];
    peminjaman: any[];
    total_permintaan: number;
    total_peminjaman: number;
}

interface StokChange {
    id: number;
    nama_barang: string;
    kode_barang: string;
    stok_awal: number;
    permintaan_keluar: number;
    peminjaman_keluar: number;
    peminjaman_kembali: number;
    stok_akhir: number;
}

interface Props {
    users: {
        data: User[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
    stokChanges: {
        data: StokChange[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
    month: number | null;
    year: number | null;
    filters: {
        search?: string;
    };
    statistics: {
        total_users: number;
        total_permintaan: number;
        total_peminjaman: number;
        total_barang: number;
    };
    months: Record<string, string>;
    years: number[];
}

const props = withDefaults(defineProps<Props>(), {
    month: () => new Date().getMonth() + 1, // Default to current month (1-12)
    year: () => new Date().getFullYear(), // Default to current year
});

const { getInitials } = useInitials();

const selectedMonth = ref(props.month?.toString() ?? (new Date().getMonth() + 1).toString());
const selectedYear = ref(props.year?.toString() ?? new Date().getFullYear().toString());

// Search and filter functionality
const searchQuery = ref(props.filters.search || '');

const applyFilters = () => {
    router.get(route('laporan.index'), {
        month: parseInt(selectedMonth.value),
        year: parseInt(selectedYear.value),
        search: searchQuery.value
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    applyFilters();
};

// Watch for changes in selectedMonth and selectedYear
watch([selectedMonth, selectedYear], ([newMonth, newYear]) => {
    if (newMonth && newYear) {
        updateReport();
    }
}, { deep: true });

const updateReport = () => {
    router.get(route('laporan.index'), {
        month: parseInt(selectedMonth.value),
        year: parseInt(selectedYear.value),
        search: searchQuery.value
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

const getStatusVariant = (status: string) => {
    switch (status) {
        case 'pending': return 'secondary';
        case 'approved': return 'default';
        case 'rejected': return 'destructive';
        case 'completed': return 'outline';
        case 'returned': return 'default';
        case 'not_returned': return 'secondary';
        default: return 'secondary';
    }
};

const getStatusText = (status: string) => {
    switch (status) {
        case 'pending': return 'Menunggu';
        case 'approved': return 'Disetujui';
        case 'rejected': return 'Ditolak';
        case 'completed': return 'Selesai';
        case 'returned': return 'Dikembalikan';
        default: return status;
    }
};

const expandedUsers = ref<number[]>([]);

const toggleUser = (userId: number) => {
    const index = expandedUsers.value.indexOf(userId);
    if (index === -1) {
        expandedUsers.value.push(userId);
    } else {
        expandedUsers.value.splice(index, 1);
    }
};

const isUserExpanded = (userId: number) => {
    return expandedUsers.value.includes(userId);
};

// Computed property untuk menghitung stok awal berdasarkan rumus
const calculatedStokChanges = computed(() => {
    return props.stokChanges.data.map(item => {
        const valuesToSum = [
            item.stok_akhir,
            item.permintaan_keluar,
            item.peminjaman_keluar,
            item.peminjaman_kembali
        ];
        
        const stokAwal = valuesToSum
            .map(value => Number(value)) // Pastikan semua nilai adalah angka
            .filter(value => value > 0)    // Abaikan nilai nol atau negatif
            .reduce((sum, value) => sum + value, 0); // Jumlahkan nilai positif

        return {
            ...item,
            stok_awal: stokAwal
        };
    });
});

const showPdfDropdown = ref(false);
const showExcelDropdown = ref(false);

function togglePdfDropdown() {
    showPdfDropdown.value = !showPdfDropdown.value;
    if (showPdfDropdown.value) showExcelDropdown.value = false;
}
function toggleExcelDropdown() {
    showExcelDropdown.value = !showExcelDropdown.value;
    if (showExcelDropdown.value) showPdfDropdown.value = false;
}

const { getPhotoUrl } = usePhotoUrl();
</script>

<template>
    <Head title="Laporan" />
    <AppLayout>
        <div class="p-3 sm:p-6 space-y-4 sm:space-y-8">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 pb-4 border-b">
                <div>
                    <h1 class="text-xl sm:text-3xl font-bold flex items-center gap-3 text-gray-900">
                        <FileText class="h-6 w-6 sm:h-8 sm:w-8 text-primary" />
                        Laporan Sistem
                    </h1>
                    <p class="text-muted-foreground text-sm sm:text-base mt-2">Laporan komprehensif permintaan, peminjaman, dan pergerakan stok barang</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- PDF Dropdown -->
                    <div class="relative" @click.stop>
                        <button
                            type="button"
                            @click="togglePdfDropdown"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-lg text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 sm:h-11 px-4 sm:px-6 py-2 w-full sm:w-auto shadow-sm"
                        >
                            <Download class="w-4 h-4 mr-2" />
                            Download Laporan PDF
                            <ChevronDown class="w-4 h-4 ml-2" />
                        </button>
                        <div
                            v-if="showPdfDropdown"
                            class="absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-10"
                        >
                            <a :href="route('laporan.download', { month: selectedMonth, year: selectedYear })" class="block px-4 py-2 hover:bg-gray-100 text-sm text-gray-900">Download Semua</a>
                            <a :href="route('laporan.download-permintaan', { month: selectedMonth, year: selectedYear })" class="block px-4 py-2 hover:bg-gray-100 text-sm text-gray-900">Download Laporan Permintaan</a>
                            <a :href="route('laporan.download-peminjaman', { month: selectedMonth, year: selectedYear })" class="block px-4 py-2 hover:bg-gray-100 text-sm text-gray-900">Download Laporan Peminjaman</a>
                        </div>
                    </div>
                    <!-- Excel Dropdown -->
                    <div class="relative" @click.stop>
                        <button
                            type="button"
                            @click="toggleExcelDropdown"
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-lg text-sm font-semibold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-green-600 text-white hover:bg-green-700 h-10 sm:h-11 px-4 sm:px-6 py-2 w-full sm:w-auto shadow-sm"
                        >
                            <FileSpreadsheet class="w-4 h-4 mr-2" />
                            Download Laporan Excel
                            <ChevronDown class="w-4 h-4 ml-2" />
                        </button>
                        <div
                            v-if="showExcelDropdown"
                            class="absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-10"
                        >
                            <a :href="route('laporan.download-excel', { month: selectedMonth, year: selectedYear })" class="block px-4 py-2 hover:bg-gray-100 text-sm text-gray-900">Download Semua</a>
                            <a :href="route('laporan.download-permintaan-excel', { month: selectedMonth, year: selectedYear })" class="block px-4 py-2 hover:bg-gray-100 text-sm text-gray-900">Download Laporan Permintaan</a>
                            <a :href="route('laporan.download-peminjaman-excel', { month: selectedMonth, year: selectedYear })" class="block px-4 py-2 hover:bg-gray-100 text-sm text-gray-900">Download Laporan Peminjaman</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <Card class="shadow-sm border-0 bg-gradient-to-br from-blue-100 to-blue-50">
                    <CardContent class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500">Total Users</p>
                                <p class="text-2xl font-bold text-blue-700">{{ statistics.total_users }}</p>
                            </div>
                            <span class="bg-blue-200 rounded-full p-2">
                                <Users class="h-6 w-6 text-blue-500" />
                            </span>
                        </div>
                    </CardContent>
                </Card>
                
                <Card class="shadow-sm border-0 bg-gradient-to-br from-green-100 to-green-50">
                    <CardContent class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500">Total Permintaan</p>
                                <p class="text-2xl font-bold text-green-700">{{ statistics.total_permintaan }}</p>
                            </div>
                            <span class="bg-green-200 rounded-full p-2">
                                <Handshake class="h-6 w-6 text-green-500" />
                            </span>
                        </div>
                    </CardContent>
                </Card>
                
                <Card class="shadow-sm border-0 bg-gradient-to-br from-purple-100 to-purple-50">
                    <CardContent class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500">Total Peminjaman</p>
                                <p class="text-2xl font-bold text-purple-700">{{ statistics.total_peminjaman }}</p>
                            </div>
                            <span class="bg-purple-200 rounded-full p-2">
                                <Box class="h-6 w-6 text-purple-500" />
                            </span>
                        </div>
                    </CardContent>
                </Card>
                
                <Card class="shadow-sm border-0 bg-gradient-to-br from-orange-100 to-orange-50">
                    <CardContent class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500">Total Barang</p>
                                <p class="text-2xl font-bold text-orange-700">{{ statistics.total_barang }}</p>
                            </div>
                            <span class="bg-orange-200 rounded-full p-2">
                                <Package class="h-6 w-6 text-orange-500" />
                            </span>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Filter Section -->
            <Card class="border-2 border-gray-100 shadow-sm">
                <CardHeader class="pb-4">
                    <CardTitle class="flex items-center gap-2 text-lg">
                        <Calendar class="h-5 w-5 text-primary" />
                        Pilih Periode Laporan
                    </CardTitle>
                    <CardDescription class="text-base">Pilih periode laporan yang ingin ditampilkan dan analisis</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-col gap-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="space-y-2">
                                <Label for="month" class="text-sm font-medium">Bulan</Label>
                                <Select v-model="selectedMonth">
                                    <SelectTrigger class="h-11">
                                        <SelectValue :placeholder="months[selectedMonth]" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="(name, value) in months" :key="value" :value="value">
                                            {{ name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label for="year" class="text-sm font-medium">Tahun</Label>
                                <Select v-model="selectedYear">
                                    <SelectTrigger class="h-11">
                                        <SelectValue :placeholder="selectedYear" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="year in years" :key="year" :value="year">
                                            {{ year }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label for="search" class="text-sm font-medium">Cari</Label>
                                <div class="relative">
                                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                                    <Input
                                        v-model="searchQuery"
                                        type="text"
                                        placeholder="Cari nama pengguna atau barang..."
                                        class="pl-9 pr-3 py-2 text-sm border-gray-200 focus:border-blue-400 focus:ring-blue-400"
                                        @keyup.enter="applyFilters"
                                    />
                                </div>
                            </div>
                            <div class="flex items-end gap-2">
                                <Button 
                                    @click="applyFilters"
                                    size="sm"
                                    class="bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 px-4"
                                >
                                    <Search class="h-4 w-4 mr-1" />
                                    Cari
                                </Button>
                                <Button 
                                    @click="clearFilters"
                                    variant="outline"
                                    size="sm"
                                    class="border-gray-300 hover:bg-gray-50 px-4"
                                >
                                    Reset
                                </Button>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="text-sm text-muted-foreground bg-muted/50 px-3 py-2 rounded-md">
                                Periode: {{ months[selectedMonth] }} {{ selectedYear }}
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Laporan perorang -->
            <Card class="border-2 border-gray-100 shadow-sm">
                <CardHeader class="pb-4">
                    <CardTitle class="flex items-center gap-3 text-xl">
                        <User class="h-6 w-6 text-primary" />
                        Laporan Aktivitas Per Pengguna
                    </CardTitle>
                    <CardDescription class="text-base">Daftar lengkap aktivitas permintaan dan peminjaman per pengguna dalam periode yang dipilih</CardDescription>
                </CardHeader>
                <CardContent>
                    <!-- Mobile view - Card layout -->
                    <div class="block md:hidden space-y-4">
                        <div v-for="user in users.data" :key="user.id" class="border rounded-lg p-4 space-y-3">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <Avatar class="w-8 h-8">
                                            <AvatarImage v-if="user.photo_url" :src="user.photo_url" alt="User Photo" />
                                            <AvatarFallback>{{ getInitials(user.name) }}</AvatarFallback>
                                        </Avatar>
                                        <div>
                                            <h3 class="font-semibold text-lg">{{ user.name }}</h3>
                                            <div class="flex gap-4 mt-2 text-sm text-muted-foreground">
                                                <span>Permintaan: {{ user.total_permintaan }}</span>
                                                <span>Peminjaman: {{ user.total_peminjaman }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    @click="toggleUser(user.id)"
                                    class="flex-shrink-0"
                                >
                                    <ChevronRight
                                        v-if="!isUserExpanded(user.id)"
                                        class="h-4 w-4"
                                    />
                                    <ChevronDown
                                        v-else
                                        class="h-4 w-4"
                                    />
                                </Button>
                            </div>
                            
                            <!-- Expanded content for mobile -->
                            <div v-if="isUserExpanded(user.id)" class="space-y-4 pt-3 border-t">
                                <!-- Header with download button -->
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                                    <h4 class="font-semibold text-sm">Detail Laporan: {{ user.name }}</h4>
                                    <div class="flex gap-2">
                                        <a 
                                            :href="route('laporan.download-user', { user_id: user.id, month: selectedMonth, year: selectedYear })"
                                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-xs font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-8 px-3"
                                        >
                                            <Download class="w-3 h-3 mr-1" />
                                            PDF
                                        </a>
                                        <a 
                                            :href="route('laporan.download-user-excel', { user_id: user.id, month: selectedMonth, year: selectedYear })"
                                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-xs font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-green-200 bg-green-50 text-green-700 hover:bg-green-100 h-8 px-3"
                                        >
                                            <FileSpreadsheet class="w-3 h-3 mr-1" />
                                            Excel
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Permintaan Details -->
                                <div v-if="user.permintaan && user.permintaan.length > 0">
                                    <h5 class="font-semibold mb-2 text-sm">Detail Permintaan</h5>
                                    <div class="space-y-2 max-h-40 overflow-y-auto">
                                        <div v-for="permintaan in user.permintaan" :key="permintaan.id" class="bg-muted/30 rounded-md p-3 space-y-1">
                                            <div class="flex justify-between items-start">
                                                <span class="font-medium text-sm">{{ permintaan.nama_barang }}</span>
                                                <Badge :variant="getStatusVariant(permintaan.status)" class="text-xs">
                                                    {{ getStatusText(permintaan.status) }}
                                                </Badge>
                                            </div>
                                            <div class="flex justify-between text-xs text-muted-foreground">
                                                <span>Jumlah: {{ permintaan.jumlah }}</span>
                                                <span>{{ formatDate(permintaan.created_at) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Peminjaman Details -->
                                <div v-if="user.peminjaman && user.peminjaman.length > 0">
                                    <h5 class="font-semibold mb-2 text-sm">Detail Peminjaman</h5>
                                    <div class="space-y-2 max-h-40 overflow-y-auto">
                                        <div v-for="peminjaman in user.peminjaman" :key="peminjaman.id" class="bg-muted/30 rounded-md p-3 space-y-1">
                                            <div class="flex justify-between items-start">
                                                <span class="font-medium text-sm">{{ peminjaman.nama_barang }}</span>
                                                <Badge :variant="getStatusVariant(peminjaman.status)" class="text-xs">
                                                    {{ getStatusText(peminjaman.status) }}
                                                </Badge>
                                            </div>
                                            <div class="text-xs text-muted-foreground space-y-1">
                                                <div class="flex justify-between">
                                                    <span>Jumlah:</span>
                                                    <span>{{ peminjaman.jumlah }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span>Pinjam:</span>
                                                    <span>{{ formatDate(peminjaman.tanggal_peminjaman) }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span>Kembali:</span>
                                                    <span>{{ formatDate(peminjaman.tanggal_pengembalian) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- No data message -->
                                <div v-if="(!user.permintaan || user.permintaan.length === 0) && (!user.peminjaman || user.peminjaman.length === 0)" class="text-center py-4 text-muted-foreground text-sm">
                                    Tidak ada data permintaan atau peminjaman untuk periode ini.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop view - Table layout -->
                    <div class="hidden md:block">
                        <div class="rounded-lg border-2 overflow-hidden">
                            <Table>
                                <TableHeader>
                                    <TableRow class="bg-muted/50 hover:bg-muted/50">
                                        <TableHead class="font-semibold text-base py-4">Nama Pengguna</TableHead>
                                        <TableHead class="font-semibold text-base py-4 text-center">Total Permintaan</TableHead>
                                        <TableHead class="font-semibold text-base py-4 text-center">Total Peminjaman</TableHead>
                                        <TableHead class="font-semibold text-base py-4 text-center">Aksi</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="user in users.data" :key="user.id" class="hover:bg-muted/30 transition-colors">
                                        <TableCell class="font-medium py-4">
                                            <div class="flex items-center gap-3">
                                                <Avatar class="w-8 h-8">
                                                    <AvatarImage v-if="user.photo_url" :src="user.photo_url" alt="User Photo" />
                                                    <AvatarFallback>{{ getInitials(user.name) }}</AvatarFallback>
                                                </Avatar>
                                                {{ user.name }}
                                            </div>
                                        </TableCell>
                                        <TableCell class="text-center py-4">
                                            <Badge variant="outline" class="text-sm px-3 py-1">
                                                {{ user.total_permintaan }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-center py-4">
                                            <Badge variant="outline" class="text-sm px-3 py-1">
                                                {{ user.total_peminjaman }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-center py-4">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                @click="toggleUser(user.id)"
                                                class="h-8 w-8 p-0"
                                            >
                                                <ChevronRight
                                                    v-if="!isUserExpanded(user.id)"
                                                    class="h-4 w-4"
                                                />
                                                <ChevronDown
                                                    v-else
                                                    class="h-4 w-4"
                                                />
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                    <!-- Expanded content for each user -->
                                    <TableRow v-for="user in users.data" :key="`expanded-${user.id}`" v-show="isUserExpanded(user.id)" class="bg-muted/20">
                                        <TableCell colspan="4" class="p-0">
                                            <div class="p-6 space-y-6 max-h-96 overflow-y-auto">
                                                <!-- Header with download button -->
                                                <div class="flex justify-between items-center pb-4 border-b">
                                                    <h3 class="text-lg font-semibold text-gray-900">Detail Laporan: {{ user.name }}</h3>
                                                    <div class="flex gap-2">
                                                        <a 
                                                            :href="route('laporan.download-user', { user_id: user.id, month: selectedMonth, year: selectedYear })"
                                                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 shadow-sm"
                                                        >
                                                            <Download class="w-4 h-4 mr-2" />
                                                            Download PDF
                                                        </a>
                                                        <a 
                                                            :href="route('laporan.download-user-excel', { user_id: user.id, month: selectedMonth, year: selectedYear })"
                                                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-green-200 bg-green-50 text-green-700 hover:bg-green-100 h-9 px-4 shadow-sm"
                                                        >
                                                            <FileSpreadsheet class="w-4 h-4 mr-2" />
                                                            Download Excel
                                                        </a>
                                                    </div>
                                                </div>
                                                
                                                <!-- Permintaan Details -->
                                                <div v-if="user.permintaan && user.permintaan.length > 0">
                                                    <h4 class="font-semibold mb-4 text-lg flex items-center gap-2">
                                                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                                        Detail Permintaan ({{ user.permintaan.length }} item)
                                                    </h4>
                                                    <div class="rounded-lg border-2 overflow-hidden">
                                                        <Table>
                                                            <TableHeader>
                                                                <TableRow class="bg-muted/30">
                                                                    <TableHead class="font-medium">Barang</TableHead>
                                                                    <TableHead class="font-medium text-center">Jumlah</TableHead>
                                                                    <TableHead class="font-medium text-center">Status</TableHead>
                                                                    <TableHead class="font-medium text-center">Tanggal</TableHead>
                                                                </TableRow>
                                                            </TableHeader>
                                                            <TableBody>
                                                                <TableRow v-for="permintaan in user.permintaan" :key="permintaan.id" class="hover:bg-muted/20">
                                                                    <TableCell class="font-medium">{{ permintaan.nama_barang }}</TableCell>
                                                                    <TableCell class="text-center">{{ permintaan.jumlah }}</TableCell>
                                                                    <TableCell class="text-center">
                                                                        <Badge :variant="getStatusVariant(permintaan.status)" class="text-xs">
                                                                            {{ getStatusText(permintaan.status) }}
                                                                        </Badge>
                                                                    </TableCell>
                                                                    <TableCell class="text-center text-sm text-muted-foreground">{{ formatDate(permintaan.created_at) }}</TableCell>
                                                                </TableRow>
                                                            </TableBody>
                                                        </Table>
                                                    </div>
                                                </div>
                                                
                                                <!-- Peminjaman Details -->
                                                <div v-if="user.peminjaman && user.peminjaman.length > 0">
                                                    <h4 class="font-semibold mb-4 text-lg flex items-center gap-2">
                                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                        Detail Peminjaman ({{ user.peminjaman.length }} item)
                                                    </h4>
                                                    <div class="rounded-lg border-2 overflow-hidden">
                                                        <Table>
                                                            <TableHeader>
                                                                <TableRow class="bg-muted/30">
                                                                    <TableHead class="font-medium">Barang</TableHead>
                                                                    <TableHead class="font-medium text-center">Jumlah</TableHead>
                                                                    <TableHead class="font-medium text-center">Status</TableHead>
                                                                    <TableHead class="font-medium text-center">Tanggal Pinjam</TableHead>
                                                                    <TableHead class="font-medium text-center">Tanggal Kembali</TableHead>
                                                                </TableRow>
                                                            </TableHeader>
                                                            <TableBody>
                                                                <TableRow v-for="peminjaman in user.peminjaman" :key="peminjaman.id" class="hover:bg-muted/20">
                                                                    <TableCell class="font-medium">{{ peminjaman.nama_barang }}</TableCell>
                                                                    <TableCell class="text-center">{{ peminjaman.jumlah }}</TableCell>
                                                                    <TableCell class="text-center">
                                                                        <Badge :variant="getStatusVariant(peminjaman.status)" class="text-xs">
                                                                            {{ getStatusText(peminjaman.status) }}
                                                                        </Badge>
                                                                    </TableCell>
                                                                    <TableCell class="text-center text-sm text-muted-foreground">{{ formatDate(peminjaman.tanggal_peminjaman) }}</TableCell>
                                                                    <TableCell class="text-center text-sm text-muted-foreground">{{ formatDate(peminjaman.tanggal_pengembalian) }}</TableCell>
                                                                </TableRow>
                                                            </TableBody>
                                                        </Table>
                                                    </div>
                                                </div>
                                                
                                                <!-- No data message -->
                                                <div v-if="(!user.permintaan || user.permintaan.length === 0) && (!user.peminjaman || user.peminjaman.length === 0)" class="text-center py-8 text-muted-foreground bg-muted/20 rounded-lg">
                                                    <div class="flex flex-col items-center gap-2">
                                                        <FileText class="w-8 h-8 text-muted-foreground/50" />
                                                        <p class="text-sm">Tidak ada data permintaan atau peminjaman untuk periode ini.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </div>

                    <!-- Pagination for Users -->
                    <div v-if="users.last_page > 1" class="flex items-center justify-center mt-6">
                        <nav class="inline-flex items-center gap-1 bg-white rounded-lg border border-gray-200 p-1 shadow-sm">
                            <Button 
                                variant="ghost" 
                                size="sm"
                                class="h-8 w-8 p-0 text-gray-500 hover:text-gray-700 hover:bg-gray-100"
                                :disabled="users.current_page === 1"
                                @click="router.get(route('laporan.index'), { 
                                    month: selectedMonth, 
                                    year: selectedYear, 
                                    search: searchQuery,
                                    page: users.current_page - 1 
                                })"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </Button>
                            
                            <span class="px-2 py-1 text-xs font-medium text-gray-700 bg-gray-50 rounded">
                                {{ users.current_page }} / {{ users.last_page }}
                            </span>
                            
                            <span class="px-2 py-1 text-xs text-gray-500">
                                ({{ users.data.length }} dari {{ users.total }})
                            </span>
                            
                            <Button 
                                variant="ghost" 
                                size="sm"
                                class="h-8 w-8 p-0 text-gray-500 hover:text-gray-700 hover:bg-gray-100"
                                :disabled="users.current_page === users.last_page"
                                @click="router.get(route('laporan.index'), { 
                                    month: selectedMonth, 
                                    year: selectedYear, 
                                    search: searchQuery,
                                    page: users.current_page + 1 
                                })"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Button>
                        </nav>
                    </div>
                </CardContent>
            </Card>

            <!-- Laporan Stok -->
            <Card class="border-2 border-gray-100 shadow-sm">
                <CardHeader class="pb-4">
                    <CardTitle class="flex items-center gap-3 text-xl">
                        <span class="w-6 h-6 bg-primary/10 rounded-full flex items-center justify-center">
                            <span class="w-3 h-3 bg-primary rounded-full"></span>
                        </span>
                        Laporan Pergerakan Stok
                    </CardTitle>
                    <CardDescription class="text-base">Analisis pergerakan stok barang dengan stok awal (pertama kali masuk) dan stok akhir (total stok saat ini) dalam periode yang dipilih</CardDescription>
                </CardHeader>
                <CardContent>
                    <!-- Mobile view - Card layout -->
                    <div class="block md:hidden space-y-4">
                        <div v-for="item in calculatedStokChanges" :key="item.id" class="border rounded-lg p-4 space-y-3">
                            <div class="space-y-2">
                                <div>
                                    <h3 class="font-semibold text-lg">{{ item.nama_barang }}</h3>
                                    <p class="text-sm text-muted-foreground">{{ item.kode_barang }}</p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div class="bg-muted/30 rounded-md p-3">
                                        <div class="text-muted-foreground text-xs">Stok Awal (Pertama Masuk)</div>
                                        <div class="font-semibold">{{ item.stok_awal }}</div>
                                    </div>
                                    <div class="bg-muted/30 rounded-md p-3">
                                        <div class="text-muted-foreground text-xs">Stok Akhir (Total Saat Ini)</div>
                                        <div class="font-semibold">{{ item.stok_akhir }}</div>
                                    </div>
                                    <div class="bg-muted/30 rounded-md p-3">
                                        <div class="text-muted-foreground text-xs">Permintaan Keluar</div>
                                        <div class="font-semibold">{{ item.permintaan_keluar }}</div>
                                    </div>
                                    <div class="bg-muted/30 rounded-md p-3">
                                        <div class="text-muted-foreground text-xs">Peminjaman Keluar</div>
                                        <div class="font-semibold">{{ item.peminjaman_keluar }}</div>
                                    </div>
                                    <div class="bg-muted/30 rounded-md p-3 col-span-2">
                                        <div class="text-muted-foreground text-xs">Peminjaman Kembali</div>
                                        <div class="font-semibold">{{ item.peminjaman_kembali }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop view - Table layout -->
                    <div class="hidden md:block">
                        <div class="rounded-lg border-2 overflow-hidden">
                            <Table>
                                <TableHeader>
                                    <TableRow class="bg-muted/50 hover:bg-muted/50">
                                        <TableHead class="font-semibold text-base py-4">Informasi Barang</TableHead>
                                        <TableHead class="font-semibold text-base py-4 text-center">Stok Awal<br><span class="text-xs font-normal text-muted-foreground">(Pertama Masuk)</span></TableHead>
                                        <TableHead class="font-semibold text-base py-4 text-center">Permintaan Keluar</TableHead>
                                        <TableHead class="font-semibold text-base py-4 text-center">Peminjaman Keluar</TableHead>
                                        <TableHead class="font-semibold text-base py-4 text-center">Peminjaman Kembali</TableHead>
                                        <TableHead class="font-semibold text-base py-4 text-center">Stok Akhir<br><span class="text-xs font-normal text-muted-foreground">(Total Saat Ini)</span></TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="item in calculatedStokChanges" :key="item.id" class="hover:bg-muted/30 transition-colors">
                                        <TableCell class="py-4">
                                            <div class="space-y-1">
                                                <div class="font-semibold text-base">{{ item.nama_barang }}</div>
                                                <div class="text-sm text-muted-foreground font-mono">{{ item.kode_barang }}</div>
                                            </div>
                                        </TableCell>
                                        <TableCell class="text-center py-4">
                                            <Badge variant="outline" class="text-sm px-3 py-1 font-semibold">
                                                {{ item.stok_awal }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-center py-4">
                                            <span class="text-red-600 font-medium">{{ item.permintaan_keluar }}</span>
                                        </TableCell>
                                        <TableCell class="text-center py-4">
                                            <span class="text-orange-600 font-medium">{{ item.peminjaman_keluar }}</span>
                                        </TableCell>
                                        <TableCell class="text-center py-4">
                                            <span class="text-green-600 font-medium">{{ item.peminjaman_kembali }}</span>
                                        </TableCell>
                                        <TableCell class="text-center py-4">
                                            <Badge :variant="item.stok_akhir > 0 ? 'default' : 'destructive'" class="text-sm px-3 py-1 font-semibold">
                                                {{ item.stok_akhir }}
                                            </Badge>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </div>

                    <!-- Pagination for Stock Changes -->
                    <div v-if="stokChanges.last_page > 1" class="flex items-center justify-center mt-6">
                        <nav class="inline-flex items-center gap-1 bg-white rounded-lg border border-gray-200 p-1 shadow-sm">
                            <Button 
                                variant="ghost" 
                                size="sm"
                                class="h-8 w-8 p-0 text-gray-500 hover:text-gray-700 hover:bg-gray-100"
                                :disabled="stokChanges.current_page === 1"
                                @click="router.get(route('laporan.index'), { 
                                    month: selectedMonth, 
                                    year: selectedYear, 
                                    search: searchQuery,
                                    stok_page: stokChanges.current_page - 1 
                                })"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </Button>
                            
                            <span class="px-2 py-1 text-xs font-medium text-gray-700 bg-gray-50 rounded">
                                {{ stokChanges.current_page }} / {{ stokChanges.last_page }}
                            </span>
                            
                            <span class="px-2 py-1 text-xs text-gray-500">
                                ({{ stokChanges.data.length }} dari {{ stokChanges.total }})
                            </span>
                            
                            <Button 
                                variant="ghost" 
                                size="sm"
                                class="h-8 w-8 p-0 text-gray-500 hover:text-gray-700 hover:bg-gray-100"
                                :disabled="stokChanges.current_page === stokChanges.last_page"
                                @click="router.get(route('laporan.index'), { 
                                    month: selectedMonth, 
                                    year: selectedYear, 
                                    search: searchQuery,
                                    stok_page: stokChanges.current_page + 1 
                                })"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Button>
                        </nav>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template> 