<template>
  <div class="flex flex-col items-center">
    <!-- Barcode Image -->
    <div class="flex flex-col items-center justify-center" :class="containerClass">
      <img 
        v-if="barcodeDataURL" 
        :src="barcodeDataURL" 
        :alt="`Barcode for ${barangData?.nama_barang || 'item'}`"
        class="block"
        :class="imageClass"
        @error="handleImageError"
      />
      <div v-else-if="isLoading" class="flex flex-col items-center justify-center p-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <p class="text-sm text-gray-500 mt-2">Generating barcode...</p>
      </div>
      <div v-else class="flex flex-col items-center justify-center p-8">
        <div class="text-red-500 text-center">
          <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
          <p class="text-sm">Failed to generate barcode</p>
        </div>
      </div>
    </div>

    <!-- Barcode Info -->
    <div v-if="showInfo && barangData" class="text-center mt-3">
      <div class="text-center">
        <p class="text-sm font-semibold text-gray-800">{{ barangData.nama_barang }}</p>
        <p class="text-xs text-gray-600 font-mono">{{ barangData.kode_barang }}</p>
        <div class="flex items-center justify-center gap-2 mt-1">
          <span :class="[
            'px-2 py-1 text-xs rounded-full font-medium',
            barangData.kategori === 'peminjaman' 
              ? 'bg-blue-100 text-blue-800' 
              : 'bg-green-100 text-green-800'
          ]">
            {{ barangData.kategori === 'peminjaman' ? 'Aset' : 'Permintaan' }}
          </span>
          <span class="text-xs text-gray-500">
            Stok: {{ barangData.stok }} {{ barangData.satuan || '' }}
          </span>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div v-if="showActions" class="flex gap-2 justify-center mt-3">
      <Button 
        v-if="allowDownload"
        @click="downloadBarcode"
        variant="outline" 
        size="sm"
        class="flex items-center gap-1"
      >
        <Download class="w-4 h-4" />
        Download
      </Button>
      <Button 
        v-if="allowPrint"
        @click="printBarcode"
        variant="outline" 
        size="sm"
        class="flex items-center gap-1"
      >
        <Printer class="w-4 h-4" />
        Print
      </Button>
      <Button 
        v-if="allowCopy"
        @click="copyBarcode"
        variant="outline" 
        size="sm"
        class="flex items-center gap-1"
      >
        <Copy class="w-4 h-4" />
        Copy
      </Button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Download, Printer, Copy } from 'lucide-vue-next';
import { 
  generateBarangBarcode, 
  generateStyledBarcode, 
  downloadBarcode as downloadBarcodeUtil,
  type BarcodeData,
  type BarcodeOptions 
} from '@/lib/barcode';

interface Props {
  barangData?: BarcodeData;
  kodeBarang?: string;
  options?: BarcodeOptions;
  showInfo?: boolean;
  showActions?: boolean;
  allowDownload?: boolean;
  allowPrint?: boolean;
  allowCopy?: boolean;
  containerClass?: string;
  imageClass?: string;
  useStyledBarcode?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  showInfo: true,
  showActions: true,
  allowDownload: true,
  allowPrint: true,
  allowCopy: true,
  containerClass: 'bg-white p-4 rounded-lg border shadow-sm',
  imageClass: 'max-w-full h-auto',
  useStyledBarcode: false
});

const barcodeDataURL = ref<string>('');
const isLoading = ref(false);
const error = ref<string>('');

const generateBarcode = async () => {
  if (!props.barangData && !props.kodeBarang) {
    error.value = 'No barcode data provided';
    return;
  }

  isLoading.value = true;
  error.value = '';

  try {
    let dataURL: string;

    if (props.barangData) {
      if (props.useStyledBarcode) {
        dataURL = generateStyledBarcode(
          props.barangData.kode_barang,
          props.barangData.kategori as 'peminjaman' | 'permintaan',
          props.options
        );
      } else {
        dataURL = generateBarangBarcode(props.barangData, props.options);
      }
    } else if (props.kodeBarang) {
      // For simple barcode generation without barang data
      const { generateBarcodeDataURL } = await import('@/lib/barcode');
      dataURL = generateBarcodeDataURL(props.kodeBarang, props.options);
    }

    barcodeDataURL.value = dataURL;
  } catch (err) {
    console.error('Error generating barcode:', err);
    error.value = 'Failed to generate barcode';
  } finally {
    isLoading.value = false;
  }
};

const downloadBarcode = () => {
  if (!barcodeDataURL.value) return;
  
  const filename = props.barangData 
    ? `barcode-${props.barangData.kode_barang}.png`
    : `barcode-${props.kodeBarang}.png`;
  
  downloadBarcodeUtil(
    props.barangData?.kode_barang || props.kodeBarang || '',
    filename,
    props.options
  );
};

const printBarcode = () => {
  if (!barcodeDataURL.value) return;
  
  const printWindow = window.open('', '_blank');
  if (printWindow) {
    printWindow.document.write(`
      <html>
        <head>
          <title>Print Barcode</title>
          <style>
            body { 
              margin: 0; 
              padding: 20px; 
              text-align: center; 
              font-family: Arial, sans-serif;
            }
            .barcode-container { 
              display: inline-block; 
              margin: 20px;
            }
            .barcode-info { 
              margin-top: 10px; 
              font-size: 14px;
            }
            @media print {
              body { margin: 0; padding: 10px; }
              .barcode-container { margin: 10px; }
            }
          </style>
        </head>
        <body>
          <div class="barcode-container">
            <img src="${barcodeDataURL.value}" alt="Barcode" style="max-width: 100%; height: auto;" />
            ${props.showInfo && props.barangData ? `
              <div class="barcode-info">
                <p><strong>${props.barangData.nama_barang}</strong></p>
                <p>${props.barangData.kode_barang}</p>
                <p>${props.barangData.kategori === 'peminjaman' ? 'Aset' : 'Permintaan'} - Stok: ${props.barangData.stok} ${props.barangData.satuan || ''}</p>
              </div>
            ` : ''}
          </div>
        </body>
      </html>
    `);
    printWindow.document.close();
    printWindow.print();
  }
};

const copyBarcode = async () => {
  if (!barcodeDataURL.value) return;
  
  try {
    // Convert data URL to blob
    const response = await fetch(barcodeDataURL.value);
    const blob = await response.blob();
    
    // Copy to clipboard
    await navigator.clipboard.write([
      new ClipboardItem({
        [blob.type]: blob
      })
    ]);
    
    // You might want to show a toast notification here
    console.log('Barcode copied to clipboard');
  } catch (err) {
    console.error('Failed to copy barcode:', err);
  }
};

const handleImageError = () => {
  error.value = 'Failed to load barcode image';
  barcodeDataURL.value = '';
};

// Watch for changes in props
watch([() => props.barangData, () => props.kodeBarang, () => props.options], () => {
  generateBarcode();
}, { deep: true });

onMounted(() => {
  generateBarcode();
});
</script>

