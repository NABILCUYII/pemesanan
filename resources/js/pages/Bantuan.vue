<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { HelpCircle, BookOpen, Users, Package, Clock, FileText, Database, Settings, ChevronDown, ChevronRight, ExternalLink, Video, FileQuestion, MessageCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Bantuan',
    href: '/bantuan',
  },
];

const openSections = ref<string[]>([]);

const toggleSection = (section: string) => {
  if (openSections.value.includes(section)) {
    openSections.value = openSections.value.filter(s => s !== section);
  } else {
    openSections.value.push(section);
  }
};

const isOpen = (section: string) => openSections.value.includes(section);
</script>

<template>
  <Head title="Bantuan">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
  </Head>

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6">
      <!-- Header Section -->
      <div class="text-center space-y-4">
        <div class="flex justify-center">
          <div class="p-4 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full">
            <HelpCircle class="w-12 h-12 text-white" />
          </div>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Pusat Bantuan</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
          Temukan jawaban untuk pertanyaan umum dan panduan lengkap penggunaan sistem pemesanan.
        </p>
      </div>

      <!-- Quick Links -->
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <BookOpen class="w-5 h-5 text-blue-500" />
            Panduan Cepat
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 border rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
              <div class="flex items-center gap-3">
                <Package class="w-6 h-6 text-blue-500" />
                <div>
                  <h3 class="font-semibold">Manajemen Barang</h3>
                  <p class="text-sm text-gray-600">Tambah, edit, hapus barang</p>
                </div>
              </div>
            </div>
            <div class="p-4 border rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
              <div class="flex items-center gap-3">
                <Clock class="w-6 h-6 text-green-500" />
                <div>
                  <h3 class="font-semibold">Peminjaman</h3>
                  <p class="text-sm text-gray-600">Proses pinjam & kembalikan</p>
                </div>
              </div>
            </div>
            <div class="p-4 border rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
              <div class="flex items-center gap-3">
                <FileText class="w-6 h-6 text-purple-500" />
                <div>
                  <h3 class="font-semibold">Permintaan</h3>
                  <p class="text-sm text-gray-600">Buat & approve permintaan</p>
                </div>
              </div>
            </div>
            <div class="p-4 border rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
              <div class="flex items-center gap-3">
                <Database class="w-6 h-6 text-orange-500" />
                <div>
                  <h3 class="font-semibold">Inventaris</h3>
                  <p class="text-sm text-gray-600">Monitoring stok barang</p>
                </div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- FAQ Section -->
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <FileQuestion class="w-5 h-5 text-purple-500" />
            Pertanyaan Umum (FAQ)
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div class="space-y-4">
            <!-- FAQ Item 1 -->
            <Collapsible :open="isOpen('faq1')" @update:open="toggleSection('faq1')">
              <CollapsibleTrigger class="flex items-center justify-between w-full p-4 text-left border rounded-lg hover:bg-gray-50 transition-colors">
                <span class="font-semibold">Bagaimana cara menambah barang baru?</span>
                <ChevronDown v-if="isOpen('faq1')" class="w-4 h-4" />
                <ChevronRight v-else class="w-4 h-4" />
              </CollapsibleTrigger>
              <CollapsibleContent class="p-4 border-t">
                <div class="space-y-2 text-gray-600">
                  <p>Untuk menambah barang baru, ikuti langkah berikut:</p>
                  <ol class="list-decimal list-inside space-y-1 ml-4">
                    <li>Buka menu "Barang" di sidebar</li>
                    <li>Klik tombol "Tambah Barang"</li>
                    <li>Isi semua field yang diperlukan (nama, kode, kategori, dll)</li>
                    <li>Upload foto barang jika diperlukan</li>
                    <li>Klik "Simpan" untuk menyimpan data</li>
                  </ol>
                </div>
              </CollapsibleContent>
            </Collapsible>

            <!-- FAQ Item 2 -->
            <Collapsible :open="isOpen('faq2')" @update:open="toggleSection('faq2')">
              <CollapsibleTrigger class="flex items-center justify-between w-full p-4 text-left border rounded-lg hover:bg-gray-50 transition-colors">
                <span class="font-semibold">Bagaimana proses peminjaman barang?</span>
                <ChevronDown v-if="isOpen('faq2')" class="w-4 h-4" />
                <ChevronRight v-else class="w-4 h-4" />
              </CollapsibleTrigger>
              <CollapsibleContent class="p-4 border-t">
                <div class="space-y-2 text-gray-600">
                  <p>Proses peminjaman barang meliputi:</p>
                  <ol class="list-decimal list-inside space-y-1 ml-4">
                    <li>Buka menu "Peminjaman"</li>
                    <li>Klik "Tambah Peminjaman"</li>
                    <li>Pilih barang yang akan dipinjam</li>
                    <li>Isi tanggal pinjam dan tanggal kembali</li>
                    <li>Tambahkan catatan jika diperlukan</li>
                    <li>Klik "Simpan" untuk membuat peminjaman</li>
                    <li>Status akan berubah menjadi "Dipinjam"</li>
                  </ol>
                </div>
              </CollapsibleContent>
            </Collapsible>

            <!-- FAQ Item 3 -->
            <Collapsible :open="isOpen('faq3')" @update:open="toggleSection('faq3')">
              <CollapsibleTrigger class="flex items-center justify-between w-full p-4 text-left border rounded-lg hover:bg-gray-50 transition-colors">
                <span class="font-semibold">Bagaimana cara membuat permintaan barang?</span>
                <ChevronDown v-if="isOpen('faq3')" class="w-4 h-4" />
                <ChevronRight v-else class="w-4 h-4" />
              </CollapsibleTrigger>
              <CollapsibleContent class="p-4 border-t">
                <div class="space-y-2 text-gray-600">
                  <p>Untuk membuat permintaan barang:</p>
                  <ol class="list-decimal list-inside space-y-1 ml-4">
                    <li>Buka menu "Permintaan"</li>
                    <li>Klik "Tambah Permintaan"</li>
                    <li>Pilih kategori barang yang diminta</li>
                    <li>Isi detail permintaan (jumlah, alasan, dll)</li>
                    <li>Upload dokumen pendukung jika ada</li>
                    <li>Klik "Kirim Permintaan"</li>
                    <li>Permintaan akan masuk ke admin untuk approval</li>
                  </ol>
                </div>
              </CollapsibleContent>
            </Collapsible>

            <!-- FAQ Item 4 -->
            <Collapsible :open="isOpen('faq4')" @update:open="toggleSection('faq4')">
              <CollapsibleTrigger class="flex items-center justify-between w-full p-4 text-left border rounded-lg hover:bg-gray-50 transition-colors">
                <span class="font-semibold">Bagaimana cara melihat laporan?</span>
                <ChevronDown v-if="isOpen('faq4')" class="w-4 h-4" />
                <ChevronRight v-else class="w-4 h-4" />
              </CollapsibleTrigger>
              <CollapsibleContent class="p-4 border-t">
                <div class="space-y-2 text-gray-600">
                  <p>Untuk melihat laporan:</p>
                  <ol class="list-decimal list-inside space-y-1 ml-4">
                    <li>Buka menu "Laporan"</li>
                    <li>Pilih jenis laporan yang diinginkan</li>
                    <li>Atur filter tanggal jika diperlukan</li>
                    <li>Klik "Generate Laporan"</li>
                    <li>Laporan dapat diunduh dalam format PDF</li>
                  </ol>
                </div>
              </CollapsibleContent>
            </Collapsible>

            <!-- FAQ Item 5 -->
            <Collapsible :open="isOpen('faq5')" @update:open="toggleSection('faq5')">
              <CollapsibleTrigger class="flex items-center justify-between w-full p-4 text-left border rounded-lg hover:bg-gray-50 transition-colors">
                <span class="font-semibold">Bagaimana jika lupa password?</span>
                <ChevronDown v-if="isOpen('faq5')" class="w-4 h-4" />
                <ChevronRight v-else class="w-4 h-4" />
              </CollapsibleTrigger>
              <CollapsibleContent class="p-4 border-t">
                <div class="space-y-2 text-gray-600">
                  <p>Jika lupa password:</p>
                  <ol class="list-decimal list-inside space-y-1 ml-4">
                    <li>Klik "Lupa Password" di halaman login</li>
                    <li>Masukkan email yang terdaftar</li>
                    <li>Klik "Kirim Link Reset"</li>
                    <li>Cek email untuk link reset password</li>
                    <li>Klik link dan buat password baru</li>
                    <li>Jika tidak ada email, hubungi admin</li>
                  </ol>
                </div>
              </CollapsibleContent>
            </Collapsible>
          </div>
        </CardContent>
      </Card>

      <!-- Panduan Detail -->
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <BookOpen class="w-5 h-5 text-blue-500" />
            Panduan Detail
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Manajemen Barang -->
            <div class="space-y-4">
              <h3 class="font-semibold text-lg flex items-center gap-2">
                <Package class="w-5 h-5 text-blue-500" />
                Manajemen Barang
              </h3>
              <div class="space-y-3 text-gray-600">
                <div class="p-3 bg-blue-50 rounded-lg">
                  <h4 class="font-medium text-blue-800">Tambah Barang</h4>
                  <p class="text-sm">Isi semua field wajib dan upload foto jika diperlukan</p>
                </div>
                <div class="p-3 bg-green-50 rounded-lg">
                  <h4 class="font-medium text-green-800">Edit Barang</h4>
                  <p class="text-sm">Klik tombol edit pada daftar barang untuk mengubah data</p>
                </div>
                <div class="p-3 bg-red-50 rounded-lg">
                  <h4 class="font-medium text-red-800">Hapus Barang</h4>
                  <p class="text-sm">Hanya barang yang tidak sedang dipinjam yang dapat dihapus</p>
                </div>
              </div>
            </div>

            <!-- Peminjaman -->
            <div class="space-y-4">
              <h3 class="font-semibold text-lg flex items-center gap-2">
                <Clock class="w-5 h-5 text-green-500" />
                Sistem Peminjaman
              </h3>
              <div class="space-y-3 text-gray-600">
                <div class="p-3 bg-green-50 rounded-lg">
                  <h4 class="font-medium text-green-800">Buat Peminjaman</h4>
                  <p class="text-sm">Pilih barang, tentukan tanggal pinjam dan kembali</p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-lg">
                  <h4 class="font-medium text-yellow-800">Status Peminjaman</h4>
                  <p class="text-sm">Monitor status: Pending, Dipinjam, Dikembalikan, Terlambat</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg">
                  <h4 class="font-medium text-blue-800">Pengembalian</h4>
                  <p class="text-sm">Klik tombol kembalikan untuk mengubah status</p>
                </div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Kontak Support -->
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <MessageCircle class="w-5 h-5 text-indigo-500" />
            Butuh Bantuan Lebih Lanjut?
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center p-4 border rounded-lg">
              <Video class="w-8 h-8 text-blue-500 mx-auto mb-2" />
              <h3 class="font-semibold">Video Tutorial</h3>
              <p class="text-sm text-gray-600">Tonton panduan video lengkap</p>
            </div>
            <div class="text-center p-4 border rounded-lg">
              <FileText class="w-8 h-8 text-green-500 mx-auto mb-2" />
              <h3 class="font-semibold">Dokumentasi</h3>
              <p class="text-sm text-gray-600">Baca dokumentasi lengkap</p>
            </div>
            <div class="text-center p-4 border rounded-lg">
              <MessageCircle class="w-8 h-8 text-purple-500 mx-auto mb-2" />
              <h3 class="font-semibold">Hubungi Admin</h3>
              <p class="text-sm text-gray-600">Kontak tim IT untuk bantuan</p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<style scoped>
.collapsible-trigger {
  transition: all 0.2s ease;
}

.collapsible-trigger:hover {
  background-color: rgba(0, 0, 0, 0.02);
}
</style> 