<template>
    <Head title="Barcode Management" />
    <AppLayout>
        <div class="p-4 md:p-8 space-y-8 bg-gradient-to-br from-blue-50 via-white to-green-50 min-h-screen">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-indigo-800 flex items-center gap-2">
                        <QrCode class="w-7 h-7 text-indigo-500" />
                        Barcode Management
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola dan cetak barcode untuk semua barang</p>
                </div>
                <div class="flex items-center gap-2">
                    <Button 
                        @click="showScanner = !showScanner" 
                        variant="outline" 
                        class="flex items-center gap-2"
                    >
                        <Camera class="w-4 h-4" />
                        {{ showScanner ? 'Sembunyikan Scanner' : 'Buka Scanner' }}
                    </Button>
                    <Button 
                        @click="generateAllBarcodes" 
                        class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700"
                        :disabled="isGeneratingAll"
                    >
                        <Download class="w-4 h-4" />
                        {{ isGeneratingAll ? 'Generating...' : 'Download Semua' }}
                    </Button>
                </div>
            </div>

            <!-- Scanner Section -->
            <div v-if="showScanner" class="bg-white rounded-lg border shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <Camera class="w-5 h-5 text-indigo-600" />
                    Barcode Scanner
                </h2>
                <!-- Custom scanner area using .scanner class -->
                <div class="scanner mb-4"></div>
                <BarcodeScanner 
                    @scan="handleScanResult"
                    @error="handleScanError"
                    container-class="bg-gray-50 rounded-lg p-4"
                />
            </div>

            <!-- Search and Filter -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari nama barang atau kode barang..."
                        class="w-full pl-10 pr-4 py-2 border border-blue-200 rounded-lg bg-white shadow focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                    />
                </div>
                <select
                    v-model="selectedKategori"
                    class="border border-blue-200 rounded-lg px-4 py-2 bg-white shadow focus:outline-none focus:ring-2 focus:ring-blue-400 w-full md:w-48"
                >
                    <option value="">Semua Kategori</option>
                    <option value="peminjaman">Aset (Peminjaman)</option>
                    <option value="permintaan">Permintaan</option>
                </select>
                <select
                    v-model="selectedFormat"
                    class="border border-blue-200 rounded-lg px-4 py-2 bg-white shadow focus:outline-none focus:ring-2 focus:ring-blue-400 w-full md:w-48"
                >
                    <option value="CODE128">CODE128</option>
                    <option value="CODE39">CODE39</option>
                    <option value="EAN13">EAN13</option>
                    <option value="EAN8">EAN8</option>
                    <option value="UPC">UPC</option>
                </select>
            </div>

            <!-- Barcode Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div
                    v-for="item in filteredBarang"
                    :key="item.id"
                    class="bg-white rounded-lg border shadow-sm hover:shadow-md transition-shadow p-4"
                >
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-800 truncate">{{ item.nama_barang }}</h3>
                        <span :class="[
                            'px-2 py-1 text-xs rounded-full font-medium',
                            item.kategori === 'peminjaman'
                                ? 'bg-blue-100 text-blue-800'
                                : 'bg-green-100 text-green-800'
                        ]">
                            {{ item.kategori === 'peminjaman' ? 'Aset' : 'Permintaan' }}
                        </span>
                    </div>
                    
                    <div class="mb-4">
                        <BarcodeDisplay 
                            :barang-data="item"
                            :show-info="false"
                            :show-actions="true"
                            :allow-download="true"
                            :allow-print="true"
                            :allow-copy="true"
                            :options="barcodeOptions"
                            container-class="bg-gray-50 p-2 rounded border"
                            image-class="max-w-full h-auto"
                            :use-styled-barcode="true"
                        />
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Kode:</span>
                            <span class="font-mono text-blue-700">{{ item.kode_barang }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Stok:</span>
                            <span :class="[
                                'font-semibold',
                                item.stok > 0 ? 'text-green-600' : 'text-red-600'
                            ]">
                                {{ item.stok }} {{ item.satuan || '' }}
                            </span>
                        </div>
                        <div v-if="item.lokasi" class="flex justify-between">
                            <span class="text-gray-500">Lokasi:</span>
                            <span class="text-gray-700">{{ item.lokasi }}</span>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-4">
                        <Button 
                            @click="printSingleBarcode(item)" 
                            variant="outline" 
                            size="sm"
                            class="flex-1"
                        >
                            <Printer class="w-4 h-4 mr-1" />
                            Print
                        </Button>
                        <Button 
                            @click="downloadSingleBarcode(item)" 
                            variant="outline" 
                            size="sm"
                            class="flex-1"
                        >
                            <Download class="w-4 h-4 mr-1" />
                            Download
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="filteredBarang.length === 0" class="text-center py-16">
                <div class="flex flex-col items-center justify-center">
                    <QrCode class="w-16 h-16 text-blue-200 mb-4" />
                    <h3 class="text-xl font-bold text-gray-400 mb-2">Tidak ada barang ditemukan</h3>
                    <p class="text-gray-500">Coba ubah filter atau tambahkan barang baru.</p>
                </div>
            </div>

            <!-- Batch Actions -->
            <div v-if="filteredBarang.length > 0" class="bg-white rounded-lg border shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Batch Actions</h3>
                <div class="flex flex-wrap gap-3">
                    <Button 
                        @click="printAllBarcodes" 
                        variant="outline"
                        class="flex items-center gap-2"
                    >
                        <Printer class="w-4 h-4" />
                        Print Semua ({{ filteredBarang.length }})
                    </Button>
                    <Button 
                        @click="downloadAllBarcodes" 
                        variant="outline"
                        class="flex items-center gap-2"
                    >
                        <Download class="w-4 h-4" />
                        Download Semua
                    </Button>
                    <Button 
                        @click="generateBarcodeLabels" 
                        variant="outline"
                        class="flex items-center gap-2"
                    >
                        <FileText class="w-4 h-4" />
                        Generate Labels
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { QrCode, Search, Camera, Download, Printer, FileText } from 'lucide-vue-next';
import BarcodeDisplay from '@/components/BarcodeDisplay.vue';
import BarcodeScanner from '@/components/BarcodeScanner.vue';
import { 
    generateBarangBarcode, 
    downloadBarcode, 
    generateBatchBarcodes,
    type BarcodeData,
    type BarcodeOptions 
} from '@/lib/barcode';

interface BarangItem {
    id: number;
    kode_barang: string;
    nama_barang: string;
    deskripsi: string;
    kategori: string;
    stok: number;
    satuan?: string;
    lokasi?: string;
}

const props = defineProps<{
    barang: BarangItem[];
}>();

const searchQuery = ref('');
const selectedKategori = ref('');
const selectedFormat = ref('CODE128');
const showScanner = ref(false);
const isGeneratingAll = ref(false);

const barcodeOptions = computed((): BarcodeOptions => ({
    format: selectedFormat.value as any,
    width: 2,
    height: 80,
    displayValue: true,
    fontSize: 12,
    margin: 8,
    background: '#ffffff',
    lineColor: '#000000',
    textAlign: 'center',
    textPosition: 'bottom',
    textMargin: 2,
    font: 'monospace',
    fontOptions: 'normal'
}));

const filteredBarang = computed(() => {
    return props.barang.filter(item => {
        const matchesSearch = 
            item.nama_barang.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.kode_barang.toLowerCase().includes(searchQuery.value.toLowerCase());
        
        const matchesKategori = !selectedKategori.value || item.kategori === selectedKategori.value;
        
        return matchesSearch && matchesKategori;
    });
});

const handleScanResult = (result: { code: string; format: string }) => {
    console.log('Scan result:', result);
    // Find barang by kode_barang
    const foundBarang = props.barang.find(item => item.kode_barang === result.code);
    if (foundBarang) {
        // You can show a modal or navigate to the item
        console.log('Found barang:', foundBarang);
    } else {
        console.log('Barang not found for code:', result.code);
    }
};

const handleScanError = (error: string) => {
    console.error('Scan error:', error);
};

const printSingleBarcode = (item: BarangItem) => {
    const printWindow = window.open('', '_blank');
    if (printWindow) {
        const barcodeDataURL = generateBarangBarcode(item, barcodeOptions.value);
        printWindow.document.write(`
            <html>
                <head>
                    <title>Print Barcode - ${item.nama_barang}</title>
                    <style>
                        body { margin: 0; padding: 20px; text-align: center; font-family: Arial, sans-serif; }
                        .barcode-container { display: inline-block; margin: 20px; }
                        .barcode-info { margin-top: 10px; font-size: 14px; }
                        @media print { body { margin: 0; padding: 10px; } }
                    </style>
                </head>
                <body>
                    <div class="barcode-container">
                        <img src="${barcodeDataURL}" alt="Barcode" style="max-width: 100%; height: auto;" />
                        <div class="barcode-info">
                            <p><strong>${item.nama_barang}</strong></p>
                            <p>${item.kode_barang}</p>
                            <p>${item.kategori === 'peminjaman' ? 'Aset' : 'Permintaan'} - Stok: ${item.stok} ${item.satuan || ''}</p>
                        </div>
                    </div>
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
};

const downloadSingleBarcode = (item: BarangItem) => {
    const filename = `barcode-${item.kode_barang}-${item.nama_barang.replace(/[^a-zA-Z0-9]/g, '_')}.png`;
    downloadBarcode(item.kode_barang, filename, barcodeOptions.value);
};

const printAllBarcodes = () => {
    const printWindow = window.open('', '_blank');
    if (printWindow) {
        let html = `
            <html>
                <head>
                    <title>Print All Barcodes</title>
                    <style>
                        body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }
                        .barcode-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
                        .barcode-container { text-align: center; page-break-inside: avoid; }
                        .barcode-info { margin-top: 10px; font-size: 12px; }
                        @media print { body { margin: 0; padding: 10px; } }
                    </style>
                </head>
                <body>
                    <div class="barcode-grid">
        `;
        
        filteredBarang.value.forEach(item => {
            const barcodeDataURL = generateBarangBarcode(item, barcodeOptions.value);
            html += `
                <div class="barcode-container">
                    <img src="${barcodeDataURL}" alt="Barcode" style="max-width: 100%; height: auto;" />
                    <div class="barcode-info">
                        <p><strong>${item.nama_barang}</strong></p>
                        <p>${item.kode_barang}</p>
                        <p>${item.kategori === 'peminjaman' ? 'Aset' : 'Permintaan'}</p>
                    </div>
                </div>
            `;
        });
        
        html += `
                    </div>
                </body>
            </html>
        `;
        
        printWindow.document.write(html);
        printWindow.document.close();
        printWindow.print();
    }
};

const downloadAllBarcodes = async () => {
    isGeneratingAll.value = true;
    
    try {
        // Create a zip file with all barcodes
        // For now, we'll download them one by one
        for (let i = 0; i < filteredBarang.value.length; i++) {
            const item = filteredBarang.value[i];
            setTimeout(() => {
                downloadSingleBarcode(item);
            }, i * 500); // Delay between downloads
        }
    } finally {
        isGeneratingAll.value = false;
    }
};

const generateAllBarcodes = () => {
    downloadAllBarcodes();
};

const generateBarcodeLabels = () => {
    // Generate a printable label sheet
    const printWindow = window.open('', '_blank');
    if (printWindow) {
        let html = `
            <html>
                <head>
                    <title>Barcode Labels</title>
                    <style>
                        body { margin: 0; padding: 10px; font-family: Arial, sans-serif; }
                        .label-sheet { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
                        .label { border: 1px solid #ccc; padding: 10px; text-align: center; page-break-inside: avoid; }
                        .label img { max-width: 100%; height: auto; }
                        .label-info { margin-top: 5px; font-size: 10px; }
                        @media print { body { margin: 0; padding: 5px; } }
                    </style>
                </head>
                <body>
                    <div class="label-sheet">
        `;
        
        filteredBarang.value.forEach(item => {
            const barcodeDataURL = generateBarangBarcode(item, {
                ...barcodeOptions.value,
                height: 60,
                fontSize: 10
            });
            html += `
                <div class="label">
                    <img src="${barcodeDataURL}" alt="Barcode" />
                    <div class="label-info">
                        <p><strong>${item.nama_barang}</strong></p>
                        <p>${item.kode_barang}</p>
                    </div>
                </div>
            `;
        });
        
        html += `
                    </div>
                </body>
            </html>
        `;
        
        printWindow.document.write(html);
        printWindow.document.close();
        printWindow.print();
    }
};
</script>
