<template>
  <div class="barcode-scanner">
    <!-- Scanner Container -->
    <div class="scanner-container" :class="containerClass">
      <div v-if="isScanning" class="scanner-video-container">
        <div ref="scannerElement" class="scanner-element"></div>
        <div class="scanner-overlay">
          <div class="scanner-frame"></div>
          <div class="scanner-instructions">
            <p class="text-white text-sm font-medium">Arahkan kamera ke barcode</p>
            <p class="text-white/80 text-xs">Pastikan barcode berada dalam frame</p>
          </div>
        </div>
      </div>
      
      <!-- Scanner Controls -->
      <div v-if="!isScanning" class="scanner-controls">
        <div class="text-center p-8">
          <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
            <Camera class="w-8 h-8 text-gray-400" />
          </div>
          <p class="text-gray-600 mb-4">Klik tombol di bawah untuk memulai scan barcode</p>
          <Button @click="startScanning" :disabled="isLoading" class="flex items-center gap-2">
            <Camera class="w-4 h-4" />
            {{ isLoading ? 'Loading...' : 'Mulai Scan' }}
          </Button>
        </div>
      </div>

      <!-- Stop Button -->
      <div v-if="isScanning" class="scanner-stop">
        <Button @click="stopScanning" variant="destructive" class="flex items-center gap-2">
          <Square class="w-4 h-4" />
          Stop Scan
        </Button>
      </div>
    </div>

    <!-- Scan Results -->
    <div v-if="scanResult" class="scan-result mt-4">
      <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center gap-2 mb-2">
          <CheckCircle class="w-5 h-5 text-green-600" />
          <h3 class="font-semibold text-green-800">Barcode Terdeteksi!</h3>
        </div>
        <div class="space-y-2">
          <p class="text-sm">
            <span class="font-medium">Kode:</span> 
            <span class="font-mono bg-green-100 px-2 py-1 rounded">{{ scanResult.code }}</span>
          </p>
          <p class="text-sm">
            <span class="font-medium">Format:</span> 
            <span class="bg-green-100 px-2 py-1 rounded text-xs">{{ scanResult.format }}</span>
          </p>
        </div>
      </div>
    </div>

    <!-- Error Display -->
    <div v-if="error" class="scan-error mt-4">
      <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center gap-2 mb-2">
          <AlertCircle class="w-5 h-5 text-red-600" />
          <h3 class="font-semibold text-red-800">Error</h3>
        </div>
        <p class="text-sm text-red-700">{{ error }}</p>
      </div>
    </div>

    <!-- Action Buttons -->
    <div v-if="scanResult" class="scan-actions mt-4 flex gap-2 justify-center">
      <Button @click="handleScanResult" class="flex items-center gap-2">
        <Search class="w-4 h-4" />
        Cari Barang
      </Button>
      <Button @click="clearResult" variant="outline" class="flex items-center gap-2">
        <X class="w-4 h-4" />
        Clear
      </Button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Camera, Square, CheckCircle, AlertCircle, Search, X } from 'lucide-vue-next';

interface ScanResult {
  code: string;
  format: string;
}

interface Props {
  containerClass?: string;
  onScan?: (result: ScanResult) => void;
  onError?: (error: string) => void;
}

const props = withDefaults(defineProps<Props>(), {
  containerClass: 'bg-white rounded-lg border shadow-sm p-4',
  onScan: () => {},
  onError: () => {}
});

const emit = defineEmits<{
  scan: [result: ScanResult];
  error: [error: string];
}>();

const scannerElement = ref<HTMLElement>();
const isScanning = ref(false);
const isLoading = ref(false);
const scanResult = ref<ScanResult | null>(null);
const error = ref<string>('');

let quaggaInstance: any = null;

const startScanning = async () => {
  if (isScanning.value) return;

  isLoading.value = true;
  error.value = '';

  try {
    // Dynamic import of Quagga
    const Quagga = await import('quagga');
    
    if (!scannerElement.value) {
      throw new Error('Scanner element not found');
    }

    quaggaInstance = Quagga.default.init({
      inputStream: {
        name: "Live",
        type: "LiveStream",
        target: scannerElement.value,
        constraints: {
          width: 640,
          height: 480,
          facingMode: "environment" // Use back camera
        },
      },
      locator: {
        patchSize: "medium",
        halfSample: true
      },
      numOfWorkers: 2,
      frequency: 10,
      decoder: {
        readers: [
          "code_128_reader",
          "ean_reader",
          "ean_8_reader",
          "code_39_reader",
          "code_39_vin_reader",
          "codabar_reader",
          "upc_reader",
          "upc_e_reader",
          "i2of5_reader"
        ]
      },
      locate: true
    }, (err: any) => {
      isLoading.value = false;
      if (err) {
        console.error('Quagga initialization error:', err);
        error.value = 'Failed to initialize camera. Please check camera permissions.';
        props.onError(error.value);
        emit('error', error.value);
        return;
      }
      
      isScanning.value = true;
      console.log("Quagga initialization finished. Ready to start");
    });

    // Listen for successful scans
    Quagga.default.onDetected((data: any) => {
      const code = data.codeResult.code;
      const format = data.codeResult.format;
      
      scanResult.value = { code, format };
      props.onScan(scanResult.value);
      emit('scan', scanResult.value);
      
      // Auto-stop scanning after successful scan
      stopScanning();
    });

  } catch (err) {
    isLoading.value = false;
    console.error('Error starting scanner:', err);
    error.value = 'Failed to start camera. Please check camera permissions and try again.';
    props.onError(error.value);
    emit('error', error.value);
  }
};

const stopScanning = () => {
  if (!isScanning.value) return;

  try {
    if (quaggaInstance) {
      quaggaInstance.stop();
      quaggaInstance = null;
    }
    
    // Also try to stop using Quagga.default if available
    if (typeof window !== 'undefined' && (window as any).Quagga) {
      (window as any).Quagga.stop();
    }
  } catch (err) {
    console.error('Error stopping scanner:', err);
  } finally {
    isScanning.value = false;
  }
};

const handleScanResult = () => {
  if (scanResult.value) {
    // This will be handled by the parent component
    // You can emit an event or call a callback
    console.log('Handling scan result:', scanResult.value);
  }
};

const clearResult = () => {
  scanResult.value = null;
  error.value = '';
};

// Cleanup on unmount
onUnmounted(() => {
  stopScanning();
});

// Expose methods for parent component
defineExpose({
  startScanning,
  stopScanning,
  clearResult
});
</script>

<style scoped>
.scanner-container {
  @apply relative;
}

.scanner-video-container {
  @apply relative;
}

.scanner-element {
  @apply w-full h-64 bg-gray-100 rounded-lg overflow-hidden;
}

.scanner-overlay {
  @apply absolute inset-0 pointer-events-none;
}

.scanner-frame {
  @apply absolute inset-4 border-2 border-white rounded-lg;
  box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.3);
}

.scanner-instructions {
  @apply absolute bottom-4 left-4 right-4 text-center;
}

.scanner-controls {
  @apply w-full h-64 flex items-center justify-center;
}

.scanner-stop {
  @apply absolute top-4 right-4 z-10;
}

.scan-result {
  @apply w-full;
}

.scan-error {
  @apply w-full;
}

.scan-actions {
  @apply w-full;
}

/* Quagga.js styles */
:deep(.quagga-container) {
  @apply w-full h-full;
}

:deep(.quagga-video) {
  @apply w-full h-full object-cover;
}

:deep(.quagga-canvas) {
  @apply absolute top-0 left-0;
}
</style>
