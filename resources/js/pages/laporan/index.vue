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
import axios from 'axios';

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

const downloadPDF = async () => {
    try {
        const response = await axios.get('/laporan/download', {
            params: {
                month: selectedMonth.value,
                year: selectedYear.value
            },
            responseType: 'blob'
        });

        // Create a blob from the PDF Stream
        const file = new Blob([response.data], { type: 'application/pdf' });
        const fileURL = window.URL.createObjectURL(file);
        
        // Create a link element and trigger download
        const link = document.createElement('a');
        link.href = fileURL;
        link.setAttribute('download', `laporan-${selectedMonth.value}-${selectedYear.value}.pdf`);
        document.body.appendChild(link);
        link.click();
        
        // Clean up
        link.remove();
        window.URL.revokeObjectURL(fileURL);
    } catch (error: any) {
        console.error('Error downloading PDF:', error);
        let errorMessage = 'Failed to download PDF';
        if (error.response) {
            try {
                const reader = new FileReader();
                reader.onload = () => {
                    try {
                        const result = reader.result;
                        if (typeof result === 'string') {
                            const errorData = JSON.parse(result);
                            errorMessage = errorData.message || errorData.error || errorMessage;
                            alert(`Error: ${errorMessage}`);
                        } else {
                            alert(`Error: ${errorMessage}\nPlease check the server logs for more details.`);
                        }
                    } catch (e) {
                        alert(`Error: ${errorMessage}\nPlease check the server logs for more details.`);
                    }
                };
                reader.readAsText(error.response.data);
            } catch (e) {
                alert(`Error: ${errorMessage}\nPlease check the server logs for more details.`);
            }
        } else {
            alert(`Error: ${errorMessage}\nPlease check the server logs for more details.`);
        }
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
                <Button @click="downloadPDF">
                    <Download class="w-4 h-4 mr-2" />
                    Download PDF
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

            <!-- Laporan perorang -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <User class="h-5 w-5" />
                        Laporan perorang
                    </CardTitle>
                    <CardDescription>Daftar laporan permintaan dan peminjaman per orang</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="mb-4">
                        <div class="relative">
                            <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input
                                v-model="searchQuery"
                                placeholder="Cari nama..."
                                class="pl-8"
                            />
                        </div>
                    </div>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nama</TableHead>
                                <TableHead>Total Permintaan</TableHead>
                                <TableHead>Total Peminjaman</TableHead>
                                <TableHead></TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="user in filteredUsers" :key="user.id">
                                <TableCell>{{ user.name }}</TableCell>
                                <TableCell>{{ user.total_permintaan }}</TableCell>
                                <TableCell>{{ user.total_peminjaman }}</TableCell>
                                <TableCell>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="toggleUser(user.id)"
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
                        </TableBody>
                    </Table>
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