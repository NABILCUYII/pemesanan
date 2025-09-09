<template>
  <Head title="Tambah Barang Rusak dari Inventaris" />

  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-orange-50">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <div class="bg-red-100 rounded-full p-2">
                  <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                  </svg>
                </div>
                Tambah Barang Rusak dari Inventaris
              </h1>
              <p class="mt-2 text-gray-600">Pilih item dari inventaris yang rusak atau hilang</p>
            </div>
            <button
              @click="goBack"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Kembali
            </button>
          </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
          <form @submit.prevent="submitForm" class="space-y-8">
            <!-- Pilih Item Inventaris -->
            <div>
              <label for="inventaris_id" class="block text-sm font-semibold text-gray-700 mb-3">
                Pilih Item Inventaris <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <select
                  id="inventaris_id"
                  v-model="form.inventaris_id"
                  required
                  class="block w-full border border-red-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 text-base py-3 px-4 bg-red-50 appearance-none"
                  @change="onInventarisChange"
                >
                  <option value="">Pilih item inventaris</option>
                  <option 
                    v-for="inventaris in inventarisList" 
                    :key="inventaris.id" 
                    :value="inventaris.id"
                  >
                    {{ inventaris.nomor_inventaris }} - {{ inventaris.nama_barang }} 
                    ({{ inventaris.nama_peminjam }} - {{ inventaris.jumlah_dipinjam }} unit)
                  </option>
                </select>
                <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-red-400">
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                  </svg>
                </span>
              </div>
              <div v-if="errors?.inventaris_id" class="mt-2 text-sm text-red-600">
                {{ errors.inventaris_id }}
              </div>
            </div>

            <!-- Detail Item yang Dipilih -->
            <div v-if="selectedInventaris" class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
              <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Detail Item Inventaris
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <p class="text-sm font-medium text-blue-800">Nomor Inventaris:</p>
                  <p class="text-lg font-bold text-blue-900">{{ selectedInventaris.nomor_inventaris }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-blue-800">Nama Barang:</p>
                  <p class="text-lg font-bold text-blue-900">{{ selectedInventaris.nama_barang }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-blue-800">Kode Barang:</p>
                  <p class="text-lg font-bold text-blue-900">{{ selectedInventaris.kode_barang }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-blue-800">Peminjam:</p>
                  <p class="text-lg font-bold text-blue-900">{{ selectedInventaris.nama_peminjam }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-blue-800">Jumlah Dipinjam:</p>
                  <p class="text-lg font-bold text-blue-900">{{ selectedInventaris.jumlah_dipinjam }} unit</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-blue-800">Tanggal Peminjaman:</p>
                  <p class="text-lg font-bold text-blue-900">{{ formatDate(selectedInventaris.tanggal_peminjaman) }}</p>
                </div>
                <div v-if="selectedInventaris.keterangan" class="md:col-span-2">
                  <p class="text-sm font-medium text-blue-800">Keterangan:</p>
                  <p class="text-lg font-bold text-blue-900">{{ selectedInventaris.keterangan }}</p>
                </div>
                <div v-if="selectedInventaris.lokasi_peminjaman" class="md:col-span-2">
                  <p class="text-sm font-medium text-blue-800">Lokasi Peminjaman:</p>
                  <p class="text-lg font-bold text-blue-900">{{ selectedInventaris.lokasi_peminjaman }}</p>
                </div>
              </div>
            </div>

            <!-- Jumlah Rusak -->
            <div>
              <label for="jumlah_rusak" class="block text-sm font-semibold text-gray-700 mb-3">
                Jumlah yang Rusak/Hilang <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input
                  id="jumlah_rusak"
                  v-model.number="form.jumlah_rusak"
                  type="number"
                  min="1"
                  :max="selectedInventaris ? selectedInventaris.jumlah_dipinjam : 1"
                  required
                  class="block w-full border border-red-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 text-base py-3 px-4 bg-red-50"
                  placeholder="Masukkan jumlah yang rusak"
                />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                  <span class="text-gray-500 text-sm">unit</span>
                </div>
              </div>
              <div v-if="selectedInventaris" class="mt-2 text-sm text-gray-600">
                Maksimal: {{ selectedInventaris.jumlah_dipinjam }} unit
              </div>
              <div v-if="errors?.jumlah_rusak" class="mt-2 text-sm text-red-600">
                {{ errors.jumlah_rusak }}
              </div>
            </div>

            <!-- Kondisi Barang -->
            <div>
              <label for="kondisi_barang" class="block text-sm font-semibold text-gray-700 mb-3">
                Kondisi Barang <span class="text-red-500">*</span>
              </label>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="relative cursor-pointer">
                  <input
                    type="radio"
                    v-model="form.kondisi_barang"
                    value="rusak_ringan"
                    class="sr-only"
                    required
                  />
                  <div class="border-2 border-gray-200 rounded-lg p-4 text-center transition-all duration-200 hover:border-yellow-300"
                       :class="{ 'border-yellow-500 bg-yellow-50': form.kondisi_barang === 'rusak_ringan' }">
                    <div class="w-12 h-12 mx-auto mb-2 bg-yellow-100 rounded-full flex items-center justify-center">
                      <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                      </svg>
                    </div>
                    <div class="font-semibold text-gray-900">Rusak Ringan</div>
                    <div class="text-sm text-gray-600">50% dapat diperbaiki</div>
                  </div>
                </label>

                <label class="relative cursor-pointer">
                  <input
                    type="radio"
                    v-model="form.kondisi_barang"
                    value="rusak_berat"
                    class="sr-only"
                    required
                  />
                  <div class="border-2 border-gray-200 rounded-lg p-4 text-center transition-all duration-200 hover:border-red-300"
                       :class="{ 'border-red-500 bg-red-50': form.kondisi_barang === 'rusak_berat' }">
                    <div class="w-12 h-12 mx-auto mb-2 bg-red-100 rounded-full flex items-center justify-center">
                      <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                      </svg>
                    </div>
                    <div class="font-semibold text-gray-900">Rusak Berat</div>
                    <div class="text-sm text-gray-600">Tidak dapat diperbaiki</div>
                  </div>
                </label>

                <label class="relative cursor-pointer">
                  <input
                    type="radio"
                    v-model="form.kondisi_barang"
                    value="hilang"
                    class="sr-only"
                    required
                  />
                  <div class="border-2 border-gray-200 rounded-lg p-4 text-center transition-all duration-200 hover:border-orange-300"
                       :class="{ 'border-orange-500 bg-orange-50': form.kondisi_barang === 'hilang' }">
                    <div class="w-12 h-12 mx-auto mb-2 bg-orange-100 rounded-full flex items-center justify-center">
                      <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z" />
                      </svg>
                    </div>
                    <div class="font-semibold text-gray-900">Hilang</div>
                    <div class="text-sm text-gray-600">Barang tidak ditemukan</div>
                  </div>
                </label>
              </div>
              <div v-if="errors?.kondisi_barang" class="mt-2 text-sm text-red-600">
                {{ errors.kondisi_barang }}
              </div>
            </div>

            <!-- Keterangan -->
            <div>
              <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-3">
                Keterangan Tambahan
              </label>
              <textarea
                id="keterangan"
                v-model="form.keterangan"
                rows="4"
                class="block w-full border border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 text-base py-3 px-4"
                placeholder="Jelaskan detail kerusakan atau kehilangan barang..."
              ></textarea>
              <div v-if="errors?.keterangan" class="mt-2 text-sm text-red-600">
                {{ errors.keterangan }}
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 pt-6">
              <button
                type="submit"
                :disabled="form.processing"
                class="flex-1 inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition"
              >
                <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                {{ form.processing ? 'Memproses...' : 'Tandai sebagai Rusak' }}
              </button>
              <button
                type="button"
                @click="goBack"
                class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition"
              >
                Batal
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface Inventaris {
  id: number;
  nomor_inventaris: string;
  barang_id: number;
  kode_barang: string;
  nama_barang: string;
  jumlah_dipinjam: number;
  nama_peminjam: string;
  tanggal_peminjaman: string;
  due_date: string;
  keterangan: string;
  lokasi_peminjaman: string;
}

interface Errors {
  inventaris_id?: string;
  jumlah_rusak?: string;
  kondisi_barang?: string;
  keterangan?: string;
}

const props = defineProps<{
  inventarisList: Inventaris[];
  errors?: Errors;
}>();

const form = useForm({
  inventaris_id: '',
  jumlah_rusak: 1,
  kondisi_barang: '',
  keterangan: ''
});

const selectedInventaris = computed(() => {
  if (!form.inventaris_id) return null;
  return props.inventarisList.find(inventaris => inventaris.id === Number(form.inventaris_id));
});

const onInventarisChange = () => {
  if (selectedInventaris.value) {
    form.jumlah_rusak = selectedInventaris.value.jumlah_dipinjam;
  }
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const submitForm = () => {
  form.post('/barang-rusak/from-inventaris', {
    onSuccess: () => {
      // Form will automatically redirect on success
    },
    onError: (errors) => {
      // Errors will be handled automatically by Inertia
    }
  });
};

const goBack = () => {
  window.history.back();
};
</script> 