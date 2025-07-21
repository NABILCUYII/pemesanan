<template>
  <Head title="Privacy Policy" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gradient-to-br from-[#F0F8FF] via-[#E6F3FF] to-[#F0FFF0] py-12 px-4 sm:px-6 lg:px-8">
      
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-[#2F4F4F] mb-2">Privacy Policy</h1>
            <p class="text-[#708090]">Kebijakan Privasi Sistem Peminjaman Barang</p>
          </div>
          <Link
            href="/dashboard"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-300"
          >
            <ArrowLeft class="h-4 w-4" />
            Kembali
          </Link>
        </div>
      </div>

      <!-- Content -->
      <Card class="max-w-4xl mx-auto overflow-hidden rounded-2xl border border-[#B0C4DE] bg-white/80 shadow-xl backdrop-blur-xl">
        <CardHeader class="border-b border-[#B0C4DE] bg-gradient-to-r from-[#20B2AA] to-[#87CEEB] p-6">
          <CardTitle class="text-xl font-bold text-white flex items-center gap-2">
            <Shield class="h-5 w-5" />
            Kebijakan Privasi
          </CardTitle>
        </CardHeader>
        <CardContent class="p-8">
          <div class="prose prose-lg max-w-none">
            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">1. Informasi yang Kami Kumpulkan</h2>
            <p class="text-[#708090] mb-6">
              Kami mengumpulkan informasi yang Anda berikan secara langsung kepada kami, seperti:
            </p>
            <ul class="list-disc list-inside text-[#708090] mb-6 space-y-2">
              <li>Informasi profil (nama, email, nomor telepon)</li>
              <li>Data peminjaman barang</li>
              <li>Riwayat aktivitas dalam sistem</li>
              <li>Informasi perangkat dan browser</li>
            </ul>

            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">2. Bagaimana Kami Menggunakan Informasi</h2>
            <p class="text-[#708090] mb-6">
              Informasi yang kami kumpulkan digunakan untuk:
            </p>
            <ul class="list-disc list-inside text-[#708090] mb-6 space-y-2">
              <li>Menyediakan layanan peminjaman barang</li>
              <li>Mengelola inventaris dan stok</li>
              <li>Mengirim notifikasi dan laporan</li>
              <li>Meningkatkan kualitas layanan</li>
              <li>Memenuhi kewajiban hukum</li>
            </ul>

            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">3. Keamanan Data</h2>
            <p class="text-[#708090] mb-6">
              Kami menerapkan standar keamanan yang tinggi untuk melindungi data Anda:
            </p>
            <ul class="list-disc list-inside text-[#708090] mb-6 space-y-2">
              <li>Enkripsi data end-to-end</li>
              <li>Autentikasi multi-faktor</li>
              <li>Backup data secara berkala</li>
              <li>Monitoring keamanan 24/7</li>
            </ul>

            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">4. Berbagi Informasi</h2>
            <p class="text-[#708090] mb-6">
              Kami tidak akan menjual, menyewakan, atau membagikan informasi pribadi Anda kepada pihak ketiga tanpa persetujuan Anda, kecuali:
            </p>
            <ul class="list-disc list-inside text-[#708090] mb-6 space-y-2">
              <li>Dibutuhkan untuk menyediakan layanan</li>
              <li>Diperlukan oleh hukum</li>
              <li>Untuk melindungi hak dan keamanan kami</li>
            </ul>

            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">5. Hak Anda</h2>
            <p class="text-[#708090] mb-6">
              Anda memiliki hak untuk:
            </p>
            <ul class="list-disc list-inside text-[#708090] mb-6 space-y-2">
              <li>Mengakses data pribadi Anda</li>
              <li>Memperbarui informasi yang tidak akurat</li>
              <li>Menghapus data pribadi Anda</li>
              <li>Membatasi penggunaan data</li>
              <li>Portabilitas data</li>
            </ul>

            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">6. Cookie dan Teknologi Serupa</h2>
            <p class="text-[#708090] mb-6">
              Kami menggunakan cookie dan teknologi serupa untuk:
            </p>
            <ul class="list-disc list-inside text-[#708090] mb-6 space-y-2">
              <li>Mengingat preferensi Anda</li>
              <li>Menganalisis penggunaan website</li>
              <li>Meningkatkan pengalaman pengguna</li>
              <li>Menyediakan konten yang relevan</li>
            </ul>

            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">7. Perubahan Kebijakan</h2>
            <p class="text-[#708090] mb-6">
              Kami dapat memperbarui kebijakan privasi ini dari waktu ke waktu. Perubahan akan diumumkan melalui:
            </p>
            <ul class="list-disc list-inside text-[#708090] mb-6 space-y-2">
              <li>Notifikasi di aplikasi</li>
              <li>Email kepada pengguna</li>
              <li>Pengumuman di website</li>
            </ul>

            <h2 class="text-2xl font-bold text-[#2F4F4F] mb-4">8. Kontak</h2>
            <p class="text-[#708090] mb-6">
              Jika Anda memiliki pertanyaan tentang kebijakan privasi ini, silakan hubungi kami:
            </p>
            <div class="bg-[#F0F8FF] p-6 rounded-lg">
              <p class="text-[#708090] mb-2"><strong>Email:</strong> privacy@peminjaman.com</p>
              <p class="text-[#708090] mb-2"><strong>Telepon:</strong> +62 21 1234 5678</p>
              <p class="text-[#708090]"><strong>Alamat:</strong> Jl. Teknologi No. 123, Jakarta Selatan</p>
            </div>

            <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
              <p class="text-yellow-800 text-sm">
                <strong>Terakhir diperbarui:</strong> 1 Januari 2024
              </p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft, Shield } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
  {
    title: 'Privacy Policy',
    href: '/privacy',
  },
];
</script> 