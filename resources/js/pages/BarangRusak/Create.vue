<template>
  <Head title="Tambah Barang Rusak" />
  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Tambah Barang Rusak
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <!-- Header -->
            <div class="mb-6">
              <h3 class="text-lg font-medium text-gray-900">
                Form Tambah Barang Rusak
              </h3>
              <p class="text-sm text-gray-500">
                Pilih barang yang rusak dan tentukan jumlahnya
              </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitForm" class="space-y-6">
              <!-- Pilih Barang -->
              <div>
                <label for="barang_id" class="block text-sm font-medium text-gray-700">
                  Pilih Barang *
                </label>
                <select
                  id="barang_id"
                  v-model="form.barang_id"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
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
                <div v-if="errors?.barang_id" class="mt-1 text-sm text-red-600">
                  {{ errors.barang_id }}
                </div>
              </div>

              <!-- Jumlah Rusak -->
              <div>
                <label for="jumlah_rusak" class="block text-sm font-medium text-gray-700">
                  Jumlah Rusak *
                </label>
                <input
                  id="jumlah_rusak"
                  v-model="form.jumlah_rusak"
                  type="number"
                  min="1"
                  :max="selectedBarang?.stok || 1"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  placeholder="Masukkan jumlah barang yang rusak"
                />
                <div v-if="selectedBarang" class="mt-1 text-sm text-gray-500">
                  Stok tersedia: {{ selectedBarang.stok }}
                </div>
                <div v-if="errors?.jumlah_rusak" class="mt-1 text-sm text-red-600">
                  {{ errors.jumlah_rusak }}
                </div>
              </div>

              <!-- Keterangan -->
              <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700">
                  Keterangan (Opsional)
                </label>
                <textarea
                  id="keterangan"
                  v-model="form.keterangan"
                  rows="3"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  placeholder="Masukkan keterangan tambahan tentang kerusakan"
                ></textarea>
                <div v-if="errors?.keterangan" class="mt-1 text-sm text-red-600">
                  {{ errors.keterangan }}
                </div>
              </div>

              <!-- Form Actions -->
              <div class="flex justify-end space-x-3 pt-6">
                <button
                  type="button"
                  @click="cancel"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-25"
                >
                  <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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