<template>
    <Head :title="`Barcode - ${barang.nama_barang}`" />
    <AppLayout>
        <div class="p-4 md:p-8 space-y-8 bg-gradient-to-br from-blue-50 via-white to-green-50 min-h-screen">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <Link :href="route('barcode.index')">
                        <Button variant="outline" size="sm">
                            <ArrowLeft class="w-4 h-4 mr-2" />
                            Kembali
                        </Button>
                    </Link>
                    <div>
                        <h1 class="text-3xl font-bold text-indigo-800 flex items-center gap-2">
                            <QrCode class="w-7 h-7 text-indigo-500" />
                            Barcode Barang
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">{{ barang.nama_barang }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Button 
                        @click="printBarcode" 
                        variant="outline"
                        class="flex items-center gap-2"
                    >
                        <Printer class="w-4 h-4" />
                        Print
                    </Button>
                    <Button 
                        @click="downloadBarcode" 
                        class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700"
                    >
                        <Download class="w-4 h-4" />
                        Download
                    </Button>
                </div>
            </div>

            <!-- Barcode Display -->
            <div class="bg-white rounded-lg border shadow-sm p-8">
                <div class="max-w-2xl mx-auto">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ barang.nama_barang }}</h2>
                        <p class="text-gray-600">Kode: {{ barang.kode_barang }}</p>
                        <div class="flex items-center justify-center gap-4 mt-4">
                            <span :class="[
                                'px-3 py-1 text-sm rounded-full font-medium',
                                barang.kategori === 'peminjaman'
                                    ? 'bg-blue-100 text-blue-800'
                                    : 'bg-green-100 text-green-800'
                            ]">
                                {{ barang.kategori === 'peminjaman' ? 'Aset' : 'Permintaan' }}
                            </span>
                            <span class="text-sm text-gray-600">
                                Stok: {{ barang.stok }} {{ barang.satuan || '' }}
                            </span>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6">
                        <BarcodeDisplay 
                            :barang-data="barangData"
                            :show-info="false"
                            :show-actions="false"
                            :allow-download="false"
                            :allow-print="false"
                            :allow-copy="false"
                            container-class="bg-white p-4 rounded border"
                            image-class="max-w-full h-auto"
                            :use-styled-barcode="true"
                        />
                    </div>

                    <!-- Barang Details -->
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="font-semibold text-blue-800 mb-3">Informasi Barang</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-blue-600">Kode Barang:</span>
                                    <span class="font-mono text-blue-800">{{ barang.kode_barang }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-blue-600">Nama Barang:</span>
                                    <span class="text-blue-800">{{ barang.nama_barang }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-blue-600">Kategori:</span>
                                    <span class="text-blue-800">{{ barang.kategori === 'peminjaman' ? 'Aset' : 'Permintaan' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-blue-600">Stok:</span>
                                    <span :class="[
                                        'font-semibold',
                                        barang.stok > 0 ? 'text-green-600' : 'text-red-600'
                                    ]">
                                        {{ barang.stok }} {{ barang.satuan || '' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 rounded-lg p-4">
                            <h3 class="font-semibold text-green-800 mb-3">Detail Tambahan</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-green-600">Lokasi:</span>
                                    <span class="text-green-800">{{ barang.lokasi || 'Tidak ditentukan' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-green-600">Satuan:</span>
                                    <span class="text-green-800">{{ barang.satuan || 'Tidak ditentukan' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-green-600">Dibuat:</span>
                                    <span class="text-green-800">{{ formatDate(barang.created_at) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-green-600">Diupdate:</span>
                                    <span class="text-green-800">{{ formatDate(barang.updated_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div v-if="barang.deskripsi" class="mt-6 bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-800 mb-2">Deskripsi</h3>
                        <p class="text-gray-600">{{ barang.deskripsi }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { QrCode, ArrowLeft, Printer, Download } from 'lucide-vue-next';
import BarcodeDisplay from '@/components/BarcodeDisplay.vue';
import { generateBarangBarcode, downloadBarcode as downloadBarcodeUtil } from '@/lib/barcode';

interface Barang {
    id: number;
    kode_barang: string;
    nama_barang: string;
    deskripsi: string;
    kategori: string;
    stok: number;
    satuan?: string;
    lokasi?: string;
    created_at: string;
    updated_at: string;
}

const props = defineProps<{
    barang: Barang;
}>();

const barangData = computed(() => ({
    kode_barang: props.barang.kode_barang,
    nama_barang: props.barang.nama_barang,
    kategori: props.barang.kategori,
    stok: props.barang.stok,
    satuan: props.barang.satuan,
    lokasi: props.barang.lokasi
}));

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const printBarcode = () => {
    const printWindow = window.open('', '_blank');
    if (printWindow) {
        const barcodeDataURL = generateBarangBarcode(barangData.value);
        printWindow.document.write(`
            <html>
                <head>
                    <title>Print Barcode - ${props.barang.nama_barang}</title>
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
                            <p><strong>${props.barang.nama_barang}</strong></p>
                            <p>${props.barang.kode_barang}</p>
                            <p>${props.barang.kategori === 'peminjaman' ? 'Aset' : 'Permintaan'} - Stok: ${props.barang.stok} ${props.barang.satuan || ''}</p>
                        </div>
                    </div>
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
};

const downloadBarcode = () => {
    const filename = `barcode-${props.barang.kode_barang}-${props.barang.nama_barang.replace(/[^a-zA-Z0-9]/g, '_')}.png`;
    downloadBarcodeUtil(props.barang.kode_barang, filename);
};
</script>
