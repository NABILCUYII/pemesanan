<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { FileText, Download, Calendar, ChevronDown, ChevronRight, User, Search } from 'lucide-vue-next';
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

interface User {
    id: number;
    name: string;
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
    users: User[];
    stokChanges: StokChange[];
    month: number | null;
    year: number | null;
    months: Record<string, string>;
    years: number[];
}

const props = withDefaults(defineProps<Props>(), {
    month: () => new Date().getMonth() + 1, // Default to current month (1-12)
    year: () => new Date().getFullYear(), // Default to current year
});

const selectedMonth = ref(props.month?.toString() ?? (new Date().getMonth() + 1).toString());
const selectedYear = ref(props.year?.toString() ?? new Date().getFullYear().toString());

const searchQuery = ref('');

const filteredUsers = computed(() => {
    if (!searchQuery.value) return props.users;
    
    const query = searchQuery.value.toLowerCase();
    return props.users.filter(user => 
        user.name.toLowerCase().includes(query)
    );
});

const updateReport = () => {
    router.get(route('laporan.index'), {
        month: parseInt(selectedMonth.value),
        year: parseInt(selectedYear.value),
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
        case 'returned': return 'success';
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

const downloadReport = () => {
    // TODO: Implement report download functionality
    alert('Fitur download laporan akan segera hadir!');
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
</script>

<template>
    <Head title="Laporan" />
    <AppLayout>
        <div class="p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-semibold flex items-center gap-2">
                        <FileText class="h-6 w-6" />
                        Laporan
                    </h1>
                    <p class="text-muted-foreground">Laporan permintaan, peminjaman, dan stok barang</p>
                </div>
                <Button @click="downloadReport">
                    <Download class="w-4 h-4 mr-2" />
                    Download Laporan
                </Button>
            </div>

            <!-- Filter -->
            <Card>
                <CardHeader>
                    <CardTitle>Filter Laporan</CardTitle>
                    <CardDescription>Pilih periode laporan yang ingin ditampilkan</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <Label for="month">Bulan</Label>
                            <Select v-model="selectedMonth" @update:modelValue="updateReport">
                                <SelectTrigger>
                                    <SelectValue :placeholder="months[selectedMonth]" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="(name, value) in months" :key="value" :value="value">
                                        {{ name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="flex-1">
                            <Label for="year">Tahun</Label>
                            <Select v-model="selectedYear" @update:modelValue="updateReport">
                                <SelectTrigger>
                                    <SelectValue :placeholder="selectedYear" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="year in years" :key="year" :value="year">
                                        {{ year }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Laporan per User -->
            <Card>
                <CardHeader>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <CardTitle>Laporan per User</CardTitle>
                            <CardDescription>Data permintaan dan peminjaman per user</CardDescription>
                        </div>
                        <div class="relative w-full sm:w-64">
                            <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input
                                v-model="searchQuery"
                                placeholder="Cari user..."
                                class="pl-8"
                            />
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="space-y-6">
                        <div v-if="filteredUsers.length === 0" class="text-center py-8 text-muted-foreground">
                            Tidak ada user yang ditemukan
                        </div>
                        <div v-for="user in filteredUsers" :key="user.id" class="border rounded-lg overflow-hidden">
                            <div 
                                class="bg-gray-50 p-4 border-b cursor-pointer hover:bg-gray-100 transition-colors"
                                @click="toggleUser(user.id)"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-primary/10 p-2 rounded-full">
                                            <User class="h-5 w-5 text-primary" />
                                        </div>
                                        <div>
                                            <h3 class="font-semibold">{{ user.name }}</h3>
                                            <div class="flex gap-4 text-sm text-muted-foreground mt-1">
                                                <span>Total Permintaan: {{ user.total_permintaan }}</span>
                                                <span>Total Peminjaman: {{ user.total_peminjaman }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <ChevronDown 
                                        v-if="isUserExpanded(user.id)"
                                        class="h-5 w-5 text-muted-foreground transition-transform"
                                    />
                                    <ChevronRight 
                                        v-else
                                        class="h-5 w-5 text-muted-foreground transition-transform"
                                    />
                                </div>
                            </div>

                            <div v-if="isUserExpanded(user.id)" class="transition-all">
                                <!-- Permintaan -->
                                <div v-if="user.permintaan.length > 0" class="p-4">
                                    <h4 class="font-medium mb-2">Permintaan</h4>
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>Tanggal</TableHead>
                                                <TableHead>Barang</TableHead>
                                                <TableHead>Jumlah</TableHead>
                                                <TableHead>Status</TableHead>
                                                <TableHead>Keterangan</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            <TableRow v-for="item in user.permintaan" :key="item.id">
                                                <TableCell>{{ formatDate(item.created_at) }}</TableCell>
                                                <TableCell>
                                                    <div>
                                                        <div class="font-medium">{{ item.nama_barang }}</div>
                                                        <div class="text-sm text-muted-foreground">{{ item.kode_barang }}</div>
                                                    </div>
                                                </TableCell>
                                                <TableCell>{{ item.jumlah }}</TableCell>
                                                <TableCell>
                                                    <span :class="[
                                                        'px-2 py-1 text-xs rounded-full font-medium',
                                                        getStatusVariant(item.status)
                                                    ]">
                                                        {{ getStatusText(item.status) }}
                                                    </span>
                                                </TableCell>
                                                <TableCell>{{ item.keterangan || '-' }}</TableCell>
                                            </TableRow>
                                        </TableBody>
                                    </Table>
                                </div>

                                <!-- Peminjaman -->
                                <div v-if="user.peminjaman.length > 0" class="p-4 border-t">
                                    <h4 class="font-medium mb-2">Peminjaman</h4>
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>Tanggal</TableHead>
                                                <TableHead>Barang</TableHead>
                                                <TableHead>Jumlah</TableHead>
                                                <TableHead>Status</TableHead>
                                                <TableHead>Tenggat</TableHead>
                                                <TableHead>Keterangan</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            <TableRow v-for="item in user.peminjaman" :key="item.id">
                                                <TableCell>{{ formatDate(item.created_at) }}</TableCell>
                                                <TableCell>
                                                    <div>
                                                        <div class="font-medium">{{ item.nama_barang }}</div>
                                                        <div class="text-sm text-muted-foreground">{{ item.kode_barang }}</div>
                                                    </div>
                                                </TableCell>
                                                <TableCell>{{ item.jumlah }}</TableCell>
                                                <TableCell>
                                                    <span :class="[
                                                        'px-2 py-1 text-xs rounded-full font-medium',
                                                        getStatusVariant(item.status)
                                                    ]">
                                                        {{ getStatusText(item.status) }}
                                                    </span>
                                                </TableCell>
                                                <TableCell>{{ formatDate(item.due_date) }}</TableCell>
                                                <TableCell>{{ item.keterangan || '-' }}</TableCell>
                                            </TableRow>
                                        </TableBody>
                                    </Table>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Laporan Stok -->
            <Card>
                <CardHeader>
                    <CardTitle>Laporan Stok</CardTitle>
                    <CardDescription>Perubahan stok barang per bulan</CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Barang</TableHead>
                                <TableHead>Stok Awal</TableHead>
                                <TableHead>Permintaan Keluar</TableHead>
                                <TableHead>Peminjaman Keluar</TableHead>
                                <TableHead>Peminjaman Kembali</TableHead>
                                <TableHead>Stok Akhir</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in stokChanges" :key="item.id">
                                <TableCell>
                                    <div>
                                        <div class="font-medium">{{ item.nama_barang }}</div>
                                        <div class="text-sm text-muted-foreground">{{ item.kode_barang }}</div>
                                    </div>
                                </TableCell>
                                <TableCell>{{ item.stok_awal }}</TableCell>
                                <TableCell>{{ item.permintaan_keluar }}</TableCell>
                                <TableCell>{{ item.peminjaman_keluar }}</TableCell>
                                <TableCell>{{ item.peminjaman_kembali }}</TableCell>
                                <TableCell>{{ item.stok_akhir }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template> 