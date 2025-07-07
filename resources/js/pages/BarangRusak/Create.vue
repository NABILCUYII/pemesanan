<template>
  <Head title="Tambah Barang Rusak" />
  <AppLayout>
    <template #header>
      <div class="flex items-center space-x-3">
        <span class="inline-flex items-center justify-center rounded-full bg-red-100 p-2">
          <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-1.414-1.414A9 9 0 003 12h2a7 7 0 0111.95-4.95l-1.414-1.414zM12 3v2m0 14v2m7.071-7.071l1.414 1.414A9 9 0 0112 21v-2a7 7 0 004.95-11.95l1.414 1.414z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/>
          </svg>
        </span>
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
          Tambah Barang Rusak
        </h2>
      </div>
    </template>

    <div class="py-12 bg-gradient-to-br from-red-50 via-white to-red-100 min-h-screen">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/90 backdrop-blur-md shadow-2xl rounded-2xl border border-red-200">
          <div class="p-8">
            <!-- Header -->
            <div class="mb-8 text-center">
              <h3 class="text-2xl font-semibold text-red-700 mb-1 flex items-center justify-center gap-2">
                <svg class="h-7 w-7 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-1.414-1.414A9 9 0 003 12h2a7 7 0 0111.95-4.95l-1.414-1.414zM12 3v2m0 14v2m7.071-7.071l1.414 1.414A9 9 0 0112 21v-2a7 7 0 004.95-11.95l1.414 1.414z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/>
                </svg>
                Form Tambah Barang Rusak
              </h3>
              <p class="text-base text-gray-500">
                Pilih barang yang rusak dan tentukan jumlahnya dengan detail.
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
                    class="block w-full border border-red-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 text-base py-2 px-3 bg-red-50 appearance-none"
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
                  <!-- Custom arrow indicator -->
                  <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-red-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                  </span>
                </div>
                <div v-if="errors?.barang_id" class="mt-1 text-sm text-red-600">
                  {{ errors.barang_id }}
                </div>
              </div>

              <!-- Jumlah Rusak -->
              <div>
                <label for="jumlah_rusak" class="block text-sm font-semibold text-gray-700 mb-1">
                  Jumlah Rusak <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-3">
                  <input
                    id="jumlah_rusak"
                    v-model="form.jumlah_rusak"
                    type="number"
                    min="1"
                    :max="selectedBarang?.stok || 1"
                    required
                    class="block w-32 border border-red-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 text-base py-2 px-3 bg-red-50"
                    placeholder="Jumlah"
                  />
                  <div v-if="selectedBarang" class="text-sm text-gray-500">
                    <span class="inline-flex items-center gap-1">
                      <svg class="h-4 w-4 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18"/>
                      </svg>
                      Stok tersedia: <span class="font-semibold text-red-700">{{ selectedBarang.stok }}</span>
                    </span>
                  </div>
                </div>
                <div v-if="errors?.jumlah_rusak" class="mt-1 text-sm text-red-600">
                  {{ errors.jumlah_rusak }}
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
                  class="block w-full border border-red-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 text-base py-2 px-3 bg-red-50"
                  placeholder="Masukkan keterangan tambahan tentang kerusakan"
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
                  class="inline-flex items-center px-5 py-2 border border-red-300 rounded-lg font-semibold text-base text-red-700 bg-white hover:bg-red-50 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2"
                >
                  <svg class="h-5 w-5 mr-2 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                  Batal
                </button>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-red-400 to-red-600 border border-transparent rounded-lg font-semibold text-base text-white shadow-lg hover:from-red-500 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition disabled:opacity-50"
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
  jumlah_rusak?: string;
  keterangan?: string;
}

const props = defineProps<{
  barangList: Barang[];
  errors?: Errors;
}>();

const form = useForm({
  barang_id: '',
  jumlah_rusak: 1,
  keterangan: ''
});

const selectedBarang = computed(() => {
  if (!form.barang_id) return null;
  return props.barangList.find(barang => barang.id === Number(form.barang_id));
});

const submitForm = () => {
  form.post('/barang-rusak', {
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