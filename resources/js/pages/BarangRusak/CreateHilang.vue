<template>
  <Head title="Tambah Barang Hilang" />
  <AppLayout>
    <template #header>
      <div class="flex items-center space-x-3">
        <span class="inline-flex items-center justify-center rounded-full bg-yellow-100 p-2">
          <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
          </svg>
        </span>
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
          Tambah Barang Hilang
        </h2>
      </div>
    </template>

    <div class="py-12 bg-gradient-to-br from-yellow-50 via-white to-yellow-100 min-h-screen">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/90 backdrop-blur-md shadow-2xl rounded-2xl border border-yellow-200">
          <div class="p-8">
            <!-- Header -->
            <div class="mb-8 text-center">
              <h3 class="text-2xl font-semibold text-yellow-700 mb-1 flex items-center justify-center gap-2">
                <svg class="h-7 w-7 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
                </svg>
                Form Tambah Barang Hilang
              </h3>
              <p class="text-base text-gray-500">
                Pilih barang yang hilang dan tentukan jumlahnya dengan detail.
              </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitForm" class="space-y-7">
              <!-- Pilih Barang -->
              <div>
                <label for="barang_id" class="block text-sm font-semibold text-gray-700 mb-1">
                  Pilih Barang <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <select
                    id="barang_id"
                    v-model="form.barang_id"
                    required
                    class="block w-full border border-yellow-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500 text-base py-2 px-3 bg-yellow-50"
                  >
                    <option value="">Pilih barang</option>
                    <option 
                      v-for="barang in barangList" 
                      :key="barang.id" 
                      :value="barang.id"
                    >
                      {{ barang.kode_barang }} - {{ barang.nama_barang }} (Stok: {{ barang.stok }})
                    </option>
                  </select>
                  <span class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-yellow-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                  </span>
                </div>
                <div v-if="errors?.barang_id" class="mt-1 text-sm text-red-600">
                  {{ errors.barang_id }}
                </div>
              </div>

              <!-- Jumlah Hilang -->
              <div>
                <label for="jumlah_hilang" class="block text-sm font-semibold text-gray-700 mb-1">
                  Jumlah Hilang <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-3">
                  <input
                    id="jumlah_hilang"
                    v-model="form.jumlah_hilang"
                    type="number"
                    min="1"
                    :max="selectedBarang?.stok || 1"
                    required
                    class="block w-32 border border-yellow-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500 text-base py-2 px-3 bg-yellow-50"
                    placeholder="Jumlah"
                  />
                  <div v-if="selectedBarang" class="text-sm text-gray-500">
                    <span class="inline-flex items-center gap-1">
                      <svg class="h-4 w-4 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18"/>
                      </svg>
                      Stok tersedia: <span class="font-semibold text-yellow-700">{{ selectedBarang.stok }}</span>
                    </span>
                  </div>
                </div>
                <div v-if="errors?.jumlah_hilang" class="mt-1 text-sm text-red-600">
                  {{ errors.jumlah_hilang }}
                </div>
              </div>

              <!-- Keterangan -->
              <div>
                <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-1">
                  Keterangan <span class="text-gray-400">(Opsional)</span>
                </label>
                <textarea
                  id="keterangan"
                  v-model="form.keterangan"
                  rows="3"
                  class="block w-full border border-yellow-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500 text-base py-2 px-3 bg-yellow-50"
                  placeholder="Masukkan keterangan tambahan tentang kehilangan barang"
                ></textarea>
                <div v-if="errors?.keterangan" class="mt-1 text-sm text-red-600">
                  {{ errors.keterangan }}
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex justify-end gap-4 pt-4">
                <button
                  type="button"
                  @click="cancel"
                  class="inline-flex items-center px-5 py-2 border border-yellow-300 rounded-lg font-semibold text-base text-yellow-700 bg-white hover:bg-yellow-50 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2"
                >
                  <svg class="h-5 w-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                  Batal
                </button>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-yellow-400 to-yellow-600 border border-transparent rounded-lg font-semibold text-base text-white shadow-lg hover:from-yellow-500 hover:to-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition disabled:opacity-50"
                >
                  <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface Barang {
  id: number;
  kode_barang: string;
  nama_barang: string;
  stok: number;
}

interface Errors {
  barang_id?: string;
  jumlah_hilang?: string;
  keterangan?: string;
}

const props = defineProps<{
  barangList: Barang[];
  errors?: Errors;
}>();

const form = useForm({
  barang_id: '',
  jumlah_hilang: 1,
  keterangan: ''
});

const selectedBarang = computed(() => {
  if (!form.barang_id) return null;
  return props.barangList.find(barang => barang.id === Number(form.barang_id));
});

const submitForm = () => {
  form.post('/barang-hilang', {
    onSuccess: () => {
      // Form will automatically redirect on success
    },
    onError: (errors) => {
      // Errors will be handled automatically by Inertia
    }
  });
};

const cancel = () => {
  form.reset();
  window.history.back();
};
</script> 